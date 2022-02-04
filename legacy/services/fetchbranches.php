<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

error_reporting(0);

$status = false;
$error = 'No outlets listed.';

// Get parameters from URL
if(isset($_GET["lat"]) && $_GET["lng"]){

	$center_lat = mysql_real_escape_string($_GET["lat"]);
	$center_lng = mysql_real_escape_string($_GET["lng"]);
	
	$query = mysql_query("SELECT *, ( 3959 * acos( cos( radians('{$center_lat}') ) *  cos( radians( latitude ) ) * cos( radians( longitude ) - radians('{$center_lng}') ) + sin( radians('{$center_lat}') ) * sin( radians( latitude ) ) ) ) AS distance FROM z_outlets WHERE isListed = 1 ORDER BY distance");

	
	while($rows = mysql_fetch_assoc($query)){

		$response[] = array(
			"outlet" => $rows['code'],
			"isDistance" => true,
			"distance" => round($rows['distance'], 2), 
			"name"=> $rows['name'],
			"city"=> $rows['city'],
			"line1"=> $rows['line1'],
			"line2"=> $rows['line2'],
			"mobile"=> $rows['contact'],
			"openHours"=> $rows['operationalHours'],
			"pictures" => json_decode($rows['fotos']),
			"isAcceptingOnlinePayment"=> $rows['isAcceptingOnlinePayment'] == 1? true: false,
			"isReservationAllowed" => $rows['isReservationAllowed'] == 1? true: false,
			"isOpen"=> $rows['isOpen'] == 1? true: false		
		);
	
		$status = true;
		$error = '';
	}
		
}
else{
	$query = mysql_query("SELECT * FROM `z_outlets` WHERE `isListed` = 1");
	
	while($rows = mysql_fetch_assoc($query)){

		$response[] = array(
			"outlet" => $rows['code'],
			"isDistance" => false,
			"name"=> $rows['name'],
			"city"=> $rows['city'],
			"line1"=> $rows['line1'],
			"line2"=> $rows['line2'],
			"mobile"=> $rows['contact'],
			"openHours"=> $rows['operationalHours'],
			"pictures" => json_decode($rows['fotos']),
			"isAcceptingOnlinePayment"=> $rows['isAcceptingOnlinePayment'] == 1? true: false,
			"isReservationAllowed" => $rows['isReservationAllowed'] == 1? true: false,
			"isOpen"=> $rows['isOpen'] == 1? true: false		
		);
	
		$status = true;
		$error = '';
	}
}




//Results
$output = array(
	"response" => $response,
	"status" => $status,
	"error" => $error
);

die(json_encode($output));

?>
