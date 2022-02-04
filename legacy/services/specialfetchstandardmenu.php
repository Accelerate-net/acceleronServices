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

//Encryption Validation
if(!isset($_GET['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

$token = $_GET['token'];
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


$query = "SELECT DISTINCT mainType FROM z_master_menu WHERE 1";
$main = mysql_query($query);

$output = [];

while($rows = mysql_fetch_assoc($main))
{
	$mainType = $rows['mainType'];
	$submenuQuery = "SELECT DISTINCT subType FROM z_master_menu WHERE mainType='{$mainType}'";
	$sub = mysql_query($submenuQuery);

	$subCategories=[];

	while($subrows = mysql_fetch_assoc($sub)){
		$subType = $subrows['subType'];

		$itemQuery = "SELECT * FROM z_master_menu WHERE mainType='{$mainType}' AND subType='{$subType}' ORDER BY name";
		$allitems = mysql_query($itemQuery);

		//Put all the items into an array.
		$items=[];
		while($item = mysql_fetch_assoc($allitems)){
			$items[] = array(
				"itemCode" => $item['code'],
				"itemName" => $item['name'],
				"itemPrice" => $item['price'],
				"isVeg" => $item['isVeg']? true : false,
				"isCustom" => $item['isCustomisable']? true : false,
				"custom" => json_decode($item['customisation']),
				"isDone" => $item['isDone'] == 1? true : false,
				"photoURL" => $item['url']
			);
		}

		//Create the subCategory with it's name and items array just created.
		$subNameInfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_types WHERE short='{$subrows['subType']}'"));
		$subCategories[] = array(
		    "subType" => $subrows['subType'],
		    "subName" => $subNameInfo['name'],
		    "items" => $items
		);
	}

			$mainNameInfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_types WHERE short='{$rows['mainType']}'"));
	$output[] =array(
		"mainType" => $rows['mainType'],
		"mainName"=> $mainNameInfo['name'],
		"subCategories" => $subCategories
	);
}

echo json_encode($output);

?>
