<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$combos = [];

if(!isset($_GET['outlet'])){
	$output = array(
		"response" => "",
		"status" => false,
		"error" => "Outlet Parameter Missing"
		);

	die(json_encode($output));
}
else{
	$check = mysql_fetch_assoc(mysql_query("SELECT code FROM z_outlets WHERE code='{$_GET['outlet']}'"));
	if($check['code']){
		$outlet = $check['code'];
	}
	else{
		$outlet = "VELACHERY";	
	}
}

$query = "SELECT * from z_combos WHERE outlet='{$outlet}'";
$main = mysql_query($query);

$status = false;
$error = '';

while($row = mysql_fetch_assoc($main)){

	$combos[]=array(
		"itemCode" => $row['code'],
		"itemName" => $row['name'],
		"itemPrice" => $row['price'],
		"isCombo" => true,
		"isCustome" => false,
		"isAvailable" => $row['isAvailable']? true : false,
		"combo" => $row['description'],
		"isImageAvailable" => $row['isImg']? true : false,
		"url" => $row['url']
	);
	$status = true;
}

$output = array(
	"response" => $combos,
	"status" => $status,
	"error" => $error
	);

echo json_encode($output);

?>
