<?php

require_once("dbAPI.php");
$api = new API();
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$status = $api->shared_checker($_POST['username']);
echo json_decode($status);