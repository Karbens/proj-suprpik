<?php
//set time zone to eastern time zone beacause all lines use eastern time
date_default_timezone_set('America/New_York');

function tep_db_connect() {
	/*if( eregi("freecontests.com",$_SERVER['SERVER_NAME']) )
	{
      	$db_link = mysql_connect('localhost', 'root', 'yogster');
      	mysql_select_db('sb_contests');
	}else
	{
	 	$db_link = mysql_connect('192.168.29.102', 'freecontsbadm11', '59FwnzyeHRg7');
   		mysql_select_db('freecontsbdb');
	}*/
	$db_link = mysql_connect('localhost', 'super100_dbmain', 'FgDvr436oy');
   	mysql_select_db('super100_contests');
	if (!$db_link) 
	{
	  die('Could not connect: ' . mysql_error());
	}
 }

function tep_db_close() {

    $result = @mysql_close();
    
    return $result;
}

function get_contests($contest_id=0) {

	$que = "SELECT * FROM `contests` WHERE 1";
	if($contest_id > 0)$que .= " AND `contest_id` = ".$contest_id;
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

function get_streakers($streak = '', $limit = '') {

	$que = 'SELECT * FROM `current_streakers` ';
	if($streak != '')$que .= ' WHERE `streak` = '.$streak;
	$que .= ' ORDER BY `streak` desc, `customer_id`';
	if($limit>0)$que .= ' LIMIT '.$limit;
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

function get_full_streakers($streak = '') {

	$que = "SELECT * FROM `current_streakers` ";
	if($streak != '')$que .= " WHERE `streak` = ".$streak;
	$que .= " ORDER BY `streak` desc, `customer_id`";
	$query = mysql_query($que);
	$ret = array();
	$today = date('Ymd');
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_assoc($query))
		{
			$last_pick_array = get_last_pick($res['customer_id']);
			$last_pick = $last_pick_array['contest_date'];
			$event_result = $last_pick_array['event_result'];
			$lastDate = '';
			$deadline = '';
			if($last_pick != '')
			{
				$lastTime = strtotime($last_pick.' 12:00');
				$lastDate = date('d/m/y',$lastTime);
				$deadTime = $lastTime+(86400*5);
				$deadDate = date('Ymd',$deadTime);
				$deadline = date('d/m/y',$deadTime);
				if($today == $deadDate)$deadline = '<span style="color:orange;">'.$deadline.'</span>';
				if($today > $deadDate)$deadline = '<span style="color:red;">'.$deadline.'</span>';
			}
			$res['last_pick'] = $lastDate;
			$res['deadline'] = $deadline;
			$res['status'] = ($event_result == 0) ? 'Pending' : '';
			$ret[] = $res;
		}
	}
	return $ret;
}

function get_streakers_count($streak = '') {

	$que = "SELECT count(`streak`) FROM `current_streakers` ";
	if($streak != '')$que .= " WHERE `streak` = ".$streak;
	$query = mysql_query($que);
	$res = @mysql_fetch_row($query);
	$ret = $res[0] + 0;
	return $ret;
}

function update_streaker($customer_id, $date) {
	$que = "SELECT `customer_id` FROM `current_streakers` 
			WHERE `customer_id` = '".$customer_id."'";
	$query = mysql_query($que);
	if(@mysql_num_rows($query) > 0)
	{
		$streak = get_streak($customer_id);
		@mysql_query("UPDATE `current_streakers`
					  SET `streak` = '".$streak."',
					 	  `last_updated` = '".$date."'
					  WHERE `customer_id` = '".$customer_id."'");
	}
	else
	{
		$streak = get_streak($customer_id);
		@mysql_query("INSERT INTO `current_streakers` (
					 `customer_id` ,
					 `streak` ,
					 `last_updated`
					 )
					 VALUES (
					 '".$customer_id."', '".$streak."', '".$date."'
					 )");
	}
}

function get_streak($customer_id)
{
	//get last date
	$lque = @mysql_query("SELECT `event_date`
						 FROM `events`
						 WHERE `contest_id` = '1'
						 AND `event_result` != '0'
						 ORDER BY `event_date` DESC
						 LIMIT 1");
	$lres = @mysql_fetch_row($lque);
	$last_date = ($lres[0] != '') ? $lres[0] : '2012-01-12';
	
	$que = "SELECT `contest_date`, `points` 
			FROM `contest_entries` 
			WHERE contest_id = 1
			AND `customer_id` = '".$customer_id."'
			AND `contest_date` <= '".$last_date."'
			ORDER BY `contest_date` DESC";
	$query = mysql_query($que);
	$ret = array();
	$r = 0;
	$check_date = date('Y-m-d', strtotime('-6 days') );
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_assoc($query))
		{
			if($r == 0)
			{
				$date = $res['contest_date'];
				if($date < $check_date)
				{
					return 0;
				}
				if($res['points'] == 0)return 0;
			}else
			{
				if($res['points'] == 0)break;
			}
			$ret[$r] = $res;
			
			$r++;
		}
	}
	$ret_count = count($ret);
	return $ret_count;
}

