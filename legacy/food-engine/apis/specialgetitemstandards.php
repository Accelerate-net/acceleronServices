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
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

if(!isset($_POST['code'])){
	$output = array(
		"status" => false,
		"error" => "Item Code missing"
	);
	die(json_encode($output));
}

$item_request_code = $_POST['code'];

$query = "SELECT * FROM z_master_menu WHERE code = '{$item_request_code}'";
$item = mysql_fetch_assoc(mysql_query($query));

$itemData = array(
	"itemCode" => $item['code'],
	"itemName" => $item['name'],
	"itemPrice" => $item['price'],
	"mainCategory" => $item['mainType'],
	"subCategory" => $item['subType'],
	"isVeg" => $item['isVeg'],
	"isCustom" => $item['isCustomisable'],
	"custom" => json_decode($item['customisation']),
	"composition" => json_decode($item['mainIngredients']),
	"isDone" => $item['isDone'] == 1? true : false,
	"imageData" => $item['url'],
	"spiceLevel" => $item['isSpicy'],
	"majorContent" => $item['nonvegContent'],
	"boneType" => $item['isBoneless'],
	"cookingType" => $item['cookingType'],
	"fryType" => $item['fryType'],
	"cookingTime" => $item['avgCookingTime']
);
			
$output =array(
	"status" => true,
	"error"=> "",
	"details" => $itemData
);

echo json_encode($output);

?>
