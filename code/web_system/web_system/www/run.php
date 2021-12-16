<?php
require_once(__DIR__ . './SevenZipArchive.php');
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
ini_set('max_execution_time', '0');
ini_set('date.timezone', 'America/New_York');
$sessionDate = date("YmdHi");
$_SESSION["sessionDate"] = $sessionDate; 
$ulid = $_SESSION["levelid"];

//input xml file location
$xmlDirectory = 'C:/GAMA/headless/samples/COVID.xml';
$totalPopulation = $_POST['totalPopulation'];
$infectedPeople = $_POST['initInfectedPpl'];

$finalStep = $_POST['simPeriod']*6;
if( $ulid != 1 && isset($_POST['runBtnToggle']))
{
	$finalStep = 10 * 6;
}
else if($ulid != 1 && !isset($_POST['runBtnToggle'])){
	$finalStep = $_POST['simPeriod']*156;
}

$numStayingInDorm = $_POST['residentialPopulation'];
if($ulid != 1)
{
	$inf_rate_no_mask = $_POST['infectionRateCps'];
}
$perc_inperson_classes = $_POST['inPersonClassP'];
if(!isfloat($perc_inperson_classes))
{
	$perc_inperson_classes = (string)$perc_inperson_classes.".0";
}
//Mask policy
if(isset($_POST['maskSwitch']) && $_POST['maskSwitch'] == TRUE)
{
    $maskSwitch = "TRUE";
	$maskPolicy = $_POST['maskPolicy'];
	if(!isfloat($maskPolicy))
	{
		$maskPolicy = (string)$maskPolicy.".0";
	}
}
else
{
    $maskSwitch = "FALSE";
    $maskPolicy = "0.8";
}
//Social Distance Indoor
if(isset($_POST['socialDisInSwitch']) && $_POST['socialDisInSwitch'] == TRUE)
{
    $socialDistInSwitch = "TRUE";
	$socialDistIn = $_POST['socialDistIn'];
	if(!isfloat($_POST['socialDistIn']))
	{
		$socialDistIn = (string)$socialDistIn.".0";
	}
}
else
{
    $socialDistInSwitch = "FALSE";
    $socialDistIn = "0.8";
}
//Social distance outdoor
if(isset($_POST['socialDisOutSwitch']) && $_POST['socialDisOutSwitch'] == TRUE)
{
    $socialDisOutSwitch = "TRUE";
	$socialDistOut = $_POST['socialDistOut'];
	if(!isfloat($socialDistOut))
	{
		$socialDistOut = (string)$socialDistOut.".0";
	}
}
else
{
    $socialDisOutSwitch = "FALSE";
    $socialDistOut = "0.8";
}
//Symptoms screening
if(isset($_POST['sympScreenSwitch']) && $_POST['sympScreenSwitch'] == TRUE)
{
    $sympScreenSwitch = "TRUE";
    $sympScreen = $_POST['sympScreen'];
	if(!isfloat($sympScreen))
	{
		$sympScreen = (string)$sympScreen.".0";
	}
}
else
{
    $sympScreenSwitch = "FALSE";
    $sympScreen = "0.8";
}
//Contact tracing
if(isset($_POST['contactTraceSwitch']) && $_POST['contactTraceSwitch'] == TRUE)
{
    $contactTraceSwitch = "TRUE";
    $contactTrace = $_POST['contactTrace'];
}
else
{
    $contactTraceSwitch = "FALSE";
    $contactTrace = "80";
}
//Viral testing
if(isset($_POST['viralTestingSwitch']) && $_POST['viralTestingSwitch'] == TRUE)
{
    $viralTestingSwitch = "TRUE";
    $viralTesting = $_POST['viralTesting'];
	if(!isfloat($viralTesting))
	{
		$viralTesting = (string)$viralTesting.".0";
	}
}
else
{
    $viralTestingSwitch = "FALSE";
    $viralTesting = "0.8";
}
//Vaccine
$vaccineSwitch = "FALSE";
if(isset($_POST['vaccineSwitch']) && $_POST['vaccineSwitch'] == TRUE)
{
    $vaccineSwitch = "TRUE";
}
//Percentage of asymptomatic infection
if($_POST['PAI'] == '')
{
    $PAI = "0.4";
}
else
{
    $PAI = $_POST['PAI'];
	if(!isfloat($PAI))
	{
		$PAI = (string)$PAI.".0";
	}
}
//infectionRateCmty
if($_POST['infectionRateCmty'] == '')
{
    $infectionRateCmty = "0.0001";
}
else
{
    $infectionRateCmty = $_POST['infectionRateCmty'];
	if(!isfloat($infectionRateCmty))
	{
		$infectionRateCmty = (string)$infectionRateCmty.".0";
	}
}
//Frequency days - viral testing

