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


$status = true;
$error = "";

/* ADD MAIN LOGIC */

$sample = '{ "current": [{ "index": 1, "date": "19-04-2018", "sales": "28670" }, { "index": 2, "date": "20-04-2018", "sales": "28907" }, { "index": 3, "date": "21-04-2018", "sales": "33034" }, { "index": 4, "date": "22-04-2018", "sales": "35029" }, { "index": 5, "date": "23-04-2018", "sales": "27841" }, { "index": 6, "date": "24-04-2018", "sales": "26533" }, { "index": 7, "date": "25-04-2018", "sales": "32817" }], "previous": [{ "index": 1, "date": "12-04-2018", "sales": "29629" }, { "index": 2, "date": "13-04-2018", "sales": "28087" }, { "index": 3, "date": "14-04-2018", "sales": "31498" }, { "index": 4, "date": "15-04-2018", "sales": "39487" }, { "index": 5, "date": "16-04-2018", "sales": "25234" }, { "index": 6, "date": "17-04-2018", "sales": "32268" }, { "index": 7, "date": "18-04-2018", "sales": "28670" }], "currentSum": 212831, "previousSum": 214873, "currentCount": 940, "rating": 3.8, "averageTimeSpent": 62, "averageAmountSpent": 340, "previousCount": 951, "outletsTotal": 2127, "outletInfo": [{ "name": "IIT Madras", "amount": "790" }, { "name": "Adyar", "amount": "454" }, { "name": "Velachery", "amount": "283" }, { "name": "Royapettah", "amount": "423" }, { "name": "Nungambakkam", "amount": "121" }, { "name": "Anna Nagar", "amount": "56" }], "topSelling": [{ "name": "Spicy Barbeque", "count": "790" }, { "name": "Chicken Shawarma", "count": "454" }, { "name": "Angara Kebab", "count": "283" }, { "name": "Spcial Falooda", "count": "129" }, { "name": "Chicken Biryani", "count": "121" }], "topSellingLastUpdate": "12th April, 2018" }';


if(isset($_POST['filterBranch'])){ //filter applied sample case
$sample = '{ "current": [{ "index": 1, "date": "19-04-2018", "sales": "120" }, { "index": 2, "date": "20-04-2018", "sales": "1314" }, { "index": 3, "date": "21-04-2018", "sales": "123" }, { "index": 4, "date": "22-04-2018", "sales": "240" }, { "index": 5, "date": "23-04-2018", "sales": "123" }, { "index": 6, "date": "24-04-2018", "sales": "450" }, { "index": 7, "date": "25-04-2018", "sales": "130" }], "previous": [{ "index": 1, "date": "12-04-2018", "sales": "104" }, { "index": 2, "date": "13-04-2018", "sales": "1022" }, { "index": 3, "date": "14-04-2018", "sales": "104" }, { "index": 4, "date": "15-04-2018", "sales": "104" }, { "index": 5, "date": "16-04-2018", "sales": "140" }, { "index": 6, "date": "17-04-2018", "sales": "210" }, { "index": 7, "date": "18-04-2018", "sales": "249" }], "currentSum": 110439, "previousSum": 13095, "currentCount": 940, "rating": 4.8, "averageTimeSpent": 12, "averageAmountSpent": 340, "previousCount": 951, "outletsTotal": 2127, "outletInfo": [{ "name": "IIT Madras", "amount": "790" }, { "name": "Adyar", "amount": "454" }, { "name": "Velachery", "amount": "283" }, { "name": "Royapettah", "amount": "423" }, { "name": "Nungambakkam", "amount": "121" }, { "name": "Anna Nagar", "amount": "56" }], "topSelling": [{ "name": "Spicy Barbeque", "count": "790" }, { "name": "Chicken Shawarma", "count": "454" }, { "name": "Angara Kebab", "count": "283" }, { "name": "Spcial Falooda", "count": "129" }, { "name": "Chicken Biryani", "count": "121" }], "topSellingLastUpdate": "12th April, 2018" }';
}

$list = json_decode($sample);

$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list
);

echo json_encode($output);

/*

{
	"current": [{
		"index": 1,
		"date": "19-04-2018",
		"sales": "28670"
	}, {
		"index": 2,
		"date": "20-04-2018",
		"sales": "28907"
	}, {
		"index": 3,
		"date": "21-04-2018",
		"sales": "33034"
	}, {
		"index": 4,
		"date": "22-04-2018",
		"sales": "35029"
	}, {
		"index": 5,
		"date": "23-04-2018",
		"sales": "27841"
	}, {
		"index": 6,
		"date": "24-04-2018",
		"sales": "26533"
	}, {
		"index": 7,
		"date": "25-04-2018",
		"sales": "32817"
	}],
	"previous": [{
		"index": 1,
		"date": "12-04-2018",
		"sales": "29629"
	}, {
		"index": 2,
		"date": "13-04-2018",
		"sales": "28087"
	}, {
		"index": 3,
		"date": "14-04-2018",
		"sales": "31498"
	}, {
		"index": 4,
		"date": "15-04-2018",
		"sales": "39487"
	}, {
		"index": 5,
		"date": "16-04-2018",
		"sales": "25234"
	}, {
		"index": 6,
		"date": "17-04-2018",
		"sales": "32268"
	}, {
		"index": 7,
		"date": "18-04-2018",
		"sales": "28670"
	}],
	"currentSum": 212831,
	"previousSum": 214873,
	"currentCount": 940,
	"rating": 3.8,
	"averageTimeSpent": 62,
	"averageAmountSpent": 340,
	"previousCount": 951,
	"outletsTotal": 2127,
	"outletInfo": [{
		"name": "IIT Madras",
		"amount": "790"
	}, {
		"name": "Adyar",
		"amount": "454"
	}, {
		"name": "Velachery",
		"amount": "283"
	}, {
		"name": "Royapettah",
		"amount": "423"
	}, {
		"name": "Nungambakkam",
		"amount": "121"
	}, {
		"name": "Anna Nagar",
		"amount": "56"
	}],
	"topSelling": [{
		"name": "Spicy Barbeque",
		"count": "790"
	}, {
		"name": "Chicken Shawarma",
		"count": "454"
	}, {
		"name": "Angara Kebab",
		"count": "283"
	}, {
		"name": "Spcial Falooda",
		"count": "129"
	}, {
		"name": "Chicken Biryani",
		"count": "121"
	}],
	"topSellingLastUpdate": "12th April, 2018"
}

*/

?>

