<?php
function redirect (){
	 header('location:home.php');
}
session_start();
require_once 'classes/membership.php';
$membership = new Membership();
if(isset($_GET['status']) && $_GET['status'] == 'loggedout'){
	$membership->logOut();
}
//print_r($_POST);

if ($_POST  && !empty($_POST['UserId']) && !empty($_POST['oldPassword']) && !empty($_POST['newPassword'])) {

	$response = $membership->update_password($_POST['UserId'],$_POST['oldPassword'],$_POST['newPassword']);
	
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Update Password</title>
    
    <style>
    
     </style>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    
   
    
  <body>
  <div id="masthead">
		<img = alt="" src="./img/header.png" style="width: 100%">
	</div>
 <br><br>
    <section class="loginform cf">
     <h2>Update Password</h2>	
     
    <form name="login" action="" method="post" accept-charset="utf-8">
	<div>
	
	    <input type="hidden"  name="UserId" value="<?php echo $_GET['id'];?>" required>
	</div>
	
	<div>
	    <label for="password">Old Password</label>
	    <input type="password" name="oldPassword" placeholder="password" required></li>
	</div>
	<div>
	    <label for="New Password">New Password</label>
	    <input id="password" type="password" name="newPassword" placeholder="password" required></li>
	</div>
	<div>
	    <label for="New Password2">Repeate New Password</label>
	    <input id="confirm_password" type="password" name="newPassword2" placeholder="password" required></li>
	</div>
	<br>
	<div>
	    <input type="submit" value="Update" name="submit">
	</div><br><br>
	<!--  <div align="center">
			<li><a href="new_account.php">Create Account</a></li>
			<li>&nbsp</li>
			<li>|</li>
			<li>&nbsp</li>
			<li><a href="passwordHelp.html">Password Help</a></li>
	</div>-->
      </form>
      <?php
	if(isset ($response)){
		 echo '<script> alert("'. $response .'")'.'</script>';
	}
	if(isset($_SESSION['status']) && isset($_SESSION['memId'])) {
	 	redirect();
		}
		 ?>
    </section>
  </body>
  <script>
  	var password = document.getElementById("password")
  , confirm_password = document.getElementById("confirm_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Passwords Don't Match");
  } 
  else {
  	if(password.value.length<5){
  		confirm_password.setCustomValidity("Password too short");
  	}
  	else{
  	    confirm_password.setCustomValidity('');
  	}
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;

  </script>
</html>
