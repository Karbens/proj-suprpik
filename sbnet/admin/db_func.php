<?php
//set time zone to eastern time zone beacause all lines use eastern time
date_default_timezone_set('America/New_York');

$signUps = get_sb_signups();//sb.net signups array

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
	$sb_link = @mysql_connect('localhost', 'super100_dbmain', 'FgDvr436oy');//main site connection
	@mysql_select_db('super100_sbnetdb');
    if (!$sb_link) 
	{
	  die('Could not connect: ' . mysql_error());
	}
	
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

function contest_graded_result($contest_id, $date) {

	$que = "SELECT e.`event_result` , c.`choice`
			FROM `events` e
			LEFT JOIN `events_choices` c ON c.`ec_id` = e.`event_result`
			WHERE `contest_id` = ".$contest_id."
			AND `event_date` = '".$date."'
			AND `event_result` >0";
	$query = @mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_row($query))
		{
			$id = $res[0];
			$ret[$id] = $res[1];
		}
	}
	return $ret;

}

function get_last_pick($customer_id)
{
	$que = "SELECT c.`contest_date`, e.`event_result`
			FROM `contest_entries` c, `events_choices` s
			LEFT JOIN `events` e ON e.event_id = s.event_id
			WHERE c.entry_value = s.ec_id
			AND c.`contest_id` = 1 
			AND c.`customer_id` = '".$customer_id."'
			ORDER BY c.`contest_date` DESC
			LIMIT 1";
	//echo $que.'<br>';
	$query = mysql_query($que);
	$res = array('contest_date' => '', 'grade_value' => '');
	if(@mysql_num_rows($query) > 0)
	{
		$res = mysql_fetch_assoc($query);
	}
	return $res;
}

function get_last_contest_date($contest_id)
{
	$que = "SELECT `event_date` as end_date,`event_time` as end_time
			FROM `events`
			WHERE `contest_id` = ".$contest_id."
			ORDER BY `event_date` DESC
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


function get_streakers($streak = '',$limit='') {

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

function get_streakers_count($streak = 0) {

	$que = "SELECT count(`streak`) FROM `current_streakers` ";
	if($streak == 1)$que .= " WHERE `streak` > 0";
	$query = mysql_query($que);
	$res = @mysql_fetch_row($query);
	$ret = $res[0] + 0;
	return $ret;
}

function update_streaker($customer_id, $date) {
	$que = "SELECT `customer_id`, `best_streak` 
			FROM `current_streakers` 
			WHERE `customer_id` = '".$customer_id."'";
	$query = mysql_query($que);
	if(@mysql_num_rows($query) > 0)
	{
		$res = mysql_fetch_assoc($query);
		$best_streak = $res['best_streak'];
		$streak  = get_streak($customer_id);
		$upd_que = "UPDATE `current_streakers`
					  SET `streak` = '".$streak."',";
		if($streak > $best_streak)
		{
			$upd_que .= "   `best_streak` = '".$streak."',";
		}
		$upd_que .= "	`last_updated` = '".$date."'
					  WHERE `customer_id` = '".$customer_id."'";
		@mysql_query($upd_que);
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
	$ret = array();//return array
	
	$que = "SELECT * FROM `events` 
			WHERE `contest_id` = ".$contest_id."
			AND `event_date` = '".$date."'";
	if($time != '')$que .= " AND `event_time` > '".$time."'";
	$que .=" ORDER BY `event_order`, `event_id`";
	$query = mysql_query($que);
	
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_assoc($query))
		{
			$ret[] = $res;
		}
	}
	return $ret;
}

function set_events($contest_id, $date, $time='')
{
	$ret = array();//return array
	
	//check if contest online and if there's a delay
	$cinfo = get_contests($contest_id);
	if($cinfo['status'] == 'Offline')return $ret;
	if( $date == $cinfo[0]['current_contest_date'] && $cinfo[0]['delay_timestamp']>0 )
	{
		$nowtime = time();
		if($cinfo[0]['delay_timestamp'] > $nowtime)return $ret;
	}
	
	return get_events($contest_id, $date, $time);
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
	global $signUps;
	
	$que = "SELECT * FROM `contest_entries` 
			WHERE `contest_id` = ".$contest_id."
			AND `contest_date` = '".$contest_date."'";
	//echo $que;
	$cquery = mysql_query($que);
	$ccount = @mysql_num_rows($cquery)+0;
	$que .= " AND `points` > 0";
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_assoc($query))
		{
			//site of customer (sb.net or sb.com)
			$site = 'sportsbetting.com';
			$cust_id = $res['customer_id'];
			if( isset($signUps[$cust_id]) && $signUps[$cust_id] == $res['customer_email'] )
			{
				$site = 'sportsbetting.net';
			}
			$res['site'] = $site;
			$ret[] = $res;
		}
	}
	$retr = array();
	$retr['entries'] = $ret;
	$retr['total_count'] = $ccount;
	return $retr;
}


