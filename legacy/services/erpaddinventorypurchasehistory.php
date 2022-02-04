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
	$admin_mobile = $tokenid['mobile'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


$date = date("Ymd", strtotime($_POST['date']));
$comments = $_POST['remarks'];
$inventory = $_POST['item'];
$vendorCode = $_POST['vendorId'];
$unitsPurchased = $_POST['units'];
$totalAmount = $_POST['amount'];
$paymentMode = $_POST['paymentMode'];
$isPaid = 0;

$status = true;
$error = "";


$query = "INSERT INTO erp_inventoryPurchaseHistory (`date`, `branch`, `inventory`, `vendorCode`, `unitsPurchased`, `totalAmount`, `isPaid`, `modeOfPayment`, `comments`, `entryAddedBy`) VALUES ('{$date}','{$outlet}','{$inventory}','{$vendorCode}','{$unitsPurchased}','{$totalAmount}','{isPaid}','{$paymentMode}','{$comments}','{$admin_mobile}')";

$main = mysql_query($query);

$output = array(
	"response" => "",
	"status" => $status,
	"error" => $error
);
  
echo json_encode($output);		
?>