<?php
// Initialize the session
session_start();

// Include config file
require_once "config.php";
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
    <title>History Gallery</title>
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
		
		.modal-dialog,
		.modal-content {
			/* 80% of window height */
			height: 90%;
			overflow: auto;
			position: relative;
		}
		}

		<!--.modal-body {
		/* 100% = dialog height, 120px = header + footer */
		max-height: calc(100% - 120px);
		overflow-y: scroll;
		}-->
		
		 <!-- @media screen and (min-width: 676px) {
        .modal-dialog {
          max-width: 1400px; /* New width for default modal */
		  max-height: 100%;
        }-->
		
		

		div.col-md-8 {
			padding-left: 0;
		}
		div.col-md-3.9{
			padding-right: 0;
		}
		
		#pagin li {
  			display: inline-block;
		}
		
		.user-text {
            margin-right: 20px;
            color: #ffffff;
        }
       	
        .navbar {
            margin-bottom: 20px;
        }

		#chartContainer { 
			height: 100%; 
			width: 100%; 
			/* float: left; */
			/* position: fixed; */
			margin: auto; 
		}
    }
    </style>
</head>

<body class="bg-light">
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
           <!-- <a class="navbar-brand" href="#">ABM</a> -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Parameter Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="result.php">Running Results</a>
                    </li>
                  		 <?php
         if($_SESSION['levelid'] == 1 )
         {
         ?>
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
					}
         ?>
		 

                </ul>
                <span class="user-text">Welcome, <?php echo $_SESSION["username"]." (".$_SESSION["level"].")"; ?></span>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </nav>
    <main role="main">

        <div class="album py-5 bg-light">
			 <div class="row mt-4">
				<div class="col">
					<nav aria-label="...">
						<ul id="pagin" class="pagination justify-content-center"> </ul>
					</nav>
				</div>
			</div>

            <div class="container">
                <div class="row">

                <!-- row -->

                </div> 
            </div>
        </div>

    </main>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="../assets/dist/js/bootstrap.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ionic/core/css/ionic.bundle.css" /> -->
    <script src="https://d3js.org/d3.v4.js"></script>
    <!--Validation js-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
	<script type="text/javascript" src="../assets/dist/js/canvasjs.min.js"></script>

    <script>	
        const segments = document.querySelectorAll('ion-segment')
        for (let i = 0; i < segments.length; i++) {
            segments[i].addEventListener('ionChange', (ev) => {
                console.log('Segment changed', ev);
                if (ev.detail.value == "1") {
                    window.location.href = 'index.php';
                } else if (ev.detail.value == "2") {
                    window.location.href = 'result.php'
                }
            })
        }
		
		var username = "<?php echo $_SESSION["username"]; ?>";	
		var elem;
		$(document).ready(function() {
		$.ajax({
					type: "GET",
					url: "getFolders.php",
					dataType: "json",
					data: {},
					success: function(data) {
						console.log(data.latestFiles[1]);
						$.each(data.folderList, function(folderk,folderv) {
							//$.each(this,function(folderk, folderv) {	
							var year = folderv.slice(0,4);
							var month = folderv.slice(4,-6);
							var day = folderv.slice(6,-4);
							var time = folderv.slice(-4)
							var hour = time.slice(0,2)
							var min = time.slice(-2);
							var timez = (hour >= 12 ? "PM":"AM");
							if(hour > 12)
								hour = hour % 12;
							var download_time = "_"+year+"_"+month+"_"+day+"_"+time+"_"+hour+"_"+min+"_"+timez;

								elem = ' <div class="col-md-4">' +
												'<div class="card mb-4 shadow-sm">' +
													'<svg class="bd-placeholder-img card-img-top" width="100%" height="225" focusable="false" role="img" aria-label="Placeholder: Thumbnail">'+
														'<title>Placeholder</title>'+
														'<image width="100%" height="100%" href="'+data.latestFiles[folderk]+'" alt="Triangle with three unequal sides" />'+
													'</svg>'+
													'<div class="card-body">'+
														'<p class="card-text">Processed time: '+year+"-"+month+"-"+day+ " " + hour+":"+min+" "+timez+'</p>'+
														'<div class="d-flex justify-content-between align-items-center">'+
														'<div class="btn-group">'+
															'<button id='+"showChart"+folderk+ ' type="button" data-toggle="modal" data-target="#exampleModalCenter'+folderk+'" onclick="showChart('+ folderv + "," + folderk + ')" class="btn btn-sm btn-outline-secondary">Details</button>'+
															<!-- Modal -->
															'<div class="modal fade bd-example-modal-lg" id=' + "exampleModalCenter"+folderk+ ' tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">'+
															'<div class="modal-dialog modal-lg modal-dialog-centered">'+
															'<div class="modal-content">'+
																'<div class="modal-header">'+
																	'<h5 class="modal-title" id="exampleModalLongTitle">Simulation result</h5>'+
																	'<button type="button" class="close" data-dismiss="modal" aria-label="Close">'+
																	'<span aria-hidden="true">&times;</span>'+
																	'</button>'+
																'</div>'+
																'<div class="modal-body">'+
																	'<div class="container-fluid">'+
																		'<div clas="row">'+
																			'<div id='+"chartContainer" + folderk + ' class="col-md-7"  style="float: left; width: 80%; height: 600px;">'+
																	

																				
																			'</div>'+
																			'<div class="col-md-3.9" style="float: right; height:600px; overflow-y: scroll;">'+
																				'<table class="table">'+
																				'<thead>'+												
																				'<tr>'+
																					'<th>Field</th>'+
																					'<th>Value</th>'+
																				'</tr>'+
																				'</thead>'+
																				'<tbody>'+
																				'<tr>'+
																					'<td>Days</td>'+
																					'<td><div class="param20"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Total population</td>'+
																					'<td><div class="param1"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Init Infected</td>'+
																					'<td><div class="param2"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Residential Population</td>'+
																					'<td><div class="param3"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>In-person Class Percentage</td>'+
																					'<td><div class="param4"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Infection Rate (Campus)</td>'+
																					'<td><div class="param5"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Infection Rate (Community)</td>'+
																					'<td><div class="param6"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Percentage of Asymptomatic Infection</td>'+
																					'<td><div class="param7"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Mask&#39s policy</td>'+
																					'<td><div class="param8"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Enforcement level of wearing masks</td>'+
																					'<td><div class="param9"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>SD indoor</td>'+
																					'<td><div class="param10"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Enforcement level of social distance indoor</td>'+
																					'<td><div class="param11"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>SD Outdoor</td>'+
																					'<td><div class="param12"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Enforcement level of social distance indoor</td>'+
																					'<td><div class="param13"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Viral Testing</td>'+
																					'<td><div class="param14"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Enforcement level of Viral Testing</td>'+
																					'<td><div class="param15"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Symptoms Screening</td>'+
																					'<td><div class="param16"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Enforcement level of Symptoms Screening</td>'+
																					'<td><div class="param17"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Contact Tracing</td>'+
																					'<td><div class="param18"></div></td>'+
																				'</tr>'+
																				'<tr>'+
																					'<td>Max Viral Testing capacity for contact tracing</td>'+
																					'<td><div class="param19"></div></td>'+
																				'</tr>'+
																			'</tbody>'+
																			'</table>'+
																		'</div>'+
																'</div>'+						
																'</div>'+
																'</div>'+
																
																'<div class="modal-footer justify-content-between">'+
								
																'<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>'+
																'</div>'+
															'</div>'+
															'</div>'+
															'</div>'+
															<!--end modal-->	
															'<a id="downloadLink" hidden download='+username+download_time+'.xml></a>'+
															'<button id='+"download"+folderk+ ' onclick="downloadXml('+folderv+')" type="button" class="btn btn-sm btn-outline-secondary">Download</button>'+
														'</div>'+
														'</div>'+
													'</div>'+
												'</div>'+
											'</div>';
							$(elem).appendTo(".container .row");
							//});
						});
						
						//Pagination
						pageSize = 6;
						var pageCount =  $(".container .row .col-md-4").length / pageSize;  
						for(var i = 0 ; i < pageCount;i++)							
						{  							
							$("#pagin").append('<li class="page-item"><a class="page-link" href="#">'+(i+1)+'</a></li> ');							
						}
						$("#pagin li").first().find("a").addClass("current")
						showPage = function(page) 
						{
							$(".container .row .col-md-4").hide();
							$(".container .row .col-md-4").each(function(n) 
							{
							if (n >= pageSize * (page - 1) && n < pageSize * page)
								$(this).show();
							});        
						}  
						showPage(1);
						var pageNumbers = $("#pagin li a").length;
						//if(pageNumbers > 2)
						//{
						//	$("#pagin").prepend('<li class="page-item"><a class="page-link" href="#" aria-label="Previous"><span class="previous" aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a></li> ');	
						//	$("#pagin").append('<li class="page-item"><a class="page-link" href="#" aria-label="Next"><span class="next" aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a></li> ');
						//}

						$("#pagin li a").click(function() 
						{
							$("#pagin li a").removeClass("current");
							$(this).addClass("current");
							showPage(parseInt($(this).text())) 
						});
						
						$("#pagin li a span.next").click(function() 
						{
							//$('#pagin li a').removeClass('current').next().addClass('current');
							var pagenumber = $("#pagin li a.current").text();
							console.log(pagenumber);
							$("#pagin li a").removeClass("current");
							showPage(parseInt(pagenumber+1))
						});
						
				
					} ,
					error: function(obj) {
						console.log(obj);
					}
				});
			});


		
		function downloadXml(folder) {
			var filepathxml = username + "/" + folder + '/simulation-outputs2.xml';
			var hiddenElement = document.getElementById('downloadLink'); 
			hiddenElement.href = filepathxml;
			hiddenElement.click();
		}	
		
		function showChart(folder,folderk) {
		var filepathxml = username + "/" + folder + '/simulation-outputs2.xml';
		var covidxml = username + "/" + folder + '/COVID.xml';
		var dataPoints1 = [];
		var dataPoints2 = [];
		var dataPoints3 = [];
		var dataPoints4 = [];
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
								if (i == 6) {
									dataPoints1.push({
										x: parseInt(x),
										y: parseFloat(y)
									});
								}

								if (i == 1) {
									dataPoints2.push({
										x: parseInt(x),
										y: parseFloat(y)
									});
								}

								if (i == 2) {
									dataPoints3.push({
										x: parseInt(x),
										y: parseFloat(y)
									});
								}
								if (i == 3) {
									dataPoints4.push({
										x: parseInt(x),
										y: parseFloat(y)
									});
								}
							})
						});
						
						//Chart logic
						var chart = null;
						setTimeout(function(){
						chart = new CanvasJS.Chart("chartContainer"+folderk, {
							title: {
								text: "Campus/Schools COVID-19 infection simualtion",
								fontSize: 25,
								fontWeight: "normal",
							},
							toolTip: {
								enabled: true,
								animationEnabled: true,
								contentFormatter: function (e) {								
									var content = "";
									for (i = 0; i < e.entries.length; i++) {
										content += "Day: " + parseInt((e.entries[i].dataPoint.x / 156) + 1) + " - " + e.entries[i].dataSeries.name + ": " + e.entries[i].dataPoint.y;
										content += "<br/>";
									}
									return content;
								}

							},
							responsive: true,
							maintainAspectRatio: true,

							legend: {
								fontSize: 15,
								horizontalAlign: "center",
								verticalAlign: "top",
							},
							axisX: {
								title: "Days",
								interval: 156,
								labelFormatter: labelGenerator,
								labelFontSize: 20,
								titleFontSize: 25,
							},
							axisY: {
								title: "Number of people",
								includeZero: false,

								labelFontSize: 20,
								titleFontSize: 25,
								minimum: -4.0
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
								}

							]
						});
						chart.render();

						function labelGenerator(e) {
							return parseInt(e.value / 156);
						}
						
					},250);
				}
			}),
			$.getJSON("showParameters.php?xmlFile="+covidxml, function(data)
			{
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
			});
		}



    </script>
</html>