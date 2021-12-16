<?php 

require_once(__DIR__ . './SevenZipArchive.php');
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
//get target directory
$value = $_POST['input'];
$errMsgRoads = array();

$errMsgRoads['postvalue'] = $value;
$targetDir = $_SESSION["username"];

if(!is_dir($targetDir. '/tmp/')){
    mkdir($targetDir.'/tmp/', 0777, true);
}

if ($value == "\"gisRoadsValue\""){
	if(!is_dir($targetDir. '/tmp/roads/')){
    mkdir($targetDir.'/tmp/roads/', 0777, true);
	}
	
	$targetDir = $targetDir.'/tmp/roads';
	$targetFileName = $targetDir. "/". $_FILES['file']['name'];
	$targetFileType = $targetDir. "/". $_FILES['file']['type'];
	$fileType = strtolower(pathinfo($targetFileName,PATHINFO_EXTENSION));
	$_SESSION["roadsFileName"] = $targetFileName;
	$_SESSION["roadsFileType"] = $fileType;
}

if ($value == "\"gisPlacesValue\""){
	if(!is_dir($targetDir. '/tmp/places/')){
    mkdir($targetDir.'/tmp/places/', 0777, true);
	}
	$targetDir = $targetDir.'/tmp/places';
	$targetFileName = $targetDir. "/". $_FILES['file']['name'];
	$targetFileType = $targetDir. "/". $_FILES['file']['type'];
	$fileType = strtolower(pathinfo($targetFileName,PATHINFO_EXTENSION));
	$_SESSION["placesFileName"] = $targetFileName;
	$_SESSION["placesFileType"] = $fileType;
}

$uploadOK = 1;
$errMsgRoads['fileName'] = $_FILES['file']['name'];
$errMsgRoads['fileType'] = $fileType;

//allow only compressed files
if($fileType != '7z' && $fileType != 'zip')
{
	$uploadOK = 0;
}

if($uploadOK == 0 )
	{
		$errMsgRoads['message'] = "File type is not allowed. Please upload only compressed files.";
	}
	else 
	{
		//echo "The file ". htmlspecialchars( basename( $_FILES['file']['name'])). " has been uploaded.";
		//file type 7z
		$extArray = array();
		$checkExt = array('cpg','dbf','prj','shp','shx'); 
		if($fileType == '7z' || $fileType == 'zip')
		{
			$archive = new SevenZipArchive($_FILES["file"]["tmp_name"], array('debug' => true));
			foreach ($archive as $entry)
				{
					$archiveFiles = $entry['Name'];
					$archiveFileType = strtolower(pathinfo($archiveFiles,PATHINFO_EXTENSION));
					if($archiveFileType != '' or $archiveFileType != 'xml')
					{
						$extArray[] = $archiveFileType;
					}
				}
			$difference = array_diff($checkExt,$extArray);
			if(count($difference)==0)
				{
					move_uploaded_file($_FILES["file"]["tmp_name"], $targetFileName);
					$errMsgRoads['message'] = "File is successfully uploaded.";
				}
			else
				{
					$errMsgRoads['message'] = "Required shapefiles are not available. Upload failed.";
				}
		}
	}
echo json_encode($errMsgRoads);

?>
