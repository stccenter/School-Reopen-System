<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'arcci-school-db.cccmszrxwdlg.us-east-1.rds.amazonaws.com');
define('DB_USERNAME', 'admin');
define('DB_PASSWORD', 'Password2021');
define('DB_NAME', 'stc-schoolreopen');
 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

function isLoggedIn()
{
	if (isset($_SESSION['username'])) {
		return true;
	}else{
		return false;
	}
}

function isadminLoggedIn()
{
	if (isset($_SESSION['adminusername'])) {
		return true;
	}else{
		return false;
	}
}

function isAdmin()
{
	if (isset($_SESSION['adminusername']) && $_SESSION['adminuserrole'] == 'admin' ) {
		return true;
	}else{
		return false;
	}
}

?>

