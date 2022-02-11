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


if(!isset($_POST['cart'])){
	$output = array(
		"status" => false,
		"error" => "Cart Object is missing"
	);
	die(json_encode($output));
}


$special_menu = "z_menu";

if(isset($_POST['outlet'])){
	
	$special_menu = "z_menu";

	$check = mysql_fetch_assoc(mysql_query("SELECT code, isSpecial FROM z_outlets WHERE code='{$_POST['outlet']}'"));
	if($check['isSpecial'] == 1){
		//Special Menu for IIT Madras etc
		$special_menu = "z_menu_".$check['code'];
	}	
	
	if($check['code']){
		$outlet = $check['code'];
	}
	else{
		$outlet = "VELACHERY";
	}
}
else{
	$outlet = "VELACHERY";
}


$special_menu = "z_menu";
$isRewardsActivated = 0;
if(isset($_POST['outlet'])){
	$check = mysql_fetch_assoc(mysql_query("SELECT code, isRewardsEnabled, isSpecial FROM z_outlets WHERE code='{$_POST['outlet']}'"));
	if($check['code']){
		$outlet = $check['code'];
		//Special Menu for IIT Madras
		if($check['isSpecial'] == 1)
		{
			$special_menu = "z_menu_".$check['code'];
		}
		
		$isRewardsActivated = $check['isRewardsEnabled'];
		if($isRewardsActivated == 0){
			$output = array(
				"status" => false,
				"error" => "NOTAVAILABLE"
			);
			die(json_encode($output));		
		}
	}
	else{
			$output = array(
				"status" => false,
				"error" => "Outlet is missing"
			);
			die(json_encode($output));
	}
}
else{
	$output = array(
		"status" => false,
		"error" => "Outlet is missing"
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
		"error" => "Session Expired. Login Again."
	);
	die(json_encode($output));
}


//Check if the token is tampered
if($tokenid['mobile']){
	$mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$user_check = mysql_fetch_assoc(mysql_query("SELECT `name`, `isRewardEnabled` FROM `z_users` WHERE `mobile`='{$mobile}'"));

if(!$user_check['name']){
	$output = array(
		"status" => false,
		"error" => "User does not exist"
	);
	die(json_encode($output));
}
else if($user_check['isRewardEnabled'] == 0){
	$output = array(
		"status" => false,
		"error" => "The outlet delivering food to you, is not participating in Rewards Program."
	);
	die(json_encode($output));
}


	$cart = $_POST['cart'];
	$items = $cart['items'];

	$i = 0;
	$not_avail_list = "";
	$rewards_eligible_sum = 0;
	while($i < sizeof($items)){

			$code = $items[$i]['itemCode'];
			
			//Combo Validations - Check if the combo is available to particular outlet
			if($items[$i]['isCombo']){ 				
				$rowscombo = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_combos` WHERE `code`='{$code}' AND `isDiscountable` = 1"));
				if($rowscombo['isDiscountable'] == 1){				
					$rewards_eligible_sum += $rowscombo['price']*$items[$i]['qty'];
				}
			}
			else{
				$discountCheck = mysql_fetch_assoc(mysql_query("SELECT `isDiscountable` FROM `z_menu_IITMADRAS` WHERE `code`='{$code}'"));
				if($discountCheck['isDiscountable'] == 1){
					$rewards_eligible_sum += $items[$i]['itemPrice']*$items[$i]['qty'];						
				}	
	
			}
			
			$i++;
		
	}
	
	//Total Rewards
	if($rewardsEnabled){
	
		//Total Balance
		$plus = 0;
		$plusQuery = mysql_fetch_assoc (mysql_query("SELECT SUM(coins) AS plusPoints FROM `z_rewards` WHERE `userID`='{$mobile}' AND `isApproved` = 1 AND `isCredit` = 1"));
		if($plusQuery['plusPoints']) {$plus = $plusQuery['plusPoints'];}
			
		$minus = 0;
		$minusQuery = mysql_fetch_assoc (mysql_query("SELECT SUM(coins) AS minusPoints FROM `z_rewards` WHERE `userID`='{$mobile}' AND `isApproved` = 1 AND `isCredit` = 0"));
		if($minusQuery['minusPoints']) {$minus = $minusQuery['minusPoints'];}
		
		$total_rewards_balance = $plus - $minus;
		
		$schemeCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_loyaltyscheme` WHERE `coinsVolume`<='{$plus}' ORDER BY `index` DESC LIMIT 1"));
		$memberClass = $schemeCheck['className'];
		
		$limitCheck = mysql_fetch_assoc(mysql_query("SELECT * FROM `z_loyaltyscheme` WHERE `className`='{$memberClass}'")); 
		$coinsSlab = $limitCheck['coinsRedeemableLimit']; //In percentage, maximum percentage one can redeem
		$max_redeemable_points = floor(($rewards_eligible_sum * $coinsSlab)/100);

		if($max_redeemable_points > $maxRedeemableLimit){
		       $max_redeemable_points = $maxRedeemableLimit;
		}
		
		if($max_redeemable_points > $total_rewards_balance){
			$max_redeemable_points = $total_rewards_balance;
		}
		
					
		$output = array(
			"status" => true,
			"error" => "",
			"coins" => $max_redeemable_points
		);
		
		die(json_encode($output));
		

	}
	else{
		$output = array(
			"status" => false,
			"error" => "Rewards Program temporarily shutdown."
		);
		
		die(json_encode($output));
	}
	

	

$output = array(
	"status" => false,
	"error" => "Something went wrong"
);

echo json_encode($output);

?>
