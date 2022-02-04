<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$special_menu = "z_menu_IITMADRAS";
$query = "SELECT DISTINCT mainType FROM ".$special_menu." WHERE 1";

$main = mysql_query($query);

$output = [];


	$mainType = 'ARABIAN';
	$submenuQuery = "SELECT DISTINCT subType FROM ".$special_menu." WHERE mainType='ARABIAN'";
	$sub = mysql_query($submenuQuery);

	$subCategories=[];

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
				"isAvailable" => true,
				"isCustom" => $item['isCustomisable']? true : false,
				"customOptions" => json_decode($item['customisation'])
			);
		}

		//Create the subCategory with it's name and items array just created.
		$subNameInfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_types WHERE short='{$subrows['subType']}'"));
		$subCategories[] = array(
		    "category" => $subNameInfo['name'],
		    "items" => $items
		);
	}


echo json_encode($subCategories);

?>
