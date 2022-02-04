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
	Brief: To fetch employee' list
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
	

//REQUIRED PARAMETERS

date_default_timezone_set('Asia/Kolkata');
$date = date('m/d/Y h:i:s a', time());

//Validations

if(!isset($_POST['key'])){
	$output = array(
			"status" => false,
			"error" => "Search Key is Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}


/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect.php';

$key = $_POST['key'];
$searchKeys = explode("-", $key);

$name_query = mysql_query("SELECT * FROM `erp_people` WHERE (`fName` LIKE '%{$key}%' || `lName` LIKE '%{$key}%' || CONCAT(`fName`, ' ', `lName`) LIKE '%{$key}%')");
$roll_query = mysql_query("SELECT * FROM `erp_people` WHERE `id` = '{$key}'");
$mobile_query = mysql_query("SELECT * FROM `erp_people` WHERE `contact` = '{$key}'");
$role_query = mysql_query("SELECT * FROM `erp_people` WHERE `role` = '{$key}'");
$branch_query = mysql_query("SELECT * FROM `erp_people` WHERE `currentBranch` = '{$key}'");

$exists = false;

while($name_result = mysql_fetch_assoc($name_query)){

		$exists = true;

		$response[] = array(
				"employeeID"=>$name_result['id'],
				"fName"=>$name_result['fName'],
				"lName"=>$name_result['lName'],
				"designation"=>$name_result['role'],
				"joinDate"=>$name_result['joinDate'],
				"joinBranch"=>$name_result['joinBranch'],
				"currentBranch"=>$name_result['currentBranch'],
				"bankInfo"=>json_decode($name_result['bankDetails']),
				"birthDate"=>$name_result['dob'],
				"height"=>$name_result['height'],
				"weight"=>$name_result['weight'],
				"gender"=>$name_result['sex'] == 'M' ? 'Male' : 'Female',
				"bloodGroup"=>$name_result['bloodGroup'],
				"contact"=>$name_result['contact'],
				"currentAddress"=>json_decode($name_result['currentAddress']),
				"permanentAddress"=>json_decode($name_result['nativeAddress']),
				"religion"=>$name_result['religion'],
				"nativePlace"=>$name_result['native'],
				"emergencyName"=>$name_result['emergencyName'],
				"emergencyNumber"=>$name_result['emergencyContact'],
				"photoURL"=>$name_result['photoUrl']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}

while($roll_result = mysql_fetch_assoc($roll_query)){

		$exists = true;

		$response[] = array(
				"employeeID"=>$roll_result['id'],
				"fName"=>$roll_result['fName'],
				"lName"=>$roll_result['lName'],
				"designation"=>$roll_result['role'],
				"joinDate"=>$roll_result['joinDate'],
				"joinBranch"=>$roll_result['joinBranch'],
				"currentBranch"=>$roll_result['currentBranch'],
				"bankInfo"=>json_decode($roll_result['bankDetails']),
				"birthDate"=>$roll_result['dob'],
				"height"=>$roll_result['height'],
				"weight"=>$roll_result['weight'],
				"gender"=>$roll_result['sex'] == 'M' ? 'Male' : 'Female',
				"bloodGroup"=>$roll_result['bloodGroup'],
				"contact"=>$roll_result['contact'],
				"currentAddress"=>json_decode($roll_result['currentAddress']),
				"permanentAddress"=>json_decode($roll_result['nativeAddress']),
				"religion"=>$roll_result['religion'],
				"nativePlace"=>$roll_result['native'],
				"emergencyName"=>$roll_result['emergencyName'],
				"emergencyNumber"=>$roll_result['emergencyContact'],
				"photoURL"=>$roll_result['photoUrl']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}



while($mobile_result = mysql_fetch_assoc($mobile_query)){

		$exists = true;

		$response[] = array(
				"employeeID"=>$mobile_result['id'],
				"fName"=>$mobile_result['fName'],
				"lName"=>$mobile_result['lName'],
				"designation"=>$mobile_result['role'],
				"joinDate"=>$mobile_result['joinDate'],
				"joinBranch"=>$mobile_result['joinBranch'],
				"currentBranch"=>$mobile_result['currentBranch'],
				"bankInfo"=>json_decode($mobile_result['bankDetails']),
				"birthDate"=>$mobile_result['dob'],
				"height"=>$mobile_result['height'],
				"weight"=>$mobile_result['weight'],
				"gender"=>$mobile_result['sex'] == 'M' ? 'Male' : 'Female',
				"bloodGroup"=>$mobile_result['bloodGroup'],
				"contact"=>$mobile_result['contact'],
				"currentAddress"=>json_decode($mobile_result['currentAddress']),
				"permanentAddress"=>json_decode($mobile_result['nativeAddress']),
				"religion"=>$mobile_result['religion'],
				"nativePlace"=>$mobile_result['native'],
				"emergencyName"=>$mobile_result['emergencyName'],
				"emergencyNumber"=>$mobile_result['emergencyContact'],
				"photoURL"=>$mobile_result['photoUrl']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}


while($role_result = mysql_fetch_assoc($role_query)){

		$exists = true;

		$response[] = array(
				"employeeID"=>$role_result['id'],
				"fName"=>$role_result['fName'],
				"lName"=>$role_result['lName'],
				"designation"=>$role_result['role'],
				"joinDate"=>$role_result['joinDate'],
				"joinBranch"=>$role_result['joinBranch'],
				"currentBranch"=>$role_result['currentBranch'],
				"bankInfo"=>json_decode($role_result['bankDetails']),
				"birthDate"=>$role_result['dob'],
				"height"=>$role_result['height'],
				"weight"=>$role_result['weight'],
				"gender"=>$role_result['sex'] == 'M' ? 'Male' : 'Female',
				"bloodGroup"=>$role_result['bloodGroup'],
				"contact"=>$role_result['contact'],
				"currentAddress"=>json_decode($role_result['currentAddress']),
				"permanentAddress"=>json_decode($role_result['nativeAddress']),
				"religion"=>$role_result['religion'],
				"nativePlace"=>$role_result['native'],
				"emergencyName"=>$role_result['emergencyName'],
				"emergencyNumber"=>$role_result['emergencyContact'],
				"photoURL"=>$role_result['photoUrl']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}


while($branch_result = mysql_fetch_assoc($branch_query)){

		$exists = true;

		$response[] = array(
				"employeeID"=>$branch_result['id'],
				"fName"=>$branch_result['fName'],
				"lName"=>$branch_result['lName'],
				"designation"=>$branch_result['role'],
				"joinDate"=>$branch_result['joinDate'],
				"joinBranch"=>$branch_result['joinBranch'],
				"currentBranch"=>$branch_result['currentBranch'],
				"bankInfo"=>json_decode($branch_result['bankDetails']),
				"birthDate"=>$branch_result['dob'],
				"height"=>$branch_result['height'],
				"weight"=>$branch_result['weight'],
				"gender"=>$branch_result['sex'] == 'M' ? 'Male' : 'Female',
				"bloodGroup"=>$branch_result['bloodGroup'],
				"contact"=>$branch_result['contact'],
				"currentAddress"=>json_decode($branch_result['currentAddress']),
				"permanentAddress"=>json_decode($branch_result['nativeAddress']),
				"religion"=>$branch_result['religion'],
				"nativePlace"=>$branch_result['native'],
				"emergencyName"=>$branch_result['emergencyName'],
				"emergencyNumber"=>$branch_result['emergencyContact'],
				"photoURL"=>$branch_result['photoUrl']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}



if(!$exists){
  $output = array(
			"status" => false,
			"error" => "",
			"errorCode" => "No data found with key as ".$key,
			"posts" => ""
		);
		die(json_encode($output));
	}

?>