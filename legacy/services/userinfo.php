<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);


//Stub Data
$output = '
[{ "line1": "S12-905, Smondoville", "line2": "NeoTown", "location": "Elecronic City Phase 1", "city": "Bangalore", "mobile": "9043960876", "isDefault": 1 }, { "line1": "220 Mandakini", "line2": "IIT Madras Campus", "location": "IIT Campus", "city": "Chennai", "mobile": "9884179675", "isDefault": 0 }]
';



// $query3 = "SELECT * FROM zaitoon_maintypes WHERE 1";
// $main = mysql_query($query3);
// $output = [];

// while($rows3 = mysql_fetch_assoc($main))
// {

// 	$query = "SELECT * FROM zaitoon_menutypes WHERE mainType='{$rows3['type']}'";
// 	$main2 = mysql_query($query);
	
	
// 	$submenu=[];
// 	$items=[];

// 	//Iterate through complete menu types
// 	while($rows = mysql_fetch_assoc($main2)){
	    
// 	        $query2 = "SELECT * from zaitoon_menu WHERE type='{$rows['subType']}'";
// 	        $main3 = mysql_query($query2);
// 	        $items = [];

// 	        while($rows2 = mysql_fetch_assoc($main3)){
// 	          $items[] = array(
// 	          	"itemCode" => $rows2['code'],
// 	            "itemName" => $rows2['name'],
// 	            "itemPrice" => $rows2['price']
// 	          );
// 	        }

// 	        //Create Sub-menu
// 	        $submenu[] = array(
// 	        	"subType" => $rows['subType'],
// 	            "subName" => $rows['subName'],
// 	            "items" => $items
// 	        	);

// 	    }
// 	    $output[] =array(
// 	        	"mainType" => $rows3['type'],
// 	          	"mainName"=> $rows3['name'],
// 	          	"submenu" => $submenu
// 	        	);

// }
// //$list = array('status' => $flag);    
echo json_decode($output);
		
?>