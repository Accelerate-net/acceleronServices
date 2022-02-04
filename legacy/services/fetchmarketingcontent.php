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

if($co_type == 'combos'){
	
	$list = mysql_query("SELECT * FROM `z_combos` WHERE `outlet`='{$outlet}'".$limiter);
	
	while($combo = mysql_fetch_assoc($list)){

				$output[] = array(
					"code" => $combo['code'],
					"name" => $combo['name'],
					"brief" => $combo['description'],
					"price" => $combo['price'],
					"isImg" => $combo['isImg'] == 1? true: false,
					"since" => $combo['validFrom'],
					"url" => $combo['url']						
				); 
				
				$status = true;
				$error = "";															
	}
}
else if($co_type == 'promotions'){
	
	$list = mysql_query("SELECT * FROM `z_deals` WHERE `type`='promotion' OR `type`='offer'".$limiter);
	
	while($promo = mysql_fetch_assoc($list)){

				$output[] = array(
					"id" => $promo['id'],
					"brief" => $promo['brief'],
					"isImg" => $promo['isImg'] == 1? true: false,
					"url" => $promo['url'],
					"expiry" => $promo['validTill'],
					"since" => $promo['validFrom'],
					"outlets" => $promo['outlet']					
				); 
				
				$status = true;
				$error = "";															
	}

}
else if($co_type == 'coupons'){
	
	$list = mysql_query("SELECT * FROM `z_deals` WHERE `type`='coupon'".$limiter);
	
	while($coupon = mysql_fetch_assoc($list)){

				$output[] = array(
					"id" => $coupon['id'],
					"code" => $coupon['code'],
					"brief" => $coupon['brief'],
					"isImg" => $coupon['isImg'] == 1? true: false,
					"url" => $coupon['url'],
					"expiry" => $coupon['validTill'],
					"since" => $coupon['validFrom'],
					"outlets" => $coupon['outlet']		
				); 
				
				$status = true;
				$error = "";															
	}

}


$figure_combos_total = 0;
$figure_combos = mysql_fetch_assoc(mysql_query("SELECT COUNT(`code`) AS total FROM `z_combos` WHERE `outlet`='{$outlet}'"));
if($figure_combos['total'] != "")
{
	$figure_combos_total = $figure_combos['total'];
}

$figure_coupons_total = 0;
$figure_coupons = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) AS total FROM `z_deals` WHERE `type`='coupon'"));
if($figure_coupons['total'] != "")
{
	$figure_coupons_total = $figure_coupons['total'];
}

$figure_promotions_total = 0;
$figure_promotions = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) AS total FROM `z_deals` WHERE `type`='promotion' OR `type`='offer'"));
if($figure_promotions['total'] != "")
{
	$figure_promotions_total = $figure_promotions['total'];
}

$result = array(
	'status' => true,
	'error' => "",
	'response' => $output,
	'totalCombos' => $figure_combos_total,
	'totalPromotions' => $figure_promotions_total,
	'totalCoupons' => $figure_coupons_total
);

echo json_encode($result);
		
?>