function get_contest_entries($contest_id, $contest_date)
{
	$que = "SELECT * FROM `contest_entries`
			WHERE `contest_id` = ".$contest_id."
			AND `contest_date` = '".$contest_date."'";
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

function entered_values_count($contest_id, $contest_date, $entry_values)
{
	if(trim($entry_values) == '')return 0;
	$que = "SELECT *
			FROM `events`
			WHERE `contest_id` =".$contest_id."
			AND `event_date` = '".$contest_date."'
			AND `event_result`
			IN ( ".$entry_values." )";
	$query = @mysql_query($que);
	$ret = @mysql_num_rows($query)+0;
	return $ret;
}

function check_additional_winners($contest_id, $contest_date, $start)
{
	$ge = get_events($contest_id, $contest_date);
	$ce = get_contest_entries($contest_id, $contest_date);
	$event_count = count($ge);
	$winners = array();
	foreach( $ce as $c)
	{
		$entry_values = $c['entry_value'];
		$v_count = entered_values_count($contest_id, $contest_date, $entry_values);
		if($v_count >= $start && $v_count < $event_count)
		{
			$winners[$v_count][] = $c;
		}
	}
	
	$message = '';
	for($i=$event_count;$i>=$start;$i--)
	{
		if( isset($winners[$i]) )
		{
				$mtext = ' ('.$i.'/'.$event_count.') ';
				$message .= "<br>" .'Users with'.$mtext.'correct answers ('.count($winners[$i]).'):' . '<br>';
				
				$message .= '<table border="1" cellpadding="2" cellspacing="2" width="600">
							 <tr>
							   <th width="45%" nowrap>User ID</th>
							   <th nowrap>User Email</th>
							 </tr>
							 ';
				foreach($winners[$i] as $en)
				{
					$message .= '
							 <tr>
							   <td nowrap>'.$en['customer_id'].'</td>
							   <td nowrap>'.$en['customer_email'].'</td>
							 </tr>';
				}
				$message .= '</table>';
		}else
		{
			$mtext = ' ('.$i.'/'.$event_count.') ';
			$message .= "<br>" .'Users with'.$mtext.'correct answers (0):' . '<br>';
			$message .= '<table border="1" cellpadding="2" cellspacing="2" width="600">
						 <tr>
						   <th width="45%" nowrap>User ID</th>
						   <th nowrap>User Email</th>
						 </tr>
						 <tr>
						   <td colspan="2" align="center" nowrap> None </td>
						 </tr>
						 </table>';
		}//end of if( isset($winners[$i]) )
	}//end of for($i=$j;$i>=$start;$i--)
	return $message;
}

function get_entry_choices($entry_value)
{
	$que = "SELECT `ec_id`, `choice`
			FROM `events_choices`
			WHERE ec_id
			IN (".$entry_value.")";
	$query = @mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_row($query))
		{
			$id = $res[0];
			$ret[$id] = $res[1];
		}
	}
	return $ret;
}


function get_entry_count($contest_id, $date)
{
	$que = "SELECT `entry_id`
			FROM `contest_entries`
			WHERE `contest_id` = ".$contest_id."
			AND `contest_date` = '".$date."'
			LIMIT 1";
	$query = @mysql_query($que);
	$count = @mysql_num_rows($query) + 0;
	return $count;
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


function check_current_contest_date($contest_id, $date)
{
	if($contest_id == 2)
	{
		if($date == '' || $date <= date('Y-m-d') )
		{
			$datetime = strtotime('2012-02-11 15:00:00');//end date for current contest
			if( date('l') == 'Wednesday' && date('H') >= 15 )
			{
				$datetime = strtotime(date('Y-m-d').' 12:00:00') + (3 * 86400);
			}
			else
			{
				$datetime = strtotime('last Wednesday') + 10800 + (3 * 86400);
			}
			$date = date('Y-m-d', $datetime);
		}
	}
	else
	{
		$que2 = 'SELECT `event_date` FROM `events` 
				 WHERE `contest_id` = '.$contest_id.'
				 GROUP BY `event_date`
				 ORDER BY `event_date` DESC
				 LIMIT 1';
		$query2 = mysql_query($que2);
		$res2 = mysql_fetch_row($query2);
		$date = $res2[0];
	}
	return $date;
}


function get_current_contest_date($contest_id)
{
	$que = "SELECT `current_contest_date` 
			FROM `contests` 
			WHERE `current_contest_date` != ''
			AND `contest_id` =".$contest_id;
	$query = mysql_query($que);
	$ret = '';
	if( @mysql_num_rows($query) > 0 )
	{
		$res = mysql_fetch_row($query);
		$ret = $res[0];
	}
	
	return check_current_contest_date($contest_id, $ret);
}

function get_bracket_entries()
{
	$que = "SELECT * FROM `bracket_user`
			WHERE `ANSWERS` != ''";
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

function bracket_graded_result($round) {

	$que = 'SELECT g.`TID1` , g.`TID2` , g.`ROUND` , t1.NAME AS TEAM1, t2.NAME AS TEAM2
			FROM `bracket_game` g
			LEFT JOIN `bracket_team` t1 ON t1.ID = g.TID1
			LEFT JOIN `bracket_team` t2 ON t2.ID = g.TID2
			WHERE g.`TID1` > 0
			AND g.`ROUND` > 1';
			
	if($round != 'All' && $round > 0)
	{
		$srnd = $round + 1;
		$que .= ' AND g.`ROUND` = '.$srnd;
	}
	$query = @mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_row($query))
		{
			$tid1 = $res[0];
			$tid2 = $res[1];
			$rnd  = $res[2]-1;
			$tmn1 = $res[3];
			$tmn2 = $res[4];
			$ret[$rnd][$tid1] = $tmn1;
			if($tmn2 != null)$ret[$rnd][$tid2] = $tmn2;
		}
	}
	return $ret;

}

