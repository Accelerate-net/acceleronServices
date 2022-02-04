<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if(!isset($_POST['brief'])){
	$output = array(
		"status" => false,
		"error" => "Brief can not be empty"
	);
	die(json_encode($output));
}

if(!isset($_POST['title'])){
	$output = array(
		"status" => false,
		"error" => "Title can not be empty"
	);
	die(json_encode($output));
}

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken, true);

//Expiry Validation
date_default_timezone_set('Asia/Calcutta');
$dateStamp = date_create($tokenid['date']);
$today = date_create(date("Y-m-j"));
$interval = date_diff($dateStamp, $today);
$interval = $interval->format('%a');

if($interval > $tokenExpiryDays){
	$output = array(
		"status" => false,
		"error" => "Expired Token"
	);
	die(json_encode($output));
}


//Check if the token is tampered
if($tokenid['outlet']){
	$outlet = $tokenid['outlet'];
	$admin_created = $tokenid['mobile'];	
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


date_default_timezone_set('Asia/Calcutta');
$date = date("g:i a j F, Y");
$dateStamp = date("Ymd");

$post_msg = $_POST['brief'];
$post_title = $_POST['title'];

	mysql_query("INSERT INTO `z_pushmessenger`(`title`, `content`, `isImg`, `url`, `user`, `date`, `dateStamp`) VALUES ('{$post_title}', '{$post_msg}', 0, '', '{$admin_created}', '{$date}', '{$dateStamp}')");

	
	//Upload Photo
	if(isset($_POST['url']) && $_POST['url'] != "")
	{
		$id_check = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `z_pushmessenger` WHERE 1 ORDER BY `id` DESC LIMIT 1"));	   
		$data = $_POST['url'];
	
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		$data = base64_decode($data);
		
		file_put_contents("images/push/".$id_check['id'].".jpg", $data);
		if(file_exists("images/push/".$id_check['id'].".jpg")){
			$my_url = "https://zaitoon.online/services/images/push/".$id_check['id'].".jpg";
			mysql_query("UPDATE `z_pushmessenger` SET `isImg`=1, `url`='{$my_url}' WHERE `id`='{$id_check['id']}'");
		}
	}
	
	
	/*
		
		INJECT ONE SIGNAL CODE HERE.
		
	*/	
	
	
			
	//FINAL RESPONSE
	
	$output = array(
		"status" => true,
		"error" => ""
	);
	
	die(json_encode($output));	
?>