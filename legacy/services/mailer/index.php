<?php
session_start();
include 'Qassim_HTTP.php';
include 'config.php';
/* By Qassim Hassan, http://wp-time.com/send-email-via-gmail-api-using-php/ */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Send email via Gmail API using PHP</title>
<meta name="description" content="Send email via Gmail API using PHP.">
</head>
<body>

<?php if( isset($_SESSION['access_token']) ) : // if user is logged in ?>




	<h1>Hello <?php echo $_SESSION['access_token']; echo $_SESSION["emailAddress"]; ?></h1>

	<?php
	if( isset($_SESSION['sent']) ){ // if message has been sent
		echo '<h3 style="color:red;">'.$_SESSION['sent'].'</h3>';
		unset($_SESSION['sent']);
	}
	?>

	<form method="POST" action="send.php">
		<p>Subject: <input name="subject" type="text" value="" placeholder="Enter subject"></p>
		<p>To: <input name="to" type="text" value="" placeholder="Enter email"></p>
		<p>Message: <textarea name="message" placeholder="Enter message"></textarea></p>
		<p><input type="submit" name="submit" value="Send!"></p>
	</form>

	<p><a href="logout.php">Logout?</a></p>


<?php else : // if user is not logged in, show sign in link ?>


	<a href="<?php echo $login_url; ?>">Sign in with Gmail</a>


<?php endif; ?>

</body>
</html>