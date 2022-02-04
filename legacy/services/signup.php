<?php

/*** YET TO ADD "NO RESULTS" SCENARIO ***/

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

define('INCLUDE_CHECK', true);
require 'connect.php';

$_POST = json_decode(file_get_contents('php://input'), true);

$mobile ="9884179675";//mysql_real_escape_string($_POST['mobile']);
$name="Anas Jafry"; //$_POST['name'];
$email="anasrazak@gmail.com"; //$_POST['email'];
date_default_timezone_set('Asia/Calcutta');
$timeStamp = date("g:i a, j F, Y");
$query = "INSERT INTO `z_users`(`mobile`, `name`, `isVerified`, `isBlocked`, `lastLogin`, `isFeedback`, `memberType`, `savedAddresses`, `email`, `password`) VALUES ('{$mobile}','{$name}',0,0,'{$timeStamp}',0,'FRESHER','','{$email}','password')";
mysql_query($query);
echo ($query);
?>