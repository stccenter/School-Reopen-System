<?php
require('config.php');
$username=$_REQUEST['username'];
$query = "DELETE FROM users WHERE username='$username'"; 
$result = mysqli_query($link,$query) or die ( mysqli_error());
header("Location: admin.php"); 
?>