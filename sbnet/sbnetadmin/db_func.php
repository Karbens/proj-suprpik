<?php

//set time zone to eastern time zone beacause all lines use eastern time
date_default_timezone_set('America/New_York');

function tep_db_connect() {

	$db_link = mysql_connect('localhost', 'root', 'password');
   	mysql_select_db('super100_sbnet_new');
	if (!$db_link) 
	{
	  die('Could not connect: ' . mysql_error());
	}
 }

function tep_db_close() {

    $result = @mysql_close();
    
    return $result;
}

function get_contests(){
	
}