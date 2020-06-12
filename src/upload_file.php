<?php
/*$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);*/
$uploaddir = "../root/".$_POST['username'].$_POST['path'];
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
   	echo "good";
} else {
    echo "File not loaded, just back and try again";
}
