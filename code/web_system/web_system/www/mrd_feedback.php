<?php
error_reporting(E_ALL);
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$syserror = $commentErr  = "";
$system = $name = $email = $success = $comment ="";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

        
     
     if (empty($_POST["inputComment"])) {
        $commentErr = "Please enter comments";
     }else {
        $param_comments = trim($_POST["inputComment"]);
     }
    $param_system = "2";
    $param_name = trim($_POST["inputName"]);
    $param_email = trim($_POST["inputEmail"]);


        
        // Prepare an insert statement
        $sql = "INSERT INTO feedback (appname, gname, email, comments) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_system, $param_name, $param_email, $param_comments);
            

            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                $param_name ="";
                $param_comments = "";
                $param_email ="";
                $success = "Thank you for any comments, suggestions, problems, questions";

            } 

            // Close statement
            mysqli_stmt_close($stmt);
        }

    
    // Close connection
    mysqli_close($link);
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
    <title>Feedback</title>
    <!-- Bootstrap core CSS -->
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

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


    </style>

</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

        <a class="navbar-brand" href="#">
            <img src="iucrc_logo.jpg" width="30" height="30" alt="">
        </a>
        <a class="navbar-brand">COVID-19 Medical Resource Deficiencies Dashboard Feedback System</a>

    </nav>



<div class="px-5">
    <h2 class="mt-5 pb-5"></h1>
    <!-- <h2 class="mt-5 pb-5">Thank you for your feedback</h1> -->
    <form id="form-feedback" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <!-- <div class="form-group row  <?php echo (!empty($syserror)) ? 'has-error' : ''; ?>">
            <label for="inputSystem" class="col-sm-2 col-form-label">System</label>
            <div class="col-sm-3">
            <span>
            <p>School reopen simulation</p>
            </span>
            <select class="form-control" id="inputSystem" name="inputSystem">
                <option value="">Please select a system</option>
                <option value="1" <?php if (isset($_POST['inputSystem']) && ($_POST['inputSystem'])=="1") echo "selected";?>>School reopen simulation</option>
                <option value="2" <?php if (isset($_POST['inputSystem']) && ($_POST['inputSystem'])=="2") echo "selected";?>>Medical Resource Deficiencies Dashboard</option>
                <option value="3" <?php if (isset($_POST['inputSystem']) && ($_POST['inputSystem'])=="3") echo "selected";?>>Health Risk prediction</option>
            </select>            
            <span class="help-block"><?php echo $syserror; ?></span>
            </div>
        </div> -->
        <div class="form-group row">
            <label for="inputName" class="col-sm-2 col-form-label">Name (optional)</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="inputName" name="inputName" placeholder="" value="<?PHP if(isset($param_name)) echo htmlspecialchars($param_name); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEmail" class="col-sm-2 col-form-label">Email (optional)</label>
            <div class="col-sm-3">
                <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="" value="<?PHP if(isset($param_email)) echo htmlspecialchars($param_email); ?>">
            </div>
        </div>
        <div class="form-group row  <?php echo (!empty($commentErr)) ? 'has-error' : ''; ?>">
            <label for="inputComment" class="col-sm-2 col-form-label">Feedback</label>
            <div class="col-sm-3">
                <textarea maxlength="1000" rows="10" class="form-control" id="inputComment" name="inputComment" placeholder=""><?PHP if(isset($param_comments)) echo htmlspecialchars($param_comments); ?></textarea>
                <span>Limit to 1000 characters.</span>
                <span class="help-block"><?php echo $commentErr; ?></span>
            </div>
        </div>


        <div class="form-group row">
        <div class="col-sm-3">
                <button  class="btn btn-primary"   style="align:center;">Submit</button>
                <!-- <button id="clear" type="button" class="btn btn-primary" onclick="formClear(event)" type="reset" >Clear</button> -->
            </div>
        </div>
    </form>
    <span class="help-block"><?php echo $success; ?></span>
</div>



    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="../assets/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script type="text/javascript"></script>

    <script>



    function formClear(e) {
        $("#form-feedback").get(0).reset();
    }
       
    $(document).ready(function() { 
        $('#inputSystem').change(function() {
            $(this).closest('.form-group').find('span.help-block').remove();
        });
    });
    </script>

</body>


</html>