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
	Brief: To fetch employee' list with attendance on a given date
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

/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect.php';

$status = false;
$error = "No designations found.";

$role_query = mysql_query("SELECT * FROM `erp_roles` WHERE 1");
while($role_info = mysql_fetch_assoc($role_query)){

	$status = true;
	$error = "";
	
	$roleWise = [];
	
	$sub_query = mysql_query("SELECT * FROM `erp_people` WHERE `currentBranch` = '{$branch}' AND `role` = '{$role_info['roleCode']}'");
	while($person_info = mysql_fetch_assoc($sub_query)){	
	
		$info = mysql_fetch_assoc(mysql_query("SELECT `status` FROM `erp_attendance` WHERE `date`='{$req_date}' AND `staff` = '{$person_info['id']}'"));
		
		$roleWise[] = array(
				"employeeID"=>$person_info['id'],
				"fName"=>$person_info['fName'],
				"lName"=>$person_info['lName'],
				"designation"=>$person_info['role'],
				"contact"=>$person_info['contact'],
				"photoURL"=>$person_info['photoUrl'],
				"attendance" => $info['status'] ? $info['status'] : 0
		);	
	
	}
	
	$branchWise[] = array(
		"designation" => $role_info['roleCode'],
		"people" => $roleWise
	);
}



$output = array(
	"status" => $status,
	"error" => $error,
	"errorCode" => "",
	"response" => $branchWise
);

die(json_encode($output));


?>