<?php
error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';


//Encryption Validation
if(!isset($_POST['name']) || $_POST['name'] == ""){
	die(' <script>window.location = "https://www.zaitoon.restaurant/index.html?error=Please enter your Name correctly";</script> ');
}

if(!isset($_POST['email']) || $_POST['email'] == ""){
	die(' <script>window.location = "https://www.zaitoon.restaurant/index.html?error=Please enter your Email Address correctly";</script> ');
}

if(!isset($_POST['mobile']) || $_POST['mobile'] == ""){
	die(' <script>window.location = "https://www.zaitoon.restaurant/index.html?error=Please enter your Mobile Number correctly";</script> ');
}


if(!isset($_POST['comments']) || $_POST['comments'] == ""){
	die(' <script>window.location = "https://www.zaitoon.restaurant/index.html?error=Please add some comments";</script> ');
}


$url = 'https://www.google.com/recaptcha/api/siteverify';
$ch = curl_init();
$fields_string = "response=".$_POST['g-recaptcha-response']."&secret=6LerF3UUAAAAAPFyK7OlZJiGAUBN71gW752bT-D3";


//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

//execute post
$result = curl_exec($ch);

$result = str_replace(' ', '_', $result);
$result = str_replace('"', '_', $result);

if(substr($result, 15, 4) != "fals"){

	date_default_timezone_set('Asia/Calcutta');
	$today = date("g:i a").' '.date("d-m-Y");
	
	$comment = mysql_real_escape_string($_POST['comments']);
	$name = mysql_real_escape_string($_POST['name']);
	$email = mysql_real_escape_string($_POST['email']);
	$mobile = mysql_real_escape_string($_POST['mobile']);
	
	if(!is_numeric ($mobile)){
		die(' <script>window.location = "https://www.zaitoon.restaurant/index.html?error=Invalid Mobile Number entered";</script> ');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        	die(' <script>window.location = "https://www.zaitoon.restaurant/index.html?error=Invalid Email entered";</script> ');
        }
	
	
	mysql_query("INSERT INTO `z_helpdesk`(`isAuthentic`, `mobile`, `name`, `email`, `comment`, `type`, `remarks`, `date`) VALUES (0, '{$mobile}', '{$name}', '{$email}', '{$comment}', 'GENERAL', 'ON WEBSITE', '{$today}')");
	
	echo '<script>window.location = "https://www.zaitoon.restaurant/index.html?success=Thank you '.$name.' for contacting us. We will reach to you via Phone or E-Mail soon"</script>';	

}
else{
	echo '<script>window.location = "https://www.zaitoon.restaurant/index.html?error=Failed to receive your mail. Please tick <strong>Im not Robot</strong> and verify the captcha, before sending."</script>';
}

//close connection
curl_close($ch);

?>

