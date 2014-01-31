<?php

//TODO: move this functionality under template.php
session_start();
include_once('sbnetadmin/db_func.php');
tep_db_connect();
if(isset($_POST['choice'], $_REQUEST['contest_id']))
{	

	$contest_id = (int)$_REQUEST['contest_id'];
	$customer_id = $_SESSION['user']['signup_id'];

	foreach($_POST['choice'] as $event_id => $choice_id){
		$event_id = (int)$event_id;
		$choice_id = (int)$choice_id;

		$query = "INSERT INTO `customer_choices`(contest_id,event_id,choice_id,customer_id) 
					  VALUES($contest_id,$event_id, $choice_id,$customer_id)";
			$result = mysql_query($query);
		}	
}