<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'C:\wamp64\www\composer\vendor\autoload.php';

$errors = array(); 
// Include config file
require_once "config.php";
require_once __DIR__ . './config/dataSource.php';
$ds = new DataSource();


$username = $password =$email= $confirm_password = $message = "";
$register_err = $password_err = $confirm_password_err =$username_err =$email_err="";

 if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $sql = "select * from users WHERE username=?";
    $paramType = 's';
    $paramValue = array(
        $username
    );
    $result = $ds->getRecordCount($sql,$paramType,$paramValue);

    if ($result > 0) {
        array_push($errors, "Username already exists."); 
    }

    $sql = "select * from users WHERE email=?";
    $paramType = 's';
    $paramValue = array(
        $email
    );
    $result = $ds->getRecordCount($sql,$paramType,$paramValue);
    if ($result > 0) {
        array_push($errors, "Email already exists."); 
    }

    if(strlen(trim($_POST["password"])) < 6){
        $register_err = "Password must have atleast 6 characters.";
        array_push($errors, "Password must have atleast 6 characters."); 
    } else{
        $password = trim($_POST["password"]);
    }
    
    $confirm_password = trim($_POST["confirm_password"]);

    if($password != $confirm_password){
        $register_err = "Password did not match.";
        array_push($errors, "Password did not match."); 
    }




    if(count($errors) == 0)
    {
        $sql = "SELECT id FROM users WHERE username = ?";
        
        // Set parameters
        $email = trim($_POST['email']);
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
        $token = bin2hex(random_bytes(50)); // generate unique token

        $inserQuery = "INSERT INTO users (username,email, password,token) VALUES (?,?,?,?)";
        $paramType = 'ssss';
        $paramValue = array(
            $param_username,
            $email,
            $param_password,
            $token
        );

        $insertResult = $ds->insert($inserQuery,$paramType,$paramValue);

        if($insertResult == true)
        {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
        
            $mail->Host = 'smtp.office365.com';
            $mail->Port       = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth   = true;
            $mail->Username = 'stc@gmu.edu';
            $mail->Password = '****';
            $mail->SetFrom('stc@gmu.edu', 'STC Center ABM');
            $mail->addAddress($email , $username);
            $mail->IsHTML(true);
            $mail->Subject = 'Email Verification';
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
                      background: #2c3a86;
                      text-decoration: none;
                      padding: 8px 15px;
                      border-radius: 5px;
                      color: white;
                    }
                  </style>
                </head>
            
                <body>
                  <div class="wrapper">
                    <p>Thank you for signing up for the S.M.A.R.T. system. Please click on the below link to verify your account:</p>
                    <a href="https://abm.stcenter.net/verify_email.php?token=' . $token . '">Verify Email</a>
                  </div>
                </body>
            
            </html>';
        
            $mail->Body    = $body;
            if(!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                $_SESSION['verified'] = false;
                $_SESSION['message'] = "Your account has been created. Please activate account by clicking link that has been sent to your email.";
                $_SESSION['type'] = 'alert-success';
                header("location: login.php");
            }

        }

    }
    else{
        foreach($errors as $error) {
            $message = $message . $error ."<br/>";
        }
        $_SESSION['message'] = $message;
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>Sign Up</title>

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

    body {
        height: 100%;
        background-color: #f5f5f5;
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

    .wrapper {
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

    </style>
</head>

<?php if (! empty($message)) {?>	
        <div class="alert alert-danger alert-dismissible fade show" style="margin:50px;">
        <strong>Error!</strong> <?php echo $_SESSION['message']; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
<?php
unset($_SESSION['message']);
}
?>

<body>
<div class="container">
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
            </div>    
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
            </div> 
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
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
    <script type="text/javascript"></script>
</body>
</html>