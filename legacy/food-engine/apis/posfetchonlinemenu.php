<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$special_menu = "z_master_menu";
/*
if(isset($_GET['outlet'])){
	$check = mysql_fetch_assoc(mysql_query("SELECT code FROM z_outlets WHERE code='{$_GET['outlet']}'"));
	if($check['code']){
		$outlet = $check['code'];
		//Special Menu for IIT Madras
		if($outlet == "IITMADRAS")
		{
			$special_menu = "z_menu_IITMADRAS";
		}
	}
	else{
		$outlet = "VELACHERY";	
	}
}
else{
	$outlet = "VELACHERY";
}
*/

$query = "SELECT DISTINCT mainType FROM ".$special_menu; //mainType='{$type}'
$main = mysql_query($query);


$output = [];
$headList = [];


while($rows = mysql_fetch_assoc($main))
{

	$mainType = $rows['mainType'];
	$submenuQuery = "SELECT DISTINCT subType FROM ".$special_menu." WHERE mainType='{$mainType}'";
	
	$sub = mysql_query($submenuQuery);

	while($subrows = mysql_fetch_assoc($sub)){
		$subType = $subrows['subType'];

		$itemQuery = "SELECT * FROM ".$special_menu." WHERE mainType='{$mainType}' AND subType='{$subType}' ORDER BY name";

		$allitems = mysql_query($itemQuery);

		//Put all the items into an array.
		$items=[];
		while($item = mysql_fetch_assoc($allitems)){		
			$items[] = array(
				"code" => $item['code'],
				"name" => $item['name'],
				"price" => $item['price'],
				"vegFlag" => $item['isVeg'] ? 1 : 2,
				"isCustom" => $item['isCustomisable']? true : false,
				"customOptions" => json_decode($item['customisation']),
				"isAvailable" => true
			); 
		}


		
		//Create the subCategory with it's name and items array just created. (ONLY IF "ITEMS" not empty.)
		if(count($items)){
			$subNameInfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_types WHERE short='{$subrows['subType']}'"));		
			$output[] = array(
			    "category" => $subNameInfo['name'],
			    "items" => $items
			);
			
			$headList[] = $subNameInfo['name'];
		}
	}
}
   
echo json_encode($headList );
		
?>