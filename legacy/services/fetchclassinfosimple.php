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


	$query1 = "SELECT `class`, `className` FROM `z_loyaltyscheme` WHERE 1";
	$main1 = mysql_query($query1);
	$outlets = [];

	while($rows1 = mysql_fetch_assoc($main1)){
	
		$count = 0; 
		$count_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`mobile`) as total FROM `z_users` WHERE `memberType`='{$rows1['class']}' AND `isBlocked` = 0"));
		if($count_check['total'] != ""){
			$count = $count_check['total'];
		}
		
		$outlets[]=array(
			"value" => $rows1['class'],
			"name" => $rows1['className'],
			"count" => $count
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
