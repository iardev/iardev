
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



if( $_POST['op']=="out" && isset($_POST['matId']) && isset($_POST['period']) && isset($_POST['matType'])) {
	$query = "insert into matchkout (MATERIALID , MATERIALTYPE , MEMBERID , CHECKOUTDATE , DUEDATE , NOTES , RenewNum )".
			" values(".$_POST['matId'].",'".$_POST['matType']."',".$_GET['id'].", now(),now()+interval '".$_POST['period']." day',".
			//date(time()) , ",'',0)";
			"'',0)";
			//date((time()+$_POST['period']*24*60*60)*1000).",'',0)";
			
			$db->query($query);
			echo '<script>alert("Material was checked out successfully");';
			echo 'window.location="./member_info.php?id='.$_GET['id'].'";';
			echo '</script>';
			//header("location: ./member_info.php?id=".$_GET['id']);
}
if( $_POST['op']=="renew" && isset($_POST['matId']) && isset($_POST['period']) && isset($_POST['matType'])) {
	$query = "update matchkout set duedate = now()+interval '".$_POST['period']." day' where materialid = ". $_POST['matId']." and materialtype = '".$_POST['matType']."' and memberid = ".$_GET['id'];
	//echo $query;
	$finequery = $_POST['query_fine'];
	if($finequery!=""){
		$query = $query.";".$finequery;
	}
	$db->query($query);
	echo '<script>alert("Material was renewed successfully");';
	echo 'window.location="./member_info.php?id='.$_GET['id'].'";';
	echo '</script>';
	//header("location: ./member_info.php?id=".$_GET['id']);
}
if( $_POST['op']=="in" && isset($_POST['matId']) && isset($_POST['matType'])) {
	$query = "delete from matchkout where materialid = ". $_POST['matId']." and materialtype = '".$_POST['matType']."' and memberid = ".$_GET['id'];
	$db->query($query);
	$query = $_POST['query_fine'];
	if($query != ""){
		$db->query($query);
	}
	echo '<script>alert("Material was returned successfully");';
	echo 'window.location="./home.php";';
	echo '</script>';
}

?>

<?php
include ("footer.php");
?>
