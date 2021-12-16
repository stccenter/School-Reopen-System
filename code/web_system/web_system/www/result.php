<?php
// Include config file
require_once "config.php";
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>Simulation</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/checkout/">
    <!-- Bootstrap core CSS -->
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

    .card-body {
        padding: .5rem;
    }


    #chartContainer {
        height: 100%;
        width: 100%;
        /* float: left; */
        /* position: fixed; */
        margin: auto;
    }


    .user-text {
        margin-right: 20px;
        color: #ffffff;
    }

    .navbar {
        margin-bottom: 20px;
    }
    </style>
    <!-- Custom styles for this template -->
    <link href="form-validation.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- <a class="navbar-brand" href="#">ABM</a> -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample07"
                aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Parameter Settings</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Running Results <span class="sr-only">(current)</span></a>
                    </li>
                    <?php
         				if($_SESSION['levelid'] == 1 )
         				{?>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="history.php">History Gallery</a>
                    </li>
                    <?php 
                    }else{
						?>

                    <li class="nav-item">
                        <a class="nav-link " href="history.php">History Gallery</a>
                    </li>
                    <?php 
					}?> 
                </ul>
                <span class="user-text">Welcome, <?php echo $_SESSION["username"]." (".$_SESSION["level"].")"; ?></span>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </nav>
    <div class="content">
        <div class="row" style="margin: 2%;">
            <div class="col">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Simulation Chart</h3>
                        </div>
                    </div>
                    <div class="card-body" style="height: 600px;">
                        <div id="chartContainer">
                            <canvas id="chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header border-0">
                                <div class="row">
                                    <div class="col">

                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title" id="title_1">Real-time Statistic Result</h3>
                                            <h3 class="card-title" id="title_2">Simulation Parameters</h3>
                                        </div>
                                    </div>
                                    <div class="col-4 align-self-center">
                                        <div class="card-tools">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    Select Panel
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" id="show_1" href="#">Real-time Statistic
                                                        Result</a>
                                                    <a class="dropdown-item" id="show_2" href="#">Simulation
                                                        Parameters</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" id="table_1" style="overflow-x: auto; height:600px">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Timestep</td>
                                            <td>
                                                <div class="value8">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Days</td>
                                            <td>
                                                <div class="value1">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Total infected people</td>
                                            <td>
                                                <div class="value2">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Number of infected people on campus</td>
                                            <td>
                                                <div class="value3">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Infected and showing symptoms</td>
                                            <td>
                                                <div class="value4">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Infected but not showing symptoms</td>
                                            <td>
                                                <div class="value5">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>People in quarantine</td>
                                            <td>
                                                <div class="value6">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Dead</td>
                                            <td>
                                                <div class="value7">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="value9">
                                            <td>Vaccine</td>
                                            <td>
                                                <div class="value9">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-body" id="table_2" style="overflow-x: auto; height:600px">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Days</td>
                                            <td>
                                                <div class="param20">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Total Population</td>
                                            <td>
                                                <div class="param1">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Init Infected People</td>
                                            <td>
                                                <div class="param2">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Residential Population</td>
                                            <td>
                                                <div class="param3">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>In-person Class Percentage</td>
                                            <td>
                                                <div class="param4">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Infection Rate (Campus)</td>
                                            <td>
                                                <div class="param5">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Infection Rate (Community)</td>
                                            <td>
                                                <div class="param6">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Percentatge of Asymptomatic Infection</td>
                                            <td>
                                                <div class="param7">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Mask's Policy</td>
                                            <td>
                                                <div class="param8">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Enforcement level of wearing masks</td>
                                            <td>
                                                <div class="param9">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>SD indoor</td>
                                            <td>
                                                <div class="param10">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Enforcement level of social distancing indoor</td>
                                            <td>
                                                <div class="param11">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>SD outdoor</td>
                                            <td>
                                                <div class="param12">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Enforcement level of social distancing outdoor</td>
                                            <td>
                                                <div class="param13">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Viral Testing</td>
                                            <td>
                                                <div class="param14">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Enforcement level of Viral testing</td>
                                            <td>
                                                <div class="param15">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Symptoms Screening</td>
                                            <td>
                                                <div class="param16">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Enforcement level of Symptoms Screening</td>
                                            <td>
                                                <div class="param17">
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Contact Tracing</td>
                                            <td>
                                                <div class="param18">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Max Viral testing capacity for contact tracing</td>
                                            <td>
                                                <div class="param19">
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:15%">
                    <div class="col">
                        <button class="btn btn-primary btn-lg btn-block" onclick="StopAndStartOver(event)" type=""
                            value="">Stop & Start Over</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="../assets/dist/js/bootstrap.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ionic/core/css/ionic.bundle.css" />
    <script src="https://d3js.org/d3.v4.js"></script>
    <!--Validation js-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="../assets/dist/js/canvasjs.min.js"></script>
    <script type="text/javascript"></script>

    <script>
    const segments = document.querySelectorAll('ion-segment')
    for (let i = 0; i < segments.length; i++) {
        segments[i].addEventListener('ionChange', (ev) => {
            console.log('Segment changed', ev);
            if (ev.detail.value == "1") {
                window.location.href = 'index.php';
            } else if (ev.detail.value == "3") {
                window.location.href = 'history.php';
            }
        })
    }

    function StopAndStartOver(e) {
        window.location.href = 'index.php';
    }

    $(document).ready(function() {
        var username = "<?php echo $_SESSION["username"]; ?>";
        var timestamp = "<?php echo $_SESSION["sessionDate"]; ?>";
        var userlevel = "<?php echo $_SESSION["levelid"]; ?>";
        var modelLevel ="";
        var indexlelvel="<?php echo $_SESSION["levelsize"]; ?>";
        var vaccineexists="<?php echo $_SESSION["vaccination"]; ?>";
        if(vaccineexists == false)
        {
            $( "#value9" ).remove();
        }
        var updateInterval = 1000;
        setInterval(function() {
            $.ajax({
                type: "GET",
                url: "showStatistics.php",
                dataType: "json",
                data: {},
                success: function(data) {
                    $(".value1").html(data.days);
                    $(".value2").html(data.totalInfected);
                    $(".value3").html(data.infOncampus);
                    $(".value4").html(data.infShowSymp);
                    $(".value5").html(data.infNoSymp);
                    $(".value6").html(data.quarantine);
                    $(".value7").html(data.dead);
                    $(".value8").html(data.timestep);
                    modelLevel = data.modelLevel;
                    if(modelLevel.includes('level1') !== true){
                        indexlelvel=156;
                        }
                        else{
                        indexlelvel = 6; 
                        }
                    if(vaccineexists == true){
                        $(".value9").html(data.vaccine);
                    }
                },

                error: function(obj) {
                    //console.log(obj);
                }
            });
            $.ajax({
                type: "GET",
                url: "showParameters.php",
                dataType: "json",
                data: {},
                success: function(data) {
                    $('.param1').html(data.totalPopulation);
                    $('.param2').html(data.initinfectedPeople);
                    $('.param3').html(data.residentialPopulation);
                    $('.param4').html(data.inPersonClass);
                    $('.param5').html(data.infectionRateCampus);
                    $('.param6').html(data.infectionRateCommunity);
                    $('.param7').html(data.percOfAsymptInf);
                    $('.param8').html(data.maskPolicy);
                    $('.param9').html(data.maskPolicyValue);
                    $('.param10').html(data.socialDistIndoor);
                    $('.param11').html(data.socialDistIndoorValue);
                    $('.param12').html(data.socialDistOutdoor);
                    $('.param13').html(data.socialDistOutdoorValue);
                    $('.param14').html(data.viralTesting);
                    $('.param15').html(data.viralTestingValue);
                    $('.param16').html(data.sympScreening);
                    $('.param17').html(data.sympScreeningValue);
                    $('.param18').html(data.contactTracing);
                    $('.param19').html(data.contactTracingValue);
                    $('.param20').html(data.days);
                },

                error: function(obj) {
                    console.log(obj);
                }

            });

        }, updateInterval);

        $("#table_2").hide();
        $("#title_2").hide();
        $("#show_1").click(function() {
            $("#table_2").hide();
            $("#title_2").hide();
            $("#table_1").show();
            $("#title_1").show();
        });
        $("#show_2").click(function() {
            $("#table_1").hide();
            $("#title_1").hide();
            $("#table_2").show();
            $("#title_2").show();
        });

        //Chart part
        var dataPoints1 = [];
        var dataPoints2 = [];
        var dataPoints3 = [];
        var dataPoints4 = [];
        var dataPoints5 = [];

        var chart = new CanvasJS.Chart("chartContainer", {

            title: {
                text: "Campus/Schools COVID-19 infection simualtion",
                fontSize: 25,
                fontWeight: "normal",
            },
            toolTip: {
                enabled: true,
                animationEnabled: true,
                contentFormatter: function(e) {
                    var content = "";
                    if(modelLevel.includes('level1') == true)
                    {
                        for (i = 0; i < e.entries.length; i++) {
                        content += "Day: " + parseInt((e.entries[i].dataPoint.x / 6) + 1) +
                            " - Timestep: " + parseInt((e.entries[i].dataPoint.x) + 1) + " - " + e
                            .entries[i].dataSeries.name + ": " + e.entries[i].dataPoint.y;
                        content += "<br/>";
                        }
                    }
                    else
                    {
                        for (i = 0; i < e.entries.length; i++) {
                        content += "Day: " + parseInt((e.entries[i].dataPoint.x / 156) + 1) +
                            " - Timestep: " + parseInt((e.entries[i].dataPoint.x) + 1) + " - " + e
                            .entries[i].dataSeries.name + ": " + e.entries[i].dataPoint.y;
                        content += "<br/>";
                        }
                    }
                    
                    return content;
                }

            },

            legend: {
                fontSize: 15,
                horizontalAlign: "center",
                verticalAlign: "top",
            },
            axisX: {
                title: "Days",
                interval: parseInt(indexlelvel),
                labelFormatter: labelGenerator,
                labelFontSize: 20,
                titleFontSize: 25,
            },
            axisY: {
                title: "Number of people",
                includeZero: false,
                labelFontSize: 20,
                titleFontSize: 25,
                minimum: -4.0,
            },
            data: [{
                    type: "line",
                    name: "Total infected people",
                    showInLegend: true,
                    markerSize: 0,
                    yValueFormatString: "#,###",
                    dataPoints: dataPoints1,
                    lineThickness: 4,
                },
                {
                    type: "line",
                    name: "Infected people on campus",
                    showInLegend: true,
                    markerSize: 0,
                    yValueFormatString: "#,###",
                    dataPoints: dataPoints2,
                    lineThickness: 4,
                },
                {
                    type: "line",
                    name: "Showing symptoms",
                    showInLegend: true,
                    markerSize: 0,
                    yValueFormatString: "#,###",
                    dataPoints: dataPoints3,
                    lineThickness: 4,
                },
                {
                    type: "line",
                    name: "Not showing symptoms yet",
                    showInLegend: true,
                    markerSize: 0,
                    yValueFormatString: "#,###",
                    dataPoints: dataPoints4,
                    lineThickness: 4,
                },
                {
                    type: "line",
                    name: "Vaccinated People",
                    showInLegend: true,
                    markerSize: 0,
                    yValueFormatString: "#,###",
                    dataPoints: dataPoints5,
                    lineThickness: 4,
                }
            ]

        }); //end of chart

        chart.render();

        function intervalGenerator(){
            var tmpinterval;
            $.ajax({
                async: false,
                type: "GET",
                url: "showStatistics.php",
                dataType: "json",
                data: {},
                success: function(data) {
                    var tmplevel = data.modelLevel;
                    if(tmplevel.includes('level1') !== true){
                        tmpinterval=156;
                        }
                        else{
                        tmpinterval=6;
                        }
                }
            });
        return parseInt(tmpinterval);
        }
    
        function labelGenerator(e) {
            if(modelLevel.includes('level1') == true){
                return parseInt(e.value / 6);
            }
            else{
                return parseInt(e.value / 156);
            } 
        }



        var x = dataPoints1.length;
        var updateChart = function() {

            // check if an element exists in array using a comparer function
            // comparer : function(currentElement)
            Array.prototype.inArray = function(comparer) {
                for (var i = 0; i < this.length; i++) {
                    if (comparer(this[i]))
                        return true;
                }
                return false;
            };

            // adds an element to the array if it does not already exist using a comparer 
            // function
            Array.prototype.pushIfNotExist = function(element, comparer) {
                if (!this.inArray(comparer)) {

                    this.push(element);
                }
            };


            var filepathxml = username + "/" + timestamp + '/simulation-outputs2.xml';
            $.ajax({
                type: "GET",
                url: filepathxml,
                dataType: "text",
                success: function(data) {
                    $(data).find("Step").each(function() {

                        var y;
                        var x = $(this).attr("id");
                        $(this).find("Variable").each(function(i, el) {
                            y = el.textContent;
                            var element = {
                                x: parseInt(x),
                                y: parseFloat(y)
                            };
                            if (i == 6) {
                                dataPoints1.pushIfNotExist(element, function(
                                e) {
                                    return e.x === element.x && e.y ===
                                        element.y;
                                });
                            }
                            if (i == 1) {
                                dataPoints2.pushIfNotExist(element, function(
                                e) {
                                    return e.x === element.x && e.y ===
                                        element.y;
                                });
                            }
                            if (i == 2) {
                                dataPoints3.pushIfNotExist(element, function(
                                e) {
                                    return e.x === element.x && e.y ===
                                        element.y;
                                });
                            }
                            if (i == 3) {
                                dataPoints4.pushIfNotExist(element, function(
                                e) {
                                    return e.x === element.x && e.y ===
                                        element.y;
                                });
                            }
                            if (i == 13) {
                                dataPoints5.pushIfNotExist(element, function(
                                e) {
                                    return e.x === element.x && e.y ===
                                        element.y;
                                });
                            }

                        })
                    });
                }
            })

            chart.render();

        }

        // update chart every second
        setInterval(function() {
            updateChart()
        }, 1000);
    });
    </script>
</body>

</html>