<?php
include ("header.php");
include ("menu.php");
?>

<?php
require_once('classes/db.php');
$db = new MyDB();//or die(mysql_error());
$query = "select event_id,action,to_char(action_tstamp_tx,'MM/DD/YYYY'), table_name, ".
	"summery_audit(action,row_data,table_name,changed_fields), row_data -> 'memberid', ".
	"row_data -> 'materialid', row_data -> 'materialtype' from audit.logged_actions  ";

if(isset($_GET['id']))
	$query = $query." where row_data ->'memberid' = '".$_GET['id']."'";
$query = $query." order by event_id";
//echo $query."<br>";	
echo "<div id='content' sytle='float:left'  align='center'>";
echo "<table border = 1 cellpadding='10'>";
displayTableHeader(array("History Id","Member Id","Material","Action","Date","Summary"));

$i = 1;
$result=$db->query($query);
while ( $row = pg_fetch_row($result)) {
	$i ++;
	if ($i == intval ( $i / 2 ) * 2) {
		echo "<tr bgcolor=lightgray>";
	} else {
		echo "<tr >";
	}
	displayCenterCell($row[0]);
	displayCell($row[5]);
	displayCell($row[7].": ".$row[6]);
	if($row[1]=='I' && $row['3']=='latefees' )
		displayCenterCell(" Add Fine ");
	if($row[1]=='D' && $row['3']=='latefees' )
		displayCenterCell(" Removed Fine ");
	if($row[1]=='I' && $row['3']=='matchkout' )
		displayCenterCell(" Check Out ");
	if($row[1]=='D' && $row['3']=='matchkout' )
		displayCenterCell(" Check In ");
	if($row[1]=='U' )
		displayCenterCell("Renew");
		
	displayCell($row[2]);
	displayCell($row[4]);
	echo "</tr>";
}
echo "</table>";

echo "</div>";

?>

<?php
include ("footer.php");
?>


