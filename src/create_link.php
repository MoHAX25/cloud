<?php

require_once("dbAPI.php");
$api = new API();
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);
$path = "../root/".$_POST['username'].$_POST['path'].$_POST['userfile'];
$self_path = substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/"));
$hash = md5(microtime());
$status = $api->create_link($path, $hash, $_POST['username'], $_POST['userfile']);
if($status['status'] == 0) {
	$status['link'] = "http://".$_SERVER['HTTP_HOST'].$self_path."/sharing.php?hash=".$hash;
}
echo json_encode($status);