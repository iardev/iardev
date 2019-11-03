<?php
require_once 'include/const.php';
class MyDB {
	private $db_conn;
	function __construct() {
		
		$this->db_conn = pg_connect ( DB_CONN_STR ) or die ( "Can't connect to db" );
	}
	function lookup_user($un) {
		//$result = pg_prepare ( $this->db_conn, "my_query", "select password,id,fully_created,active,hash,first_name,last_name from ver1.member where email = $1" );
		$result = pg_prepare ( $this->db_conn, "my_query", "select password from users where lower(firstname) = lower($1)");
		$result = pg_execute ( $this->db_conn, "my_query", array (
				$un 
		) );
		return $result;
	}
	function query($qr){
		return pg_query($this->db_conn,$qr);
	}
	function new_doc_id(){
		$result =  pg_query($this->db_conn, "select ver1.uuid_generate_v4()");
		$row = pg_fetch_row($result);
		return $row[0];
	}
	function verify_user($un, $pwd, &$name) {
		//return 1;
		$result = self::lookup_user ( $un );
		if ($row = pg_fetch_row ( $result )) {
			if (password_verify ( $pwd, $row [0] )) {
				return 1;
			}
		}
		return 0;
	}
	function update_password($un, $oldPwd, $newPwd) {
		//return 1;
		$result = self::lookup_user ( $un );
		if ($row = pg_fetch_row ( $result )) {
			if (password_verify ( $oldPwd, $row [0] )) {
				$result=pg_prepare($this->db_conn,"myqr","update users set password = $1 where LOWER(firstname) = LOWER($2)");
				$result = pg_execute($this->db_conn, "myqr",array(password_hash($newPwd, PASSWORD_DEFAULT),$un));
				return 1;
			}
		}
		
		return 0;
	}
	function update_view_date($id){
		$result = pg_prepare($this->db_conn, 
				"my_query4",
				"update ver1.doc set view_date = date(now()) + auto_extend+1 where owner_id=$1 and auto_extend>0 and view_date <= date(now()) + auto_extend+1");
		$result = pg_execute ( $this->db_conn, "my_query4", array($id));
	}
	function activate_user($email, $hash) {
		$result = pg_prepare ( $this->db_conn, "my_query", "update ver1.member set active='t', hash='' where email=$1 and hash=$2 returning id" );
		$result = pg_execute ( $this->db_conn, "my_query", array (
				$email,
				$hash 
		) );
		if ($row = pg_fetch_row ( $result )) {
			return $row [0];
		}
		return 0;
	}
	function check_invited_user($email,$hash){
		static $once = false;
		if($once===false){
			$query = "select id,first_name,last_name from ver1.member where email =$1 and hash=$2";
			$result = pg_prepare($this->db_conn,"checkInvitedUser", $query);
		}
		$result = pg_execute ( $this->db_conn, "checkInvitedUser", array($email,$hash));
		if ($row = pg_fetch_row ( $result )) {
			return $row [0];	
		}
		return 0;	
			
	}
	function update_user($fname, $lname, $un, $pwd,&$name){
		static $once = false;
		if($once===false){
			$query = "update ver1.member set first_name = $1, last_name = $2, password=$4, hash='', active='t' where email=$3 returning id,first_name||' '||last_name";
			$result = pg_prepare($this->db_conn,"update_user", $query);
		}
		$result = pg_execute ( $this->db_conn, "update_user", array($fname, $lname, $un, $pwd));
		if ($row = pg_fetch_row ( $result )) {
			$name = $row[1];
			return $row [0];
		}
		return 0;
	}
	
