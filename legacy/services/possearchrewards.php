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
		"errorCode" => 404,
		"error" => "Security Token is missing. Please login again to prove your identity."
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
		"errorCode" => 404,
		"error" => "Security Token is too old. Please login again to prove your identity."
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
		"errorCode" => 404,
		"error" => "Something suspicious noticed. Please login again to prove your identity."
	);
	die(json_encode($output));
}

$mobile = $_POST['key'];


/* FRAMING QUERY */
//Howmany results to output
$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']},5";
}


$list = array();

$status = false;
$error = "Sorry! No matches found.";


	$query = "SELECT * FROM zaitoon_orderlist WHERE userID = '{$mobile}' AND isVerified='1' ORDER BY orderID DESC".$limiter;
	$all = mysql_query($query);

	while($order = mysql_fetch_assoc($all))
	{
		
		$cart = json_decode($order['cart']);
		$list[] = array(
			'orderID' => $order['orderID'],
			'isTakeaway' => $order['isTakeaway'] == 1? true: false,
			'userID' => $order['userID'],
			'status' => $order['status'],
			'cart' => $cart,
			'deliveryAddress' => $order['deliveryAddress'],
			'date' => $order['date'],
			'outlet' => $order['outlet'],
			'timePlace' => $order['timePlace'],
			'timeConfirm' => $order['timeConfirm'],
			'timeDeliver' => $order['timeDeliver'],
			'paidAmount' => $order['paidAmount'],
			'feedback' => json_decode($order['feedback'])
			);
			
		$status = true;
		$error = "";
	}
	
/*User Info*/
$response = array(
	"name" => "Abhijith C S",
	"email" => "abhijithcs1993@gmail.com",
	"mobile" => "9043960876"
);

$couponInfo = array(
	"brief" => "Saarang Complimentary",
	"expiry" => "31.03.2018",
	"issuedTo" => "Abhijith C S (9043960876)",
	"issuedDate" => "12.02.2018",
	"issuedAdmin" => "Sahadudheen",
	"issuedOutlet" => "IIT Madras",
	"totalValue" => 100,
	"minBill" => 350
);

		
$count = "";
$query1 = "SELECT `orderID` FROM zaitoon_orderlist WHERE userID = '{$mobile}' AND isVerified='1' AND `status` = 2";
$count = mysql_query($query1);
$rowcount = mysql_num_rows($count);

$volume_check = mysql_fetch_assoc(mysql_query("SELECT SUM(`paidAmount`) as total FROM `zaitoon_orderlist` WHERE `userID`='{$mobile}' AND `status`=2"));
	
$output = array(
"query"=> $query,
	"status" => $status,
	"error" => $error,
	"response" => $response,
	"list" => $list,
	"type" => "REDEEM", 
	"couponData" => $couponInfo,
	"count" => $rowcount,
	"volume" => $volume_check['total'],
	"points" => 10
);

echo json_encode($output);

?>
