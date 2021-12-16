
<!DOCTYPE html>
<html>
<head>

</head>
<body>
<form action="dummyupload.php" id="gisForm">
        <div class="row" style="margin-top: 10px;">
                  <div class="col">
					<input type="file" class="form-control" id="gisRoads" name="gisRoads" placeholder="" value="" >
					<input type="hidden" class="form-control" id="gisRoadsInput" name="gisRoadsInput" value="gisRoadsValue" >
					<p id="gisRoadsMsg"></p>
				  </div>
				  <div class="col">
                     <button type="submit"  value="roadfileUpload">GIS-roads Upload</button>
                  </div>
	
		</div>
	</form>
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="../assets/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.esm.js"></script>
  <script nomodule src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ionic/core/css/ionic.bundle.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
  <!--Validation js-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>
   <script>
      $(document).ready(function() {
		 //File upload logic
	$('#gisForm').submit(function (e){
		

    });  
	  })
  </script>
</body>
</html>
