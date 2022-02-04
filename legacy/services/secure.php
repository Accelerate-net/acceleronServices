<?php
	if(!defined('SECURE_CHECK')) die('You are not authorized to access this page.');

	$encryptionMethod = "AES-256-CBC";
	$secretHash = "7a6169746f6f6e746f6b656e";
	$tokenExpiryDays = 6; //5 days

?>
