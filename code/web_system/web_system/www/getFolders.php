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

  
if(is_dir($username)){
    $dirList = opendir($username);
	while($entryName = readdir($dirList))
	{
		if($entryName != "." && $entryName != ".."){
			$dirArray[] = $entryName;
			rsort($dirArray);				
			$imageFiles = array_merge(glob($username."/".$entryName."/snapshot/*.png"));
			$files = array_combine($imageFiles, array_map("filemtime", $imageFiles));
			arsort($files); 
			$latestFile[] = key($files);
			rsort($latestFile);	
		}	
	}
	closedir($dirList);
}

$folderDetails = array();
$folderDetails['folderList'] = $dirArray;
$folderDetails['latestFiles'] = $latestFile;
echo json_encode($folderDetails);
?>