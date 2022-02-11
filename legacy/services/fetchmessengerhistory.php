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

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['type'])){
	$output = array(
		"status" => false,
		"error" => "Content Type is missing"
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
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');
$today = date("Y-m-d");

$status = false;
$error = "No contents found";

$limiter = "";
if(isset($_POST['page'])){
	$range = $_POST['page'] * 10;
	$limiter = " LIMIT  {$range}, 10";	
}

$co_type = $_POST['type'];

if($co_type == 'sms'){
	
	$list = mysql_query("SELECT * FROM `z_smsmessenger` WHERE 1".$limiter);
	
	while($combo = mysql_fetch_assoc($list)){
	
		$username = "Unknown Admin";
		$user_check = mysql_fetch_assoc(mysql_query("SELECT `name`, `branch` FROM `z_roles` WHERE `code`='{$combo['user']}'"));
		if($user_check['name'] != ""){
			$username = $user_check['name']." (".$user_check['branch'].")";
		}

				$output[] = array(
					"code" => $combo['id'],
					"name" => $combo['title'],
					"brief" => $combo['content'],
					"date" => $combo['date'],
					"user" => $username,
					"target" => $combo['target'],
					"count" => $combo['count']						
				); 
				
				$status = true;
				$error = "";															
	}
}
else if($co_type == 'push'){
	
	$list = mysql_query("SELECT * FROM `z_pushmessenger` WHERE 1".$limiter);
	
	while($promo = mysql_fetch_assoc($list)){
	
		$username = "Unknown Admin";
		$user_check = mysql_fetch_assoc(mysql_query("SELECT `name`, `branch` FROM `z_roles` WHERE `code`='{$promo['user']}'"));
		if($user_check['name'] != ""){
			$username = $user_check['name']." (".$user_check['branch'].")";
		}

				$output[] = array(
					"code" => $promo['id'],
					"name" => $promo['title'],
					"brief" => $promo['content'],
					"isImg" => $promo['isImg'] == 1? true: false,
					"url" => $promo['url'],
					"date" => $promo['date'],
					"user" => $username					
				); 
				
				$status = true;
				$error = "";															
	}

}

$figure_sms_total = 0;
$figure_sms = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) AS total FROM `z_smsmessenger` WHERE 1"));
if($figure_sms['total'] != "")
{
	$figure_sms_total = $figure_sms['total'];
}

$figure_push_total = 0;
$figure_push = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) AS total FROM `z_pushmessenger` WHERE 1"));
if($figure_push['total'] != "")
{
	$figure_push_total = $figure_push['total'];
}

$sms_last = "";
$sms_last_check = mysql_fetch_assoc(mysql_query("SELECT `date` FROM `z_smsmessenger` WHERE 1 ORDER BY `id` LIMIT 1"));
if($sms_last_check['date'] != ""){
	$sms_last = $sms_last_check['date'];
}

$push_last = "";
$push_last_check = mysql_fetch_assoc(mysql_query("SELECT `date` FROM `z_pushmessenger` WHERE 1 ORDER BY `id` LIMIT 1"));
if($push_last_check['date'] != ""){
	$push_last = $push_last_check['date'];
}

$result = array(
	'status' => true,
	'error' => "",
	'response' => $output,
	'totalSMS' => $figure_sms_total,
	'totalPush' => $figure_push_total,
	'smsLast' => $sms_last,
	'pushLast' => $push_last	
);

echo json_encode($result);
		
?>