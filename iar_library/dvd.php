<?php
include ("header.php");
include ("menu.php");
function disp($new,$row,$x,$y){
	if($new)
		echo "value='".$y."' ";
	else
		echo "value='".$row[$x]."' ";
	
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>DVD</title>
    
    <style>
    
     </style>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
  <body>
<?php //header ( 'Content-type: text/html; charset=utf-8' );
	$row=0;
	$pTitle="New DVD";
   require_once('classes/db.php');
   $db = new MyDB();//or die(mysql_error());
   $newDvd=true;
   if(isset($_GET['id'])){
   	if($_GET['id']!="new"){
   		$pTitle="Update DVD";
   		$result = $db->query("select dvdname,  author1, author2,ref ,yearpub, price, publisher,notes ".
   				"from dvdstbl where dvdid=".$_GET['id']);
   		
   		if(pg_num_rows($result)==0){
   			//echo '<script>alert("Member not found");</script>';
   			echo '<script>messageDlg("dvd not found");</script>';
   		}
   		$row=pg_fetch_row($result);
   		$newDvd=false;
   			
   	}
   }
   ?>
 <br><br>
     
    <section class="loginform cf">
    <form name="login" action="dvdDb.php" " method="post" accept-charset="utf-8">
    <h2><?php echo $pTitle;?></h2>	
	<div>
	    <label>DVD Title</label>
	    <input type="text" name="title" placeholder="Title" <?php disp($newDvd,$row,0,'');?> required>
	</div>
	
	<div>
	    <label>author1</label>
	    <input type="text" name="author1" placeholder="Author 1" <?php disp($newDvd,$row,1,'');?> required>
	</div>

	<div>
	    <label >author2</label>
	    <input type="text" name="author2" placeholder="Author 2" <?php disp($newDvd,$row,2,'');?> >
	</div>
	
	<div style='width: 35%;float:left'>
	    <label>year</label>
	    <input type="number" name="year" placeholder="Pub Year"<?php disp($newDvd,$row,4,'2000');?> >
	</div>

	<div style='width: 35%;margin-left:20px;float:left'>
	    <label>price</label>
	    <input type="number" name="price" placeholder="Price" <?php disp($newDvd,$row,5,'10.0');?> >
	</div>
	<div style='width: inhert;float:left;margin-left:20px;margin-top:20px'>
	     <input style="width: inherit;" type="checkbox" name="ref" placeholder="Reference" <?php if(!$newDvd && $row[3]=='t') echo 'checked';?> >Ref</input>
	</div>

	<div style='clear:left'>
	    <label>publisher</label>
	    <input type="text" name="publisher" placeholder="Publisher" <?php disp($newDvd,$row,6,'');?> >
	</div>

	<div>
	    <label>notes</label>
	    <input type="text" name="notes" placeholder="Notes" <?php disp($newDvd,$row,7,'');?> >
	</div>
 	<br>
	<div>
		<input type="hidden"  name="op" value="<?php echo $_GET['id'];?>" >
	    <input type="submit" value="Submit" name="submit">
	</div><br><br>
	<!--  <div align="center">
			<li><a href="new_account.php">Create Account</a></li>
			<li>&nbsp</li>
			<li>|</li>
			<li>&nbsp</li>
			<li><a href="passwordHelp.html">Password Help</a></li>
	</div>-->
      </form>
  
    </section>

  

<?php
include ("footer.php");
?>
 