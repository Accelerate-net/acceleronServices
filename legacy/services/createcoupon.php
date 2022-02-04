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
$today = date("d-m-Y");
$coupon_code = $_POST['code'];

//Exist Check
$exist_check = mysql_fetch_assoc(mysql_query("SELECT `from` FROM `z_couponrules` WHERE `code`='{$coupon_code}'"));
if($exist_check['from'] != ""){
	$result = array(
		'status' => false,
		'error' => "Coupon Code already exists"
	);	
	die(json_encode($result));
}

//Rule - PERCENTAGE
if($_POST['rule'] == "PERCENTAGE"){

	//Validations
	if($_POST['percentageP'] < 1 || $_POST['percentageP'] > 100 || !isset($_POST['percentageP']) || $_POST['percentageD'] < 1 || !isset($_POST['percentageD']) || $_POST['percentageM'] < 1 || !isset($_POST['percentageM']) ){
		$result = array(
			'status' => false,
			'error' => "One of the parameter is out of range (Percentage or Maximum Discount or Minimum Cart Value)"
		);	
		die(json_encode($result));
	}
	
	//Outlet Validations
	$all_list = $_POST['list'];
	$final_list = "";	
	
	if(strlen($all_list) > 0){
		$mylist = explode(", ", $all_list);
		foreach ($mylist as $out) {
			$outlet_check = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_outlets` WHERE `code`='{$out}'"));
		    	if($outlet_check['name'] != ""){
		    		if($final_list == ""){
			    		$final_list = $out; 
			    	}
			    	else{
			    		$final_list = $final_list.", ".$out; 
			    	}
		    	}
		}
	}
	
	$coupon_object = array(
		"rule" => "PERCENTAGE",
		"minimumCart" => $_POST['percentageM'],
		"maximum" => $_POST['percentageD'],
		"percentage" => $_POST['percentageP'],
		"isAppOnly" => false
	);
	
	$myrule = json_encode($coupon_object);
	
	//Add to table
	mysql_query("INSERT INTO `z_couponrules`(`code`, `rule`, `list`, `from`, `to`, `brief`, `limit`) VALUES ('{$coupon_code}', '{$myrule}', '{$final_list}', '{$today}', '{$_POST['expiry']}', '{$_POST['brief']}', '{$_POST['limit']}')");
	
	$result = array(
		'status' => true,
		'error' => ""
	);
	die(json_encode($result));
	

}

$result = array(
	'status' => false,
	'error' => "Something went wrong"
);

echo json_encode($result);
		
?>