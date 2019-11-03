<?php
include ("header.php");
include ("menu.php");
?>
<script>
function checkout(memId)
{
  window.location="./member_info.php?id="+memId;
}
</script>
<?php

//header ( 'Content-type: text/html; charset=utf-8' );
require_once('classes/db.php');
$db = new MyDB();//or die(mysql_error());

date_default_timezone_set ( 'America/New_York' );
//hidden form to collect information and send it to checkout.php

echo "<div id='content' sytle='float:left'  align='center'>";
echo "<table cellspacing='2'>";

$tableName = "";
$matColId = "";
$materialTitle = "";
if ($_GET ['matKind'] == "BOOK") {
	$tableName = "bookstbl";
	$materialTitle = "BookName";
	$matColId = "bookid";
	$maxOut = "BKCHOUTPERIOD";
	$icon = "'./img/book_icon.jpg'";
	$grace = "bkgraceperiod";
}
if ($_GET ['matKind'] == "DVD") {
	$tableName = "dvdstbl";
	$materialTitle = "DvdName";
	$matColId = "dvdid";
	$maxOut = "VEDCHOUTPERIOD";
	$icon = "'./img/dvd.jpg'";
	$grace = "dvdgraceperiod";
}
if ($_GET ['matKind'] == "CD") {
	$tableName = "cdstbl";
	$materialTitle = "CDName";
	$matColId = "cdid";
	$maxOut = "CDCHOUTPERIOD";
	$icon = "'./img/cdicon.jpg'";
	$grace = "cdgraceperiod";
}

$query = "select " . $maxOut . " as maxOut, ".$grace." from configuration";
$result = $db->query ( $query );
$row = pg_fetch_row($result);
$maxOutDays = $row[0];
$graceDays = $row[1];
$checkoutdate = 0;
//$query = 
$query = "select materialid, date(checkoutdate), duedate, extract(day from (now()-duedate)) as late, special from matchkout mat, memberinfo mem where mem.memberid=mat.memberid and materialid = ".  $_GET ['materialId']." and materialtype='".$_GET['matKind']."'";
$result = $db->query($query);

if($_GET['op']=='renew' && pg_num_rows($result)<1){
	echo '<script>alert("Error.  Material is not checked out");';
	echo 'window.location="./member_info.php?id='.$_GET['memberId'].'";';
	echo '</script>';
}

if($_GET['op']=='out' && pg_num_rows($result)>0){
	echo '<script>alert("Error.  Material is already checked out");';
	echo 'window.location="./member_info.php?id='.$_GET['memberId'].'";';
	echo '</script>';
}
if($_GET['op']=='in' && pg_num_rows($result)<1){
	echo '<script>alert("Error.  Material is NOT checked out");';
	echo 'window.location="./home.php";';
	echo '</script>';
}

$fine_amount=0;
$fine_query="";
if($_GET['op']=='in' || $_GET['op']=='renew'){
	if(pg_num_rows($result)>0){
		$row = pg_fetch_row($result);
		$checkoutdate = $row[1];
		if($row[4]=='f'){
			
		
			if($row[3]>$graceDays){
				$fine_amount =  $row[3]*0.25;
				if($fine_amount > 10)
					$fine_amount=10;
				if($fine_amount<1)
					$fine_amount=0;
				else{
					$fine_amount = money_format('%i', $fine_amount);
					$fine_query = "insert into latefees (recid,materialid,materialtype,memberid,checkoutdate,duedate,checkindate,latefees) values(now(),".
					$row[0].",'".$_GET['matKind']."',".$_GET['memberId'].
						",'".$row[1]."','".$row[2]."',now(),".$fine_amount.")";
				}
			}
		}
	}
}

echo "<tr><td>Type:</td><td></td><td><img src=" . $icon . "></img></td></tr>";
if($_GET['op']=="out"){
	echo "<h3>The following " . $_GET ['matKind'] . " will be checked out.  Please confirm</h3>";
}
else if($_GET['op']=="renew"){
	echo "<h3>The following " . $_GET ['matKind'] . " will be renewed";
	if($fine_amount>0)
		echo "<font color='red'> (with a fine)</font>";
	echo ".  Please confirm</h3>";
}
else {
	echo "<h3>The following " . $_GET ['matKind'] . " will be returned.  Please confirm</h3>";
}

$query = "select FirstName as fn, LastName as ln from MemberInfo where memberid =" . $_GET ['memberId'];
$result = $db->query ( $query );
$row = pg_fetch_row($result);
echo "<tr><td>Member: </td>";
echo "<td></td><td>" . $row [0] . "&nbsp" . $row [1] . " (".$_GET['memberId'].")</td></tr>";

$query = "select " . $materialTitle . " as title from " . 
	$tableName . " mat where " . $matColId . "=" . $_GET ['materialId'];
	
$result = $db->query ( $query );
$row = pg_fetch_row($result);
echo "<tr><td>Title: </td><td></td>";
echo "<td>" . $row [0] . "</td></tr>";
if($_GET['op']=="out" )
	echo "<tr><td>Checkout Date:</td><td></td><td>" . strftime ( "%m-%d-%Y", time () ) . "</td></tr>";
else 
	echo "<tr><td>Checkout Date:</td><td></td><td>" . $checkoutdate. "</td></tr>";

echo "<tr><td>Due Date:</td><td></td><td>" . strftime ( "%m-%d-%Y", time () + $maxOutDays * 60 * 60 * 24 ) . "</td></tr>";
if($fine_amount>0)
	echo "<tr style='color:red'><td>Fine: </td><td></td><td>".$fine_amount."</td>";
echo "</table><br>";

echo "<form action='./do_circulation.php?id=".$_GET['memberId']."' method='post'>\n";

echo "<input type='hidden' name='matId' value=".$_GET['materialId']." >\n";
echo "<input type='hidden' name='matType' value=".$_GET['matKind']." >\n";
echo "<input type='hidden' name='op' value=".$_GET['op'].">\n";
echo "<input type='hidden' name='period' value=".$maxOutDays." >\n";
echo "<input type='hidden' name='query_fine' value=\"".$fine_query."\" >\n";

echo "<input type='submit' value='Submit'>\n";
echo "</form>\n";
echo "</div>";
?>


<?php
include ("footer.php");
?>
