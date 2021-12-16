<?php 
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ERROR | E_PARSE);
global $directory,	 $xmlInfile, $xmlInDirectory, $totalPopulation;
$ulid = $_SESSION["levelid"];


//input xml
if (isset($_GET['xmlFile']))
{
	$xmlInDirectory = $_GET['xmlFile'];
}
else{
$xmlInDirectory = join(DIRECTORY_SEPARATOR, array($_SESSION["username"],$_SESSION["sessionDate"], "COVID.xml"));
}
if (file_exists($xmlInDirectory)) 
{
$xmlInFile = simplexml_load_file($xmlInDirectory);
$stepid = $xmlInFile -> Simulation[0]['finalStep'];
$modelLevel = $xmlInFile -> Simulation[0]['sourcePath'];
$days = (int)($stepid/156);
if(strpos($modelLevel, "level1") === true){
	$days = (int)($stepid/6);
}

$totalPopulation = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[0]['value'];
$residentialPopulation = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[1]['value'];
$initinfectedPeople = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[2]['value'];
//Infection rate campus
$infectionRateCampus = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[3]['value'];	
//Social mask policy
$maskPolicy = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[9]['value'];
$maskPolicyValue = "N/A";
if ($maskPolicy === "TRUE")
{
	$maskPolicyValue = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[4]['value'];
}
//social distance outdoor
$socialDistOutdoor = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[10]['value'];
$socialDistOutdoorValue = "N/A";
if ($socialDistOutdoor === "TRUE")
{
	$socialDistOutdoorValue = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[5]['value'];
}
//social distance indoor
$socialDistIndoor = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[11]['value'];
$socialDistIndoorValue = "N/A";
if ($socialDistIndoor === "TRUE")
{
	$socialDistIndoorValue = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[6]['value'];
}
//symptoms screening
$sympScreening = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[12]['value'];
$sympScreeningValue = "N/A";
if ($sympScreening === "TRUE")
{
	$sympScreeningValue = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[7]['value'];
}
//viral testing
$viralTesting = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[13]['value'];
$viralTestingValue = "N/A";
if ($viralTesting === "TRUE")
{
	$viralTestingValue = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[8]['value'];
}
//contact tracing
$contactTracing = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[14]['value'];
$contactTracingValue = "N/A";
if ($contactTracing === "TRUE")
{
	$contactTracingValue = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[16]['value'];
}
$inPersonClass = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[15]['value'];
$percOfAsymptInf = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[19]['value'];
$infectionRateCommunity = (string)$xmlInFile->Simulation->Parameters[0]->Parameter[20]['value'];
} 

$paramdata = array();
//days
$paramdata['days'] = $days;
//total population
$paramdata['totalPopulation'] = $totalPopulation;
//ini infected people
$paramdata['initinfectedPeople'] = $initinfectedPeople;
//residential population
$paramdata['residentialPopulation'] = $residentialPopulation;
//in person class
$paramdata['inPersonClass'] = $inPersonClass;
//infection rate campus
$paramdata['infectionRateCampus'] = $infectionRateCampus;
//infection rate community
$paramdata['infectionRateCommunity'] = $infectionRateCommunity;
//percentage of asymptomatic symptoms
$paramdata['percOfAsymptInf'] = $percOfAsymptInf;
//mask policy
$paramdata['maskPolicy'] = $maskPolicy;
$paramdata['maskPolicyValue'] = $maskPolicyValue;
//social distance indoor
$paramdata['socialDistIndoor'] = $socialDistIndoor;
$paramdata['socialDistIndoorValue'] = $socialDistIndoorValue;
//social distance outdoor
$paramdata['socialDistOutdoor'] = $socialDistOutdoor;
$paramdata['socialDistOutdoorValue'] = $socialDistOutdoorValue;
//viral testing
$paramdata['viralTesting'] = $viralTesting;
$paramdata['viralTestingValue'] = $viralTestingValue;
//symptoms screening
$paramdata['sympScreening'] = $sympScreening;
$paramdata['sympScreeningValue'] = $sympScreeningValue;
//contact tracing
$paramdata['contactTracing'] = $contactTracing;
$paramdata['contactTracingValue'] = $contactTracingValue;
echo json_encode($paramdata);

?>