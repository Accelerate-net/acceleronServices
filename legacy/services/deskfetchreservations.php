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
	$outlet= $tokenid['outlet'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Token is tampered"
	);
	die(json_encode($output));
}


/* sample data 
$sample = '{ "status": true, "error": "", "message": "Reservations on 03-05-2018", "response": [{ "date": "03-05-2018", "time": "2:27 pm", "timeLapse": "", "session": "Lunch", "id": "12920", "count": "10", "user": "Abhijith", "mobile": "9043960876", "email": "-", "isBirthday": false, "isAnniversary": false, "status": "Received", "statusCode": "2", "comments": "Nothing" }, { "date": "03-05-2018", "time": "12:59 pm", "timeLapse": "2 hrs ago", "session": "Lunch", "id": "12919", "count": "3", "user": "Baisakhi Nayak", "mobile": "8093589389", "email": "-", "isBirthday": false, "isAnniversary": false, "status": "Seated", "statusCode": "2", "comments": "-" }, { "date": "03-05-2018", "time": "6:27 pm", "timeLapse": "", "session": "Lunch", "id": "12920", "count": "4", "user": "Anas Jafry", "mobile": "9884179675", "email": "-", "isBirthday": false, "isAnniversary": false, "status": "Received", "statusCode": "1", "comments": "-" }, { "date": "03-05-2018", "time": "6:27 pm", "timeLapse": "", "session": "Tea", "id": "12920", "count": "5", "user": "Hamdan", "mobile": "9884179675", "email": "-", "isBirthday": false, "isAnniversary": false, "status": "Received", "statusCode": "0", "comments": "-" }], "sessionSummary": [{ "sessionName": "Lunch", "activeCount": 0, "doneCount": 3, "activePAX": 0, "donePAX": 17 }, { "sessionName": "Dinner", "activeCount": 0, "doneCount": 0, "activePAX": 0, "donePAX": 0 }, { "sessionName": "Tea", "activeCount": 1, "doneCount": 0, "activePAX": 5, "donePAX": 0 }] }';


if(isset($_POST['date'])){

$sample = '{ "status": true, "error": "", "message": "Reservations on 03-05-2018", "response": [], "sessionSummary": [{ "sessionName": "Lunch", "activeCount": 0, "doneCount": 0, "activePAX": 0, "donePAX": 0 }, { "sessionName": "Dinner", "activeCount": 0, "doneCount": 0, "activePAX": 0, "donePAX": 0 }, { "sessionName": "Tea", "activeCount": 0, "doneCount": 0, "activePAX": 0, "donePAX": 0 }] }';

}



die(($sample));

*/


$status = false;
$error = 'No upcoming reservations found';
$bookings = "";
$searchKey = $_POST['key'];
if($searchKey == ""){
	$searchKey = date('d-m-Y');
}

$limiter = "";
if(isset($_POST['id'])){
	$limiter = " LIMIT  {$_POST['id']}, 10";	
}


$active_dinner_count = 0; 
$active_lunch_count = 0; 
$done_dinner_count = 0; 
$done_lunch_count = 0; 

