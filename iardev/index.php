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
if ($_POST  && !empty($_POST['UserId']) && !empty($_POST['password'])) {
	$response = $membership->validate_user($_POST['UserId'],$_POST['password']);
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Login</title>
    
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
    <form name="login" action="" method="post" accept-charset="utf-8">
    <h2>Login</h2>	
	<div>
	    <label for="User Id">User Id</label>
	    <input type="text" name="UserId" placeholder="User Id" required>
	</div>
	
	<div>
	    <label for="password">Password</label>
	    <input type="password" name="password" placeholder="password" required></li>
	</div>
	<br>
	<div>
	    <input type="submit" value="Login" name="submit">
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
</html>
