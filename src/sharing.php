<?php
require_once("dbAPI.php");
$api = new API();

$hash = $_GET['hash'];
$file = $api->get_file_by_link($hash)[0];
$filename = $file[3];
var_dump($filename);
if(strlen($hash) != 32) {
 exit("Не правильныая ссылка");
}
header ("Content-Type: application/octet-stream");
header ("Accept-Ranges: bytes");
header ("Content-Disposition: attachment; filename=".$filename);  
readfile($file[1]);