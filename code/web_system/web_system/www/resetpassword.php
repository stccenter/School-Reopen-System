<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'C:\wamp64\www\composer\vendor\autoload.php';
ini_set('date.timezone', 'America/New_York');
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

// Include config file
require_once __DIR__ . './config/dataSource.php';
$ds = new DataSource();

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email=$_POST['email'];
    $query = "SELECT email,username FROM users WHERE email=?";
    $paramType = 's';
    $paramValue = array(
          $email
      );
    $selectResult = $ds->select($query,$paramType,$paramValue);
    if(!empty($selectResult)){
        // generate a unique random token of length 100
        $token = bin2hex(random_bytes(50));
        $sql = "INSERT INTO password_reset(email, token,tdate) VALUES (?,?,NOW())";
        $paramType = 'ss';
        $paramValue = array(
            $email,
            $token
        );
        $insertResult = $ds->insert($sql,$paramType,$paramValue);
        if($insertResult == true){

            $mail = new PHPMailer(true);
            $mail->isSMTP();
    
            $mail->Host = 'smtp.office365.com';
            $mail->Port       = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth   = true;
            $mail->Username = 'stc@gmu.edu';
            $mail->Password = '*****';
            $mail->SetFrom('stc@gmu.edu', 'STC Center ABM');
            $mail->addAddress($email , $selectResult[0]["username"]);
            $mail->IsHTML(true);
    
            $mail->Subject = 'Password Reset';
            $body = '<!DOCTYPE html>
            <html lang="en">
        
            <head>
              <meta charset="UTF-8">
              <title>Test mail</title>
              <style>
                .wrapper {
                  padding: 20px;
                  color: #444;
                  font-size: 1.3em;
                }
                a {
                  background: #592f80;
                  text-decoration: none;
                  padding: 8px 15px;
                  border-radius: 5px;
                  color: #fff;
                }
              </style>
            </head>
        
            <body>
              <div class="wrapper">
                <p>Please click below link to reset password</p>
                <a href="https://abm.stcenter.net/passwordchange.php?token=' . $token . '">Password Reset</a>
              </div>
            </body>
        
            </html>';
    
            $mail->Body    = $body;
    
            if(!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                $_SESSION['verified'] = false;
                $_SESSION['message'] = "Password reset email send, please check your email";
                $_SESSION['type'] = 'alert-success';
                header("location: login.php");
            }
        }
    }
    else{
        $error_msg = "Sorry, no user exists on our system with that email";
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

    .form-signin {
        width: 100%;
        max-width: 330px;
        padding: 15px;
        margin: auto;
    }

    .form-signin .checkbox {
        font-weight: 400;
    }

    .form-signin .form-control {
        position: relative;
        box-sizing: border-box;
        height: auto;
        padding: 10px;
        font-size: 16px;
    }

    .form-signin .form-control:focus {
        z-index: 2;
    }

    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .error-msg {
	padding-top: 10px;
	color: #D8000C;
	text-align: center;
}


    </style>
</head>
<?php if(!empty($error_msg)){?>
    <div class="alert alert-danger alert-dismissible fade show" style="margin:50px;">
        <strong>Error!</strong> <?php echo $error_msg; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php }?>
<body>
    <div class="container">
        <div class="wrapper-login">
        <form class="login-form" action="resetpassword.php" method="post">
            <h1 class="h3 mb-4 font-weight-normal">Password Recovery</h1>
                <div class="form-group">
                <label for="email" class="sr-only">Please enter email address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Please enter email address" required autofocus>
                </div>
                <div class="form-group">
                    <button class="btn btn-lg btn-primary btn-block" name="reset-password" type="submit">Submit</button>
                    <p>Login <a href="login.php">here</a>.</p>
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