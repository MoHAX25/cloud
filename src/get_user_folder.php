<?php

require_once("dbAPI.php");
$api = new API();
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
if(isset($_POST['username'])) {
	if($_POST['username'] != '') {
		$status = $api->get_user_folder($_POST['username'], $_POST['sel_dir']);
		echo json_encode($status);
	}
} else {
	echo 'something was incorrect';
}

