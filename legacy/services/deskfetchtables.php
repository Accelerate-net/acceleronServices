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
	$branch = $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}

$mytime = date("g:i a");

$error = "No Tables found. Add a table and try again.";
$status = false;

$freeingList = [];

$all_sections = mysql_query("SELECT `section` FROM `z_desk_table_sections` WHERE `branch`='{$branch}'");
$complete_list = [];
while($sec = mysql_fetch_assoc($all_sections)){
	$all = mysql_query("SELECT * FROM `z_desk_tables` WHERE `section`= '{$sec['section']}' AND `branch`='{$branch}'");
		
	$list = [];
	while($role = mysql_fetch_assoc($all))
	{
	
	
		//Occupant Check
		$occupant_check = mysql_fetch_assoc(mysql_query("SELECT `userName`, `userID`,`count` FROM `z_reservations` WHERE `id`='{$role['allocatedBookingID']}' AND `outlet`='{$branch}'"));
		
		if($occupant_check['count'] == 1){
			$brief = $occupant_check['userName'];
		}
		else if($occupant_check['count'] > 1){
			$brief = $occupant_check['userName'].' + '.($occupant_check['count']-1);
		}
		else{
			$brief = 'Free';
		}
		
		/* Tables to be free in short time - already 45min spent */
		$mytime_difference = time() - $role['seatedTime'];
		if($mytime_difference > 3000 && $brief != 'Free'){ // Greater than 50 mins (50 * 60 seconds)
			$freeingList [] = $role['name'];
		}		
		
		$data = array(
		   	"reservationID" => $role['allocatedBookingID'],
			"name" => $occupant_check['userName'],
			"mobile" => $occupant_check['userID'],
			"count" => $occupant_check['count'],
			"seatedTime" => $role['seatedTime'] ? get_time_ago($role['seatedTime']) : ""
		);
	
	
		$list [] = array(
			'name' => $role['name'],
			'capacity' => $role['capacity'],
			'status' => $role['status'],
			'occupant' => $brief,
			'occupantData' => $data
		);
			
		$error = "";
		$status = true;
	}
	
	$complete_list[] = array(
		'sectionName' => $sec['section'],
		'tables' => $list
	);


}


function get_time_ago($time)
{
    $time_difference = time() - $time;

    if( $time_difference < 1 ) { return 'less than 1 second ago'; }
    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $time_difference / $secs;

        if( $d >= 1 )
        {
            $t = round( $d );

            return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}





	$output = array(
		'status' => $status,
		'error' => $error,
		'response' => $complete_list,
		'time' => $mytime,
		'freeingList' => $freeingList
	);
		
		

echo json_encode($output);
		
?>
