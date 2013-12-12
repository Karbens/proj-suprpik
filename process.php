<?php
if( isset( $_POST['email'] ) )
{
	$to_email = 'thebelt@superpicks.com';
	$subject = "Email from SuperPicks.com";
	$body = "A new user has submit their email address on your coming soon web page. The email is ".$_POST['email']." save their email to keep them updated with the website.";
	mail($to_email, $subject, $body);
	echo 'success';
}
else
{
	echo 'failed';
}
?>