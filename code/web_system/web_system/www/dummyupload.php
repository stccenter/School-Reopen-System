<?php 
ini_set('post_max_size','500M');
ini_set('upload_max_filesize','100M');

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$targetDir = $_SESSION["username"];
$val = $_POST['gisRoadsInput'];
$targetFileName = $targetDir. "/". basename($_FILES["gisRoads"]["name"]);
$targetFileType = $targetDir. "/". $_FILES["gisRoads"]["type"];
$uploadOK = 1;
echo $targetFileName;
echo $val;

?>