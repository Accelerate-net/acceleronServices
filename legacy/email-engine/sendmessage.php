<?php
define('INCLUDE_CHECK', true);

header('Access-Control-Allow-Origin: https://abhijithcs.in'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');


require 'mail.php';

    $name	 	= $_POST['name'];
    $content 	= $_POST['content'];
    $mail	 	= $_POST['email'];
    $phone	 	= $_POST['mobile'];
    
    $destination = "writetoabhijith@gmail.com";
    
    if(isset($_POST["destination"]) && $_POST["destination"] != ""){
        $destination = $_POST["destination"];
    }
    
    if($destination != "writetoabhijith@gmail.com"){
        if(!isset($_POST["authkey"]) || $_POST["authkey"] != "YWJoaWppdGhjczo5MDQzOTYwODc2"){
            die("Error");
        }
    }

	date_default_timezone_set('Asia/Calcutta');
	$timeStamp = date('H:i d M Y');

	$briefs = $content."\n\nRegards,\n".$name."\nM: ".$phone;

	$forSub = $name."'s Message";

	mailer($name, $destination, $forSub , $briefs, $mail);
?>




