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

require 'errorlist.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => $vegaError_TokenMissing,
		"errorCode" => 400
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
		"error" => $vegaError_TokenExpired,
		"errorCode" => 400
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
		"error" => $vegaError_TokenInvalid,
		"errorCode" => 400
	);
	die(json_encode($output));
}

if($_POST['filter'] == ''){
    if($_POST['index'] == 1){
        $sampleData = '[{ "uid": "13", "type": "EXPENSE", "reference": "", "issuedBy": "Sahadudheen", "issuedTo": "Abhijith", "issuedToType": "Staff", "amount": "300", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "purpose": "Transfer", "authorizedBy": "Manager" } }, { "uid": "13", "type": "CREDIT", "reference": "239_PAY", "issuedBy": "Sahadudheen", "issuedTo": "Account", "issuedToType": "Account", "amount": "300", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "purpose": "Transfer", "receivedFrom": "Abhijith", "receivedType": "Staff", "receivedCode": "9043960876" } }, { "uid": "12", "type": "SALARY", "reference": "SAL19139", "issuedBy": "Sahadudheen", "issuedTo": "Muhammed Ameen", "issuedToType": "Staff", "amount": "2220", "paymentStatus": "PAID", "modeOfPayment": "TRANSFER", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "salaryIssuingMonth": "February 2019", "staffCode": "9043960876", "comments": "Advance" } }, { "uid": "13", "type": "PURCHASE", "reference": "PU1129", "issuedBy": "Sahadudheen", "issuedTo": "Ali Mutton Stall", "issuedToType": "Vendor", "amount": "1200", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "itemsPurchased": "1 Kg Mutton Legs" } }, { "uid": "13", "type": "EXPENSE", "reference": "", "issuedBy": "Sahadudheen", "issuedTo": "Abhijith", "issuedToType": "Staff", "amount": "300", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "purpose": "Transfer", "authorizedBy": "Manager" } }, { "uid": "13", "type": "CREDIT", "reference": "239_PAY", "issuedBy": "Sahadudheen", "issuedTo": "Account", "issuedToType": "Account", "amount": "300", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "purpose": "Transfer", "receivedFrom": "Abhijith", "receivedType": "Staff", "receivedCode": "9043960876" } }, { "uid": "12", "type": "SALARY", "reference": "SAL19139", "issuedBy": "Sahadudheen", "issuedTo": "Muhammed Ameen", "issuedToType": "Staff", "amount": "2220", "paymentStatus": "PAID", "modeOfPayment": "TRANSFER", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "salaryIssuingMonth": "February 2019", "staffCode": "9043960876", "comments": "Advance" } }, { "uid": "13", "type": "PURCHASE", "reference": "PU1129", "issuedBy": "Sahadudheen", "issuedTo": "Ali Mutton Stall", "issuedToType": "Vendor", "amount": "1200", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "itemsPurchased": "1 Kg Mutton Legs" } }, { "uid": "13", "type": "EXPENSE", "reference": "", "issuedBy": "Sahadudheen", "issuedTo": "Abhijith", "issuedToType": "Staff", "amount": "300", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "purpose": "Transfer", "authorizedBy": "Manager" } }, { "uid": "13", "type": "CREDIT", "reference": "239_PAY", "issuedBy": "Sahadudheen", "issuedTo": "Account", "issuedToType": "Account", "amount": "300", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "purpose": "Transfer", "receivedFrom": "Abhijith", "receivedType": "Staff", "receivedCode": "9043960876" } }]';
    }
    else{
        $sampleData = '[{ "uid": "12", "type": "SALARY", "reference": "SAL19139", "issuedBy": "Sahadudheen", "issuedTo": "Muhammed Ameen", "issuedToType": "Staff", "amount": "1111", "paymentStatus": "PAID", "modeOfPayment": "TRANSFER", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "salaryIssuingMonth": "February 2019", "staffCode": "9043960876", "comments": "Advance" } }, { "uid": "13", "type": "PURCHASE", "reference": "PU1129", "issuedBy": "Sahadudheen", "issuedTo": "Ali Mutton Stall", "issuedToType": "Vendor", "amount": "2222", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "itemsPurchased": "1 Kg Mutton Legs" } }]';
    }

    $output = array(
    	"status" => true,
    	"totalCount" => 12,
    	"filterCount" => 0,
    	"error" => "",
    	"response" => json_decode($sampleData, true)
    );
}
else{
    $sampleData = '[{ "uid": "12", "type": "SALARY", "reference": "SAL19139", "issuedBy": "Sahadudheen", "issuedTo": "Muhammed Ameen", "issuedToType": "Staff", "amount": "1111", "paymentStatus": "PAID", "modeOfPayment": "TRANSFER", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "salaryIssuingMonth": "February 2019", "staffCode": "9043960876", "comments": "Advance" } }, { "uid": "13", "type": "PURCHASE", "reference": "PU1129", "issuedBy": "Sahadudheen", "issuedTo": "Ali Mutton Stall", "issuedToType": "Vendor", "amount": "2222", "paymentStatus": "PAID", "modeOfPayment": "CASH", "dateOfPayment": "21-03-2019", "date": "13-03-2019", "time": "12:10 PM", "details": { "itemsPurchased": "1 Kg Mutton Legs" } }]';

    $output = array(
    	"status" => true,
    	"totalCount" => 12,
    	"filterCount" => 0,
    	"error" => "",
    	"response" => json_decode($sampleData, true)
    );
    
}


echo json_encode($output);
		
?>