function update_streaker_board()
{
	$que = mysql_query("SELECT `customer_id` FROM `contest_entries`
						WHERE `contest_id` = 1
						GROUP BY `customer_id`");
	$now_date = date('Y-m-d H:i:s');
	while($res = mysql_fetch_row($que))
	{
		update_streaker($res[0], $now_date);
	}
}

function mail_streaker_board($ret_it = 0)
{
		$streakers = get_streakers();
		if( count($streakers) > 0)
		{
			$dque = mysql_query("SELECT `last_updated` FROM `current_streakers` ORDER BY `last_updated` DESC LIMIT 1");
		    $dres = mysql_fetch_row($dque);
			$message = '<br />Statistics for the <b>CURRENT STREAKERS</b>, last updated:'. date('l, F d, Y H:i:s', strtotime($dres[0])) .
					   '<br />'.
					   'Total Entrants: <b>' . get_streakers_count() . '</b><br />'.
					   'Total Streakers: <b>' . get_streakers_count('1') . '</b><br />';
			
				$message .= "<br>" .'STREAKERS:' . "<br>";
				$message .= '<table cellpadding="2" cellspacing="2" style="border: 1px solid #000000;">
							   
							   <tr bgcolor="#808080" style="color:#ffffff;">
							   	 <th width="50" align="center"> # </th>
							     <th width="200" align="center"> Customer ID </th>
								 <th width="50" align="center"> Streak </th>
							   </tr>
							 ';
				$skc = 1;
				foreach($streakers as $sk => $sv)
				{
					$bcol = '';
					if( ($skc%2) == 0 )
					{
						$bcol = ' bgcolor="#dcdcdc"';
					}
					$message .= '
							 <tr'.$bcol.'>
						   	   <td align="center">'.$skc.'</td>
						       <td align="center" nowrap>'.$sv['customer_id'].'</td>
							   <td align="center">'.$sv['streak'].'</td>
						     </tr>';
					$skc++;
				}
				$message .= '</table>';
			$cemail = 'joe@epm3ltd.com';
			$temail = 'jeff@epm3ltd.com';
			$nowDate = date('l, F d, Y H:i:s', strtotime($now_date));
			if($ret_it == 1)
			{
				return $message;
			}
			else
			{
			  mail($temail, "Statistics for the CURRENT STREAKERS, sent at ". $nowDate, $message, 'Content-Type: text/html; charset="iso-8859-15"' . "\n" . 'Content-Transfer-Encoding: 8bit' . "\n" . 'From: '.$cemail);
			}
			
		}//end of if( count($streakers) > 0)
}

function valid_contest($contest_id) {

	$que = "SELECT * FROM `contests` 
			WHERE `contest_id` = ".$contest_id;
	$query = mysql_query($que);
	$ret = FALSE;
	if(@mysql_num_rows($query) > 0)
	{
		$ret = TRUE;
	}
	return $ret;
}

function get_events($contest_id, $date, $time='')
{
	$que = "SELECT * FROM `events` 
			WHERE `contest_id` = ".$contest_id."
			AND `event_date` = '".$date."'";
	if($time != '')$que .= " AND `event_time` > '".$time."'";
	$que .=" ORDER BY `event_order`, `event_id`";
	//echo $que;
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


function get_choices($event_id) 
{
	$que = 'SELECT * FROM `events_choices` WHERE
			`event_id` = '.$event_id.'
			ORDER BY `ec_order`, `ec_id`';
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

function get_entries($contest_id, $contest_date)
{
	$que = "SELECT * FROM `contest_entries` 
			WHERE `contest_id` = ".$contest_id."
			AND `contest_date` = '".$contest_date."'";
	$cquery = mysql_query($que);
	$ccount = @mysql_num_rows($cquery)+0;
	$que .= " AND `points` > 0";
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_assoc($query))
		{
			$ret[] = $res;
		}
	}
	$retr = array();
	$retr['entries'] = $ret;
	$retr['total_count'] = $ccount;
	return $retr;
}

function insert_data($data)
{
	//set variables
	$cur_date = date('Y-m-d');
	$contest_date = $data['contestDate'];
	$contest_id = $data['contestID'];
	$servType = $data['servType'];
	$servValue = $dat['servValue'];
	$event_id = 0;
	
	if($servType == 'choice')
	{
		$event_id = $servValue;
		$ins2 = "INSERT INTO `events_choices` 
	  			(
				`ec_id` , `event_id` , `choice` , `ec_order`
				)
				VALUES 
				(
				NULL , '".$event_id."', '', '';
				)";
	}//end of if($servType == 'choice')
	elseif($servType == 'event')
	{
		if($contestDate > $curDate)
		{
			$ins1 = "INSERT INTO `events` 
					(
					`event_id` ,  `contest_id` , `event_date` , `event_desc` , `event_order`
					)
					VALUES 
					(
					NULL , '".$contest_id."', '".$contest_date."', '', ''
					)";
			if( @mysql_query($ins1) )
			{
			  $event_id = @mysql_insert_id();
			  $ins2 = "INSERT INTO `events_choices` 
			  			(
						`ec_id` , `event_id` , `choice` , `ec_order`
						)
						VALUES 
						(
						NULL , '".$event_id."', '', ''
						), (
						NULL , '".$event_id."', '', ''
						)";
			  @mysql_query($ins2);
			}
	  }//end of if(@mysql_query($cque) > 0)
	}//end of elseif($servType == 'event')
	
	return $event_id;
}

function get_contest_dates($contest_id)
{
	$que = 'SELECT `event_date` FROM `events` 
			WHERE `contest_id` = '.$contest_id.'
			GROUP BY `event_date`
			ORDER BY `event_date`';
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_row($query))
		{
			$ret[] = $res[0];
		}
	}
	return $ret;
}