	function create_user($fname, $lname, $un, $pwd, $hash) {
		$result = self::lookup_user ( $un );
		if (pg_num_rows ( $result ) > 0)
			return 0;
		
		$query = "insert into ver1.member (first_name,last_name,email,password,hash,active,fully_created) values($1,$2,$3,$4,$5,$6,$7) returning id";
		$result = pg_prepare ( $this->db_conn, "my_query1", $query );
		$result = pg_execute ( $this->db_conn, "my_query1", array (
				$fname,
				$lname,
				$un,
				$pwd,
				$hash,
				'f',
				't' 
		) );
		if ($row = pg_fetch_row ( $result )) {
			return $row [0];
		}
		// }
		return 0;
	}
	function create_user_as_viewer($un, $hash, $memId) {
		$query = "insert into ver1.member (first_name,last_name,email,password,hash,active,fully_created) values($1,$2,$3,$4,$5,$6,$7) returning id";
		$result = pg_prepare ( $this->db_conn, "my_query1", $query );
		$result = pg_execute ( $this->db_conn, "my_query1", array (
						"",
						"",
						$un,
						$hash,
						$hash,
						'f',
						't'
		) );
		if ($row = pg_fetch_row ( $result )) {
			$result = pg_prepare($this->db_conn,"aqr","insert into ver1.viewers values($1,$2) on conflict(owner_id,viewer_id) do nothing;");
			$result = pg_execute($this->db_conn, "aqr",array($memId,$row[0]));
			return $row[0];
		}
		// }
		return 0;
	}
	function add_viewer($memid,$viewer_email){
		$result = self::lookup_user ( $viewer_email );
		
		if (pg_num_rows ( $result ) > 0){
			$row = pg_fetch_row ( $result );
			$result = pg_prepare($this->db_conn,"aqr","insert into ver1.viewers values($1,$2) on conflict(owner_id,viewer_id) do nothing;");
			$result = pg_execute($this->db_conn, "aqr",array($memid,$row[1]));
			return 1;
		}
		return 0;
			
	}
	function create_doc($memid) {
			$query = "insert into ver1.doc (owner_id) values($1) returning id";
			$result = pg_prepare ( $this->db_conn, "my_query1", $query );
			$result = pg_execute ( $this->db_conn, "my_query1", array ($memid));
			if ($row = pg_fetch_row ( $result )) {
				return $row [0];
			}
			return 0;
	}
	
		// "id,title,description,password,create_date,view_date,doc_id, viewer_count, expired, d.file_name
	function getMyDocs($id,$filter=null) {
		/* old query uses join with doc_viewer.    
		$qry = "select d.id,d.title,d.description,d.password,to_char(d.create_date,'MM/DD/YYYY'),to_char(d.view_date,'MM/DD/YYYY'),d.doc_id, " . 
				"(select count(*) from ver1.doc_viewer w where w.doc_id = d.id), " . 
				"case when d.view_date < now() then true else false end as expired, " . 
				"d.file_name " . "from ver1.doc d left outer join ver1.doc_viewer v on d.id = v.doc_id " . 
				"where d.owner_id=$1 ";
		if(!is_null($filter))
			$qry .= $filter;
		$qry .= " group by d.id order by d.view_date ";
		*/
		$qry = "select d.id,d.title,d.description,d.password,to_char(d.create_date,'MM/DD/YYYY'),to_char(d.view_date,'MM/DD/YYYY'),d.doc_id, " . 
				"(select count(*) from ver1.doc_viewer w where w.doc_id = d.id), " . 
				"case when d.view_date < now() then true else false end as expired, " . 
				"d.file_name, d.view_date - date(now()), auto_extend " . "from ver1.doc d " . 
				"where d.owner_id=$1 ";
		if(!is_null($filter))
			$qry .= $filter;
		$qry .= " order by d.view_date ";
		$result = pg_prepare ( $this->db_conn, "my_query",$qry);
		$result = pg_execute ( $this->db_conn, "my_query", array (
				$id 
		) );
		return $result;
	}
	
	
	//select d.id,d.title,d.description,d.password,to_char(d.create_date,'MM/DD/YYYY'),to_char(d.view_date,'MM/DD/YYYY'), d.doc_id, CASE WHEN EXISTS (select true from ver1.doc_viewer vv where vv.doc_id=d.id and vv.member_id=$2) THEN true ELSE false end as apply, case when d.view_date < now() then true else false end as expired, d.file_name, CASE WHEN EXISTS (select true from ver1.doc_viewer vv where vv.doc_id=d.id and vv.member_id=$2 and vv.ack_view='t') THEN true ELSE false end as seen  from ver1.doc d left outer join ver1.doc_viewer v on d.id = v.doc_id where d.owner_id=$1 group by d.id order by d.id" )
	
	
	
	function verify_viewer($me,$viewer, &$name){
		$result = pg_prepare($this->db_conn, "aquery",
				"select first_name||' '||last_name,email from ver1.member m, ver1.viewers v where v.owner_id=$1 and v.viewer_id=$2 and m.id=v.viewer_id");
		$result = pg_execute($this->db_conn, "aquery",array($me,$viewer));
		if ($row = pg_fetch_row ( $result )){
			if( $row[0]==" ")
				$name = $row[1];
			else 
				$name = $row[0];
			return true;
		}
		return false;
		
	}
	function del_viewer($me,$viewer){
		$result = pg_prepare($this->db_conn, "aquery","delete from ver1.doc_viewer dv where member_id = $2 and doc_id = (select d.id from ver1.doc d where d.id = dv.doc_id and d.owner_id=$1);");
		$result = pg_execute($this->db_conn, "aquery",array($me,$viewer));
		if($result)
		{
			$result = pg_prepare($this->db_conn,"qry", "delete from ver1.viewers where owner_id=$1 and viewer_id=$2");
			$result = pg_execute($this->db_conn,"qry", array($me,$viewer));
			return $result;
		}
		return  $result;
	}
	
