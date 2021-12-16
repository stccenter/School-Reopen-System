<?php

require('config.php');

if(isset($_POST['value']) && isset($_POST['id'])){
   $value = mysqli_real_escape_string($link,$_POST['value']);
   $editid = mysqli_real_escape_string($link,$_POST['id']);

   $query = "UPDATE users SET userlevel='".$value."' WHERE id=".$editid;
   mysqli_query($link,$query);

   echo 1;
}else{
   echo 0;
}
exit;