<?php
require_once("dbAPI.php");
$api = new API();
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$file = "../root/".$_POST['username'].$_POST['path'].$_POST['userfile'];

if(is_dir($file)) {
	rmdir($file);
} else {
	if (unlink($file)) {
	    $status = $api->delete_link($file);
	    $status['deleted'] = true;
   		echo $status;
	} else {
	    echo $status;
	}

}

