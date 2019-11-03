
<?php
include ("header.php");
include ("menu.php");
?>
<script>
function getMaterialId(memberId,$matKind)
{

var x="Please enter the material ID";
var matId=prompt(x,"");

if (matId!=null)
  {
	
  window.location="./checkin.php?memberId="+memberId+"&materialId="+matId+"&matKind="+$matKind;
  }
}

</script>

<?php
   require_once('classes/db.php');
   $db = new MyDB();//or die(mysql_error());


date_default_timezone_set ( 'America/New_York' );
$today = time ();

if(!isset($_GET['id'])){

	echo  '<script type="text/javascript"> window.location="./home.php";</script>';
}
else {
	$query = "select memberid,checkoutdate::date,duedate::date  from matchkout where MATERIALID = ".$_GET['id'].
		" and MATERIALTYPE = '".$_GET['matType']."'";
//	echo "select count(*) as c, memberid ".$query."<br>";

	$result = $db->query($query);
	if(pg_num_rows($result)==0){
		echo '<script>alert("Error.  Material is NOT checked out");';
		echo 'window.location="./home.php";';
		echo '</script>';
	}
	$row = pg_fetch_row($result);
	header("location: ./confirm_circulation.php?memberId=".$row[0]."&materialId=".$_GET["id"]."&matKind=".$_GET["matType"]."&op=in");
}
//		echo '<script> window.location="./confirm_circulation.php?memberId="+$row["memberid"]+"&materialId="+$_GET["id"]+"&matKind="+$_GET["matType+"&op=in" </script>' ;
//	$result = $db->exec ( "select * ".$query);
//	echo var_dump($result)."<br>";	
	/*$row = $result->fetchArray(SQLITE3_ASSOC);
	echo var_dump($row);*/

?>

<?php
include ("footer.php");
?>
