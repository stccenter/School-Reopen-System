<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";
require_once "getProcessInfo.php";
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>System</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/checkout/">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css" />



    <!-- Bootstrap core CSS -->
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">


    <style>

.tooltip-wrapper .btn[disabled] {
  /* don't let button block mouse events from reaching wrapper */
  pointer-events: none;
}

.tooltip-wrapper.disabled {
  /* OPTIONAL pointer-events setting above blocks cursor setting, so set it here */
  cursor: not-allowed;
}

    /* .bg-light {
      overflow: auto;
    } */
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }

    .content {
        height: 100%;
        overflow: auto;
        position: relative;
    }

    /* .container-fluid {
      width: max-content;
      display: flex;
      align-items: stretch;
    } */

    .card-body {
        padding: .5rem;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        /* margin-left: 5%;
      margin-right: 5%; */
        margin: 0 5px;
        height: 26px;
    }

    .user-text {
        margin-right: 20px;
        color: #ffffff;
    }

    .navbar {
        margin-bottom: 20px;
    }

    .slider-row {
        margin: 8px 0;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(30px);
        -ms-transform: translateX(30px);
        transform: translateX(30px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    input.error {
        border: 1px solid red;
    }

    label.error {
        background: url('images/unchecked.gif') no-repeat;
        padding-left: 16px;
        margin-left: .3em;
    }

    label {
        font-size: 20px;
    }
    </style>
    <!-- Custom styles for this template -->
    <link href="form-validation.css" rel="stylesheet">
</head>

<body class="bg-white">


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!--<a class="navbar-brand" href="#">ABM</a>-->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample07"
                aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a id="paramsetNav" class="nav-link" href="#">Parameter Settings <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a id="resultNav" class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Running Results</a>
                    </li>
                    <?php
         if($_SESSION['levelid'] == 1 )
        {
        ?>
                    <li class="nav-item">
                        <a id="hisNav" class="nav-link disabled" href="history.php">History Gallery</a>
                    </li>
                    <?php 
        }
		else{		
		?>
                    <li class="nav-item">
                        <a id="hisNav" class="nav-link " href="history.php">History Gallery</a>
                    </li>
                    <?php 
			}
         ?>

                </ul>
                <span class="user-text">Welcome, <?php echo $_SESSION["username"]." (".$_SESSION["level"].")"; ?></span>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </nav>


    <div class="content">
        <div class="container-fluid">
            <form class="needs-validation" action="run.php" method="post" id="runForm" enctype="multipart/form-data">
                <div class="row" style="margin-top: 2%;">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Campus / Schools Information</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row my-4">
                                    <div class="col-4">
                                        <label for="totalPopulation">Total Population</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="totalPopulation" name="totalPopulation" placeholder="" value="" required>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_TP" type="text" name="totalPopulation" value="">
                                    </div>
                                </div>

                                <div class="row my-4">
                                    <div class="col-4">
                                        <label for="initInfectedPpl">Init Infected People</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="initInfectedPpl" name="initInfectedPpl" placeholder="" value="" required>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_IIP" type="text" name="initInfectedPpl" value="">
                                    </div>
                                </div>

                                <div class="row my-4">
                                    <div class="col-4">
                                        <label for="residentialPopulation">Residential Population</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="residentialPopulation" name="residentialPopulation" placeholder="" value="" required>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_RP" type="text" name="residentialPopulation" value="">
                                    </div>
                                </div>

                                <div class="row my-4">
                                    <div class="col-4">
                                        <label for="inPersonClassP">In Person Class Percentage</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="inPersonClassP" name="inPersonClassP" placeholder="" value="" required>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_IPCP" type="text" name="inPersonClassP" value="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <label for="infectionRateCps">Infection Rate (Campus)</label>
                                        <input type="text" class="form-control" id="infectionRateCps" name="infectionRateCps" placeholder="" value="0.004366584" required>
                                    </div>

                                    <div class="col align-self-end">
                                        <select id="select_Campus" class="selectpicker">
                                            <option>GMU</option>
                                            <option>TBA</option>
                                            <option>TBA</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row my-4">
                                    <div class="col-4">
                                        <label for="infectionRateCmty">Infection Rate (Community)</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="infectionRateCmty" name="infectionRateCmty" placeholder="" value="0.0001" required>
                                    </div>
                                    <div class="col align-self-end">
                                        <select id="select_Community" class="selectpicker">
                                            <option>Fairfax county</option>
                                            <option>TBA</option>
                                            <option>TBA</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row my-4">
                                    <div class="col-4">
                                        <label for="PAI">Percentage of Asymptomatic Infection</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="PAI" name="PAI" placeholder="" value="" required>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_PoAI" type="text" name="PAI" value="">
                                    </div>
                                </div>


                                <div class="row mt-4">
                                    <div class="col">
                                        <input type="file" class="form-control" id="gisRoads" name="gisRoads" placeholder="" value="" form="gisRoadsForm">
                                        <input type="hidden" id="gisRoadsInput" name="gisRoadsInput" value="gisRoadsValue" form="gisRoadsForm">
                                        <p id="gisRoadsMsg"></p>
                                    </div>
                                    <div class="col">
                                        <button id="gisRoadsButton" class="btn btn-primary btn-md btn-block" onclick="roadUpload(event)" type="submit" form="gisRoadsForm" value="roadfileUpload">GIS-roads Upload</button>
                                    </div>
                                </div>


                                <div class="row" style="margin-top: 10px;">
                                    <div class="col">
                                        <input type="file" class="form-control" id="gisPlaces" name="gisPlaces"placeholder="" value="" form="gisPlacesForm">
                                        <input type="hidden" id="gisPlacesInput" name="gisPlacesInput" value="gisPlacesValue" form="gisPlacesForm">
                                        <p id="gisPlacesMsg"></p>
                                    </div>
                                    <div class="col">
                                        <button id="gisPlacesButton" class="btn btn-primary btn-md btn-block" onclick="placeUpload(event)" type="submit" value="placesfileUpload" form="gisPlacesForm">GIS-places Upload</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card" style="margin-top: 2%;">
                            <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Simulation Settings</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row my-4">
                                    <div class="col-4">
                                        <label for="simPeriod">Simulation Period (Days)</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="simPeriod" name="simPeriod" placeholder="" value="" required>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_SP" type="text" name="simPeriod" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.col-md-6 -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Control Policies</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row my-4 mx-1">
                                    <div class="col-2">
                                        <label class="switch">
                                            <input name="maskSwitch" id="maskSwitch" type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="col-3">
                                        <label for="maskPolicy">Mask's Policy</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="maskPolicy" name="maskPolicy" placeholder="" value="" disabled>
                                    </div>

                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_MP" type="text" name="maskPolicy" value="">
                                    </div>
                                </div>

                                <div class="row my-4 mx-1">
                                    <div class="col-2">
                                        <label class="switch">
                                            <input name="socialDisInSwitch" id="socialDisInSwitch" type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="col-3">
                                        <label for="socialDistIn">SD Indoor<span class="text-muted"></span></label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="socialDistIn" name="socialDistIn"placeholder="" value="" disabled>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_SDI" type="text" name="socialDistIn" value="">
                                    </div>
                                </div>


                                <div class="row my-4 mx-1">
                                    <div class="col-2">
                                        <label class="switch">
                                            <input name="socialDisOutSwitch" id="socialDisOutSwitch" type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="col-3">
                                        <label for="socialDistOut">SD Outdoor<span class="text-muted"></span></label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="socialDistOut" name="socialDistOut" placeholder="" value="" disabled>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_SDO" type="text" name="socialDistOut" value="">
                                    </div>
                                </div>

                                <div class="row my-4 mx-1">
                                    <div class="col-2">
                                        <label class="switch">
                                            <input name="viralTestingSwitch" id="viralTestingSwitch" type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="col-3">
                                        <label for="viralTesting">Viral Testing<span class="text-muted"></span></label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="viralTesting" name="viralTesting" value="" disabled>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_VT" type="text" name="viralTesting" value="">
                                    </div>
                                </div>

                                <div class="row my-4 mx-1" style="padding-top: .75rem">
                                    <div class="col">
                                        <label class="d-flex align-items-center">Checking Frequency (days)</label>
                                    </div>
                                    <div class="col align-self-center">
                                        <select class="selectpicker" id="dropdown_1" name="dropdown_1" disabled>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                            <option>6</option>
                                            <option>7</option>
                                            <option>8</option>
                                            <option>9</option>
                                            <option>10</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row my-4 mx-1">
                                    <div class="col-2">
                                        <label class="switch">
                                            <input name="sympScreenSwitch" id="sympScreenSwitch" type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="col-3">
                                        <label for="sympScreen">Symptoms Screening<span class="text-muted"></span></label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="sympScreen" name="sympScreen" value="" disabled>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_SS" type="text" name="sympScreen" value="">
                                    </div>
                                </div>

                                <div class="row my-4 mx-1" style="padding-top: .75rem">
                                    <div class="col">
                                        <label class="d-flex align-items-center">Checking Frequency (days)</label>
                                    </div>
                                    <div class="col align-self-center">
                                        <select class="selectpicker" id="dropdown_2" name="dropdown_2" disabled>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                            <option>6</option>
                                            <option>7</option>
                                            <option>8</option>
                                            <option>9</option>
                                            <option>10</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="row my-4 mx-1">
                                    <div class="col-2">
                                        <label class="switch">
                                            <input name="contactTraceSwitch" id="contactTraceSwitch" type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="col-3">
                                        <label for="contactTrace">Contact Tracing<span class="text-muted"></span></label>
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" id="contactTrace" name="contactTrace" value="" disabled>
                                    </div>
                                    <div class="col align-self-center">
                                        <input class="scroll-bar" id="slider_CT" type="text" name="contactTrace" value="">
                                    </div>
                                </div>

                                <div class="row my-4 mx-1">
                                    <div class="col-2">
                                        <label class="switch">
                                            <input name="vaccineSwitch" id="vaccineSwitch" type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="col-3">
                                        <label for="vaccineSwitch">Vaccine<span class="text-muted"></span></label>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <!-- /.card -->
                        <?php if ($_SESSION["levelid"] != 1) { ?>
                        <div class="row justify-content-start" style="margin: 3%">
                            <div class="col-2">
                                <label class="switch">
                                    <input name="runBtnToggle" id="runBtnToggle" type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="col-3">
                                        <label for="runBtnToggle">Express Mode<span class="text-muted"></span></label>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row justify-content-center" style="margin: 3%">
                            <div class="col">
                                <label class="d-flex align-items-center">Pre-set Simulations</label>
                            </div>
                            <div class="col">
                                <select class="selectpicker" id="dropdown_3"  name="dropdown_3" multiple data-max-options="1">
                                    <option value="1">GMU</option>
                                    <option value="2">TBA</option>
                                    <option value="3">TBA</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin: 3%">
                            <div class="col">
                                <button id="startOver" class="btn btn-primary btn-lg btn-block" onclick="buttonStartover(event)" type="reset" value="">Start Over</button>
                            </div>
                            <?php 
                            if((isset($_SESSION["pid"])) && (is_process_running($_SESSION["pid"])))
                            {
                            ?>
                            <div class="tooltip-wrapper col" data-title="Last process is running.">
                                    <button class="btn btn-primary btn-lg btn-block" disabled>Processing</button>
                            </div>
                            <?php
                            }
                            else{ ?>
                                <div class="col">
                                <button class="btn btn-primary btn-lg btn-block" form="runForm" onclick="buttonRun(event)" id="modelRun" name="run" type="submit" value="">Run</button>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>

                    </div>
                    <!-- /.col-md-6 -->
                </div>
            </form>
            <form action="fileUpload.php" id="gisRoadsForm"></form>
            <form action="fileUpload.php" id="gisPlacesForm"></form>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="../assets/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <!--Validation js-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>

    <script>
    let preset1 = [5000, 5, 3350, 0.1, 0.4, 1, 0.8, 0.8, 0.8, 0.2, 0.8, 80];
    $(function() {
        /* ION SLIDER */
        $('#slider_TP').ionRangeSlider({
            min: 1,
            max: 10000,
            from: 5000,
            step: 1,
            onStart: function(data) {
                $("#totalPopulation").val("5000")
            },
            onFinish: function(data) {
                $("#totalPopulation").val(data.from)
            },
            onUpdate: function(data) {
                $("#totalPopulation").val(data.from)
            },
            // hasGrid: true      
        })
        $('#slider_IIP').ionRangeSlider({
            min: 1,
            max: 1000,
            from: 5,
            step: 1,
            onStart: function(data) {
                $("#initInfectedPpl").val("5")
            },
            onFinish: function(data) {
                $("#initInfectedPpl").val(data.from)
            },
            onUpdate: function(data) {
                $("#initInfectedPpl").val(data.from)
            }
        })
        $('#slider_RP').ionRangeSlider({
            min: 1,
            max: 10000,
            from: 3350,
            step: 1,
            onStart: function(data) {
                $("#residentialPopulation").val("3350")
            },
            onFinish: function(data) {
                $("#residentialPopulation").val(data.from)
            },
            onUpdate: function(data) {
                $("#residentialPopulation").val(data.from)
            },
        })
        $('#slider_IPCP').ionRangeSlider({
            min: 0.0,
            max: 1.0,
            from: 0.8,
            step: 0.01,
            onStart: function(data) {
                $("#inPersonClassP").val("0.8")
            },
            onFinish: function(data) {
                $("#inPersonClassP").val(data.from)
            },
            onUpdate: function(data) {
                $("#inPersonClassP").val(data.from)
            },
        })

        $('#slider_PoAI').ionRangeSlider({
            min: 0.0,
            max: 1.0,
            from: 0.4,
            step: 0.01,
            onStart: function(data) {
                $("#PAI").val("0.4")
            },
            onFinish: function(data) {
                $("#PAI").val(data.from)
            },
            onUpdate: function(data) {
                $("#PAI").val(data.from)
            },
        })
        // second session
        $('#slider_MP').ionRangeSlider({
            min: 0.0,
            max: 1.0,
            from: 0.8,
            step: 0.01,
            disable: true,
            onStart: function(data) {
                $("#maskPolicy").val(data.from)
            },
            onFinish: function(data) {
                $("#maskPolicy").val(data.from)
            },
            onUpdate: function(data) {
                if ($("#maskSwitch").is(':checked')) {
                    $("#maskPolicy").val(data.from)
                }
            },
        })
        $('#slider_SDI').ionRangeSlider({
            min: 0.0,
            max: 1.0,
            from: 0.8,
            step: 0.01,
            disable: true,
            onStart: function(data) {
                $("#socialDistIn").val(data.from)
            },
            onFinish: function(data) {
                $("#socialDistIn").val(data.from)
            },
            onUpdate: function(data) {
                if ($("#socialDisInSwitch").is(':checked')) {
                    $("#socialDistIn").val(data.from)
                }
            },
        })
        $('#slider_SDO').ionRangeSlider({
            min: 0.0,
            max: 1.0,
            from: 0.8,
            step: 0.01,
            disable: true,
            onStart: function(data) {
                $("#socialDistOut").val(data.from)
            },
            onFinish: function(data) {
                $("#socialDistOut").val(data.from)
            },
            onUpdate: function(data) {
                if ($("#socialDisOutSwitch").is(':checked')) {
                    $("#socialDistOut").val(data.from)
                }
            },
        })

        $('#slider_VT').ionRangeSlider({
            min: 0.0,
            max: 1.0,
            from: 0.2,
            step: 0.01,
            disable: true,
            onStart: function(data) {
                $("#viralTesting").val(data.from)
            },
            onFinish: function(data) {
                $("#viralTesting").val(data.from)
            },
            onUpdate: function(data) {
                if ($("#viralTestingSwitch").is(':checked')) {
                    $("#viralTesting").val(data.from)
                }
            },
        })

        $('#slider_SS').ionRangeSlider({
            min: 0.0,
            max: 1.0,
            from: 0.8,
            step: 0.01,
            disable: true,
            onStart: function(data) {
                $("#sympScreen").val(data.from)
            },
            onFinish: function(data) {
                $("#sympScreen").val(data.from)
            },
            onUpdate: function(data) {
                if ($("#sympScreenSwitch").is(':checked')) {
                    $("#sympScreen").val(data.from)
                }
            },
        })

        $('#slider_CT').ionRangeSlider({
            min: 1,
            max: 500,
            from: 80,
            step: 1,
            disable: true,
            onStart: function(data) {
                // $("#contactTrace").val("80")
            },
            onFinish: function(data) {
                $("#contactTrace").val(data.from)
            },
            onUpdate: function(data) {
                if ($("#contactTraceSwitch").is(':checked')) {
                    $("#contactTrace").val(data.from)
                }
            },
        })

        //third section
        $('#slider_SP').ionRangeSlider({
            min: 1,
            max: 100,
            from: 1,
            step: 1,
            onStart: function(data) {
                $("#simPeriod").val(data.from)
            },
            onFinish: function(data) {
                $("#simPeriod").val(data.from)
            },
            onUpdate: function(data) {
                $("#simPeriod").val(data.from)
            },
        })
    })


    $(".form-control").on("input", function() {
        let field_name = $(this).prop("name")
        let val = $(this).prop("value")
        $('.scroll-bar').each(function() {
            if (field_name == $(this).prop("name")) {
                $(this).data("ionRangeSlider").update({
                    from: val
                })
            }
        })
    })


    function buttonRun(e) {
        $("#runForm").validate();
        if ($("#runForm").valid()) {
            document.getElementById("runForm").submit();
        };
    }


    function buttonStartover(e) {
        $('#runForm').get(0).reset();
        $("#maskPolicy").attr('disabled', 'disabled');
        $("#maskPolicy").val("");
        $("#socialDistIn").attr('disabled', 'disabled');
        $("#socialDistIn").val("");
        $("#socialDistOut").attr('disabled', 'disabled');
        $("#socialDistOut").val("");
        $("#sympScreen").attr('disabled', 'disabled');
        $("#sympScreen").val("");
        $("#contactTrace").attr('disabled', 'disabled');
        $("#contactTrace").val("");
        $("#viralTesting").attr('disabled', 'disabled');
        $("#viralTesting").val("");

        $('.scroll-bar').each(function() {
            instance = $(this).data("ionRangeSlider")
            // console.log(instance.result.min)
            instance.update({
                from: instance.result.min
            })
        })
       $('#dropdown_3').selectpicker('val', null)
    }

    $(document).ready(function() {
        $('.tooltip-wrapper').tooltip({position: "bottom"});
        
        $("#infectionRateCps").val(0.004366584)
        $("#infectionRateCmty").val(0.0001)

        

        $('#dropdown_3').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
            console.log(clickedIndex)
            if (clickedIndex == 0) {
                $("#infectionRateCps").val(0.004366584)
                $("#infectionRateCmty").val(0.0001)

                let i = 0
                $('.scroll-bar').each(function() {
                    instance = $(this).data("ionRangeSlider")
                    instance.update({
                        from: preset1[i]
                    })
                    i++
                })
            }
        });


        var level = "<?php echo $_SESSION["levelid"]; ?>";
        if(level == 1){
          //total population
          $("#totalPopulation").prop("readonly", true);
          $("#slider_TP").data("ionRangeSlider").update({
            disable: true
          })
          //Init infected people
          $("#initInfectedPpl").prop("readonly", true);
          $("#slider_IIP").data("ionRangeSlider").update({
            disable: true
          })
          //Residential population
          $("#residentialPopulation").prop("readonly", true);
          $("#slider_RP").data("ionRangeSlider").update({
            disable: true
          })
          //In person class percentage
          $("#inPersonClassP").prop("readonly", true);
          $("#slider_IPCP").data("ionRangeSlider").update({
            disable: true
          })
          //Infection rate (campus)
          $("#infectionRateCps").prop("readonly", true);
          $("#select_Campus").attr("disabled", "true");
          //Infection rate (community)
          $("#infectionRateCmty").prop("readonly", true);
          $("#select_Community").prop("disabled", true);
          //Percentage of asymptomatic infection
          $("#PAI").prop("readonly", true);
          $("#slider_PoAI").data("ionRangeSlider").update({
                disable: true
            })
          //roads shapefile
          $("#gisRoads").prop("disabled", true);
          $("#gisRoadsInput").prop("disabled", true);
          $("#gisRoadsButton").prop("disabled", "disabled");
          //places shapefile
          $("#gisPlaces").prop("disabled", true);
          $("#gisPlacesInput").prop("disabled", true);
          $("#gisPlacesButton").prop("disabled", true);

         
          //Simulation period
          $('#slider_SP').data("ionRangeSlider").update({
              disable: true,
            min: 1,
            max: 100,
            from: 10,
            step: 1,
            onStart: function(data) {
                $("#simPeriod").val(data.from)
            },
            onFinish: function(data) {
                $("#simPeriod").val(data.from)
            },
            onUpdate: function(data) {
                $("#simPeriod").val(data.from)
            },
        })
         $("#simPeriod").prop("readonly", true);
         // $("#simPeriod").prop("value", "10");
         // $("#slider_SP").data("ionRangeSlider").update({
         //      disable: true
          //  })
   
          //mask's policy
          $("#maskSwitch").prop('disabled', true);
          $("#maskPolicy").prop('readonly', true);
          $("#maskPolicy").prop('value', "0.8");
          $("#slider_MP").data("ionRangeSlider").update({
                disable: true
            })
          //social distance indoor
          $("#socialDisInSwitch").prop('disabled', true);
          $("#socialDistIn").prop('readonly', true);
          $("#socialDistIn").prop('value', "0.8");
          $("#slider_SDI").data("ionRangeSlider").update({
                disable: true
            })
          //social distance outdoor
          $("#socialDisOutSwitch").prop('disabled', true);
          $("#socialDistOut").prop('readonly', true);
          $("#socialDistOut").prop('value', "0.8");
          $("#slider_SDO").data("ionRangeSlider").update({
                disable: true
            })

          //Viral testing
          $("#viralTestingSwitch").prop('disabled', true);
          $("#viralTesting").prop('readonly', true);
          $("#viralTesting").prop('value', "0.2");
          $("#slider_VT").data("ionRangeSlider").update({
                disable: true
          })
          //symptoms screening
          $("#dropdown_1").prop("disabled", "disabled");
          $("#sympScreenSwitch").prop('disabled', true);
          $("#sympScreen").prop('readonly', true);
          $("#sympScreen").prop('value', "0.8");
          $("#slider_SS").data("ionRangeSlider").update({
              disable: true
          })
          //contact tracing
          $("#dropdown_2").prop("disabled", "disabled");
          $("#contactTraceSwitch").prop('disabled', true);
          $("#contactTrace").prop('readonly', true);
          $("#contactTrace").prop('value', "80");
          $("#slider_CT	").data("ionRangeSlider").update({
              disable: true
          })
          $('#startOver').prop("disabled", true)
        }
        if (level == 2) {
            //in person class percentage
            $("#inPersonClassP").prop("readonly", true);
            $("#slider_IPCP").data("ionRangeSlider").update({
                disable: true
            })
            //infection rate campus
            $("#infectionRateCps").prop("readonly", true);
            $("#select_Campus").prop("disabled", "disabled");
            //infection rate community
            $("#infectionRateCmty").prop("readonly", true);
            $("#select_Community").prop("disabled", "disabled");
            //PAI
            $("#PAI").prop("readonly", true);
            $("#slider_PoAI").data("ionRangeSlider").update({
                disable: true
            })
            //file upload roads
            $("#gisRoads").prop("disabled", true);
            $("#gisRoadsInput").prop("disabled", true);
            $("#gisRoadsButton").prop("disabled", "disabled");
            //file upload places
            $("#gisPlaces").prop("disabled", true);
            $("#gisPlacesInput").prop("disabled", true);
            $("#gisPlacesButton").prop("disabled", true);
            //viral testing
            $("#viralTestingSwitch").prop('disabled', true);
            $("#viralTesting").prop('readonly', true);
            $("#viralTesting").prop('value', "0.2");
            $("#slider_VT").data("ionRangeSlider").update({
                disable: true
            })
            //symptoms screening
            $("#dropdown_1").prop("disabled", "disabled");
            $("#sympScreenSwitch").prop('disabled', true);
            $("#sympScreen").prop('readonly', true);
            $("#sympScreen").prop('value', "0.2");
            $("#slider_SS").data("ionRangeSlider").update({
                disable: true
            })
            //contact tracing
            $("#dropdown_2").prop("disabled", "disabled");
            $("#contactTraceSwitch").prop('disabled', true);
            $("#contactTrace").prop('readonly', true);
            $("#contactTrace").prop('value', "80");
            $("#slider_CT	").data("ionRangeSlider").update({
                disable: true
            })
           // $("#dropdown_3 option[value=1]").attr('selected', 'selected')  
           // $("#dropdown_3").prop('selected', true)          
        }

        $('#maskSwitch').click(function() {
            if ($("#maskSwitch").is(':checked')) {
                $('#maskPolicy').removeAttr('disabled');
                $('#maskPolicy').attr('required', 'required');
                $("#slider_MP").data("ionRangeSlider").update({
                    disable: false
                })
            } else {
                $("#maskPolicy").attr('disabled', 'disabled');
                $("#slider_MP").data("ionRangeSlider").reset()
                $("#slider_MP").data("ionRangeSlider").update({
                    disable: true
                })
                $("#maskPolicy").val("");
            }
        });



        $('#socialDisInSwitch').click(function() {
            if ($("#socialDisInSwitch").is(':checked')) {
                $('#socialDistIn').removeAttr('disabled');
                $('#socialDistIn').attr('required', 'required');
                $("#slider_SDI").data("ionRangeSlider").update({
                    disable: false
                })
            } else {
                $("#socialDistIn").attr('disabled', 'disabled');
                $("#slider_SDI").data("ionRangeSlider").reset()
                $("#slider_SDI").data("ionRangeSlider").update({
                    disable: true
                })
                $("#socialDistIn").val("");
            }
        });

        $('#socialDisOutSwitch').click(function() {
            if ($("#socialDisOutSwitch").is(':checked')) {
                $('#socialDistOut').removeAttr('disabled');
                $('#socialDistOut').attr('required', 'required');
                $("#slider_SDO").data("ionRangeSlider").update({
                    disable: false
                })
            } else {
                $("#socialDistOut").attr('disabled', 'disabled');
                $("#slider_SDO").data("ionRangeSlider").reset()
                $("#slider_SDO").data("ionRangeSlider").update({
                    disable: true
                })
                $("#socialDistOut").val("");
            }
        });

        $('#contactTraceSwitch').click(function() {
            if ($("#contactTraceSwitch").is(':checked')) {
                $('#contactTrace').removeAttr('disabled');
                $('#contactTrace').attr('required', 'required')
                $("#slider_CT").data("ionRangeSlider").update({
                    disable: false
                })
            } else {
                $("#contactTrace").attr('disabled', 'disabled');
                $("#slider_CT").data("ionRangeSlider").reset()
                $("#slider_CT").data("ionRangeSlider").update({
                    disable: true
                })
                $("#contactTrace").val("");
            }
        });

        // events with dropdown
        $('#sympScreenSwitch').click(function() {
            if ($("#sympScreenSwitch").is(':checked')) {
                $('#dropdown_2').prop('disabled', false);
                $('#dropdown_2').selectpicker('refresh');
                // $('#dropdown_2').removeAttr('disabled');
                $('#sympScreen').removeAttr('disabled');
                $('#sympScreen').attr('required', 'required');
                $("#slider_SS").data("ionRangeSlider").update({
                    disable: false
                });
            } else {
                $('#dropdown_2').prop('disabled', true);
                $('#dropdown_2').selectpicker('refresh');
                // $("#dropdown_2").attr('disabled', 'disabled');
                $("#sympScreen").attr('disabled', 'disabled');
                $("#slider_SS").data("ionRangeSlider").reset();
                $("#slider_SS").data("ionRangeSlider").update({
                    disable: true
                });
                $("#sympScreen").val("");
            }
        });

        $('#viralTestingSwitch').click(function() {
            if ($("#viralTestingSwitch").is(':checked')) {
                $('#dropdown_1').prop('disabled', false);
                $('#dropdown_1').selectpicker('refresh');
                // $('#dropdown_1').removeAttr('disabled');
                $('#viralTesting').removeAttr('disabled');
                $('#viralTesting').attr('required', 'required')
                $("#slider_VT").data("ionRangeSlider").update({
                    disable: false
                });
            } else {
                $('#dropdown_1').prop('disabled', true);
                $('#dropdown_1').selectpicker('refresh');
                // $("#dropdown_1").attr('disabled', 'disabled');
                $("#viralTesting").attr('disabled', 'disabled');
                $("#slider_VT").data("ionRangeSlider").reset();
                $("#slider_VT").data("ionRangeSlider").update({
                    disable: true
                });
                $("#viralTesting").val("");
            }
        });

        //File upload logic
        $('#gisRoadsForm').submit(function(e) {
            e.preventDefault();
            var formValue;
            var file_data;
            var form = $(this);
            var formId = $(this).prop('id');
            //php name
            var url = form.attr('action');
            //input hidden 
            formValue = JSON.stringify($('#gisRoadsInput').prop('value'));
            //input file
            file_data = $('#gisRoads').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('input', formValue);
            $.ajax({
                type: "POST",
                url: url,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',
                data: form_data, // serializes the form's elements.
                dataType: "json",
                success: function(data) {
                    $('#gisRoadsMsg').html(data.message);
                },
                error: function() {
                    alert('error occured');
                }
            });
        });

        $('#gisPlacesForm').submit(function(e) {
            e.preventDefault();
            var formValue;
            var file_data;
            var form = $(this);
            var formId = $(this).prop('id');
            //php name
            var url = form.attr('action');
            //input hidden 
            formValue = JSON.stringify($('#gisPlacesInput').prop('value'));
            //input file
            file_data = $('#gisPlaces').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('input', formValue);
            $.ajax({
                type: "POST",
                url: url,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',
                data: form_data, // serializes the form's elements.
                dataType: "json",
                success: function(data) {
                    $('#gisPlacesMsg').html(data.message);
                },
                error: function() {
                    alert('error occured');
                }
            });
        });

        <?php if(isset($_SESSION["pid"]))
        {?>
            var pid = "<?php echo $_SESSION["pid"]; ?>";
            $.ajax({
                url: "getProcessInfo.php",
                data:{head:'check' ,pid:pid},
                type: "post",
                dataType: "json",
                success: function(data){
                    if(data.isrunning)
                    {
                        //$("#modelRun").prop("disabled","disabled");
                        $("#resultNav").removeClass("disabled");
                        $("#resultNav").attr("href","result.php");
                    }
                }
            });
        <?php } ?>



 
    });
    </script>
    <script src="form-validation.js"></script>
</body>

</html>