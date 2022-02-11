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
	$branch = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$mobile = $_POST['mobile'];

//Howmany results to output
$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']},5";
}

$list = [];

$status = false;
$error = "No History Found.";


	//Guests Visits
	$query = "SELECT * FROM `z_desk_reviews` WHERE `mobile`='{$mobile}'".$limiter;
	$all = mysql_query($query);

	$count = 0;
	while($visit = mysql_fetch_assoc($all))
	{
		$cart = json_decode($order['cart']);
		$list[] = array(
			'date' => $visit['date'],
			'mobile' => $visit['mobile'],
			'branch' => $visit['outlet'],
			'amount' => $visit['amount'],
			'billNumber' => $visit['billNumber'],
			'rating' => $visit['stars'],
			'review' => json_decode($order['review'])
		);
			
		$status = true;
		$error = "";
		$count++;
	}
	
//Analytics
$total_rewards = 0;
$total_visits = 0;
$total_volume = 0;
$analyticsFlag = false;

if((isset($_POST['id']) && $_POST['id'] == 0) || !isset($_POST['id'])){

	$analyticsFlag = true;

	//Calculate Rewards
	$onemonth = date('Ymd', strtotime(' -90 day'));

	$plus = 0;
	$plusQuery = mysql_fetch_assoc(mysql_query("SELECT SUM(coins) AS plusPoints FROM `z_desk_loyalty` WHERE `mobile`='{$mobile}' AND `isApproved` = 1 AND `isCredit` = 1 AND `time`>'{$onemonth}'"));
	if($plusQuery['plusPoints']) {$plus = $plusQuery['plusPoints'];}
	
	$minus = 0;
	$minusQuery = mysql_fetch_assoc(mysql_query("SELECT SUM(coins) AS minusPoints FROM `z_desk_loyalty` WHERE `mobile`='{$mobile}' AND `isApproved` = 1 AND `isCredit` = 0 AND `time`>'{$onemonth}'"));
	if($minusQuery['minusPoints']) {$minus = $minusQuery['minusPoints'];}
	
	$total_rewards = $plus - $minus;
	
	//Total Visits
	$visit_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `z_reservations` WHERE `userID`='{$mobile}' AND `status`=2"));
	if($visit_check['total']){
		$total_visits = $visit_check['total'];
	}
	
	//Total Volume
	$volume_check = mysql_fetch_assoc(mysql_query("SELECT SUM(`amount`) as total FROM `z_desk_reviews` WHERE `mobile`='{$mobile}'"));
	if($volume_check['total']){
		$total_volume = $volume_check['total'];
	}
	
}
	
	
$output = array(
	"status" => $status,
	"error" => $query,
	"response" => $list,
	"count" => $count,
	"analyticsAvailable" => $analyticsFlag,
	"totalVisits" => $total_visits,
	"totalAmount" => $total_volume,
	"totalRewards" => $total_rewards
);

echo json_encode($output);

?>
