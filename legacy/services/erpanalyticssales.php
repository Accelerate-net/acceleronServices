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

$sample = '{ "current": [{ "index": 1, "date": "12-04-2018", "sales": "29629", "flag": 0 }, { "index": 2, "date": "13-04-2018", "sales": "28087", "flag": 0 }, { "index": 3, "date": "14-04-2018", "sales": "31498", "flag": 0 }, { "index": 4, "date": "15-04-2018", "sales": "39487", "flag": 2 }, { "index": 5, "date": "16-04-2018", "sales": "25234", "flag": 0 }, { "index": 6, "date": "17-04-2018", "sales": "32268", "flag": 0 }, { "index": 7, "date": "18-04-2018", "sales": "28670", "flag": 0 }, { "index": 8, "date": "19-04-2018", "sales": "28670", "flag": 0 }, { "index": 9, "date": "20-04-2018", "sales": "28907", "flag": 0 }, { "index": 10, "date": "21-04-2018", "sales": "33034", "flag": 0 }, { "index": 11, "date": "22-04-2018", "sales": "35029", "flag": 2 }, { "index": 12, "date": "23-04-2018", "sales": "27841", "flag": 0 }, { "index": 13, "date": "24-04-2018", "sales": "26533", "flag": 1 }, { "index": 14, "date": "25-05-2018", "sales": "32817", "flag": 0 } ], "daywise": [{ "day": "Sunday", "amount": 34200 }, { "day": "Monday", "amount": 12000 }, { "day": "Tuesday", "amount": 18492 }, { "day": "Wednesday", "amount": 17829 }, { "day": "Thursday", "amount": 24039 }, { "day": "Friday", "amount": 39023 }, { "day": "Saturday", "amount": 67294 } ], "monthwise": [{ "month": "January", "amount": 34200 }, { "month": "February", "amount": 12000 }, { "month": "March", "amount": 18492 }, { "month": "April", "amount": 17829 }, { "month": "May", "amount": 24039 }, { "month": "June", "amount": 39023 }, { "month": "July", "amount": 67294 }, { "month": "August", "amount": 34200 }, { "month": "September", "amount": 12000 }, { "month": "October", "amount": 18492 }, { "month": "November", "amount": 17829 }, { "month": "December", "amount": 24039 } ], "todaysSum": 212831, "yesterdaysSum": 204873, "averageSum": 203400, "outletsTotal": 2127, "outletInfo": [{ "name": "IIT Madras", "amount": "790" }, { "name": "Adyar", "amount": "454" }, { "name": "Velachery", "amount": "283" }, { "name": "Royapettah", "amount": "423" }, { "name": "Nungambakkam", "amount": "121" }, { "name": "Anna Nagar", "amount": "56" }], "paymentModes": [{ "name": "Cash", "amount": 1450 }, { "name": "Card", "amount": 4382 }, { "name": "Online", "amount": 3291 }, { "name": "PayTM", "amount": 982 }, { "name": "BHIM", "amount": 244 }], "paymentModesSum": 10349, "orderTypes": [{ "name": "Dine In", "count": 13, "amount": 1450 }, { "name": "Online", "count": 48, "amount": 4382 }, { "name": "Zomato Orders", "count": 34, "amount": 3291 }, { "name": "Swiggy", "count": 9, "amount": 982 }, { "name": "Takeaways", "count": 2, "amount": 244 }], "orderTypesSum": 10349, "orderTypesCount": 106 }';


