<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

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

$status = false;
$error = "No vendors found.";

$query = "SELECT * FROM `erp_inventoryVendors` WHERE 1";
$main = mysql_query($query);



while($rows = mysql_fetch_assoc($main))
{

		
		$inventoryTemp = json_decode($rows['inventoriesProvided'], true);
		
		$inventorySet = [];
		foreach ($inventoryTemp as $inventory) {
		    $info = mysql_fetch_assoc(mysql_query("SELECT `id`, `name`, `category` FROM `erp_inventoryList` WHERE `id` = '{$inventory}'"));
		    $inventorySet[] = array(
		    	"code" => $info['id'],
		    	"name" => $info['name'] ? $info['name'] : 'Unknown',
		    	"category" => $info['category'] ? $info['category'] : 'UNKNOWN'
		    );
		}
		
		$branchTemp = json_decode($rows['providingBranches'], true);
		
		$branchSet = [];
		$isProvidingToThisBranch = false;
		foreach ($branchTemp as $branches) {
		    $info = mysql_fetch_assoc(mysql_query("SELECT `code`, `name` FROM `z_outlets` WHERE `code` = '{$branches}'"));
		    $branchSet[] = array(
		    	"code" => $info['code'],
		    	"name" => $info['name']
		    );
		    
		    if($info['code'] == $outlet){
		    	$isProvidingToThisBranch = true;
		    }
		   
		}


			$output[] = array(
				"code" => $rows['code'],
				"address" => $rows['address'],
				"contact"=> $rows['contact'],
				"name" => $rows['name'],
				"inventoriesProvided" => $inventorySet,
				"branchesProvided" => $branchSet,
				"modeOfPayment"=> $rows['modeOfPayment'],
				"paymentReference"=> $rows['paymentReference'],
				"isProvidingToThisBranch" => $isProvidingToThisBranch
			);
			
			

		$status = true;
		$error = "";
}

$error = "";

$out = array(
	"status" => $status,
	"error" => $error,
	"response" => $output
);

echo json_encode($out);

?>
