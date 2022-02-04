<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

//fetching deals and coupon codes
$deals = [];



/* FRAMING QUERY */
//Howmany results to output
$limiter = "";
if(isset($_GET['id'])){
	$limiter = " LIMIT  {$_GET['id']},5";
}


$query = "SELECT * from z_deals".$limiter;
$main = mysql_query($query);



$status = false;
$error = '';

while($row = mysql_fetch_assoc($main)){

	$deals[]=array(
		"type" => $row['type'],
		"code" => $row['code'],
		"outlet" => $row['outlet'],
		"description" => $row['brief'],
		"isImageAvailable" => $row['isImg'],
		"url" => $row['url'],
		"isAppOnly" => $row['isAppOnly'],
		"validTill" => $row['validTill'],
		"isPurchasable" => $row['isPurchasable'] == 1? true: false,
		"isCustom" => $row['isCustomisable'] == 1? true: false,
		"custom" => json_decode($row['customisation']),
		"itemCode" => $row['id'],
		"itemName" => $row['name'],
		"itemPrice" => $row['price'],			
	);
	$status = true;
}

$output = array(
	"response" => $deals,
	"status" => $status,
	"error" => $error
	);

echo json_encode($output);

?>
