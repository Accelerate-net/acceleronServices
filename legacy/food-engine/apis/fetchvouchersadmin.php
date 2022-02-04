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
	$outlet= $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


$status = false;
$error = 'No vouchers found';
$vouchersList = "";
$searchKey = $_POST['key'];
if($searchKey == ""){
	$searchKey = date('Ymd');
}

$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 10";	
}
		
	//Case 1: Search with Voucher Name
	if(!$status && $searchKey != ""){		
		
		$main = mysql_query("SELECT * FROM `z_vouchers` WHERE `code` LIKE '%{$searchKey}%' ORDER BY `id`".$limiter);

		while($rows = mysql_fetch_assoc($main)){	
			$status = true;
			$error = "";
			$resultKey = "Similar Voucher Found";
			
			$userNameCheck = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_users` WHERE `mobile`='{$rows['userRestriction']}'"));
			
				$admin_user = '';
				if($rows['selfCreated'] == 0){
					$adminCheck = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$rows['adminUser']}'"));
					$admin_user = $adminCheck['name'];
				}			
			
				$vouchersList[] = array(
					"date" => date('d-m-Y', strtotime($rows['createdDate'])),
					"code" => $rows['code'],
					"expiry" => date('d-m-Y', strtotime($rows['expiry'])),
					"id" => $rows['id'],
					"value" => $rows['value'],
					"minAmount" => $rows['minAmount'],
					"user" => $rows['userRestriction'],
					"userName" => $userNameCheck['name'],
					"isActive" => $rows['isActive'] == 1? true: false,
					"isUsed" => $rows['usedOrder'] != ''? true: false,
					"usedOrder" => $rows['usedOrder'] != ''? $rows['usedOrder']: 'Not Used',
					"isSelfCreated" => $rows['selfCreated'] == 1? true: false,
					"admin" => $admin_user
				);	
		}
	}
	
	//Case 2: Search with Mobile
	if(!$status && $searchKey != ""){		
		
		$main = mysql_query("SELECT * FROM `z_vouchers` WHERE `userRestriction` = '{$searchKey}' ORDER BY `id`".$limiter);

		while($rows = mysql_fetch_assoc($main)){	
			$status = true;
			$error = "";
			$resultKey = "Results found for ".$searchKey;
			
			$userNameCheck = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_users` WHERE `mobile`='{$rows['userRestriction']}'"));
			
				$admin_user = '';
				if($rows['selfCreated'] == 0){
					$adminCheck = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$rows['adminUser']}'"));
					$admin_user = $adminCheck['name'];
				}
			
				$vouchersList[] = array(
					"date" => date('d-m-Y', strtotime($rows['createdDate'])),
					"code" => $rows['code'],
					"expiry" => date('d-m-Y', strtotime($rows['expiry'])),
					"id" => $rows['id'],
					"value" => $rows['value'],
					"minAmount" => $rows['minAmount'],
					"user" => $rows['userRestriction'],
					"userName" => $userNameCheck['name'],
					"isActive" => $rows['isActive'] == 1? true: false,
					"isUsed" => $rows['usedOrder'] != ''? true: false,
					"usedOrder" => $rows['usedOrder'] != ''? $rows['usedOrder']: 'Not Used',
					"isSelfCreated" => $rows['selfCreated'] == 1? true: false,
					"admin" => $admin_user
				);
		}
	}
	
	
	
	
	//Case 0: By Date (Default - Today)
	if(!$status){		
		$queryDate = $searchKey; 	
			
			$main = mysql_query($query = "SELECT * FROM `z_vouchers` WHERE `createdDate`='{$queryDate}' ORDER BY `id`");
			while($rows = mysql_fetch_assoc($main)){	
				$status = true;
				$error = "";
				$resultKey = "Vouchers created on ".date('d-M-Y', strtotime($queryDate));
				
				$userNameCheck = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_users` WHERE `mobile`='{$rows['userRestriction']}'"));
				
				$admin_user = '';
				if($rows['selfCreated'] == 0){
					$adminCheck = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$rows['adminUser']}'"));
					$admin_user = $adminCheck['name'];
				}
							
				$vouchersList[] = array(
					"date" => date('d-m-Y', strtotime($rows['createdDate'])),
					"code" => $rows['code'],
					"expiry" => date('d-m-Y', strtotime($rows['expiry'])),
					"id" => $rows['id'],
					"value" => $rows['value'],
					"minAmount" => $rows['minAmount'],
					"user" => $rows['userRestriction'],
					"userName" => $userNameCheck['name'],
					"isActive" => $rows['isActive'] == 1? true: false,
					"isUsed" => $rows['usedOrder'] != ''? true: false,
					"usedOrder" => $rows['usedOrder'] != ''? $rows['usedOrder']: 'Not Used',
					"isSelfCreated" => $rows['selfCreated'] == 1? true: false,
					"admin" => $admin_user
				);
		
			}		
	}


$output = array(
	"status" => $status,
	"error" => $error,
	"message" => $resultKey,
	"response" => $vouchersList
);

echo json_encode($output);

?>
