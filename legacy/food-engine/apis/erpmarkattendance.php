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
	Brief: To mark employee attendance on a given date
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
	
//Validations
if(!isset($_POST['date'])){
	$output = array(
			"status" => false,
			"error" => "Date is Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}

$req_date = $_POST['date'];

if(!isset($_POST['info'])){
	$output = array(
			"status" => false,
			"error" => "Attendance Object is Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}

$attendance_obj = $_POST['info'];

/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect.php';

$status = false;
$error = "Something went wrong";

foreach ($attendance_obj as $attendanceBlock) {
	foreach ($attendanceBlock['people'] as $person) {
		
		//mysql_query("UPDATE `erp_attendance` SET `staff`='{$person['employeeID']}',`branch`='{$branch}', `date`='{$req_date}',`status`='{$person['attendance']}' WHERE `staff`='{$person['employeeID']}' AND `date`='{$req_date}'"))
		
		mysql_query("INSERT INTO erp_attendance (staff, branch, date, status) VALUES('{$person['employeeID']}', '{$branch}', '{$req_date}','{$person['attendance']}') ON DUPLICATE KEY UPDATE status='{$person['attendance']}'");
		
		
		
	   
	}   
}

$output = array(
	"status" => true,
	"error" => ""
);

die(json_encode($output));


?>