function get_pick_pending($contest_id, $username)
{
	$que = "SELECT c.`contest_date`, c.`contest_time`, e.`event_desc`, s.`choice`
			FROM `contest_entries` c, `events_choices` s
			LEFT JOIN `events` e ON e.event_id = s.event_id
			WHERE c.entry_value = s.ec_id
			AND c.`contest_id` = ".$contest_id." 
			AND c.`customer_id` = '".$username."'
			ORDER BY c.`contest_date` DESC
			LIMIT 1";
	//echo $que.'<br>';
	$query = mysql_query($que);
	$res = array();
	if(@mysql_num_rows($query) > 0)
	{
		$res = mysql_fetch_assoc($query);
	}
	return $res;
}

function get_pick_history($contest_id, $username, $start = 0)
{
	$que = "SELECT c.`contest_date`, c.`contest_time`, c.`entry_value`,
				   c.`points`, e.`event_desc`, s.`choice`,  e.`event_result`
			FROM `contest_entries` c, `events_choices` s
			LEFT JOIN `events` e ON e.event_id = s.event_id
			WHERE c.entry_value = s.ec_id
			AND c.`contest_id` = ".$contest_id." 
			AND c.`customer_id` = '".$username."'
			ORDER BY c.`contest_date` DESC 
			LIMIT ".$start.",30";
	//echo $que.'<br>';
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

function get_pick_history_count($contest_id, $username)
{
	$que = "SELECT count(contest_id) as c_count
			FROM contest_entries
			WHERE contest_id = '".$contest_id."' 
			AND customer_id = '".$username."'";
	//echo $que.'<br>';
	$query = mysql_query($que);
	$res = mysql_fetch_row($query);
	$ret = $res[0];
	return $ret;
}

?>
