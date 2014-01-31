<?php

define('IS_FRONTEND', true);
//TODO: Add a indirect access token

//set time zone to eastern time zone
date_default_timezone_set('America/New_York');

function get_active_contests(){

	$now = date("Y-m-d H:i:s");

	$query = "SELECT * FROM `contests` WHERE `status`= 	'Online' AND `contest_publish_date` <= '$now' AND current_contest_date >
	 '$now'";
    $results = mysql_query($query);
    if(@mysql_num_rows($results)>0){
		$resultset = array();
		while ($row = mysql_fetch_assoc($results)) {
		  $resultset[] = $row;
		}
	}

	return $resultset;
}
