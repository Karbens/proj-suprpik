<?php 
	 session_start();
	 unset($_SESSION['user']['signup_id']);
     session_destroy();

	header('Location: index.php');
	exit;
