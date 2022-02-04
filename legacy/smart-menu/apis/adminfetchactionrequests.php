<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require '../connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require '../secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);
/*
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
$outlet = "";
// if($tokenid['outlet']){
// 	$outlet = $tokenid['outlet'];
// }
// else{
// 	$output = array(
// 		"status" => false,
// 		"error" => "Token is tampered"
// 	);
// 	die(json_encode($output));
// }

*/
date_default_timezone_set('Asia/Calcutta');

$outlet = "JPNAGAR";
$steward = "9000090001";

$allRequests = [];

$actionRequestQuery = mysql_query("SELECT * FROM `smart_service_requests` WHERE `is_active` = 1 AND `status` = 0 AND `branch` = '{$outlet}'");
while($request = mysql_fetch_assoc($actionRequestQuery)){
    
    $stewardName = "Unknown";
    $stewardCode = "";
    if($request['fk_steward_code']){
        $stewardData = mysql_fetch_assoc(mysql_query("SELECT * FROM `smart_registered_stewards` WHERE `code` = '{$request['fk_steward_code']}'"));
        $stewardName = $stewardData['name'] && $stewardData['name'] != "" ? $stewardData['name'] : "Unknown";
        $stewardCode = $request['fk_steward_code'];
    }
    
    $allRequests[] = array(
        "id" => $request['id'],
        "table" => $request['table_code'],
        "activeServiceRequest" => $request['request'],
        "stewardCode" => $stewardCode,
        "stewardName" => $stewardName,
        "selfRequest" => $request['fk_steward_code'] == $steward ? true : false,
        "date" => $request['date_created']
    );
}

$finalOutput = array(
    "status" => true,
    "data" => $allRequests
);

die(json_encode($finalOutput));

?>
