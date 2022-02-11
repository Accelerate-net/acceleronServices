<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

	$items=[];
	
	$query = "SELECT * from zaitoon_menu WHERE 1 ORDER BY name";
	$main = mysql_query($query);
	        
	while($rows = mysql_fetch_assoc($main)){
	    $items[] = array(
	    "id" => $rows['code'],
	    "name" => $rows['name'],
	    "price" => $rows['price'],
	    "variety"=> 'Hard Coded'
	    );
	}

echo json_encode($items);
		
?>