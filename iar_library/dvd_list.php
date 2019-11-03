<?php
include ("header.php");
include ("menu.php");
?>

<?php

header ( 'Content-type: text/html; charset=utf-8' );
require_once('classes/db.php');
$db = new MyDB();//or die(mysql_error());

// MemberInfo has: memberid, firstname, lastname
$result = $db->query ( 'SELECT dvdid,dvdname, author1, checkedout FROM dvdsTbl order by dvdid' );
$result = $db->query ( 'SELECT dvdid,dvdname, author1, '.
		"case when exists (select true from matchkout m where m.materialid= b.dvdid and m.materialtype='DVD') then true else false  end as out  ".
		'FROM dvdsTbl b order by dvdid' );

// var_dump($result->fetchArray());
echo "<div id='content' sytle='float:left' align='center'>";
echo "<table border = 1 >";
displayTableHeader(array("DVD Id","DVD Name","Author","Checked Out"));

$i = 1;

while ( $row = pg_fetch_row($result) ) {
	$i ++;
	if ($i == intval ( $i / 2 ) * 2) {
		echo "<tr bgcolor=lightgray>";
	} else {
		echo "<tr >";
	}
	displayCenterCell("<a href='dvd.php?id=".$row[0]."'>".$row[0]."</a>");
	displayCell($row[1]);
	displayCell($row[2]);
	displayTFCell($row[3]);
	echo "</tr>";
}
echo "</table>";
echo "</div>";
?>

<?php
include ("footer.php");
?>
