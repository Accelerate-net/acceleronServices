<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$special_menu = "z_menu";

if(isset($_POST['outlet'])){
	
	$special_menu = "z_menu";

	$check = mysql_fetch_assoc(mysql_query("SELECT code, isSpecial FROM z_outlets WHERE code='{$_POST['outlet']}'"));
	if($check['isSpecial'] == 1){
		//Special Menu for IIT Madras etc
		$special_menu = "z_menu_".$check['code'];
	}	
	
	if($check['code']){
		$outlet = $check['code'];
	}
	else{
		$outlet = "VELACHERY";
	}
}
else{
	$outlet = "VELACHERY";
}

$query = "SELECT DISTINCT mainType FROM ".$special_menu; //mainType='{$type}'
$main = mysql_query($query);


$output = [];

while($rows = mysql_fetch_assoc($main))
{

	$mainType = $rows['mainType'];
	$submenuQuery = "SELECT DISTINCT subType FROM ".$special_menu." WHERE mainType='{$mainType}' ORDER BY sortIndex";
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
				"itemCode" => $item['code'],
				"itemName" => $item['name'],
				"itemPrice" => $item['price'],
				"isVeg" => $item['isVeg']? true : false,
				"isCustom" => $item['isCustomisable']? true : false,
				"custom" => json_decode($item['customisation']),
				"isAvailable" => $item[$outlet]? true : false
			); 
		}


		
		//Create the subCategory with it's name and items array just created. (ONLY IF "ITEMS" not empty.)
		if(count($items)){
			$subNameInfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_types WHERE short='{$subrows['subType']}'"));		
			$subCategories[] = array(
			    "subType" => $subrows['subType'],
			    "subName" => $subNameInfo['name'],
			    "items" => $items
			);
		}
	}


	//Add to final Output (only if subCategories NOT EMPTY)
	if(count($subCategories)){
		$mainNameInfo = mysql_fetch_assoc(mysql_query("SELECT name FROM z_types WHERE short='{$rows['mainType']}'"));
		$output[] =array(
			"mainType" => $rows['mainType'],
			"mainName"=> $mainNameInfo['name'],
			"subCategories" => $subCategories
		);
	}
}
   
echo json_encode($output);
		
?>