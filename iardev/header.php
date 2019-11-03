<?php
   require_once 'classes/membership.php';
   $membership = new Membership();
   $membership->hasSession();
   
   function displayTableHeader($a){
   	echo "<tr>";
   	echo "<th><p style='margin-left:14px;'>".$a[0]."</th>";
   	for($i=1;$i<count($a);$i++)
   		echo "<th>".$a[$i]."</th>";
   	echo "</tr>\n";
   }
   function displayCenterCell($a){
   	echo "<td ><center>"; // <p style='margin-left:14px;'>";
   	echo $a;
   	echo "</center></td>";
   }
   function displayCell($a){
   	echo "<td >"; // <p style='margin-left:14px;'>";
   	echo $a;
   	echo "</td>";
   }
function displayTFCell($a) {
	echo "<td><center>";
	if ($a == 't') {
		echo "Yes";
	} else {
		echo "No";
	}
	echo "</center></td>";
}
   
  ?>
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<!-- DW6 -->
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Main Library</title>

<style>

</style>

<!-- Including CSS & jQuery Dialog UI Here-->
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/ui-darkness/jquery-ui.css" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script src="js/dialog.js" type="text/javascript"></script>
<link rel="stylesheet" href="style.css">
</head>
<body>
	<div id="masthead">
		<img = alt="" src="./img/header.png" style="width: 100%">
	</div>