<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);



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
		"description" => $row['brief'],
		"isImageAvailable" => $row['isImg'] == 1? true: false,
		"url" => $row['url'],
		"isAppOnly" => $row['isAppOnly'] == 1? true: false,
		"validTill" => $row['validTill']
	);
	$status = true;
}



//All Combos
$combos = [];

if(isset($_POST['outlet'])){
	$check = mysql_fetch_assoc(mysql_query("SELECT code, name FROM z_outlets WHERE code='{$_POST['outlet']}'"));
	if($check['code']){
		$outlet = $check['code'];		
		$main = mysql_query("SELECT * from z_combos WHERE outlet='{$outlet}'");
		
		while($row = mysql_fetch_assoc($main)){
		
			$combos[]=array(
				"itemCode" => $row['code'],
				"itemName" => $row['name'],
				"itemPrice" => $row['price'],
				"isCombo" => true,
				"isCustom" => false,
				"isAvailable" => $row['isAvailable'] == 1? true : false,
				"comboBrief" => $row['description'],
				"isImageAvailable" => $row['isImg'] == 1? true : false,
				"url" => $row['url'],
				"availableAt" => $check['name']
			);
			$status = true;
		}				
	}
}


$output = array(
	"deals" => $deals,
	"combos" => $combos,
	"status" => $status,
	"error" => $error
	);

echo json_encode($output);

?>
