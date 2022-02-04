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
	$check = mysql_fetch_assoc(mysql_query("SELECT code, isSpecial FROM z_outlets WHERE code='{$_POST['outlet']}'"));
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




//validate token

$type = $_POST['cuisine'];

$query = "SELECT * FROM `z_menu` WHERE `mainType`='{$type}' AND `isFeatured` = 1"; //mainType='{$type}'
$main = mysql_query($query);

$output = [];

while($item = mysql_fetch_assoc($main))
{
			$output[] = array(
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
   
echo json_encode($output);
		
?>