function bracket_values_count($round, $correct_values, $entry_values)
{
	//get teams
	$teams = array();
	$tque = mysql_query('SELECT * FROM `bracket_team`');
	while($tres = mysql_fetch_assoc($tque))
	{
		$tid = $tres['ID'];
		$tnm = $tres['NAME'];
		$teams[$tid] = $tnm;
	}
	$count = 0;
	$exv = explode(';',$entry_values);
	$exv_array = array();
	$str = '';
	for($i=0;$i<count($exv);$i++)
	{
		$j = $i+1;
		$str = 'Round '.$j.':';
		$ntr = str_replace($str, '', $exv[$i]);
		$exv_array[$j] = explode(',',$ntr);
	}
	
	$ret = array();
	if($round == 'All')
	{
		$ret = $exv_array;
	}else
	{
		$ret[$round] = $exv_array[$round];
	}
	
	$str_array = array();
	if(count($ret) > 0)
	{
		foreach($ret as $rk => $rv)
		{
			foreach($rv as $k => $v)
			{
				if( isset( $correct_values[$rk][$v] ) )
				{
					$count++;
					$str_array[$rk][] = '<b>'.$correct_values[$rk][$v].'</b>';
				}else
				{
					$str_array[$rk][] = $teams[$v];
				}
			}
		}
		$str = '';
		foreach($str_array as $sk => $st)
		{
			if(count($str_array)>1 && $sk > 1)$str.='<br>';
			if($round == 'All')$str .= 'Round '.$sk.': ';
			$str .= implode(' | ',$st);
		}
	}
	$return['count'] = $count;
	$return['str'] = $str;
	return $return;
}

function bracket_total_count($round)
{
	$que = 'SELECT ID
			FROM `bracket_game` g
			WHERE g.`TID1` > 0
			AND g.`ROUND` > 1';
	if($round != 'All' && $round > 0)
	{
		$srnd = $round + 1;
		$que .= ' AND g.`ROUND` = '.$srnd;
	}
	$query = mysql_query($que);
	$rows = @mysql_num_rows($query)+0;
	$count = $rows*2;
	if($count > 0 && ($round == 'All' || $round == 6) )
	{
	  $count--;
	}
	return $count;
}

function bracketResults()
{
	$ret = array();
	$str = '';
	$counts = array(1 => 32, 16, 8, 4, 2, 1);
	$que = 'SELECT ID, ROUND, TID1, TID2
			FROM `bracket_game` g
			WHERE g.`ROUND` > 1
			AND (g.`TID1` > 0 || g.`TID2` > 0)';
	$query = mysql_query($que);
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_row($query))
		{
			$id = $res[0];
			$rd = $res[1];
			$t1 = $res[2];
			$t2 = $res[3];
			if($t1>0)$ret[$rd][] = $t1;
			if($t2>0)$ret[$rd][] = $t2;
		}
		foreach($ret as $i => $rt)
		{
			$j = $i-1;
			$rd_count = count($ret[$i]);
			if($rd_count  > 0 )
			{
				if($rd_count == $counts[$j])
				{
					$str .= '<a href="../bracket_entries.php?contest_id=6&date=2012-03-15&contest_round='.$j.'" style="color:green; font-weight:bold;">Round '.$j.'</a>';
				}else
				{
					$str .= 'Round '.$j. ' (Update all teams for this round)';
				}
				$str .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			if( count($ret) == 6 && $j==6)
			{
				$str .= '<a href="../bracket_entries.php?contest_id=6&date=2012-03-15&contest_round=All" style="color:green; font-weight:bold;">All Rounds</a>';
			}
		}
		$str = '<b>Results:</b>&nbsp;<span style="font-size:12px;">'.$str.'</span>';
	}
	return $str;
}
?>
