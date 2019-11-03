<?php

require "db.php";
require_once 'classes/db.php';
class Membership {
	function validate_user($un,$pwd	) {
		$db = new MyDB();
		$name = "";
		$memid = $db->verify_user($un, $pwd,$name);
		if($memid == -1)
			return "Error.  Account hasn't been created.  Please create an account";
		if($memid == -2)
			return "Error.  Account hasn't been activated.  Please visit your email to activate";
		if($memid == 0)
			return "Error.  Please enter the correct name/password";
		if($pwd != "Welcome"){
			$_SESSION['status']='authorized';
			$_SESSION['memId']=$memid;
			$_SESSION['name']=$name;
			header("location: home.php");
		}
		else{
			header("location: update_password.php?id=".$un);
		}
	}
	function update_password($userId,$oldPass,$newPass){
		$db = new MyDB();
		$result = $db->update_password($userId,$oldPass,$newPass);
		if($result<1){
			return "Error.  Please confirm the old password";
		}
		$_SESSION['status']='authorized';
		//$_SESSION['memId']=$memid;
		$_SESSION['name']=userId;
		header("location: home.php");
	}
	function activate_user($email,$hash	) {
		$db = new MyDB();
		$memid = $db->activate_user($email,$hash);
		if($memid > 0)
			return "Account Activated.  You can login now";
		return "Error.  Failed to activate account.  Please try again";
	}
	function newDocId(){
		$db = new MyDB();
		return $db->new_doc_id();
	}
	
	function send_email($un,$hash){
		$to      = $un; // Send email to our user
		$subject = 'Signup | Verification'; // Give the email a subject 
		$message = '
 
			Thanks for signing up!
			Your account has been created, you can login using your credentials after you have activated your account by pressing the url below.
			Please click this link to activate your account:

			http://timedinfo.com/verify.php?email='.$un.'&hash='.$hash.'
 
			'; // Our message above including the link
                     
		$headers = 'From:noreply@timedinfo.com' . "\r\n"; // Set from headers
		mail($to, $subject, $message, $headers); // Send our email
	}
	function send_email_2_viewer($un,$hash){
		$to      = $un; // Send email to our user
		$subject = 'Signup | Verification'; // Give the email a subject
		$message = '
	
			A friend has added you as a viewer.  
			Your account has been created.  Please click on the link below to verify and activated your account.
			Please click this link to activate your account:
	
			http://timedinfo.com/update_account.php?email='.$un.'&hash='.$hash.'
	
			'; // Our message above including the link
		 
		$headers = 'From:noreply@timedinfo.com' . "\r\n"; // Set from headers
		mail($to, $subject, $message, $headers); // Send our email
	}
	function add_user_as_viewer($memId,$viewerEmail	) {
		$db = new MyDB();
	
		if($db->add_viewer( $memId,$viewerEmail)>0)
			return "Viewer was added";
		$hash = md5( rand(0,1000) );
		$memid = $db->create_user_as_viewer($viewerEmail,$hash,$memId);
		if($memid == 0)
		{
			return "Failed to create the account.";
		}
		self::send_email_2_viewer($viewerEmail,$hash);
		
		return "Viewer was added.  Please notify viewer to visit his/her email to activate the account";
	
	}
	function check_invited_user($email,$hash){
		$db = new MyDB();
		$result = $db->check_invited_user($email,$hash);
		return $result;
		
	}
	function create_user($fname,$lname,$un,$pwd	) {
		$db = new MyDB();
		$hash = md5( rand(0,1000) );
		$memid = $db->create_user($fname,$lname,$un, password_hash($pwd, PASSWORD_DEFAULT),$hash);
		if($memid == 0)
		{
			return "Failed to create the account.  The email you specified is already in use";	
		}
		self::send_email($un,$hash);
		return "Account Created.  Please visit your email to activate the account.";

	}
	function update_user($fname,$lname,$un,$pwd	){
		$db = new MyDB();
		echo $pwd;
		$name = "";
		$memid = $db->update_user($fname,$lname,$un,password_hash($pwd, PASSWORD_DEFAULT),$name);
		$_SESSION['status']='authorized';
		$_SESSION['memId']=$memid;
		$_SESSION['name']=$name;
		header("location: dashboard.php");
		
	}
	
	function logOut(){
		if(isset($_SESSION['status'])){
			unset($_SESSION['status']);
			unset($_SESSION['name']);
			unset($_SESSION['memId']);
		}
		if(isset($_COOKIE[session_name()]))
			setcookie(session_name(),'',time()-10000);
		session_destroy();
		header("location: index.php");
	}
	function verifyViewer($me,$viewer){
		$db = new MyDB();
		$name="";
		if(!$db->verify_viewer($me,$viewer,$name)){
			self::logOut();
		}
		return $name;
	}
	function hasSession(){
		session_start();
		if($_SESSION['status'] != 'authorized')
			header("location: index.php");
	}
	
	
}
?>

