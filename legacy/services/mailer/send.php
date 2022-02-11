<?php

session_start();

/* By Qassim Hassan, http://wp-time.com/send-email-via-gmail-api-using-php/ */


include 'Qassim_HTTP.php';
include 'config.php';


if( isset($_POST['submit']) and $_SERVER['REQUEST_METHOD'] == "POST" and isset($_SESSION["access_token"]) ){
	$access_token = $_SESSION["access_token"];
	$url = "https://www.googleapis.com/gmail/v1/users/me/messages/send";
	$header = array('Content-Type: application/json', "Authorization: Bearer $access_token");

	if( empty($_POST['subject']) or empty($_POST['to']) or empty($_POST['message']) ){ // input validator
		echo "Please fill all fields.";
		return false;
	}

	$subject = $_POST['subject'];
	$to = $_POST['to'];
	$message = $_POST['message'];

	$line = "\n";
	$raw = "to: $to".$line;
	$raw .= "subject: $subject".$line.$line;
	$raw .= $message;

	$base64 = base64_encode($raw);
	$data = '{ "raw" : "'.$base64.'" }';
	$send = Qassim_HTTP(1, $url, $header, $data);

	if( !empty($send['id']) ){ // if message has been sent, will be redirect to index.php and show "Message has been sent!".
		$_SESSION['sent'] = "Message has been sent!";
		header("location: index.php");
	}else{ // if access token is expired or has some problems, will be logout!
		if( !empty($send['error']['errors'][0]['reason']) ){
			header("location: logout.php");
		}else{
			header("location: logout.php");
		}
	}
}

else{
	header("location: index.php");
}

?>