	// id  title  description  password  createDate  viewData  uuid  canView  expired file_name seen
	function getMyDocsForViewer($memid, $viewerid) {
		$result = pg_prepare ( $this->db_conn, "my_query", "select d.id,d.title,d.description,d.password,to_char(d.create_date,'MM/DD/YYYY'),to_char(d.view_date,'MM/DD/YYYY'), d.doc_id, " . 
				"CASE WHEN EXISTS (select true from ver1.doc_viewer vv where vv.doc_id=d.id and vv.member_id=$2) THEN true ELSE false end as apply, " . 
				"case when d.view_date < now() then true else false end as expired, d.file_name, " .
				"CASE WHEN EXISTS (select true from ver1.doc_viewer vv where vv.doc_id=d.id and vv.member_id=$2 and vv.ack_view='t') THEN true ELSE false end as seen, " .
				"d.view_date - date(now()), auto_extend ".
				"from ver1.doc d left outer join ver1.doc_viewer v on d.id = v.doc_id where d.owner_id=$1 group by d.id order by d.view_date" );
		
		$result = pg_execute ( $this->db_conn, "my_query", array (
				$memid,
				$viewerid 
		) );
		return $result;
	}
	function addViewerToDoc($docid, $viewerid) {
		$result = pg_prepare ( $this->db_conn, "my_query", "insert into ver1.doc_viewer(doc_id,member_id) values($1,$2) " );
		$result = pg_execute ( $this->db_conn, "my_query", array (
				$docid,
				$viewerid 
		) );
		return $result;
	}
	function deleteViewerFromDoc($docid, $viewerid) {
		$result = pg_prepare ( $this->db_conn, "my_query", "delete FROM ver1.doc_viewer where doc_id=$1 and member_id=$2" );
		$result = pg_execute ( $this->db_conn, "my_query", array (
				$docid,
				$viewerid 
		) );
		return $result;
	}

function getViewersForDoc($docid, $memid) {
		$result = pg_prepare ( $this->db_conn, "my_query1", "select ".
				"m.id, ".
				"m.first_name, ".
				"m.last_name, ".
				"m.email, " . 
				"CASE WHEN EXISTS (select dv.member_id from ver1.doc_viewer dv where dv.member_id = v.viewer_id and dv.doc_id=$1) THEN true ELSE false end as apply, " . 
				"CASE WHEN EXISTS (select true from ver1.doc_viewer dv where dv.member_id = v.viewer_id and dv.doc_id=$1 and dv.ack_view='t') THEN true ELSE false end as viewer_received ".
				"from ver1.member m, ver1.viewers v where v.viewer_id = m.id and v.owner_id = $2" );
		
		$result = pg_execute ( $this->db_conn, "my_query1", array (
				$docid,
				$memid 
		) );
		return $result;
	}
	function delDoc($qry, $id, $ownerid) {
		$result = pg_prepare ( $this->db_conn, "qr1", $qry );
		$result = pg_execute ( $this->db_conn, "qr1", array (
				$id ,
				$ownerid
		) );
		return $result;
	}
	function updateDoc($qry, $id) {
		$result = pg_prepare ( $this->db_conn, "qr1", $qry );
		$result = pg_execute ( $this->db_conn, "qr1", array (
				$id 
		) );
		return $result;
	}
	function getDocsICanView($id, $filter) {
		$res =  pg_prepare ( $this->db_conn, 
				"my_query3", 
				"update ver1.doc_viewer set ack_view='t' where ".
				"member_id=$1 and doc_id in ".
				"(select dv.doc_id from ver1.doc d, ver1.doc_viewer dv where view_date < now() and dv.member_id=$1 and d.id=dv.doc_id)");
		$res = pg_execute ( $this->db_conn, "my_query3", array (
				$id 
		) );
		
		$result = pg_prepare ( $this->db_conn, "my_query2", "select ".
				"m.first_name, ".
				"m.last_name, ".
				"m.email, ".
				"d.title, ".
				"d.description, ".
				"to_char(d.view_date,'MM/DD/YYYY'), ".
				"d.file_name, ".
				"d.doc_id, " . 
				"case when d.view_date < now() then d.password else '' end as password " . 
				"from ver1.doc_viewer dv, ver1.doc d, ver1.member m where m.id = d.owner_id and d.id = dv.doc_id and member_id = $1 ".$filter );
		$result = pg_execute ( $this->db_conn, "my_query2", array (
				$id 
		) );
		return $result;
	} // select m.id,m.first_name, m.last_name,m.email, (select count(*) from ver1.doc d, ver1.doc_viewer dv where dv.doc_id = d.id and d.owner_id=v.owner_id and dv.member_id=v.viewer_id) from ver1.viewers v, ver1.member m where m.id = v.viewer_id and v.owner_id =
	function getMyViewers($id,$filter) {
		static $once = false;
		if($once === false)
		{
			$result = pg_prepare ( $this->db_conn, "GetViewers", "select * from (select ".
					"m.id, ".
					"m.first_name, ".
					"m.last_name, ".
					"m.email, " . 
				"(select count(*) from ver1.doc d, ver1.doc_viewer dv where dv.doc_id = d.id and d.owner_id=v.owner_id and dv.member_id=v.viewer_id) as access, " . 
				"(select count(*) from ver1.doc d, ver1.doc_viewer dv where dv.doc_id = d.id and d.owner_id=v.owner_id and dv.member_id=v.viewer_id and dv.ack_view=true) as seen, " .
				"(select count(*) from ver1.doc d, ver1.doc_viewer dv where dv.doc_id = d.id and d.owner_id=v.owner_id and dv.member_id=v.viewer_id and d.view_date<now()) as expired " .
					"from ver1.viewers v, ver1.member m where m.id = v.viewer_id and v.owner_id =$1 ) qr ".$filter);
			$once=true;
		}
		$result = pg_execute ( $this->db_conn, "GetViewers", array (
				$id 
		) );
		return $result;
	}
	function getDoc($id,$memid) {
		$result = pg_prepare ( $this->db_conn, "my_query", "select title,description,password,file_name, ".
				"to_char(view_date,'MM/DD/YYYY'),doc_id, " . 
				"case when view_date < now() then true else false end as expired, auto_extend, " .
				"send_ency_2_viewers, to_char(create_date,'MM/DD/YYYY') ".
				"from ver1.doc where id = $1 and owner_id=$2");
		$result = pg_execute ( $this->db_conn, "my_query", array (
				$id ,
				$memid
		) );
		return $result;
	}
	function getDocStatus($memid) {
		
		$query = "select (select count(*) from ver1.doc where owner_id=$1) as total, ".
			"(select count(*) from ver1.doc where owner_id=$1 and view_date<now()) as expired,  ".
			"(select count(*) from ver1.doc where owner_id=$1 and file_name is null) as missing_file, ".
			"(select count(*) from ver1.doc where owner_id=$1 and file_name is null and view_date<now()) as expired_missing_file, ".
			"(select to_char(view_date,'MM/DD/YYYY') from ver1.doc where owner_id=$1 and view_date>now()  order by view_date limit 1) as expire_next_week, ".
			"(select count(*) from ver1.doc where owner_id=$1 and view_date>now() and view_date <= date(now())+integer '1'  ) as expire_within_day, ".
			"(select count(*) from ver1.doc where owner_id=$1 and view_date>now() and view_date <= date(now())+integer '7'  ) as expire_within_week,  ".
			"(select count(*) from ver1.doc where owner_id=$1 and view_date>now() and view_date <= date(now())+integer '30'  ) as exipre_with_month,  ".
			"(select count(*) from ver1.doc where owner_id=$1 and view_date>now() and view_date <= date(now())+integer '365'  ) as exipre_with_year, ".
			"(select count(*) from ver1.doc where owner_id=$1 and auto_extend=0  ) as no_auto_extend; ";
		$result = pg_prepare($this->db_conn,"qryy", $query);
		$result = pg_execute($this->db_conn, "qryy",array($memid));
		return $result;
	}
	function getFriendsStatus($memid){
		$query = "select ".
				"(select count(*) from ver1.viewers where owner_id=$1) as total, ".
				"(select count(distinct member_id) from ver1.doc_viewer dv, ver1.doc d where d.id = dv.doc_id and d.owner_id=$1) as associated, ".
				"(select count(distinct member_id) from ver1.doc_viewer dv, ver1.doc d where d.id = dv.doc_id and d.owner_id=$1 and d.view_date < now()) as associate_expired, ".
				"(select count(distinct member_id) from ver1.doc_viewer dv, ver1.doc d where d.id = dv.doc_id and d.owner_id=$1 and d.view_date < now() and dv.ack_view='t') as seen ";
		$result = pg_prepare($this->db_conn,"qryy1", $query);
		$result = pg_execute($this->db_conn, "qryy1",array($memid));	
		return $result;
				
	}
	function getOthersDocsStatus($memid){
		$query = "select ".
				"(select count(*) from ver1.doc_viewer where member_id=$1) as total, ".
				"(select count(*) from ver1.doc_viewer v, ver1.doc d where v.member_id=$1 and d.id=v.doc_id and d.view_date < now() and d.file_name != '') as associated";				
		$result = pg_prepare($this->db_conn,"qryy2", $query);
		$result = pg_execute($this->db_conn, "qryy2",array($memid));
		return $result;
	
	}
}
?>

