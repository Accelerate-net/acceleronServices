<?php

session_start();

include 'Qassim_HTTP.php';
include 'config.php';

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';

if(!isset($_GET['code'])){
	die('Error. Incorrect Parameters in the request. <a href="https://zaitoon.online/manager">Go back to Manager Portal</a>');
}

$header = array( "Content-Type: application/x-www-form-urlencoded" );

$data = http_build_query(
			array(
				'code' => str_replace("#", null, $_GET['code']),
				'client_id' => $client_id,
				'client_secret' => $client_secret,
				'redirect_uri' => $redirect_uri,
				'grant_type' => 'authorization_code'
			)
		);

$url = "https://www.googleapis.com/oauth2/v4/token"; 

$result = Qassim_HTTP(1, $url, $header, $data);
$access_token = $result['access_token']; // Get access token

$info = Qassim_HTTP(0, "https://www.googleapis.com/gmail/v1/users/me/profile", array("Authorization: Bearer $access_token"), 0); // Get email address


if(!empty($result['error'])){ // if have some problems, will be logout
	die('Error. Something went wrong. <a href="https://zaitoon.online/manager">Go back to Manager Portal</a>');
}
else{ // if get access token, will be redirect to index.php
	date_default_timezone_set('Asia/Calcutta');
	$today = date("Y-m-d");
	mysql_query("UPDATE `z_gtoken` SET `gmailToken`='{$access_token}', `date`='{$today}' WHERE `code` = 'ZAITOON'");
	header("location: https://zaitoon.online/manager/helpline-inbox.html");
}

?>