<?php

/*** YET TO ADD "NO RESULTS" SCENARIO ***/

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);


$oid = $_POST['orderID']; 

$encryptionMethod = "AES-256-CBC";
$secretHash = "7a6169746f6f6e746f6b656e";
$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken,true);

$query = "SELECT * FROM `zaitoon_orderlist` WHERE `orderID`='{$oid}'";
$all = mysql_query($query);
$order = mysql_fetch_assoc($all);

$list = "";

$status = 'fail';
$error = 'Not authorized!';

if($order['outlet'] == $tokenid['outlet'])
{
	$status = 'success';
	$error = '';
	$cart = json_decode($order['cart']);
	$list = array(
		'orderID' => $order['orderID'], 
		'status' => $order['status'], 
		'comment' => $order['comments'], 
		'cart' => $cart,
		'date' => $order['date'], 
		'timePlace' => $order['timePlace'], 
		'timeConfirm' => $order['timeConfirm'], 
		'timeDeliver' => $order['timeDeliver']
		);
}

$output = array(
	"status" => $status,
	"error" => $error,
	"response" => $list
);

echo json_encode($output);
		
?>