<?php 
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

function xml_child_exists($xml, $childpath)
 {
     $result = $xml->xpath($childpath);
     if(!empty($result ))
     {
         return TRUE;
     }
     else
     {
         return FALSE;
     }
 }

error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ERROR | E_PARSE);
global $directory,	 $xmlOutfile, $xmlInDirectory, $totalPopulation;

//output xml
$xmlOutfile = join(DIRECTORY_SEPARATOR, array($_SESSION["username"],$_SESSION["sessionDate"], "simulation-outputs2.xml"));
$ulid = $_SESSION["levelid"];


if (file_exists($xmlOutfile)) {
	$xmldata = file_get_contents($xmlOutfile);
	if(strpos($xmldata, "</Simulation>") === false)
	{
        $xmldata = $xmldata . '</Simulation>';
	}

//input xml
$xmlInDirectory = join(DIRECTORY_SEPARATOR, array($_SESSION["username"],$_SESSION["sessionDate"], "COVID.xml"));
//$xmlInDirectory = 'D:/GAMA/headless/samples/COVID.xml';
if (file_exists($xmlInDirectory)) 
{
$xmlInFile = simplexml_load_file($xmlInDirectory);
$totalPopulation = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[0]['value'];
$modelLevel = (string)$xmlInFile->Simulation['sourcePath'];
} 
      
//Read output xml file
$xmlFile = simplexml_load_string($xmldata);
$count = count($xmlFile -> Step);
$stepid = $xmlFile -> Step[$count-1]['id'];
//timestep
$timestep = (int)$stepid+1;
//days - (stepid / 156) + 1

if(strpos($modelLevel, "level1") !== false){
    $days = (int)($stepid/6)+1;
} else{
    $days = (int)($stepid/156)+1;
}

/*if($ulid == 1)
{
	$days = (int)($stepid/6)+1;
}
else{
	$days = (int)($stepid/156)+1;
}*/
//Total infected people
$totalInfected = (string)$xmlFile -> Step[$count-1]->Variable[6];
//Number of infected people on campus
$infOncampus = (string)$xmlFile -> Step[$count-1]->Variable[1];
//Infected and showing symptom
$infShowSymp = (string)$xmlFile -> Step[$count-1]->Variable[2];
//Infected but not showing symptoms
$infNoSymp = (string)$xmlFile -> Step[$count-1]->Variable[3];
//active people
$active = (string)$xmlFile -> Step[$count-1]->Variable[4];
//people on quarantine - totalpopulation - active + 1
$quarantine = (string)$totalPopulation - $active + 1;
//dead
$dead = (string)$xmlFile -> Step[$count-1]->Variable[5];
//vaccine
$childpath = "//Variable[@name='vaccinated people']";
$vaccineexists = xml_child_exists($xmlFile, $childpath);
if ($vaccineexists)
{
	$vaccine = (string)$xmlFile -> Step[$count-1]->Variable[13];
}
}
else{
$timestep = 0;
$days = 0;
$totalInfected = 0;
$infOncampus = 0;
$infShowSymp = 0;
$infNoSymp = 0;
$quarantine = 0;
$dead = 0;
if ($vaccineexists)
{
$vaccine = 0;
}
}
$statdata = array();
$statdata['timestep'] = $timestep;
$statdata['days'] = $days;
$statdata['totalInfected'] = $totalInfected;
$statdata['infOncampus'] = $infOncampus;
$statdata['infShowSymp'] = $infShowSymp;
$statdata['infNoSymp'] = $infNoSymp;
$statdata['quarantine'] = $quarantine;
$statdata['dead'] = $dead;
$statdata['modelLevel']=$modelLevel;
if($vaccineexists)
{
	$statdata['vaccine'] = $vaccine;
}
$statdata['vaccineexists'] = $vaccineexists;
echo json_encode($statdata);
?>
