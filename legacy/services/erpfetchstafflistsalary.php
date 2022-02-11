<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);
$_POST = json_decode(file_get_contents('php://input'), true);


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

$role_query = mysql_query("SELECT DISTINCT `role` FROM `erp_people` WHERE `currentBranch` = '{$branch}'");

$exists = false;

while($roleWise = mysql_fetch_assoc($role_query)){
	$exists = true;

	$name_query = mysql_query("SELECT * FROM `erp_people` WHERE `currentBranch` = '{$branch}' AND `role` = '{$roleWise['role']}' ORDER BY `fName`");
	
	$roleBlock = [];
	
	while($name_result = mysql_fetch_assoc($name_query)){

		$roleBlock[] = array(
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
				"photoURL"=>$name_result['photoUrl'],
				"payAmountDue"=>2000
		);
	}	


	$blocks[] = array(
		"role" => $roleWise['role'],
		"staff" => $roleBlock
	);
}



if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $blocks
	
	);
	die(json_encode($output));

}



if(!$exists){
  	$output = array(
			"status" => false,
			"error" => "No data found",
			"errorCode" => "",
			"posts" => ""
		);
		die(json_encode($output));
	}

?>