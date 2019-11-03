<?php
include ("header.php");
include ("menu.php");
?>

<?php

header ( 'Content-type: text/html; charset=utf-8' );
require_once('classes/db.php');
$db = new MyDB();//or die(mysql_error());


// MemberInfo has: memberid, firstname, lastname
$result = $db->query ( 'SELECT bookid,bookname, author1, bookcode, ref, '.
	"case when exists (select true from matchkout m where m.materialid= b.bookid and m.materialtype='BOOK') then true else false  end as out  ".
	'FROM BooksTbl b order by bookid' );
// var_dump($result->fetchArray());
echo "<div id='content' sytle='float:left' align='center'>";
echo "<table border = 1 >";
displayTableHeader(array("Book Id","Book Name","Author","Book Code","Ref","Checked Out"));


$i = 1;

while ( $row = pg_fetch_row($result) ) {
	$i ++;
	if ($i == intval ( $i / 2 ) * 2) {
		echo "<tr bgcolor=lightgray>";
	} else {
		echo "<tr >";
	}
	displayCenterCell("<a href='book.php?id=".$row[0]."'>".$row[0]."</a>");
	displayCell($row[1]);
	displayCell($row[2]);
	displayCell($row[3]);
	displayTFCell($row[4]);
	displayTFCell($row[5]);
	echo "</tr>";
}
echo "</table>";

echo "</div>";
?>

<?php
include ("footer.php");
?>
