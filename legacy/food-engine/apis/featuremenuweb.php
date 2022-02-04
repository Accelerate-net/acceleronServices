<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$special_menu = "z_menu";

if(isset($_GET['outlet'])){
	$check = mysql_fetch_assoc(mysql_query("SELECT code, isSpecial FROM z_outlets WHERE code='{$_GET['outlet']}'"));
	if($check['isSpecial'] == 1){
		$special_menu = "z_menu_".$check['code'];
	}
	else{
		$outlet = "VELACHERY";	
	}
}
else{
	$outlet = "VELACHERY";
}


$response = [];
$mainType = mysql_query("SELECT DISTINCT `mainType` FROM `z_menu` WHERE 1");
while($mainMenuType = mysql_fetch_assoc($mainType)){

	$main = mysql_query("SELECT * FROM `z_menu` WHERE `mainType`='{$mainMenuType['mainType']}' AND `isFeatured` = 1");
	
	$featuredItems = [];
	
	while($item = mysql_fetch_assoc($main))
	{
				$featuredItems[] = array(
					"itemCode" => $item['code'],
					"itemName" => $item['name'],
					"itemPrice" => $item['price'],
					"isVeg" => $item['isVeg']? true : false,
					"isAvailable" => $item[$outlet]? true : false,
					"isCustom" => $item['isCustomisable']? true : false,
					"custom" => json_decode($item['customisation']),
					"photo" => "https://zaitoon.online/services/images/featured/".$item['code'].".jpg"
				); 
	}
	
	$response[] = array(
		"cuisine" => $mainMenuType['mainType'],
		"featured" => $featuredItems
	); 		
}
   
echo json_encode($response);	
?>