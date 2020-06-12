<?php

require_once("dbAPI.php");
$api = new API();
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
if(isset($_POST['folder'])) {
	$status = $api->create_folder($_POST['folder'],$_POST['username']);
	echo json_encode($status);
} else {
	echo 'some place is empty';
}

