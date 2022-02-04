<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

error_reporting(0);

$city = $_GET['city'];

	$res = mysql_query("SELECT DISTINCT location, locationCode from z_locations WHERE city='{$city}'");
	while($rows1 = mysql_fetch_assoc($res))
	{
		$cities [] = array(
			"code"  => $rows1['locationCode'],
			"name"  => $rows1['location']
		);
	}

//Final Results
$output = array(
	"response" => $cities,
	"status" => true,
	"error" => ""
	);
echo json_encode($output);
?>
