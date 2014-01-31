<?php
session_start();
if(isset($_SESSION['user'])){
	header('Location: index.php');
	exit;
}

include_once('sbnetadmin/db_func.php');
tep_db_connect();
if(isset($_POST['username'], $_POST['password']))
{	
	$UserName = trim($_POST['username']);
	$Password = md5(trim($_POST['password']));

	$query = "SELECT * 
			  FROM sb_signups 
			  WHERE `username` = '".$UserName."'
			  AND `password` = '".$Password."'";
    $result = mysql_query($query);
    if(@mysql_num_rows($result)<1){
	  echo 1;
	}
	else{
		$_SESSION['user'] = array( 'signup_id' => mysql_result($result,0,"signup_id"),
								 	'username' => mysql_result($result,0,"username")
								 	);
		echo 0;
	}
}else{
	header('Location: index.php');
	exit;
}