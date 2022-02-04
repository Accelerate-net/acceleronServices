<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

error_reporting(0);

	$res = mysql_query("SELECT DISTINCT city from z_outlets WHERE 1");
	while($rows1 = mysql_fetch_assoc($res))
	{
		$cities [] = array(
			"name"  => $rows1['city']
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
