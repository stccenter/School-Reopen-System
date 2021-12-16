<?php
// Initialize the session
session_start();
 
// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
session_destroy();
//setcookie("uname",$username,time()-1);
//setcookie("pass",$password,time()-1);
// Redirect to login page
header("location: adminlogin.php");
exit;
?>