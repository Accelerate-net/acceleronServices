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

//Rewards Setup
define('REWARDS_CHECK', true);
require 'rewards.php';

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
if($tokenid['mobile']){
	$userID = $tokenid['mobile'];
	$mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

//User Blocked Check
$userblockcheck = mysql_fetch_assoc(mysql_query("SELECT isBlocked, isRewardEnabled from z_users WHERE mobile='{$userID}'"));
if($userblockcheck['isBlocked'] == 1){
	$output = array(	
		"status" => false,
		"error" =>"User is blocked by Zaitoon. Please contact care@zaitoon.online"
	);
	die(json_encode($output));
}

if($userblockcheck['isRewardEnabled'] == 0){
	$output = array(	
		"status" => false,
		"error" =>"Rewards Program is not enabled for this account"
	);
	die(json_encode($output));
}




date_default_timezone_set('Asia/Calcutta');
$date = date("j F, Y");
$time = date("g:i a");

$dateStamp = date("Ymd");
$onemonth = date('Ymd', strtotime(' -30 day'));

$status = false;
$error = "Failed to generate voucher";

		
                //Total Balance Points
		$plus = 0;
		$plusQuery = mysql_fetch_assoc (mysql_query("SELECT SUM(coins) AS plusPoints FROM `z_rewards` WHERE `userID`='{$mobile}' AND `isApproved` = 1 AND `isCredit` =  1 AND `time`>'{$onemonth}'")); 
		if($plusQuery['plusPoints']) {$plus = $plusQuery['plusPoints'];}
			
		$minus = 0;
		$minusQuery = mysql_fetch_assoc (mysql_query("SELECT SUM(coins) AS minusPoints FROM `z_rewards` WHERE `userID`='{$mobile}' AND `isApproved` = 1 AND `isCredit`  =  0 AND `time`>'{$onemonth}'"));
		if($minusQuery['minusPoints']) {$minus = $minusQuery['minusPoints'];}
		
		$total_rewards_balance = $plus-$minus; //Active points in last 30 days
		
		$schemeCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_loyaltyscheme` WHERE `coinsVolume`<='{$plus}' ORDER BY `index` DESC LIMIT 1"));
		$memberClass = $schemeCheck['className'];
		
		$limitCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_loyaltyscheme` WHERE `className`='{$memberClass}'")); 
		$coinsSlab = $limitCheck['coinsRedeemableLimit']; //In percentage, maximum percentage one can redeem
		
		if($total_rewards_balance >= 1){
			if($total_rewards_balance > 100){
				$voucher_value = 100; //max voucher value (depends on the member class)
			}
			else{
				$voucher_value = $total_rewards_balance;
			}				
		}
		else{
			$output = array(
				"status" => false,
				"error" => "You need to have a minimum total of 50 coins to redeem it"
			);			
			die(json_encode($output));
		}
				
		$voucher_minimum = 0;
		$voucher_expiry = date('Ymd', strtotime(' +30 day'));

		
		//Voucher Generator
		$generatedVoucher = generateCoupon();
		while(1){
			$duplicate_check = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_vouchers` WHERE `code`='{$generatedVoucher}'"));
			if($duplicate_check['code'] != ""){ //duplicate found				
				$generatedVoucher = generateCoupon();				
			}
			else{					
				break;
			}
		}
		
		//Save Voucher
		mysql_query("INSERT INTO `z_vouchers`(`createdDate`, `code`, `value`, `minAmount`, `expiry`, `selfCreated`, `isRestricted`, `userRestriction`, `isActive`) VALUES ('{$dateStamp}', '{$generatedVoucher}', '{$voucher_value}', '{$voucher_minimum}', '{$voucher_expiry}', 1, 1, '{$mobile}', 1)");
		
		//Add entry to ledger
		mysql_query("INSERT INTO `z_rewards`(`orderID`, `userID`, `time`, `isCredit`, `coins`, `amount`, `isApproved`) VALUES ('{$generatedVoucher}', '{$mobile}', '{$dateStamp}', 0, '{$voucher_value}', '{$voucher_value}', 1)");
		
		//SMS to Customer		
		//SMS Balance Check
		$sms_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `sms_limt` WHERE 1"));
		
		if($sms_info['used'] < $sms_info['purchased']){
		
			// Account details
			$username = 'support@accelerate.net.in';
			$hash = 'c91c1f65213965173fb2353ea7863061d5df526e5ecf94badb88b8f6545fa45e';
			
			// Message details - to customer
			$numbers = array('91'.$mobile);
			$sender = urlencode('ZAITON');				
			
			$message = rawurlencode("Congratulations, coins are redeemed successfully! Use voucher code ".$generatedVoucher." and get Rs. ".$voucher_value." OFF. Code expires on ".$voucher_expiry.". www.zaitoon.online");				
		 
			$numbers = implode(',', $numbers);
		 
			// Prepare data for POST request
			$data = array('username' => $username, 'hash' => $hash, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		 
			// Send the POST request with cURL
			$ch = curl_init('http://api.textlocal.in/send/');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$SMSresponse = curl_exec($ch);
			curl_close($ch); 
	
			mysql_query("UPDATE  `sms_limt` SET  `used` =  `used`+1 WHERE 1");
		}
		
		
		
		
		function generateCoupon(){
			$characters = '23456789ABCDEFGHJKLMNPQRSTWXYZ';
    			$randomString = '';
    			for ($i = 0; $i < 12; $i++) {
        			$randomString .= $characters[rand(0, 29)];
    			}
			return $randomString;
		}
		
		
		$status = true;
		$error = "";

$output = array(
		"status" => $status,
		"error" => $error,
		"totalPoints" => $total_rewards_balance,
		"memberClass" => $memberClass,
		"voucher" => $generatedVoucher,
		"brief" => "Use this Voucher and get Rs. ".$voucher_value." off on next order.",
		"expiry" => date("d-m-Y", strtotime($voucher_expiry))
);

echo json_encode($output);
?>
