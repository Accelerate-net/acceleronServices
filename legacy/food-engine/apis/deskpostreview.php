<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');
error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//SMS Credentials
define('SMS_INCLUDE_CHECK', true);
require 'smsblackbox.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Login to again to submit review",
		"callback" => "MAIN"
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
		"error" => "Login to again to submit review",
		"callback" => "MAIN"
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
		"error" => "Login to again to submit review",
		"callback" => "MAIN"
	);
	die(json_encode($output));
}


$status = false;
$error = 'Something went wrong. Try again.';

$data = $_POST['details'];
$ratingObj = $_POST['review'];
$rating = $ratingObj['rating'];
$comm = $ratingObj['comment'];
$comm = trim(preg_replace('/\s+/', ' ', $comm));

$ratingObj['comment'] = $comm;

$flag_ambience = $ratingObj['app'];
$flag_food = $ratingObj['food'];
$flag_time = $ratingObj['delivery'];
$flag_service = $ratingObj['service'];
$flag_value = $ratingObj['quality'];

$review = json_encode($ratingObj);

date_default_timezone_set('Asia/Calcutta');
$timeStamp = date("g:i a")." on ".date("j F, Y");
$date = date('d-m-Y');

		$status = true;
		$error = '';
		
		$query1 ="INSERT INTO `z_desk_reviews`(`outlet`, `date`, `timestamp`, `name`, `mobile`, `email`, `review`, `stars`, `comments`) VALUES ('{$outlet}', '{$date}',   '{$timeStamp}', '{$data['userName']}','{$data['userMobile']}', '{$data['userEmail']}','{$review}', '{$rating}', '{$comm}')";
		
		mysql_query($query1);
			
		//Send SMS to customer
		$kopper_name = substr($data['userName'], 0, 15);
		$kopper_mobile = $data['userMobile'];
		
		//Custom Greet
		if($rating == 5){
			$greet = "Great to know that you had an awesome experience with us today. Hope to see you again!";
		}
		else if($rating == 3 || $rating == 4){
			$greet = "We've noted down your feedback and assure you a better experience on your next visit. See you soon again!";
		}
		else{
			$greet = "Sorry to hear, we couldn't meet your expectations today. Promising you an awesome time on your next visit.";
		}
				
		$message = "Thank you ".$kopper_name." for dining with us today. ".$greet;
		//vegaSendSMS($data['userMobile'], $raw_msg);	
		
		
		//Message to Manager
		if($rating < 3){
			
			$notesSub = "";
			
			if($flag_food == 'true'){
				$notesSub = "food";
			}
			
			if($flag_time == 'true'){
				if($notesSub == "")
					$notesSub = "serving time";
				else
					$notesSub = $notesSub.", serving time";
			}
			
			if($flag_service == 'true'){
				if($notesSub == "")
					$notesSub = "overall service";
				else
					$notesSub = $notesSub.", overall service";
			}
			
						
			if($notesSub == "")	
				$notes = "Nothing Mentioned";
			else
				$notes = "Not satisfied with ".$notesSub;
	
			
			//$raw_msg = $kopper_name." (Mob. ".$kopper_mobile.") rated ".$rating." for Table # ".$table_no.", attended by ".$kopper_staff.". Note: ".$notes;
			//vegaSendSMS('9035057515', $raw_msg);			
		}


$output = array(
	"status" => $status,
	"error" => $error			
);

echo json_encode($output);

?>
