<?php


require_once 'classes/membership.php';
$membership = new Membership();
$membership -> hasSession();
require_once ('classes/db.php');
$db = new MyDB();
//print_r($_SESSION);
//print_r($_POST);
$special='f';
if(isset($_POST['special']))
	$special='t';
//$db -> query($_POST["query"]);

//header("location: member_info.php?id=".$_POST['memid']+1);
//echo "<script type='text/javascript'> document.location = 'home.php'; </script>";
if($_POST['op']=='new'){
	$query = "insert into memberinfo (firstname, lastname, address, city, state,zipcode, special,
		homephone, workphone,emailaddress,memberid,dateexpired) values('".
		$_POST['fname']."','".
		$_POST['lname']."','".
		$_POST['address']."','".
		$_POST['city']."','".
		$_POST['state']."','".
		$_POST['zcode']."','".
		$special."', '".
		$_POST['hphone']."','".
		$_POST['wphone']."','".
		$_POST['email']."',".
		"(select max(memberid)+1 from memberinfo),'".
		$_POST['expire']."') returning memberid";
	$result = $db->query($query);
	if(pg_numrows($result)!=1){
		echo "<script>alert('Error while trying to create user in DB');</script>";
		header("location:home.php");
	}
	$row = pg_fetch_row($result);
	header("location:member_info.php?id=".$row[0]);
}
else {
	$query = "update  memberinfo set ".
			"firstname='". $_POST['fname']."',".	
			"lastname='". $_POST['lname']."',".
			"address='". $_POST['address']."',".
			"city='". $_POST['city']."',".
			"special='".$special."',".
			"state='". $_POST['state']."',".
			"zipcode='". $_POST['zcode']."',".
			"homephone='". $_POST['hphone']."',".
			"workphone='". $_POST['wphone']."',".
			"emailaddress='". $_POST['email']."',".
			"dateexpired='". $_POST['expire']."' where memberid=".$_POST['op'];
	//print_r($query);
	$db->query($query);
	header("location:member_info.php?id=".$_POST['op']);
}
?>