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

if(!isset($_POST['type'])){
	$output = array(
		"status" => false,
		"error" => "Content Type is missing"
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

date_default_timezone_set('Asia/Calcutta');
$today = date("d-m-Y");

$status = false;
$error = "Something went wrong";

$co_type = $_POST['type'];
//$co_type = 'purchases';

if($co_type == 'promotion'){

	if(!isset($_POST['sub']) || !isset($_POST['expiry']) || !isset($_POST['brief'])){
		$result = array(
			'status' => false,
			'error' => "Missing parameters"
		);
		die(json_encode($result));
	}

	mysql_query("INSERT INTO `z_deals`(`type`, `brief`, `isAppOnly`, `validFrom`, `validTill`) VALUES ('{$_POST['sub']}', '{$_POST['brief']}', '0', '{$today}', '{$_POST['expiry']}')");
	
	//Upload Photo
	if(isset($_POST['url']) && $_POST['url'] != "")
	{
		$id_check = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `z_deals` WHERE 1 ORDER BY `id` DESC LIMIT 1"));	   
		$data = $_POST['url'];
	
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		$data = base64_decode($data);
		
		file_put_contents("images/contents/".$id_check['id'].".jpg", $data);
		if(file_exists("images/contents/".$id_check['id'].".jpg")){
			$my_url = "https://zaitoon.online/services/images/contents/".$id_check['id'].".jpg";
			mysql_query("UPDATE `z_deals` SET `isImg`=1, `url`='{$my_url}' WHERE `id`='{$id_check['id']}'");
		}
	}

}

else if($co_type == 'purchases'){

	//if(!isset($_POST['sub']) || !isset($_POST['expiry']) || !isset($_POST['brief'])){
	//	$result = array(
	//		'status' => false,
	//		'error' => "Missing parameters"
	//	);
	//	die(json_encode($result));
	//}
	$brief = 'Welcome the New Year with us. We invite you all for the Grand Dinner Buffet on 31st. Get 10% OFF when you pre book through our App.';
	$expiry = '3-6-2018';
	$type = 'purchase';
	
	mysql_query("INSERT INTO `z_deals`(`type`, `brief`, `isAppOnly`, `validFrom`, `validTill`) VALUES ('{$type}', '{$brief}', '0', '{$today}', '{$expiry}')");
	$url = 'http://s3.india.com/wp-content/uploads/2016/12/Happy-New-Year.jpg';
	//Upload Photo
	if($url)
	{
		$id_check = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `z_deals` WHERE 1 ORDER BY `id` DESC LIMIT 1"));	   
		//$data = $_POST['url'];
		$data = $url;
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		$data = base64_decode($data);
		
		file_put_contents("images/contents/".$id_check['id'].".jpg", $data);
		if(file_exists("images/contents/".$id_check['id'].".jpg")){
			$my_url = "https://zaitoon.online/services/images/contents/".$id_check['id'].".jpg";
			mysql_query("UPDATE `z_deals` SET `isImg`=1, `url`='{$my_url}' WHERE `id`='{$id_check['id']}'");
		}
	}

}

else if($co_type == 'combo'){

	if(!isset($_POST['name']) || !isset($_POST['expiry']) || !isset($_POST['brief']) || !isset($_POST['price'])){
		$result = array(
			'status' => false,
			'error' => "Missing parameters"
		);
		die(json_encode($result));
	}

        mysql_query("INSERT INTO `z_combos`(`name`, `description`, `outlet`, `price`, `isImg`, `isAvailable`) VALUES ('{$_POST['name']}', '{$_POST['brief']}', '{$outlet}', '{$_POST['price']}', 0, 1)");
        

	//Upload Photo
	if(isset($_POST['url']) && $_POST['url'] != "")
	{
		$id_check = mysql_fetch_assoc(mysql_query("SELECT `code` FROM `z_combos` WHERE `outlet` = '{$outlet}' ORDER BY `code` DESC LIMIT 1"));	   
		$data = $_POST['url'];
	
		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);
		$data = base64_decode($data);
		
		file_put_contents("images/combos/".$id_check['code'].".jpg", $data);
		if(file_exists("images/combos/".$id_check['code'].".jpg")){
			$my_url = "https://zaitoon.online/services/images/combos/".$id_check['code'].".jpg";
			mysql_query("UPDATE `z_combos` SET `isImg`=1, `url`='{$my_url}' WHERE `code`='{$id_check['code']}'");
		}
	}	

}
else if($co_type == 'coupon'){

	if(!isset($_POST['code']) || !isset($_POST['expiry']) || !isset($_POST['brief'])){
		$result = array(
			'status' => false,
			'error' => "Missing parameters"
		);
		die(json_encode($result));
	}

	mysql_query("INSERT INTO `z_deals`(`code`, `type`, `brief`, `isAppOnly`, `validFrom`, `validTill`) VALUES ('{$_POST['code']}', 'coupon', '{$_POST['brief']}', '0', '{$today}', '{$_POST['expiry']}')");


}


$result = array(
	'status' => true,
	'error' => ""
);

echo json_encode($result);
		
?>