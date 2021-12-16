<?php
require('config.php');
$post_ids = $_POST['post_id'];

foreach($post_ids as $id){ 
    // Delete record 
    $query = "DELETE FROM feedback WHERE id=".$id; 
    mysqli_query($link,$query);
  }
  echo 1;
?>