if(isset($_POST['filterBranch'])){ //filter applied sample case
$sample = '{ "current": [{ "index": 1, "date": "12-04-2018", "sales": "29629", "flag": 0 }, { "index": 2, "date": "13-04-2018", "sales": "28087", "flag": 0 }, { "index": 3, "date": "14-04-2018", "sales": "31498", "flag": 0 }, { "index": 4, "date": "15-04-2018", "sales": "39487", "flag": 2 }, { "index": 5, "date": "16-04-2018", "sales": "25234", "flag": 0 }, { "index": 6, "date": "17-04-2018", "sales": "32268", "flag": 0 }, { "index": 7, "date": "18-04-2018", "sales": "28670", "flag": 0 }, { "index": 8, "date": "19-04-2018", "sales": "28670", "flag": 0 }, { "index": 9, "date": "20-04-2018", "sales": "28907", "flag": 0 }, { "index": 10, "date": "21-04-2018", "sales": "33034", "flag": 0 }, { "index": 11, "date": "22-04-2018", "sales": "35029", "flag": 2 }, { "index": 12, "date": "23-04-2018", "sales": "27841", "flag": 0 }, { "index": 13, "date": "24-04-2018", "sales": "26533", "flag": 1 }, { "index": 14, "date": "25-05-2018", "sales": "32817", "flag": 0 }], "daywise": [{ "day": "Sunday", "amount": 34200 }, { "day": "Monday", "amount": 12000 }, { "day": "Tuesday", "amount": 18492 }, { "day": "Wednesday", "amount": 17829 }, { "day": "Thursday", "amount": 24039 }, { "day": "Friday", "amount": 39023 }, { "day": "Saturday", "amount": 67294 }], "monthwise": [{ "month": "January", "amount": 12002 }, { "month": "February", "amount": 49934 }, { "month": "March", "amount": 12492 }, { "month": "April", "amount": 11023 }, { "month": "May", "amount": 24039 }, { "month": "June", "amount": 39023 }, { "month": "July", "amount": 67294 }, { "month": "August", "amount": 34200 }, { "month": "September", "amount": 12000 }, { "month": "October", "amount": 18492 }, { "month": "November", "amount": 17829 }, { "month": "December", "amount": 24039 }], "todaysSum": 1032942, "yesterdaysSum": 1232942, "averageSum": 203400, "outletsTotal": 2127, "outletInfo": [{ "name": "IIT Madras", "amount": "790" }, { "name": "Adyar", "amount": "454" }, { "name": "Velachery", "amount": "283" }, { "name": "Royapettah", "amount": "423" }, { "name": "Nungambakkam", "amount": "121" }, { "name": "Anna Nagar", "amount": "56" }], "paymentModes": [{ "name": "Cash", "amount": 1450 }, { "name": "Card", "amount": 4382 }, { "name": "Online", "amount": 3291 }, { "name": "PayTM", "amount": 982 }, { "name": "BHIM", "amount": 244 }], "paymentModesSum": 10349, "orderTypes": [{ "name": "Dine In", "count": 13, "amount": 1450 }, { "name": "Online", "count": 48, "amount": 4382 }, { "name": "Zomato Orders", "count": 34, "amount": 3291 }, { "name": "Swiggy", "count": 9, "amount": 982 }, { "name": "Takeaways", "count": 2, "amount": 244 }], "orderTypesSum": 10349, "orderTypesCount": 106 }';
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
			"date": "12-04-2018",
			"sales": "29629",
			"flag": 0
		}, {
			"index": 2,
			"date": "13-04-2018",
			"sales": "28087",
			"flag": 0
		}, {
			"index": 3,
			"date": "14-04-2018",
			"sales": "31498",
			"flag": 0
		}, {
			"index": 4,
			"date": "15-04-2018",
			"sales": "39487",
			"flag": 2
		}, {
			"index": 5,
			"date": "16-04-2018",
			"sales": "25234",
			"flag": 0
		}, {
			"index": 6,
			"date": "17-04-2018",
			"sales": "32268",
			"flag": 0
		}, {
			"index": 7,
			"date": "18-04-2018",
			"sales": "28670",
			"flag": 0
		},
		{
			"index": 8,
			"date": "19-04-2018",
			"sales": "28670",
			"flag": 0
		}, {
			"index": 9,
			"date": "20-04-2018",
			"sales": "28907",
			"flag": 0
		}, {
			"index": 10,
			"date": "21-04-2018",
			"sales": "33034",
			"flag": 0
		}, {
			"index": 11,
			"date": "22-04-2018",
			"sales": "35029",
			"flag": 2
		}, {
			"index": 12,
			"date": "23-04-2018",
			"sales": "27841",
			"flag": 0
		}, {
			"index": 13,
			"date": "24-04-2018",
			"sales": "26533",
			"flag": 1
		}, {
			"index": 14,
			"date": "25-05-2018",
			"sales": "32817",
			"flag": 0
		}
	],
	"daywise": [{
			"day": "Sunday",
			"amount": 34200
		},
		{
			"day": "Monday",
			"amount": 12000
		}, {
			"day": "Tuesday",
			"amount": 18492
		}, {
			"day": "Wednesday",
			"amount": 17829
		},
		{
			"day": "Thursday",
			"amount": 24039
		}, {
			"day": "Friday",
			"amount": 39023
		}, {
			"day": "Saturday",
			"amount": 67294
		}
	],
	"monthwise": [{
			"month": "January",
			"amount": 34200
		},
		{
			"month": "February",
			"amount": 12000
		}, {
			"month": "March",
			"amount": 18492
		}, {
			"month": "April",
			"amount": 17829
		},
		{
			"month": "May",
			"amount": 24039
		}, {
			"month": "June",
			"amount": 39023
		}, {
			"month": "July",
			"amount": 67294
		},
		{
			"month": "August",
			"amount": 34200
		},
		{
			"month": "September",
			"amount": 12000
		}, {
			"month": "October",
			"amount": 18492
		}, {
			"month": "November",
			"amount": 17829
		},
		{
			"month": "December",
			"amount": 24039
		}
	],
	"todaysSum": 212831,
	"yesterdaysSum": 214873,
	"averageSum": 203400,
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
	"paymentModes": [{
		"name": "Cash",
		"amount": 1450
	}, {
		"name": "Card",
		"amount": 4382
	}, {
		"name": "Online",
		"amount": 3291
	}, {
		"name": "PayTM",
		"amount": 982
	}, {
		"name": "BHIM",
		"amount": 244
	}],
	"paymentModesSum": 10349,
	"orderTypes": [{
		"name": "Dine In",
		"count": 13,
		"amount": 1450
	}, {
		"name": "Online",
		"count": 48,
		"amount": 4382
	}, {
		"name": "Zomato Orders",
		"count": 34,
		"amount": 3291
	}, {
		"name": "Swiggy",
		"count": 9,
		"amount": 982
	}, {
		"name": "Takeaways",
		"count": 2,
		"amount": 244
	}],
	"orderTypesSum": 10349,
	"orderTypesCount": 106
}

*/

?>

