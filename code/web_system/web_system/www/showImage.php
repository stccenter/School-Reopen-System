<?php 
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}

//get username
$username = $_SESSION["username"];
//set current working directory
$curDirectory = getcwd()."/".$username; 
if(is_dir($username)){
    $dirList = opendir($username);
	while($entryName = readdir($dirList))
	{
		if($entryName != "." && $entryName != ".."){
			$dirArray[] = $entryName;	
			$imageFiles = array_merge(glob($username."/".$entryName."/snapshot/*.png"));
			$files = array_combine($imageFiles, array_map("filemtime", $imageFiles));
			arsort($files); 
			$latestFile[] = key($files); 
		}	
	}
	closedir($dirList);
}
	
	$pngjson = array();
	$pngjson['latestFile'] = $latestFile;
	echo json_encode($latestFile);
?>
