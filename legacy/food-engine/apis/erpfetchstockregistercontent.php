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

if(!isset($_POST['type'])){
	$output = array(
		"status" => false,
		"error" => "Content Type is missing"
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
$today = date("Y-m-d");

$status = false;
$error = "No contents found";

$limiter = "";
if(isset($_POST['page'])){
	$range = $_POST['page'] * 10;
	$limiter = " LIMIT  {$range}, 10";	
}

$co_type = $_POST['type'];

if($co_type == 'out'){
	
	$list = mysql_query("SELECT * FROM `erp_inventoryStockRegister` WHERE `branch`='{$outlet}' ORDER BY `id` DESC".$limiter);
	
	while($outList = mysql_fetch_assoc($list)){
	
		$myInfo = mysql_fetch_assoc(mysql_query("SELECT * FROM `erp_inventoryList` WHERE `id` = '{$outList['inventoryCode']}'"));
		$adminInfo = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code` = '{$outList['adminCode']}'"));

				$output[] = array(
					"id" => $outList['id'],
					"inventoryCode" => $outList['inventoryCode'],
					"inventoryName" => $myInfo['name'],
					"category" => $myInfo['category'],
					"unit" => $myInfo['unit'],
					"date" => date("d-m-Y", strtotime($outList['date'])),
					"quantity" => $outList['outQuantity'],
					"remarks" => $outList['remarks'],
					"createdAdmin" => $adminInfo['name']						
				); 
				
				$status = true;
				$error = "";															
	}
}
else if($co_type == 'purchases'){
	
	$list = mysql_query("SELECT * FROM `erp_inventoryPurchaseHistory` WHERE `branch` = '{$outlet}' ORDER BY `id` DESC".$limiter);
	
	while($purchaseList = mysql_fetch_assoc($list)){
	
		$myInfo = mysql_fetch_assoc(mysql_query("SELECT * FROM `erp_inventoryList` WHERE `id` = '{$purchaseList['inventory']}'"));
		$adminInfo = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code` = '{$purchaseList['entryAddedBy']}'"));
		$vendorInfo = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `erp_inventoryVendors` WHERE `code` = '{$purchaseList['vendorCode']}'"));

				$output[] = array(
					"id" => $purchaseList['id'],
					"inventoryCode" => $purchaseList['inventory'],
					"inventoryName" => $myInfo['name'],
					"category" => $myInfo['category'],
					"unit" => $myInfo['unit'],	
					"date" => date("d-m-Y", strtotime($purchaseList['date'])),
					"quantity" => $purchaseList['unitsPurchased'],
					"amount" => $purchaseList['totalAmount'],
					"comments" => $purchaseList['comments'],
					"mode" => $purchaseList['modeOfPayment'],
					"isPaid" => $purchaseList['isPaid'] == 1 ? true : false,
					"vendorName" => $vendorInfo['name'],
					"createdAdmin" => $adminInfo['name']
								
				); 
				
				$status = true;
				$error = "";															
	}

}
else if($co_type == 'payments'){
	
	$list = mysql_query("SELECT * FROM `erp_inventoryPaymentsHistory` WHERE `branch` = '{$outlet}' ORDER BY `id` DESC".$limiter);
	
	while($paymentList = mysql_fetch_assoc($list)){

		$myInfo = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `erp_inventoryList` WHERE `id` = '{$paymentList['paymentMadeFor']}'"));
		$adminInfo = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code` = '{$paymentList['entryAddedBy']}'"));
		$vendorInfo = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `erp_inventoryVendors` WHERE `code` = '{$paymentList['paymentMadeTo']}'"));

				$output[] = array(
					"id" => $paymentList['id'],
					"date" => date("d-m-Y", strtotime($paymentList['date'])),
					"amount" => $paymentList['totalAmount'],
					"mode" => $paymentList['modeOfPayment'],
					"reference" => $paymentList['paymentReference'],
					"comments" => $paymentList['comments'],	
					"createdAdmin" => $adminInfo['name'],
					"paymentTo" => $vendorInfo['name'] ? $vendorInfo['name'] : '',
					"paymentFor" => $myInfo['name']	? $myInfo['name'] : ''
				); 
				
				$status = true;
				$error = "";															
	}

}


$figure_out_total = 0;
$figure_out = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `erp_inventoryStockRegister` WHERE `branch` ='{$outlet}'"));
if($figure_out['total'] != "")
{
	$figure_out_total = $figure_out['total'];
}

$figure_purchases_total = 0;
$figure_purchases = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `erp_inventoryPurchaseHistory` WHERE `branch` = '{$outlet}'"));
if($figure_purchases['total'] != "")
{
	$figure_purchases_total = $figure_purchases['total'];
}

$figure_payments_total = 0;
$figure_payments = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `erp_inventoryPaymentsHistory` WHERE `branch` = '{$outlet}'"));
if($figure_payments['total'] != "")
{
	$figure_payments_total = $figure_payments['total'];
}


$result = array(
	'status' => true,
	'error' => "",
	'response' => $output,
	'totalOut' => $figure_out_total,
	'totalPurchases' => $figure_purchases_total,
	'totalPayments' => $figure_payments_total
);

echo json_encode($result);
		
?>