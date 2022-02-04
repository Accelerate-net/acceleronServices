<?php

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

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


$list = "";

$status = false;
$error = 'Something went wrong';



$query = "SELECT feedback FROM `zaitoon_orderlist` WHERE `feedback` != 'NA' AND `outlet`='{$outlet}'";
$all = mysql_query($query);

$star_count = 0;
$star_rating_sum = 0;


//Separate Counts
$count_service = 0;
$count_delivery = 0;
$count_quality = 0;
$count_app = 0;
$count_food = 0;

$extra_rating_service_sum = 0;
$extra_rating_delivery_sum = 0;
$extra_rating_quality_sum = 0;
$extra_rating_app_sum = 0;
$extra_rating_food_sum = 0;

while($order = mysql_fetch_assoc($all)){



	$feedback = json_decode($order['feedback']);
	$rating = $feedback -> rating;
	
	$star_rating_sum = $star_rating_sum + $rating;
	$star_count++;
	
		
	//Iterate through extra feedback
	if(json_encode($feedback -> quality) == 'true'){
		$count_quality++;
		$extra_rating_quality_sum = $extra_rating_quality_sum + $rating;	
	}
	if(json_encode($feedback -> service) == 'true'){
		$count_service++;
		$extra_rating_service_sum = $extra_rating_service_sum + $rating;	
	}
	if(json_encode($feedback -> delivery) == 'true'){
		$count_deliver++;
		$extra_rating_deliver_sum = $extra_rating_deliver_sum + $rating;	
	}
	if(json_encode($feedback -> app) == 'true'){
		$count_app++;
		$extra_rating_app_sum = $extra_rating_app_sum + $rating;	
	}
	if(json_encode($feedback -> food) == 'true'){
		$count_food++;
		$extra_rating_food_sum = $extra_rating_food_sum + $rating;	
	}
	
}

$overall_star_rating = $star_rating_sum/$star_count;

/*
$overall_service = ($star_rating_sum + $extra_rating_service_sum)/($star_count + $count_service);
$overall_quality = ($star_rating_sum + $extra_rating_quality_sum)/($star_count + $count_quality);
$overall_app = ($star_rating_sum + $extra_rating_app_sum)/($star_count + $count_app);
$overall_delivery = ($star_rating_sum + $extra_rating_delivery_sum)/($star_count + $count_delivery);
$overall_food = ($star_rating_sum + $extra_rating_food_sum)/($star_count + $count_food);
*/

$overall_service = $extra_rating_service_sum/$count_service;
$overall_quality = $extra_rating_quality_sum/$count_quality;
$overall_app = $extra_rating_app_sum/$count_app;
$overall_delivery = $extra_rating_deliver_sum/$count_deliver;
$overall_food = $extra_rating_food_sum/$count_food;

$output = array(
	"overall" => round($overall_star_rating, 1, PHP_ROUND_HALF_UP),
	"service" => round($overall_service, 1, PHP_ROUND_HALF_UP),
	"delivery" => round($overall_delivery, 1, PHP_ROUND_HALF_UP),
	"app" => round($overall_app, 1, PHP_ROUND_HALF_UP),
	"food" => round($overall_food, 1, PHP_ROUND_HALF_UP),
	"quality" =>round($overall_quality, 1, PHP_ROUND_HALF_UP) 				
);


echo json_encode($output);
		
?>