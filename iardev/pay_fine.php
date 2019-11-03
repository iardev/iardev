<?php


require_once 'classes/membership.php';
$membership = new Membership();
$membership -> hasSession();
require_once ('classes/db.php');
$db = new MyDB();
//print_r($_SESSION);
//print_r($_POST);

$db -> query($_POST["query"]);
$status="success";
//header("location: member_info.php?id=".$_POST['memid']);
//echo "<script type='text/javascript'> document.location = 'home.php'; </script>";
?>
