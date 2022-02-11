<?php

/*** YET TO ADD "NO RESULTS" SCENARIO ***/

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_GET['orderID']))
	$oid = $_GET['orderID']; 

$query = "SELECT * FROM `zaitoon_orderlist` WHERE `orderID`='{$oid}'";
$all = mysql_query($query);

$list = "";
$status = 'fail';
$error = 'No such order exists!';

while($order = mysql_fetch_assoc($all))
{	
	$status = 'success';
	$error = '';
	$cart = json_decode($order['cart']);
	$list = array(
		'orderID' => $order['orderID'], 
		'status' => $order['status'],
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