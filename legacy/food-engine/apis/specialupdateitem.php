<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';


$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
	);
	die(json_encode($output));
}

$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken, true);

//Expiry Validation
date_default_timezone_set('Asia/Calcutta');
$dateStamp = date_create($tokenid['date']);
$today = date_create(date("Y-m-j"));
$interval = date_diff($dateStamp, $today);
$interval = $interval->format('%a');

if($interval > $tokenExpiryDays){
	$output = array(
		"status" => false,
		"error" => "Expired Token"
	);
	die(json_encode($output));
}


//Check if the token is tampered
if($tokenid['outlet']){
	$outlet = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


if(!isset($_POST['itemCode']) || $_POST['itemCode'] == ""){
	$output = array(
		"status" => false,
		"error" => "Item Code is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['itemName']) || $_POST['itemName'] == ""){
	$output = array(
		"status" => false,
		"error" => "Item Name is missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['itemCodeOriginal'])){
	$output = array(
		"status" => false,
		"error" => "Original Item Code is missing"
	);
	die(json_encode($output));
}

$req_item_id = $_POST['itemCode'];
$original_item_id = $_POST['itemCodeOriginal'];

$original_image = $_POST['imageDataOriginal'];


$done_flag = 0;

	if($original_item_id == ""){ //New Item
		
	}
	else if($req_item_id != $original_item_id){ //Change in Item Code
		$item_exist_check = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_master_menu` WHERE `code` = '{$req_item_id}'"));
		if($item_exist_check['name']){
			$output = array(
				"status" => false,
				"error" => "Duplicate Item Code. ".$item_exist_check['name']." has the same code ".$req_item_id
			);
			die(json_encode($output));
		}
		else{
		
			$done_flag = 1;
			
			$object_custom = json_encode($_POST['custom']);
			if($object_custom == null || $object_custom == 'null') {$object_custom = "";}
			
			$object_composition = json_encode($_POST['composition']);
			if($object_composition == null || $object_composition == 'null') {$object_composition = "";}
			
			mysql_query("UPDATE `z_master_menu` SET `code`='{$req_item_id}',`name`='{$_POST['itemName']}',`price`='{$_POST['itemPrice']}', `mainType`='{$_POST['mainCategory']}',`subType`='{$_POST['subCategory']}',`isCustomisable`='{$_POST['isCustom']}',`customisation`='{$object_custom}',`isVeg`='{$_POST['isVeg']}',`isSpicy`='{$_POST['spiceLevel']}',`nonvegContent`='{$_POST['majorContent']}',`isBoneless`='{$_POST['boneType']}',`cookingType`='{$_POST['cookingType']}',`fryType`='{$_POST['fryType']}',`avgCookingTime`='{$_POST['cookingTime']}',`mainIngredients`='{$object_composition}',`isDone`='{$done_flag}' WHERE `code` = '{$original_item_id}'");
			
			//Rename photo file
			if(file_exists("images/standard-menu/".$original_item_id.".jpg")){
				unlink("images/standard-menu/".$original_item_id.".jpg");
				mysql_query("UPDATE `z_master_menu` SET `url` = '' WHERE `code` = '{$req_item_id}'");
			}
		
		}
	
	}
	else if($req_item_id == $original_item_id){ //same item for updates
		
		$done_flag = 1;
		
		$object_custom = json_encode($_POST['custom']);
		if($object_custom == null || $object_custom == 'null') {$object_custom = "";}
		
		$object_composition = json_encode($_POST['composition']);
		if($object_composition == null || $object_composition == 'null') {$object_composition = "";}
		
		mysql_query("UPDATE `z_master_menu` SET `name`='{$_POST['itemName']}',`price`='{$_POST['itemPrice']}', `mainType`='{$_POST['mainCategory']}',`subType`='{$_POST['subCategory']}',`isCustomisable`='{$_POST['isCustom']}',`customisation`='{$object_custom}',`isVeg`='{$_POST['isVeg']}',`isSpicy`='{$_POST['spiceLevel']}',`nonvegContent`='{$_POST['majorContent']}',`isBoneless`='{$_POST['boneType']}',`cookingType`='{$_POST['cookingType']}',`fryType`='{$_POST['fryType']}',`avgCookingTime`='{$_POST['cookingTime']}',`mainIngredients`='{$object_composition}',`isDone`='{$done_flag}' WHERE `code` = '{$original_item_id}'");
	
		$error = "UPDATE `z_master_menu` SET `name`='{$_POST['itemName']}',`price`='{$_POST['itemPrice']}', `mainType`='{$_POST['mainCategory']}',`subType`='{$_POST['subCategory']}',`isCustomisable`='{$_POST['isCustom']}',`customisation`='{$object_custom}',`isVeg`='{$_POST['isVeg']}',`isSpicy`='{$_POST['spiceLevel']}',`nonvegContent`='{$_POST['majorContent']}',`isBoneless`='{$_POST['boneType']}',`cookingType`='{$_POST['cookingType']}',`fryType`='{$_POST['fryType']}',`avgCookingTime`='{$_POST['cookingTime']}',`mainIngredients`='{$object_composition}',`isDone`='{$done_flag}' WHERE `code` = '{$original_item_id}'";
	}



	$image_url_check = mysql_fetch_assoc(mysql_query("SELECT `url` FROM `z_master_menu` WHERE `code` = '{$req_item_id}'"));
	$server_image = $image_url_check['url'];
	
	//Upload Photo
	if(isset($_POST['imageData']) && $_POST['imageData'] != "")
	{
		if($_POST['imageData'] == $server_image){
			//Skip
		}
		else{
			$data = $_POST['imageData'];
		
			list($type, $data) = explode(';', $data);
			list(, $data)      = explode(',', $data);
			$data = base64_decode($data);
			
			file_put_contents("images/standard-menu/".$req_item_id.".jpg", $data);
			if(file_exists("images/standard-menu/".$req_item_id.".jpg")){
				$my_url = "https://zaitoon.online/services/images/standard-menu/".$req_item_id.".jpg";
				mysql_query("UPDATE `z_master_menu` SET `url` = '{$my_url}' WHERE `code` = '{$req_item_id}'");
			}
		}
	}
	else{ //remove image
		if(file_exists("images/standard-menu/".$req_item_id.".jpg")){
			unlink("images/standard-menu/".$req_item_id.".jpg");
			mysql_query("UPDATE `z_master_menu` SET `url` = '' WHERE `code` = '{$req_item_id}'");
		}
	}



	
$output = array(
	"status" => true,
	"error"=> $error
);

echo json_encode($output);

?>
