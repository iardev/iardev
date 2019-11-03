<?php
include ("header.php");
include ("menu.php");
?>

<?php

header ( 'Content-type: text/html; charset=utf-8' );
   require_once('classes/db.php');
   $db = new MyDB();//or die(mysql_error());


// MemberInfo has: memberid, firstname, lastname
//$result = $db->query('update MemberInfo set FirstName="muamar" where memberid=1');
$result = $db->query('SELECT MemberId, FirstName, LastName FROM MemberInfo where memberid > 0 order by memberid ' );
// var_dump($result->fetchArray());
echo "<div id='content' sytle='float:left' align='center'>";
echo "<table border = 1 >";
echo "<tr>";
echo "<th><p style='margin-left:14px;'>Member ID</p>";
echo "</th>";
echo "<th>First Name";
echo "</th>";
echo "<th>Last Name";
echo "</th>";
echo "</tr>\n";
$i = 1;

while ( $row = pg_fetch_row($result) ) {
	$i ++;
	if ($i == intval ( $i / 2 ) * 2) {
		echo "<tr bgcolor=lightgray>";
	} else {
		echo "<tr >";
	}
	echo "<td> <center>"; // <p style='margin-left:14px;'>";
	echo "<a href='./member_info.php?id=" . $row [0] . "'>" . $row [0] . "</a>";
	
	echo "</center></td>";
	echo "<td>";
	echo $row [1];
	echo "</td>";
	echo "<td>";
	echo $row [2];
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
echo "</div>";
?>

<?php
include ("footer.php");
?>
