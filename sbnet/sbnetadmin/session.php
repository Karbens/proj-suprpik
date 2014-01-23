<?php
session_start();
if(empty($_SESSION['UserID']))
{
	header("Location: login.php");
	exit();
}
else
{
	$UserID=$_SESSION['UserID'];
	$UserName=$_SESSION['UserName'];
}
  include_once('db_func.php');
  tep_db_connect();
  
  include_once('contests_func.php');



function show_session_message(){
	echo $_SESSION['message'];
	unset($_SESSION['message']);
}