<?php

//TODO: move this functionality under template.php
session_start();
include_once('sbnetadmin/db_func.php');
tep_db_connect();
//print_r($_POST);
if(isset($_POST['choice'], $_REQUEST['contest_id']))
{	

	$contest_id = (int)$_REQUEST['contest_id'];
	$customer_id = $_SESSION['user']['signup_id'];

	foreach($_POST['choice'] as $event_id => $choice_id){
		$event_id = (int)$event_id;
		$choice_id = (int)$choice_id;

		$query = "INSERT INTO `customer_choices`(ec_id,customer_id,event_id,contest_id) 
					  VALUES($choice_id,$customer_id,$event_id,$contest_id)";
					  //echo $query;
			$result = mysql_query($query);
		}	
}