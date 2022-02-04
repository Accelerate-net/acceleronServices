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
	//die(json_encode($output));
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
	//die(json_encode($output));
}

$mobile = $_POST['mobile'];


/* FRAMING QUERY */
//Howmany results to output
$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']},5";
}

//Shortlist based on current status
$orderstatus = "";
if(isset($_POST['status'])){
	$orderstatus = " AND status = '{$_POST['status']}'";
}

$list = array();

$status = false;
$error = "No Orders Found.";


//User Specific Orders


	$status = true;

	$query = "SELECT * FROM zaitoon_orderlist WHERE userID = '{$mobile}' AND isVerified='1'".$orderstatus." ORDER BY orderID DESC".$limiter;
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
		
$count = "";
$query1 = "SELECT `orderID` FROM zaitoon_orderlist WHERE userID = '{$mobile}' AND isVerified='1' AND `status` = 2";
$count = mysql_query($query1);
$rowcount = mysql_num_rows($count);

$volume_check = mysql_fetch_assoc(mysql_query("SELECT SUM(`paidAmount`) as total FROM `zaitoon_orderlist` WHERE `userID`='{$mobile}' AND `status`=2"));
	
$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list,
	"count" => $rowcount,
	"volume" => $volume_check['total']
);

echo json_encode($output);

?>
