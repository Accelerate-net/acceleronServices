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
		"error" => "Session Expired. Login Again."
	);
	die(json_encode($output));
}




/* FRAMING QUERY */
//Howmany results to output
$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']},5";
}

$list = array();

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

$onemonth = date('Ymd', strtotime(' -30 day'));

	$status = true;

	$query = "SELECT * FROM `z_rewards` WHERE `userID` = '{$mobile}' AND `isApproved` = 1 ORDER BY id DESC".$limiter;
	$all = mysql_query($query);

	while($reward = mysql_fetch_assoc($all))
	{
		$list[] = array(
			'orderID' => $reward['isCredit'] == 1? $reward['orderID'] : 'Voucher',
			'time' => date('d-m-Y', strtotime($reward['time'])),
			'coins' => $reward['coins'],
			'amount' => $reward['amount'],
			'isCredit' => $reward['isCredit'] == 1? true: false,
			'isExpired' =>  $reward['time'] > $onemonth ? false : true
		);
	}
	

$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list
);

echo json_encode($output);

?>
