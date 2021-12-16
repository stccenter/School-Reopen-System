<?php
session_start();
ini_set('date.timezone', 'America/New_York');

// Include config file
require_once __DIR__ . './config/dataSource.php';
$ds = new DataSource();
$password = $confirm_password =$error_msg = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $password = trim($_POST["password"]);
    $token = $_SESSION['token'];
    $confirm_password = trim($_POST["confirm_password"]);

    if ($password !== $confirm_password) $_SESSION['message'] = "Password do not match";

    if(!empty($_SESSION['message'])){
        header('location: passwordchange.php?token='.$token); 
        exit(0);
    }
    else 
    {
        $sql = "SELECT * FROM password_reset WHERE token=? and valid=true  LIMIT 1";
        $paramType = 's';
        $paramValue = array(
            $token
          );
        $result = $ds->select($sql,$paramType,$paramValue);
        if (!empty($result)) {
            $selectsql = "SELECT * FROM users WHERE email=? and verified=true LIMIT 1";
            $paramType = 's';
            $paramValue = array(
                $result[0]["email"]
            );
            
            $selectresult = $ds->select($selectsql,$paramType,$paramValue);
            if (!empty($selectresult)) {
                $new_password = password_hash($password, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE users SET password=? WHERE email=?";
                $paramType = 'ss';
			    $paramValue = array(
                    $new_password,
                    $result[0]["email"]
                );
                $updateResult = $ds->update($updateQuery,$paramType,$paramValue);
                $updateReset = "UPDATE password_reset SET valid=false WHERE email=?";
			    $paramType = 's';
			    $paramValue = array(
                    $result[0]["email"]
                );
                $updateResult = $ds->update($updateReset,$paramType,$paramValue);

                if($updateResult == true && $updateResult == true){
                    $_SESSION['message'] = "Password reset successfully, please continue login.";
                    $_SESSION['type'] = 'alert-success';
                    header('location: login.php');
                    exit(0);
                }
                else{
                    $_SESSION['message'] = "Database error occured, please try again.";
                    $_SESSION['type'] = 'alert-danger';
                    header('location: login.php'); 
                    exit(0);
                }
            }
            else
            {
                $_SESSION['message'] = "Please verify email address before password reset.";
                $_SESSION['type'] = 'alert-danger';
                header('location: login.php');
                exit(0);
            }
        }
    }
}
else if(isset($_GET['token']))
{
    $_SESSION['token'] = $_GET['token'];
    $sql = "SELECT * FROM password_reset WHERE token=? and valid=true  LIMIT 1";
    $paramType = 's';
    $paramValue = array(
        $_GET['token']
    );
    $result = $ds->select($sql,$paramType,$paramValue);
    if (empty($result)) {
        $_SESSION['message'] = "Invalid token";
        $_SESSION['type'] = 'alert-danger';
        header('location: login.php');
        exit(0);
    }
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
    <title>Sign In</title>

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


    .container {
        width: 100%;
        min-height: 100vh;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        padding: 15px;
    }

    .wrapper-login {
        margin: auto auto;
        padding: 50px;
        display: inline-block;
        width: 500px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 20px 0 rgba(0, 0, 0, .1);
        -moz-box-shadow: 0 3px 20px 0 rgba(0, 0, 0, .1);
        -webkit-box-shadow: 0 3px 20px 0 rgba(0, 0, 0, .1);
        -o-box-shadow: 0 3px 20px 0 rgba(0, 0, 0, .1);
        -ms-box-shadow: 0 3px 20px 0 rgba(0, 0, 0, .1)
    }

    .error-msg {
	padding-top: 10px;
	color: #D8000C;
	text-align: center;
}


    </style>
</head>
<?php if(isset($_SESSION['message']) ): ?>
        <div class="alert alert-danger alert-dismissible fade show" style="margin:50px;">
        <strong>Error! </strong><?php echo $_SESSION['message']; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button></div>
        </div>
<?php endif;?>
    <?php
            unset($_SESSION['message']);
    ?>

<body>
    <div class="container">
        <div class="wrapper-login">
        <form class="login-form" action="passwordchange.php" method="post">
                <h2 class="form-title">Password reset</h2>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <button class="btn btn-lg btn-primary btn-block" name="new-password" type="submit">Submit</button>
                </div>
	    </form>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="../assets/dist/js/bootstrap.min.js"></script>
    <!--Validation js-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script type="text/javascript">

    </script>

</body>
</html>