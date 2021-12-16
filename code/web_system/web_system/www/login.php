<?php
// Initialize the session
session_start();
 ini_set('date.timezone', 'America/New_York');
// Check if the user is already logged in, if yes then redirect him to index page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $userrole = $userlevel = $password = $username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
        $_SESSION['message'] = $username_err;
        $_SESSION['type'] = 'alert-danger';
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
        $_SESSION['message'] = $password_err;
        $_SESSION['type'] = 'alert-danger';
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT u.id, username, password, u.userlevel, ul.userlevel,role,verified FROM users u inner join userlevel ul on ul.id = u.userlevel WHERE role='user' and (u.username = ? or u.email = ?)";
  
        
        if($stmt = mysqli_prepare($link, $sql)){

            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_username);
            $param_username = $username;
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){                    

                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $ulid,  $userlevel, $userrole,$verified);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            if($verified){
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username; 
                                $sessionDate = date("YmdHi");
                                $_SESSION["sessionDate"] = $sessionDate; 
                                $_SESSION["levelid"] = $ulid;
                                $_SESSION["level"] = $userlevel;
                                $_SESSION["userrole"] = $userrole;
                                if(isset($_POST["ch1"]))
                                {
                                    setcookie("uname",$username,time()+1000);
                                    setcookie("pass",$password,time()+1000);
                                }
                                header("location: index.php");
                            }
                            else{
                                $password_err = "Your email address has not verified, please verify email.";
                                $_SESSION['message'] = $password_err;
                                $_SESSION['type'] = 'alert-danger';
                            }
                        } else{
                            $password_err = "The password you entered was not valid.";
                            $_SESSION['message'] = $password_err;
                            $_SESSION['type'] = 'alert-danger';
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found.";
                    $_SESSION['message'] = $username_err;
                    $_SESSION['type'] = 'alert-danger';
                }
            } else{
                $_SESSION['message'] = "Oops! Something went wrong. Please try again later.";
                $_SESSION['type'] = 'alert-danger';
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
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
    </style>
</head>

<body>
<?php if(!empty($loginStatus)){?>
				<div class="alert alert-danger alert-dismissible fade show" style="margin:50px;"><strong>Error! </strong><?php echo $loginStatus;?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
<?php }?>

<?php if(isset($_SESSION['message'])): ?>
        <div class="alert <?php echo $_SESSION['type'] ?> alert-dismissible fade show" style="margin:50px;">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button></div>
        </div>
<?php endif;?>
    <?php
            unset($_SESSION['message']);
            unset($_SESSION['type']);
    ?>
    <div class="container">
        <div class="wrapper-login">
            <form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1 class="h3 mb-4 font-weight-normal">Please Sign In</h1>

                <label for="username" class="sr-only">User name</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="User name"
                    value="<?php if (isset($_COOKIE['uname'])){echo $_COOKIE["uname"];} ?>" required autofocus>

                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                    value="<?php if (isset($_COOKIE['pass'])){echo $_COOKIE["pass"];}  ?>" required>

                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" name="ch1" value="remember-me"> Remember me
                        <p><a href="resetpassword.php">Forgot your password</a>?</p>
                        <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">SIGN IN</button>
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