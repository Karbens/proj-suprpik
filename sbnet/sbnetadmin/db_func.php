<?php

//set time zone to eastern time zone beacause all lines use eastern time
date_default_timezone_set('America/New_York');

function tep_db_connect() {

	$db_link = mysql_connect('localhost', 'karbensc_superp', '21+~*lBsBLv#');
   	mysql_select_db('karbensc_superpicks_sbnet');
	if (!$db_link) 
	{
	  die('Could not connect: ' . mysql_error());
	}
 }

function tep_db_close() {

    $result = @mysql_close();
    
    return $result;
}

function get_sb_signups($all='') {

	/*if( eregi("freecontests.com",$_SERVER['SERVER_NAME']) )
	{
      	$sb_link = mysql_connect('localhost', 'root', 'yogster');
      	mysql_select_db('sb_lines');
	}else
	{
	 	$sb_link = @mysql_connect('192.168.29.102:3306', 'joesbnet', 'dathUhuch8tr');//main site connection
		@mysql_select_db('sbnetdb');
	}*/
	tep_db_connect();
	
	if( $all == 'all')
	{
		$que = "SELECT * 
				FROM `sb_signups` WHERE 1
				ORDER BY `signup_id`";
		$query = mysql_query($que);
		$ret = array();
		if(@mysql_num_rows($query) > 0)
		{
			while($res = mysql_fetch_assoc($query))
			{
				$ret[] = $res;
			}
		}
	}else
	{
		$que = "SELECT `username`, `email` 
				FROM `sb_signups` WHERE 1";
		$query = mysql_query($que);
		$ret = array();
		if(@mysql_num_rows($query) > 0)
		{
			while($res = mysql_fetch_row($query))
			{
				$id = $res[0];
				$ret[$id] = $res[1];
			}
		}
	}//end of if( $all == 'all')
	
	@mysql_close($sb_link);
	
	return $ret;
}

function get_contest_name($contest_id){
	$query = "select contest_name from contests where contest_id =".$contest_id;
	//echo $query;
	$result = mysql_query($query);
	if(@mysql_num_rows($result) > 0){
		while($res = mysql_fetch_row($result))
		{
			$contest_name = $res[0];
			//print_r($res);
			return $contest_name;
		}
	}
}
