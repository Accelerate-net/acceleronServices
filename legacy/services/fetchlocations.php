<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//$token = mysql_real_escape_string($_POST['token']);

//fetching deals and coupon codes

$query = "SELECT DISTINCT city from z_locations";
$main = mysql_query($query);

$status = 'fail';
$error = '';

while($rows = mysql_fetch_assoc($main)){
	$query1 = "SELECT * from z_locations WHERE city='{$rows['city']}'";
	$main1 = mysql_query($query1);
	$localities = [];
	while($rows1 = mysql_fetch_assoc($main1)){
		$localities[]=array(
			"name" => $rows1['location'],
			"isServed" => $rows1['isServed'],
			"outlet" => $rows1['outlet'],
			"minOrder" => $rows1['minOrder'],
			"minTime" => $rows1['minTime']
		);
		$status = 'success';
	}
	$response = array(
		"city" => $rows['city'],
		"localities" => $localities
	);
	//echo(sizeof ($rows1));
}

$output = array(
	"response" => $response,
	"status" => $status,
	"error" => $error
	);
//$list = array('status' => $flag);    
echo json_encode($output);
		
?>

