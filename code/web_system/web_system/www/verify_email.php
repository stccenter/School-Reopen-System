<?php
session_start();
require_once __DIR__ . './config/dataSource.php';
$ds = new DataSource();

if (isset($_GET['token'])) {
    $_SESSION['verified'] = false;
    $token = $_GET['token'];
    $sql = "select * from users WHERE token=? and verified=0";
    $paramType = 's';
    $paramValue = array(
        $token
      );

    $result = $ds->getRecordCount($sql,$paramType,$paramValue);
    if ($result > 0) {
        $updateQuery = "UPDATE users SET verified=1 WHERE token=?";
        $paramType = 's';
        $paramValue = array(
              $token
          );
        $updateResult = $ds->update($updateQuery,$paramType,$paramValue);
        if($updateResult == true){
            $_SESSION['message'] = "Your email address has been verified successfully, please continue login.";
            $_SESSION['type'] = 'alert-success';
            $_SESSION['verified'] = true;
            header('location: login.php');
        }
    } else {
        $_SESSION['message'] = "Email already verified, please continue login.";
        $_SESSION['type'] = 'alert-success';
        $_SESSION['verified'] = true;
        header('location: login.php');
    }
} else {
    echo "No token provided!";
}