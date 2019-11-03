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
	
  	window.location="./confirm_circulation.php?memberId="+memberId+"&materialId="+matId+"&matKind="+$matKind+"&op=out";
  }
}

function payFines(memid){
	var sum=0;
	var query="";
	var tbl = document.getElementById("LateFees");
	var arr=[];
	for (var i=1; i<tbl.rows.length; i++){
		if(tbl.rows[i].cells[6].children[0].checked){
			sum+=parseFloat(tbl.rows[i].cells[5].innerHTML);
			query+="delete from latefees where recid='"+ tbl.rows[i].cells[7].innerHTML+"';";
			arr.push(i);
		}
	}
	
	console.log("Testing");
	
	confirm("Pay: "+sum.toFixed(2),"pay_fine.php",query,arr,memid);
	
}
function renew(materialid,memid,type,dueday){
//	if(dueday>4)
//		message("Can't renew.  Material is passed the grace period");
//	else{
		
		txt = "./confirm_circulation.php?memberId="+memid+"&matKind="+type+"&op=renew&materialId="+materialid;
	
		window.location.href = txt;
//	}
}
</script>

<?php
//header ( 'Content-type: text/html; charset=utf-8' );
   require_once('classes/db.php');
   $db = new MyDB();//or die(mysql_error());

   
   
   
echo "<div id='content' sytle='float:left' align='center'>";
date_default_timezone_set ( 'America/New_York' );
$today = time ();

$result = $db->query ( "SELECT FirstName, LastName, to_char(DateExpired,'MM/DD/YYYY') as ed FROM MemberInfo where memberid =" . $_GET ['id'] );
while ( $row = pg_fetch_row ($result) ) {
	echo "<h1 style='display: inline-block'>&nbsp &nbsp";
	echo $row [0];
	echo "&nbsp";
	echo $row [1];
	echo "</h1>";
	echo " <a href=member.php?id=".$_GET['id'].">(edit)</a>";
	echo "<p>";
	echo "  Membership Expires On:&nbsp";
	if ($row [2] < date($today)) {
		echo "<font color='red'>" . $row [2] . "</font>";
	} else {
		//echo strftime ( '%m-%d-%Y', date($row [2]) );
		echo $row[2];
	}
	echo "<p></p>";
}
$has_fines=0;
$query = "select materialid,materialtype,date(checkoutdate),date(duedate),date(checkindate),".
	"latefees,recid from latefees where memberid = ".$_GET['id'];
$result = $db->query($query);
if(pg_num_rows($result)>0){
	$has_fines=1;
	echo "<div id='content' sytle='float:left' align='center'>";
	echo "<P><h2>Late Fees</h2>";
	echo "<table border = 1 id='LateFees' >";
	echo "<tr>";
	echo "<th>Material Id</th>";
	echo "<th>Material Type</th>";
	echo "<th>Check Out Date</th>";
	echo "<th>Due Date</th>";
	echo "<th>Return Date</th>";
	echo "<th>Fee</th>";
	echo "<th><a href='javascript:payFines(".$_GET['id'].")'>Pay Fines</a>";
	echo "</tr>";
	while ( $row = pg_fetch_row ($result) ) {
		echo "<tr>".
				"<td>".$row[0]."</td>".
				"<td>".$row[1]."</td>".
				"<td>".$row[2]."</td>".
				"<td>".$row[3]."</td>".
				"<td>".$row[4]."</td>".
				"<td>".money_format('%i', $row[5])."</td>".
				"<td><input type=checkbox></td>".
				"<td hidden=true>".$row[6]."</td>".
				"</tr>";
	}
	echo "</table></div>";
	echo "<br>";
}
	
	
	
	echo "<P><h2>Checkout Information</h2>";
