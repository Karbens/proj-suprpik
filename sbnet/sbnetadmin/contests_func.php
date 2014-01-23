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