
<?php


function isAdmin()
{
	if (isset($_SESSION['adminloggedin']) && $_SESSION['adminloggedin'] == true ) {
		return true;
	}else{
		return false;
	}
}

?>