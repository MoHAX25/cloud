<?php

require_once("dbAPI.php");
$api = new API();
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
if(isset($_POST['username']) && isset($_POST['password'])) {
	if($_POST['username'] != '' && $_POST['password']!= '') {
		$status = $api->sign_up($_POST['username'],md5($_POST['password']));
		echo json_encode($status);
	}
} else {
	echo 'some place is empty';
}