$active_dinner_PAX = 0; 
$active_lunch_PAX = 0; 
$done_dinner_PAX = 0; 
$done_lunch_PAX = 0; 
		
	//Case 1: Search with Name
	if(!$status && $searchKey != ""){		
		
		$main = mysql_query("SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `userName` LIKE '%{$searchKey}%' ORDER BY `id`".$limiter);
		$error = "SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `status` < 5 AND `userName` LIKE %'{$searchKey}'% ORDER BY `id`";
		while($rows = mysql_fetch_assoc($main)){	
			$status = true;
			$error = "";
			$resultKey = "Similar Names Found";
			$myLapsedTime = "";
			
			if($rows['status'] == 0){
				$my_status = "Received";
			}
			else if($rows['status'] == 1){
				$my_status = "Seated";
				
				$time_lapse_check = mysql_fetch_assoc(mysql_query("SELECT `seatedTime` FROM `z_desk_tables` WHERE `allocatedBookingID`='{$rows['id']}' AND `branch`='{$outlet}'"));
				if($time_lapse_check['seatedTime']){
					$myLapsedTime = get_time_ago($time_lapse_check['seatedTime']);
				}
			}
			else if($rows['status'] == 2){
				$my_status = "Completed";
			}
			else if($rows['status'] == 4){
				$my_status = "Billed";
			}			
			else if($rows['status'] == 5){
				$my_status = "Cancelled";
			}
			else if($rows['status'] == 6){
				$my_status = "No Show";
			}
			else{
				$my_status = "-";
			}
		
			
				$isLunch = true;
				if((int)$rows['time'] > 1600){
					$isLunch = false;
					if($rows['status'] == 0){
						$active_dinner_count++;
						$active_dinner_PAX += $rows['count'];
					}
					else{
						$done_dinner_count++;
						$done_dinner_PAX += $rows['count'];
					}										
				}
				else{
					if($rows['status'] == 0){
						$active_lunch_count++;
						$active_lunch_PAX += $rows['count'];
					}
					else{
						$done_lunch_count++;
						$done_lunch_PAX += $rows['count'];
					}
				}	
				
			
			$bookings[] = array(
				"date" => $rows['date'],
				"time" => date("g:i a", strtotime($rows['time'])),
				"timeLapse" => $myLapsedTime,
				"session" => "SESSION_TO_BE_ADDED",
				"id" => $rows['id'],
				"count" => $rows['count'],
				"user" => $rows['userName'],
				"mobile" => $rows['userID'],
				"email" => $rows['userEmail'] != ""? $rows['userEmail'] : "-",
				"isBirthday" => $rows['isBirthday'] == 1? true: false,
				"isAnniversary" => $rows['isAnniversary'] == 1? true: false,
				"statusCode" => $rows['status'],
				"status" => $my_status,
				"statusCode" => $rows['status'],
				"comments" => $rows['comments'] != ""? $rows['comments'] : "-"	
			);	
		}
	}
	
	//Case 2: Search with Mobile
	if(!$status && $searchKey != ""){		
		
		$main = mysql_query("SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `userID` = '{$searchKey}' ORDER BY `id`".$limiter);
		$error = "SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}' AND `status` < 5 AND `userName` LIKE %'{$searchKey}'% ORDER BY `id`";
		while($rows = mysql_fetch_assoc($main)){	
			$status = true;
			$error = "";
			$resultKey = "Results found for ".$searchKey;
			$myLapsedTime = "";
			
			if($rows['status'] == 0){
				$my_status = "Received";
			}
			else if($rows['status'] == 1){
				$my_status = "Seated";

				$time_lapse_check = mysql_fetch_assoc(mysql_query("SELECT `seatedTime` FROM `z_desk_tables` WHERE `allocatedBookingID`='{$rows['id']}' AND `branch`='{$outlet}'"));
				if($time_lapse_check['seatedTime']){
					$myLapsedTime = get_time_ago($time_lapse_check['seatedTime']);
				}				
			}
			else if($rows['status'] == 2){
				$my_status = "Completed";
			}
			else if($rows['status'] == 5){
				$my_status = "Cancelled";
			}
			else if($rows['status'] == 6){
				$my_status = "No Show";
			}
			else{
				$my_status = "-";
			}
			
			
				$isLunch = true;
				if((int)$rows['time'] > 1600){
					$isLunch = false;
					if($rows['status'] == 0){
						$active_dinner_count++;
						$active_dinner_PAX += $rows['count'];
					}
					else{
						$done_dinner_count++;
						$done_dinner_PAX += $rows['count'];
					}										
				}
				else{
					if($rows['status'] == 0){
						$active_lunch_count++;
						$active_lunch_PAX += $rows['count'];
					}
					else{
						$done_lunch_count++;
						$done_lunch_PAX += $rows['count'];
					}
				}	
				
				
				
			
			$bookings[] = array(
				"date" => $rows['date'],
				"time" => date("g:i a", strtotime($rows['time'])),
				"timeLapse" => $myLapsedTime,
				"session" => "SESSION_TO_BE_ADDED",
				"id" => $rows['id'],
				"count" => $rows['count'],
				"user" => $rows['userName'],
				"mobile" => $rows['userID'],
				"email" => $rows['userEmail'] != ""? $rows['userEmail'] : "-",
				"isBirthday" => $rows['isBirthday'] == 1? true: false,
				"isAnniversary" => $rows['isAnniversary'] == 1? true: false,
				"statusCode" => $rows['status'],
				"status" => $my_status,
				"statusCode" => $rows['status'],
				"comments" => $rows['comments'] != ""? $rows['comments'] : "-"	
			);	
		}
	}
	
	
	
	
	//Case 0: By Date (Default - Today)
	if(!$status){		
		$queryDate = $searchKey; 	
				
			$main = mysql_query($query = "SELECT * FROM `z_reservations` WHERE `outlet`='{$outlet}'  AND `status` < 5  AND `date`='{$queryDate}' ORDER BY status, time");
			while($rows = mysql_fetch_assoc($main)){	
				$status = true;
				$error = "";
				$resultKey = "Reservations on ".$searchKey;
				$myLapsedTime = "";
				
				if($rows['status'] == 0){
					$my_status = "Received";
				}
				else if($rows['status'] == 1){
					$my_status = "Seated";

					$time_lapse_check = mysql_fetch_assoc(mysql_query("SELECT `seatedTime` FROM `z_desk_tables` WHERE `allocatedBookingID`='{$rows['id']}' AND `branch`='{$outlet}'"));
					if($time_lapse_check['seatedTime']){
						$myLapsedTime = get_time_ago($time_lapse_check['seatedTime']);
					}					
				}
				else if($rows['status'] == 2){
					$my_status = "Completed";
				}
				else if($rows['status'] == 5){
					$my_status = "Cancelled";
				}
				else if($rows['status'] == 6){
					$my_status = "No Show";
				}
				else{
					$my_status = "-";
				}
				
				
				$isLunch = true;
				if((int)$rows['time'] > 1600){
					$isLunch = false;
					if($rows['status'] == 0){
						$active_dinner_count++;
						$active_dinner_PAX += $rows['count'];
					}
					else{
						$done_dinner_count++;
						$done_dinner_PAX += $rows['count'];
					}										
				}
				else{
					if($rows['status'] == 0){
						$active_lunch_count++;
						$active_lunch_PAX += $rows['count'];
					}
					else{
						$done_lunch_count++;
						$done_lunch_PAX += $rows['count'];
					}
				}	
				
									
				
				$bookings[] = array(
					"date" => $rows['date'],
					"time" => date("g:i a", strtotime($rows['time'])),
					"timeLapse" => $myLapsedTime,
					"session" => "SESSION_TO_BE_ADDED",
					"id" => $rows['id'],
					"count" => $rows['count'],
					"user" => $rows['userName'],
					"mobile" => $rows['userID'],
					"email" => $rows['userEmail'] != ""? $rows['userEmail'] : "-",
					"isBirthday" => $rows['isBirthday'] == 1? true: false,
					"isAnniversary" => $rows['isAnniversary'] == 1? true: false,
					"status" => $my_status,
					"statusCode" => $rows['status'],
					"comments" => $rows['comments'] != ""? $rows['comments'] : "-"	
				);
		
			}	
	}
	



function get_time_ago($time)
{
    $time_difference = time() - $time;

    if( $time_difference < 1 ) { return '1s ago'; }
    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hrs',
                60                      =>  'min',
                1                       =>  's'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $time_difference / $secs;

        if( $d >= 1 )
        {
            $t = round( $d );

            return $t.' '.$str.' ago';
        }
    }
}



$output = array(
	"status" => $status,
	"error" => $error,
	"message" => $resultKey,
	"response" => $bookings,
	"activeLunchCount" => $active_lunch_count,
	"activeDinnerCount" => $active_dinner_count,
	"doneLunchCount" => $done_lunch_count,
	"doneDinnerCount" => $done_dinner_count,
	"activeLunchPAX" => $active_lunch_PAX,
	"activeDinnerPAX" => $active_dinner_PAX,
	"doneLunchPAX" => $done_lunch_PAX,
	"doneDinnerPAX" => $done_dinner_PAX		
);

echo json_encode($output);

?>
