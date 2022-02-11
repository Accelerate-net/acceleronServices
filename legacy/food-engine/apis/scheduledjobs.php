<?php
error_reporting(0);

die('DIED');

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//SMS Pending List
$not_done_sms = mysql_query("SELECT `id`, `content` FROM `z_smsmessenger` WHERE `isDone` = 0");
while($campaign = mysql_fetch_assoc($not_done_sms)){

	$post_msg = $campaign['content'];
	$post_uid = $campaign['id'];

	$total_target_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `z_smsdeliveryreports` WHERE `status` = 0 AND `id`='{$post_uid}'"));
	$total_target = $total_target_check['total'];
	$total_splits = ceil($total_target/200);
	
	$round = 0;
	$total_count = 0;
	while($round < $total_splits){
		
		$split_start = $round*200;
		$split_end = $split_start + 200;	
	
		$xmlData = '
		<SMS>
		<Account Name="support@accelerate.net.in" Password="Abhijith*003" Test="1" Info="1" JSON="0">
		<Sender From="ZAITON" rcpurl="https://www.zaitoon.online/services/smscallback.php">
		<Messages>
		';
		
		$user_fetch = mysql_query("SELECT `user` FROM `z_smsdeliveryreports` WHERE `id`='{$post_uid}' AND `status` = 0 LIMIT ".$split_start.", ".$split_end);
		while($user = mysql_fetch_assoc($user_fetch)){
			$xmlData = $xmlData.'<Msg ID="'.$post_uid.'" Number="91'.$user['user'].'"> <Text>'.rawurlencode($post_msg).'</Text> </Msg>';	
		}

		$xmlData = $xmlData.'
		</Messages>
		</Sender>
		</Account>
		</SMS>'; 
		
		
		$post = 'data='. urlencode($xmlData); 
		$url = "https://api.textlocal.in/xmlapi.php";
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_POST ,1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS ,$post); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); 
		$data = curl_exec($ch); 
		curl_close($ch);
		echo $data; 

		$round++;
break;
	}

}

?>