if(!isset($_POST['dropdown_1']))
{
	$viralTestingFreqDays = 6;
	if($ulid != 1 && isset($_POST['runBtnToggle']))
	{
		$viralTestingFreqDays = 6;
	}
	else if($ulid != 1 && !isset($_POST['runBtnToggle'])){
		$viralTestingFreqDays = 156;
	}
}
else
{
	$viralTestingFreq = $_POST['dropdown_1'];
	if(isset($_POST['runBtnToggle']))
	{
		$viralTestingFreqDays = $viralTestingFreq * 6;
	}
	else{
		$viralTestingFreqDays = $viralTestingFreq * 156;
	}
}
//Frequency days - symptoms screening
if(!isset($_POST['dropdown_2']))
{
	$sympScreeningFreqDays = 6;
	if($ulid != 1 && isset($_POST['runBtnToggle']))
	{
		$sympScreeningFreqDays = 6;
	}
	else if($ulid != 1 && !isset($_POST['runBtnToggle'])){
		$sympScreeningFreqDays = 156;
	}
}
else
{
	$sympScreeningFreq = $_POST['dropdown_2'];
	if(isset($_POST['runBtnToggle']))
	{
		$sympScreeningFreqDays = $sympScreeningFreq * 6;
	}
	else{
		$sympScreeningFreqDays = $sympScreeningFreq * 156;
	}
}
function isfloat($num)
{
	return is_float($num) || is_numeric($num) && ((float) $num != (int) $num);
}


#Create a XML document
$domDocument = new DOMDocument('1.0', "UTF-8");
$domDocument->preserveWhiteSpace = false;
$domDocument->formatOutput = true;
#Create root element - Experiment_plan
$domElement = $domDocument->createElement('Experiment_plan');
#Child of root element - Simulation and its attributes
$domSimulation = $domDocument -> createElement('Simulation');
$domSimulation->setAttribute('id','2');
$_SESSION["levelsize"] = 6;
$_SESSION["vaccination"] = false;
$domSimulation->setAttribute('sourcePath','C:/GAMA_Workspace/abm_demo/models/model4_11_23_level1.gaml');
if($ulid != 1 && !isset($_POST['runBtnToggle']))
{
	$_SESSION["levelsize"] = 156;
	if(!isset($_POST['vaccineSwitch']))
	{
		$domSimulation->setAttribute('sourcePath','C:/GAMA_Workspace/abm_demo/models/model4_11_23.gaml');
	}
	else{
		$_SESSION["vaccination"] = true;
		$domSimulation->setAttribute('sourcePath','C:/GAMA_Workspace/abm_demo/models/model4_3_8_21_vaccine.gaml');
	}
}
else if(isset($_POST['vaccineSwitch']))
{

	$_SESSION["vaccination"] = true;
	$domSimulation->setAttribute('sourcePath','C:/GAMA_Workspace/abm_demo/models/model4_3_8_21_vaccine_level1.gaml');
}

$domSimulation->setAttribute('finalStep',$finalStep );
$domSimulation->setAttribute('experiment','covidEXP' );
$domSimulation->setAttribute('seed','3' );
#Child of Simulation - Parameters
$domParameters = $domDocument -> createElement('Parameters');
#Child of Parameters - Parameter - Total population
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','nb_people');
$domParameter->setAttribute('type','INT');
$domParameter->setAttribute('value',$totalPopulation);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - StayingInDorm
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','numStayingInDorm');
$domParameter->setAttribute('type','INT');
$domParameter->setAttribute('value',$numStayingInDorm);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Infected people
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','nb_infected_init');
$domParameter->setAttribute('type','INT');
$domParameter->setAttribute('value',$infectedPeople);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Infected Rate No Mask
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','inf_rate_no_mask');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',"0.15");
$domParameters->appendChild($domParameter);

