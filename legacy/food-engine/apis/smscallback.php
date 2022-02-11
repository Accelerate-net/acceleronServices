<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';


if($_POST['status'] == 'D'){
	$post_status = 1;
}
else{
	$post_status = 5;
}

date_default_timezone_set('Asia/Calcutta');
$timestamp = date("g:i a j F, Y");

$post_user = substr($_POST['number'], 2);

mysql_query("UPDATE `z_smsdeliveryreports` SET `status`=$post_status, `finshedTime`='{$timestamp}' WHERE `user`='{$post_user}' AND `id`='{$_POST['customID']}'");
mysql_query("UPDATE  `sms_limt` SET  `used` =  `used`+1 WHERE 1");
	
?>