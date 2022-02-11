<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

error_reporting(0);

$status = false;
$error = "Something went wrong";

	$query1 = "SELECT * from z_outlets WHERE isSpecial = 0 AND isOpen = 1";
	$main1 = mysql_query($query1);
	$outlets = [];

	while($rows1 = mysql_fetch_assoc($main1)){
		$outlets[]=array(
			"value"=> $rows1['code'],
			"name"=> $rows1['name'].', '.$rows1['line1'].', '.$rows1['line2'],
			"isAcceptingOnlinePayment"=> $rows1['isAcceptingOnlinePayment'] == 1? true: false,
			"razorpayID"=> $rows1['razorpayID']
		);
		$status = true;
		$error = "";
	}

//Final Results
$output = array(
	"response" => $outlets,
	"status" => $status,
	"error" => $error
);

echo json_encode($output);
?>
