<?php
define ('DB_CONN_STR', 'host=localhost port=5432 dbname=library user=dbadmin password=athzmtmtyt');
function displayDocRow($row){
	if($row[9]=="")
		echo '<td> Missing File</td>';
	else{
		if($row[8]=="t")
			echo '<td> Expired</td>';
		else {
			echo '<td> Active </td>';
		}
	}
//	echo '<td> <div contenteditable="true" style="border-style:groove;color:DarkOrchid;background-color:white" id=title:'.$row[0].' >'. $row[1].'</div></td>';
//	echo '<td> <div contenteditable="true" style="border-style:groove;border-width=1px;color:DarkOrchid;background-color:white" id=description:'.$row[0].'> ' . $row[2] . '</div></td>';
	echo '<td> <div id=title:'.$row[0].' >'. $row[1].'</div></td>';
	echo '<td> <div id=description:'.$row[0].'> ' . $row[2] . '</div></td>';
	echo '<td>'. $row[3].'</td>';    //code
	echo '<td>' . $row[4] . '</td>'; //created on
		   // view on
//	echo '<td ><input  type="text" onchange="handleViewDateChanged(this)" class="viewdate" name="'.$row[0].'" id=doc_"'.$row[0].'"  value="'.$row[5].'" /></td>';
	echo '<td >'.$row[5].'</td>';
	echo '<td>'. $row[9].'</td>';
	if($row[9]=="")
	{
		echo '<td></td>';
	}
	else {
		echo '<td>'. $row[6].'</td>';
	}
	
}
function addColor($x,$c)
{
	return "<font color='".$c."'>".$x."</font>";
}
function displayRow($left,$right,&$counter){

	echo '<div  align="Left" style="font-size:small;float: left;'.(is_null($right)?'width:100%;':'').'" >',PHP_EOL;
	echo $left.'</div>',PHP_EOL;
	echo '<div align="right" style="',PHP_EOL;
	if($counter % 2 != 0)
		echo 'background-color:#f0f0f0;',PHP_EOL;
		echo 'font-size:small">'.$right.'</div>',PHP_EOL;
		echo '<div style="clear: left"></div>',PHP_EOL;
		$counter++;

}
function makeLink($href,$text){
	return '<a href="'.$href.'" >'.$text.'</a>';
}
function printNav($text,$hasMenu,$name,$operation=NULL,$entity=NULL,$butnTitle=NULL)
{
	$addRemove = "Add ";
	if(!is_null($operation)){
		if($operation==-1)
			$addRemove = "Delete ";//128465
	}
	echo '<div style="padding:2px;background-color: #205081;color:#fff">';//9547, 8801
	echo '<div style="width:15%;display: inline-block" >';
	if($hasMenu)
		{
			echo '<div   ><button onclick="dropDownMenu()" class="dropbtn" style>&#9776;</button>';
			echo    '<div  id="myDropdown" class="dropdown-content">';
			echo 		'<a href="dashboard.php">Dashboard</a>';
			echo 		'<a href="documents.php">My Documents</a>';
			echo 		'<a href="viewers.php">My Viewers</a>';
			echo 		'<a href="othersdocs.php">Others\' Documents</a>';
			echo 		'<hr>';
			echo 		'<a href="logout.php">Sing out</a>';
			echo 		'</div></div>';
		}
	echo '	</div>';
	echo '<div style="width:85%;display: inline-block;vertical-align:bottom;" align="right" >';
	echo $name;
	echo '</div></div>';
	if(!is_null($operation)){
		$addRemove .=$entity;
		if(!is_null($butnTitle))
			$addRemove = $butnTitle;
		echo '<div style="width:40%; float:left">&nbsp;</div>';	
		echo '<div align="center" style="width:20%; float:left">';
		echo '<button  ';
		if($operation==-1)
			echo 'style="background: linear-gradient(to bottom, #F00000 0%,#FF0000 100%);" ';
		echo ' onclick="updateItem()"  id="addremove" class="addremove" >'.$addRemove.'</button>';
		echo '</div>';
	}
	if($hasMenu)
	{	echo '<div align="right" style="width:40%; float:right"><a  style="margin-right:8px" title="'.$name.'&#13;Logout" href="logout.php"><img style="margin-top:6px" src="image/exit.ico"></a></div>';
		echo '<div style="clear:both"></div>';
	}
	echo '<div align="center" style="margin-top:20px"><h3>'.$text.'</h3></div>';

}
function makeLabel($x){
	return '<label>'.$x.'</label>';
}

function printHeader($title)
{
	echo '<!DOCTYPE html>',PHP_EOL;
	echo '<html>',PHP_EOL;
	echo '<head>',PHP_EOL;
	echo '<title>'.$title.'</title>',PHP_EOL;
	
	echo '<!-- Including CSS & jQuery Dialog UI Here-->',PHP_EOL;
	echo '<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet">',PHP_EOL;
	echo '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>',PHP_EOL;
	echo '<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>',PHP_EOL;
	echo '<script type="text/javascript" src="ti.js"></script>',PHP_EOL;
	echo '<link rel="stylesheet" href="style.css?version=17">',PHP_EOL;
	echo '<script src="aes.js"></script>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">',PHP_EOL;

}
?>

