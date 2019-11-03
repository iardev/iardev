<?php
include ("header.php");
include ("menu.php");
function disp($row,$x){
	
	echo "value='".$row[$x]."' ";
	
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Usert</title>
    
    <style>
    
     </style>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript">
$(function() {
    $( "#viewdate" ).datepicker({ 
    	minDate: 0,
       showButtonPanel: true,
    dateFormat: "mm/dd/yy",
    beforeShow: function(){    
           $(".ui-datepicker").css('font-size', 11) 
		}
	});
});
</script>
    </head>
  <body>
<?php //header ( 'Content-type: text/html; charset=utf-8' );
	$pTitle="New Member";
   require_once('classes/db.php');
   $db = new MyDB();//or die(mysql_error());
   $newMem=true;
   if(isset($_GET['id'])){
   	if($_GET['id']!="new"){
   		$pTitle="Update Member";
   		$result = $db->query("select firstname, lastname, address, city, state,zipcode,
   				homephone, workphone,emailaddress,to_char(dateexpired,'MM/DD/YYYY'), special from memberinfo where memberid=".$_GET['id']);
   		
   		if(pg_num_rows($result)==0){
   			//echo '<script>alert("Member not found");</script>';
   			echo '<script>messageDlg("Member not found");</script>';
   		}
   		$row=pg_fetch_row($result);
   		$newMem=false;
   			
   	}
   }
   ?>
 <br><br>
     
    <section class="loginform cf">
    <form name="login" action="memberDb.php" " method="post" accept-charset="utf-8">
    <h2><?php echo $pTitle;?></h2>	
	<div>
	    <label>First Name</label>
	    <input type="text" name="fname" placeholder="First Name" <?php if(!$newMem)disp($row,0);?> required>
	</div>
	
	<div>
	    <label>Last Name</label>
	    <input type="text" name="lname" placeholder="Last Name" <?php if(!$newMem)disp($row,1);?> required></li>
	</div>

	<div>
	    <label>Address</label>
	    <input type="text" name="address" placeholder="Address"  <?php if(!$newMem)disp($row,2);?> required></li>
	</div>
	<div style='width: 40%;float:left'>
	    <label>City</label>
	    <input type="text" name="city" placeholder="City"  <?php if(!$newMem)disp($row,3);?> required></li>
	</div>
	<div style='width: 10%;margin-left:15px;float:left'>
	    <label>State</label>
	    <input type="text" name="state" placeholder="NC"  <?php if(!$newMem)disp($row,4);?> required></li>
	</div>
	<div style='width: 36%;margin-left:20px;float:left'>
	    <label>Zip Code</label>
	    <input type="text" name="zcode" placeholder="Zip Code"  <?php if(!$newMem)disp($row,5);?> required></li>
	</div>
	<div style='width: 45%;float:left'>
	    <label>Primary Phone</label>
	    <input type="number" name="hphone" placeholder=""  <?php if(!$newMem)disp($row,6);?> ></li>
	</div>
	<div style='width: 45%;margin-left:25px;float:left'>
	    <label>Secondary Phone</label>
	    <input type="number" name="wphone" placeholder=""  <?php if(!$newMem)disp($row,7);?> ></li>
	</div>
	<div style='width: 50%;float:left'>
	    <label>Email</label>
	    <input type="email" name="email" placeholder="aaa@bbb.com"  <?php if(!$newMem)disp($row,8);?> required></li>
	</div>
	<div style='width: 25%;margin-left:10px;float:left'>
	    <label>Expire On</label>
	    <input type="text" name="expire" id="viewdate" placeholder=""  
	    <?php 
	    if(!$newMem)
	    	disp($row,9); 
	    else {
	    	$d=strtotime("+1 year");
	    	echo "value=".date("m/d/Y",$d);
	    }
	    	?> 
	    	>
	</div>
	<div style='width: inhert;float:left;margin-left:10px;margin-top:20px'>
	     <input style="width: inherit;" type="checkbox" name="special" placeholder="Reference" <?php if(!$newMem && $row[10]=='t') echo 'checked';?> ><font size='2'>Special</font></input>
	</div>
	<div style='clear: left'></div>
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
 