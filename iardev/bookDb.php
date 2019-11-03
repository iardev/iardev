<?php


require_once 'classes/membership.php';
$membership = new Membership();
$membership -> hasSession();
require_once ('classes/db.php');
$db = new MyDB();
//print_r($_SESSION);
print_r($_POST);
$ref='f';
if(isset($_POST['ref']))
	$ref='t';
//$db -> query($_POST["query"]);

//header("location: member_info.php?id=".$_POST['memid']+1);
//echo "<script type='text/javascript'> document.location = 'home.php'; </script>";
if($_POST['op']=='new'){
	
	$query = "insert into bookstbl (bookname, author1, author2, ref, notes	,yearpub,
		price,publisher,bookid) values('".
		$_POST['title']."','".
		$_POST['author1']."','".
		$_POST['author2']."','".
		$ref."', '".
		$_POST['notes']."',".
		$_POST['year'].",".
		$_POST['price'].",'".
		$_POST['publisher']."',".
		"(select max(bookid)+1 from bookstbl) ) returning bookid";
	$result = $db->query($query);
	//print_r( $query."<br>");
	if(pg_numrows($result)!=1){
		echo "<html><head></head><body><script>alert('Error while trying to create Book in DB');window.location.href = 'home.php';</script><body></html>";
	}
	$row = pg_fetch_row($result);
	echo "<html><head></head><body><script>alert('Created Book successfuly');window.location.href = 'home.php';</script><body></html>";
	
}
else {
	$query = "update  bookstbl set ".
			"bookname='". $_POST['title']."',".	
			"author1='". $_POST['author1']."',".
			"author2='". $_POST['author2']."',".
			"ref='".$ref."',".
			"notes='". $_POST['notes']."',".
			"yearpub=". $_POST['year'].",".
			"price=". $_POST['price'].",".
			"publisher='". $_POST['publisher']."' ".
			" where bookid=".$_POST['op'];
	//print_r($query);
	$db->query($query);
	//print_r ($query."<br>");
	echo "<html><head></head><body><script>alert('Updated Book successfuly');window.location.href = 'home.php';</script><body></html>";
	
}
?>