if($ulid != 1 && !isset($_POST['runBtnToggle']))
{
	$domParameter = $domDocument -> createElement('Parameter');
	$domParameter->setAttribute('name','inf_rate_no_mask');
	$domParameter->setAttribute('type','FLOAT');
	$domParameter->setAttribute('value',$inf_rate_no_mask);
	$domParameters->appendChild($domParameter);
}

#Child of Parameters - Parameter - Mask policy
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','perc_wearing_masks');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',$maskPolicy);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Social Distance Outdoor
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','enf_socdist');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',$socialDistOut);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Social Distance Indoor
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','follow_bcp');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',$socialDistIn);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Symptoms screening
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','enf_monitor');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',$sympScreen);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Viral testing
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','enf_testing');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',$viralTesting);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Mask on policy
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','masks_on_policy');
$domParameter->setAttribute('type','BOOLEAN');
$domParameter->setAttribute('value',$maskSwitch);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Build cap policy
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','build_cap_policy');
$domParameter->setAttribute('type','BOOLEAN');
$domParameter->setAttribute('value',$socialDistInSwitch);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Social dist policy
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','soc_dist_policy');
$domParameter->setAttribute('type','BOOLEAN');
$domParameter->setAttribute('value',$socialDisOutSwitch);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Monitoring symptoms policy
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','monitoring_symtps_policy');
$domParameter->setAttribute('type','BOOLEAN');
$domParameter->setAttribute('value',$sympScreenSwitch);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Testing policy
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','testing_policy');
$domParameter->setAttribute('type','BOOLEAN');
$domParameter->setAttribute('value',$viralTestingSwitch);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Contact tracing policy
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','contact_tracing_policy');
$domParameter->setAttribute('type','BOOLEAN');
$domParameter->setAttribute('value',$contactTraceSwitch);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Percentage inperson classes
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','perc_inperson_classes');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',$perc_inperson_classes);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Upper testing limit
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','upper_testing_limit');
$domParameter->setAttribute('type','INT');
$domParameter->setAttribute('value',$contactTrace);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Monitor checkup time
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','monitor_checkup_time');
$domParameter->setAttribute('type','INT');
$domParameter->setAttribute('value',$sympScreeningFreqDays);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Testing checkup time
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','testing_checkup_time');
$domParameter->setAttribute('type','INT');
$domParameter->setAttribute('value',$viralTestingFreqDays);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Percentage of asymptomatic infection - newly added
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','perc_asympt');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',$PAI);
$domParameters->appendChild($domParameter);
#Child of Parameters - Parameter - Perc inf off campus - newly added
$domParameter = $domDocument -> createElement('Parameter');
$domParameter->setAttribute('name','perc_inf_off_campus');
$domParameter->setAttribute('type','FLOAT');
$domParameter->setAttribute('value',$infectionRateCmty);
$domParameters->appendChild($domParameter);
#Append Parameters to Simulation
$domSimulation->appendChild($domParameters);
#Child of Simulation - Outputs
$domOutputs = $domDocument -> createElement('Outputs');
#Child of Outputs - Output
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('id','1');
$domOutput->setAttribute('name','chart_display');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('id','2');
$domOutput->setAttribute('name','num infected');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('id','3');
$domOutput->setAttribute('name','infected show symptoms');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('id','4');
$domOutput->setAttribute('name','infected no symptoms');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('id','5');
$domOutput->setAttribute('name','active people');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('id','6');
$domOutput->setAttribute('name','dead');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output newly added
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('id','7');
$domOutput->setAttribute('name','total infected people');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output newly added
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('name','masks_on_policy');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output newly added
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('name','build_cap_policy');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output newly added
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('name','soc_dist_policy');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output newly added
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('name','monitoring_symtps_policy');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output newly added
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('name','testing_policy');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
#Child of Outputs - Output newly added
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('name','contact_tracing_policy');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
if(isset($_POST['vaccineSwitch'])){
#Child of Outputs - Output newly added
$domOutput = $domDocument ->createElement('Output');
$domOutput->setAttribute('name','vaccinated people');
$domOutput->setAttribute('framerate',1);
$domOutputs->appendChild($domOutput);
}
#Append outputs to Simulation
$domSimulation->appendChild($domOutputs);
#Append simulation to experiment plan
$domElement->appendChild( $domSimulation );
#append experiment plan to xml root
$domDocument->appendChild($domElement);
$outputdirectory = join("/", array($_SESSION["username"],$_SESSION["sessionDate"]));
if(!is_dir($outputdirectory)){
    mkdir($outputdirectory, 0777, true);
}


