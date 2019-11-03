<?php
session_start();
require_once 'classes/membership.php';
$membership = new Membership();
$membership->logOut();
?>