<?php

/*** YET TO ADD "NO RESULTS" SCENARIO ***/

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$encryptionMethod = "AES-256-CBC";
$secretHash = "7a6169746f6f6e746f6b656e";

$_POST = json_decode(file_get_contents('php://input'), true);

$oid = $_POST['orderID'];
$orderstatus = $_POST['status'];

$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken,true);

$status = 'fail';
$error = 'Not authorized!';

$query = "SELECT * FROM `zaitoon_orderlist` WHERE `orderID`='{$oid}'";
$all = mysql_query($query);
$order = mysql_fetch_assoc($all);


if(1) //$order['outlet'] == $tokenid['outlet']
{
	$status = 'success';
	$error = '';
	$query = "UPDATE `zaitoon_orderlist` SET `status`='{$orderstatus}' WHERE `orderID`='{$oid}'";
	echo($query);
	$all = mysql_query($query);
}

$output = array(
	"status" => $status,
	"error" => $error
);

echo json_encode($output);		
?>