if (isset($_SESSION['roadsFileName'])) {
	$shapefileName = $_SESSION["roadsFileName"];
	$shapefileType = $_SESSION["roadsFileType"];
	if(file_exists($shapefileName))
	{
		copy($shapefileName, $outputdirectory.'/roads.'.$shapefileType);
		unlink($shapefileName);
		rmdir($_SESSION["username"].'/tmp/roads');
		$archive = new SevenZipArchive($outputdirectory.'/roads.'.$shapefileType, array('debug' => true));
		if(!is_dir($outputdirectory.'/roads/')){
		mkdir($outputdirectory.'/roads/', 0777, true);
		}
		$archive->extractTo($outputdirectory.'/roads/');
		unlink($outputdirectory.'/roads.'.$shapefileType);
		}
	}

if (isset($_SESSION['placesFileName'])) {
	$shapefileName = $_SESSION["placesFileName"];
	$shapefileType = $_SESSION["placesFileType"];
	if(file_exists($shapefileName))
	{
		copy($shapefileName, $outputdirectory.'/places.'.$shapefileType);
		unlink($shapefileName);
		rmdir($_SESSION["username"].'/tmp/places');
		$archive = new SevenZipArchive($outputdirectory.'/places.'.$shapefileType, array('debug' => true));
		if(!is_dir($outputdirectory.'/places/')){
		mkdir($outputdirectory.'/places/', 0777, true);
		}
		$archive->extractTo($outputdirectory.'/places/');
		unlink($outputdirectory.'/places.'.$shapefileType);
		}
	}
rmdir($_SESSION["username"].'/tmp');

$domDocument->save($xmlDirectory); 
$xmlOutPutLocation = $_SERVER["DOCUMENT_ROOT"]. "/" . $outputdirectory. '/COVID.xml';
$domDocument->save($xmlOutPutLocation); 


$tmpcmd='C:\GAMA\jdk\bin\java -cp C:\GAMA\plugins\org.eclipse.equinox.launcher_1.5.300.v20190213-1655.jar -Xms512m -Xmx2048m -Djava.awt.headless=true org.eclipse.core.launcher.Main -application msi.gama.headless.id4 -data ".work21837" ' . $xmlOutPutLocation . " " . $_SERVER["DOCUMENT_ROOT"]."/".$outputdirectory ;
$cmd ='wmic process call create "'.$tmpcmd.'" | find "ProcessId"';
echo $cmd;
//$handle = popen("start /B ". $cmd . " 1> update_log 2>&1 &" , "r");
$handle = popen("start /B ". $cmd, "r");
$read = fread($handle, 200); //Read the output 
$pid=substr($read,strpos($read,'=')+1);
$pid=substr($pid,0,strpos($pid,';') );
//setcookie("processid", $pid, time() + 3600);
$_SESSION['pid'] = $pid;
pclose($handle);
//exec('start /B D:\GAMA\jdk\bin\java -cp D:\GAMA\plugins\org.eclipse.equinox.launcher_1.5.300.v20190213-1655.jar -Xms512m -Xmx2048m -Djava.awt.headless=true org.eclipse.core.launcher.Main -application msi.gama.headless.id4 -data ".work21837" D:/GAMA/headless/samples/COVID.xml C:/wamp64/www/abmadmin/202010280840 /dev/null 2>&1 & echo $!');
header('Location: result.php');	

?>
