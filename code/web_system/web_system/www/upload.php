<?php

$uploaddir = '/var/www/html/uploads/';
$uploadfile = $uploaddir . basename($_FILES['uploadFile']['name']);

echo '<pre>';
if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}
header('Location: index.php);
/*
$uploaddir = '/ABM/uploads/';

echo '<pre>';
$uploadFile = $_FILES['uploadFile'];
print_r($uploadFile);
if(!empty($uploadFile))
{
    $file_desc = reArrayFiles($uploadFile);
    print_r($file_desc);
   
    foreach($file_desc as $val)
    {
        $uploadfile = $uploaddir . basename($val['uploadFile']['name']);
        move_uploaded_file($val['uploadFile']['tmp_name'], $uploadfile);
    }
}

function reArrayFiles($file)
{
    $file_ary = array();
    $file_count = count($file['name']);
    $file_key = array_keys($file);
   
    for($i=0;$i<$file_count;$i++)
    {
        foreach($file_key as $val)
        {
            $file_ary[$i][$val] = $file[$val][$i];
        }
    }
    return $file_ary;
}
*/
?>