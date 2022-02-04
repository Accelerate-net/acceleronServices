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
	Brief: Return all Outlets and Roles
*/

/**********************************
  1.1 AUTHENTICATION STANDARD PART
***********************************/

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

define('INCLUDE_CHECK', true);
require 'connect.php';


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


$branch_query = mysql_query("SELECT name, code FROM z_outlets WHERE 1");	
$branchesList = [];
while($info = mysql_fetch_assoc($branch_query)){
		$branchesList[] = array(
			"name"=>$info['name'],
			"code"=>$info['code']
		);
}


$roles_query = mysql_query("SELECT roleName, roleCode FROM erp_roles WHERE 1");
$rolesList = []; 
while($RoleInfo = mysql_fetch_assoc($roles_query)){
		$rolesList[] = array(
			"name"=>$RoleInfo['roleName'],
			"code"=>$RoleInfo['roleCode']
		);
}


	$output = array(
		"status" => true,
		"error" => "",
		"errorCode" => "",
		"branches" => $branchesList,
		"roles" => $rolesList
	);
	die(json_encode($output));

?>