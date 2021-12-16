<!DOCTYPE HTML>
<html>
<head>
<script type = "text/javascript" >
window.onload = function () {
	var dataPoints = [];
	var chart = new CanvasJS.Chart("chartContainer", {
			title : {
				text : "Dynamic Data"
			},
			data : [{
					type : "line",
					dataPoints : dataPoints
				}
			]
		});

	chart.render();
	var xVal = dataPoints.length + 1;
	var yVal = 0;
	var updateChart = function () {

		yVal = yVal + Math.round(5 + Math.random() * (-5 - 5));
  
				console.log(yVal);
		dataPoints.push({y : yVal});
		xVal++;
      	
		chart.render();    
		
	};

	// update chart every second
	setInterval(function(){updateChart()}, 1000);
}	
</script>
<script type="text/javascript" src="../assets/dist/js/canvasjs.min.js"></script>
</head>
<body>
<div id = "chartContainer" style = "height: 300px; width: 100%;" />
</body>
</html>