<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');
error_reporting(0);

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$userID = $_POST['userID'];

$encryptionMethod = "AES-256-CBC";
$secretHash = "7a6169746f6f6e746f6b656e";
$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
//echo($decryptedtoken);
$tokenid = json_decode($decryptedtoken,true);
//$error =
//$output = [];

$status = 'fail';
$error = 'No such user exists!';
$response = "";
//echo($tokenid['mobile']);
if($tokenid['mobile'] == $userID){
	$status = 'success';
	$error = "";
	$query = "SELECT * from z_reservations WHERE userID='{$userID}' ORDER BY `date` DESC LIMIT 2";
	$main = mysql_query($query);
	while($rows = mysql_fetch_assoc($main)){
		$response[] =array(
			"userID" => $userID,
			"outlet" => $rows['outlet'],
			"date" => $rows['date'],
			"time" => $rows['time'],
			"count" => $rows['count']	
		);	
	}
	
}


$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $response
);

echo json_encode($output);

?>