function displayMaterial($datab, $maxOut, $materialTable, $materialType, $materialColName, $colMatchOn, $materialName, $icon, $hasfines) {

	
	echo "<tr style='background-color:#FFaa00'><td style='height:40px' colspan=6 align='center'>".$materialName."s</td></tr>";
	echo "<tr>";
	echo "<th>Type</th>";
	echo "<th>Id</th>";
	echo "<th>Title</th>";
	echo "<th>Checkout Date</th>";
	echo "<th>Due Date</th>";
	echo "</tr>";
	$query = "select b." . $materialColName . ",m.MATERIALID, " . "to_char(m.CHECKOUTDATE,'MM/DD/YYYY') as outd, " . 
	"to_char(DueDate, 'MM/DD/YYYY') as retd, " . "date(now())-date(m.duedate) from matchkout m join " . $materialTable . " b " . "on b." . $colMatchOn . "=materialid where memberid=" . $_GET ['id'] . " and materialtype='" . $materialType . "'";
	//echo $query."<br>";
	$result = $datab->query ( $query );
	
	// echo "<tr><td align='center' colspan=5><font size='5'>".$tblHeading."</font></td></tr>";
	$i = 1;
	while ( $row = pg_fetch_row($result) ) {
		$i ++;
		if ($i == intval ( $i / 2 ) * 2) {
			//echo "<tr style='background-color:#eeeeee'>";
			echo "<tr>";
		} else {
			echo "<tr >";
		}
		echo "<td><a href=\"javascript:renew(".$row[1].",".$_GET['id'].",'".$materialType."',".$row[4].")\">renew</a></td>";
		echo "<td align='center'>" . $row [1] . "</td>";
		echo "<td align='center' style='overflow: hidden;max-width:300px;word-wrap: break-word'>" . $row [0] . "</td>";
		echo "<td align='center'>" . $row [2] . "</td>";
		echo "<td align='center'>" . $row [3] . "</td>";
		
		
		echo "</tr>";
	}
	$i --;
	while ( $i < $maxOut ) {
		echo "<tr>";
		// echo "<td><a href='./checkout.php'><img src='./img/".$icon."'></a></td>";
		//echo "<td><button onclick=\"getMaterialId(" . $_GET ['id'] . ",'" . $materialType . "')\"><img style='cursor:pointer' src='./img/" . $icon . "'></img></button></td>";
		$txt = "./confirm_circulation.php?memberId=".$_GET['id']."&matKind=".$materialType."&op=out&materialId=";
		if($hasfines==0){
			//echo "<td><button onclick=\"javascript:getId('Please enter material id','".$txt."')\"><img style='cursor:pointer' src='./img/" . $icon . "'></img></button></td>";
			echo "<td colspan=6 align='center'><a href=\"javascript:getId('Please enter ".$materialType." id','".$txt."')\">Check out a ". $materialName . "</a></td></tr>";
		}
		else{
			//echo "<td><img src='./img/" . $icon . "'></td>";
			echo "<td colspan=6><font color='red'>Can't checkout more material till the fines are paid<font></td></tr>";
		}
		$i ++;
	}
	//echo "</table><br><br>";
}


echo "<table  border = 1 style='border:none'>";
displayMaterial ( $db, 3, 'bookstbl', 'BOOK', 'BookName', 'bookid', 'Book', 'book_icon.jpg' ,$has_fines);
echo "<tr style='border:none;height:45px;border-color:transparent'><td style='border:none' colspan=6></td></tr>";
displayMaterial ( $db, 1, 'dvdstbl', 'DVD', 'DvdName', 'dvdid', 'DVD', 'dvd.jpg', $has_fines);
echo "<tr style='border:none;height:45px;border-color:transparent'><td style='border:none' colspan=6></td></tr>";
displayMaterial ( $db, 3, 'cdstbl', 'CD', 'CDName', 'cdid', 'CD', 'cdicon.jpg', $has_fines);
echo "</table>";
echo "<p></p>";
echo "</div>";



?>

<?php
include ("footer.php");
?>
