<?php
require_once("dbAPI.php");
$api = new API();
$ans = $api->session_checker();
echo json_encode($ans);
