<?php


function get_templates(){
	require_once('template.php');

	return $templates;
}

function get_contests($contest_id=0) {

	$que = "SELECT * FROM `contests`";
	if($contest_id > 0)$que .= " WHERE `contest_id` = ".$contest_id;
	
	$que .= " ORDER BY `contest_id` ASC";
	
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_assoc($query))
		{
			$ret[] = $res;
		}
	}
	return $ret;
}

function get_customer_choices($contest_id=null,$customer_id = null){

      $que = "SELECT * FROM customer_choices ";
      $que .= "LEFT JOIN events_choices on customer_choices.ec_id=events_choices.ec_id ";
    	$que .= "LEFT JOIN events on events_choices.event_id = events.event_id ";
      if($contest_id > 0)$que .= " WHERE events.contest_id = ".$contest_id." AND customer_choices.customer_id = ".$customer_id;
      
    	$query = mysql_query($que);
    	$ret = array();
    	if(@mysql_num_rows($query) > 0)
    	{
    		while($res = mysql_fetch_assoc($query))
    		{
    			$ret[] = $res;
    		}
    	}
    	return $ret;
    
}