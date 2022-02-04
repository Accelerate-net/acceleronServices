<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);
$_POST = json_decode(file_get_contents('php://input'), true);

/*******************
  0. DOCUMENTATION
********************/
/*
	Version: 1.0
	Admin API: NO
	Brief: To fetch employee' list with attendance
*/


/**********************************
  1.1 AUTHENTICATION STANDARD PART
***********************************/

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';


//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
			"status" => false,
			"error" => "Access Token Missing",
			"errorCode" => 103,
			"response" => ""
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
			"error" => "Login Expired",
			"errorCode" => 401,
			"response" => ""
	);
	die(json_encode($output));
}

/**********************************
  1.2 AUTHENTICATION CUSTOM PART
***********************************/
//Check if the token is tampered
if($tokenid['outlet']){
	$branch = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered",
		"errorCode" => 401
	);
	die(json_encode($output));
}
	

/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect.php';

$status = true;
$error = "";


$todayDate = date("Ymj");
$info = mysql_fetch_assoc(mysql_query("SELECT COUNT(`staff`) as total FROM `erp_attendance` WHERE `date` = '{$todayDate}' AND (`status` = 2 OR `status` = 1) AND `branch` = '{$branch}'"));
$infoAbsent = mysql_fetch_assoc(mysql_query("SELECT COUNT(`staff`) as total FROM `erp_attendance` WHERE `date` = '{$todayDate}' AND `status` = 5 AND `branch` = '{$branch}'"));
$totalInfo = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `erp_people` WHERE `currentBranch` = '{$branch}' AND `isActive` = 1"));



$output = array(
	"status" => $status,
	"error" => $error,
	"figure_total_employees" => $totalInfo['total'],
	"figure_total_present" => $info['total'],
	"figure_total_absent" => $infoAbsent['total']
);

die(json_encode($output));


?>