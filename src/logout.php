<?php
require_once("dbAPI.php");
$api = new API();
$all = $api->logout();
echo json_encode($all);
