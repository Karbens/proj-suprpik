<?php

function getContests($id = 0)
{
	$where = $id==0? '' : ' AND `contest_id` = '.(int)$id;
	
	$cur_date = date('Y-m-d H:i:s');
	$query = 'SELECT *
			  FROM `'.MOSCONFIG_BRDPREFIX.'contests`
			  WHERE start_date <= \''.$cur_date.'\'
			  AND   end_date >= \''.$cur_date.'\''. $where .
			  ' ORDER BY `contest_order`';

	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);
	$ret = array();
	if($rows_count > 0)
	{
		while($res = mysql_fetch_assoc($result))
		{
			$ret[$res['contest_id']] = $res;
		}
	}
	return $ret;
}

function getContestName($id)
{
	$query = 'SELECT contest_name
			  FROM `'.MOSCONFIG_BRDPREFIX.'contests`
			  WHERE contest_id = '.$id;
	$result = @mysql_query($query);
	$res = @mysql_fetch_row($result);
	return $res[0];
}

function getContestById($id)
{
	
	$cur_date = date('Y-m-d H:i:s');
	$query = 'SELECT *
			  FROM `'.MOSCONFIG_BRDPREFIX.'contests`
			  WHERE `contest_id` = '.$id;
	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);
	$ret = array();
	if($rows_count > 0)
	{
		$ret = mysql_fetch_assoc($result);
	}
	return $ret;
}

function getSoccerSchedule($spread=0, $week=0)
{
	$curr_week = getCurrentWeek();
		
	if($week > 0){
		$curr_week = $week;
		$sp_que = '';
	}
	
	if($spread == 1)$sp_que .= " AND ps_away != '' AND ps_home != ''";
	$query = 'SELECT * 
			  FROM `br3_contests_soccer`
			  WHERE week_num = ' . $curr_week . '
			  '.$sp_que.'
			  AND `published` = \'yes\'
			  ORDER BY `game_date` ASC';
	//echo $query; exit();
	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);
	$ret = array();
	if($rows_count > 0)
	{
		while($res = mysql_fetch_assoc($result))
		{
			$ret[] = $res;
		}
	}
	return $ret;
}


function getContestUserPicks($contest_id = 3, $week=0, $user_id=0)
{
	$curr_week = getCurrentWeek();
	$user	= wp_get_current_user();
	$query = 'SELECT * 
			  FROM `br3_contests_soccer_picks`
			  WHERE contest_id = '.$contest_id;
	if( isset($_REQUEST['user']) && $_REQUEST['user'] > 0)
	{
		$query .= ' AND user_id = '.$_REQUEST['user'];
	}
	elseif($user_id > 0)
	{
		$query .= ' AND user_id = '.$user_id;
	}
	else
	{
		$query .= ' AND user_id = '.$user->ID;
	}
	if($week > 0)$query .= ' AND week_num = '.$week;
	//echo 'query: '.$query;
	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);
	$ret = array();
	if($rows_count > 0)
	{


		while($res = mysql_fetch_assoc($result))
		{
			$check_graded_query = 'SELECT * FROM `br3_contests_soccer` WHERE `week_num` = '.$res['week_num'] .' LIMIT 1';
			$check = mysql_query($check_graded_query);
			$graded = mysql_fetch_assoc($check);
			$is_graded = !empty($graded['winning_spread_team'])? true : false;


			//set blank for winning team
			$res['winn_team'] = '';
			//get results for passed week(s)
			if(true)//if($res['week_num'] < $curr_week)
			{
			  $team_arr = getTeamsByWeek($res['week_num'], $res['user_picks']);
			  if($contest_id == 3)
			  {
				$teams = array();
				$w = 0;//wins
				$l = 0;//losses
				$t = 0;//ties

				$user_picks_array = explode(',',$res['user_picks']);
				foreach($team_arr as $team)
				{
					$game_id = $team['game_id'];
					$team['winning_spread_team'];
					if( in_array($team['winning_spread_team'], $user_picks_array))$w++;
					if($team['winning_spread_team'] == 'Tie')$t++;
				}
				$l = 5-($w+$t);
				$res['winn_team'] = $w.'-'.$l.'-'.$t;
				if(!$is_graded) $res['winn_team'] = '0-0-0';
			  }
			}
			$ret[] = $res;
		}
	}
	return $ret;
}

function getTeamsByWeek($week, $team='')
{
	$query = 'SELECT * 
			  FROM `br3_contests_soccer`
			  WHERE week_num = ' . $week;
	$team_arr = explode(',',$team);
	if(count($team_arr) > 1)
	{
		foreach($team_arr as $key => $team)
		{
			$team_arr[$key] = "'".$team."'";
		}
		$teams = implode(',',$team_arr);
		$query .= ' AND (away_team IN ('.$teams.') OR home_team IN ('.$teams.') )';
	}else
	{
	  if($team != '')$query .= ' AND (away_team = \''.$team.'\' || home_team = \''.$team.'\' )';
	}
	$query .= ' ORDER BY game_id';
	//echo '<br>que: '.$query;
	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);
	$ret = array();
	if($rows_count > 0)
	{
		while($res = mysql_fetch_assoc($result))
		{
			$ret[] = $res;
		}
	}
	return $ret;
}

function getHandicap($team_name, $week){

	$query = 'SELECT `ps_away`, `ps_home`, `home_team`, `away_team`  FROM `br3_contests_soccer`
			  WHERE week_num = ' . $week . ' AND (`home_team` = "'.$team_name.'" OR `away_team` = "'.$team_name.'") LIMIT 1';

	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);

	$handicap = '';
	$ret = array();
	if($rows_count > 0)
	{
		while($res = mysql_fetch_assoc($result))
		{
			if($res['home_team']==$team_name){
				$handicap = $res['ps_home'];
				break;
			}else{
				$handicap = $res['ps_away'];
				break;
			}
		}
	}
	return $handicap;

}

function contestDisplaySpread($id)
{
	$mosConfig_live_site =  sprintf(MOSCONFIG_LIVE_SITE, $id);
	$curr_week = getCurrentWeek();
	$user	= wp_get_current_user();
	$now_date = date('Y-m-d H:i:s');
	
	//contest image
	$contest_image = '';//'<p><img src="/components/com_contests/images/Contest-Banners-'.$id.'.jpg"></p>';
	
	$data = $contest_image.'<p> <a href="'.$mosConfig_live_site.'&tab=pick">'.MAKEPICKS.'</a> | <a href="'.$mosConfig_live_site.'&tab=mypicks">'.MYSUPERPICKS.'</a> | <a href="'.$mosConfig_live_site.'&tab=leaderboard">Leaderboard</a></p>';
	$requests_array = array('mypicks', 'pick', 'status', 'leaderboard');
	
	if(isset($_REQUEST['tab']) && in_array($_REQUEST['tab'], $requests_array) )
	{
		if($_REQUEST['tab'] == 'mypicks')
		{
			if( isset($_REQUEST['week']) && $_REQUEST['week'] > 0)
			{
				$data = $contest_image.'<p> <a href="'.$mosConfig_live_site.'&tab=pick">'.MAKEPICKS.'</a> | <a href="'.$mosConfig_live_site.'&tab=mypicks">'.MYSUPERPICKS.'</a> | <a href="'.$mosConfig_live_site.'&tab=leaderboard">Leaderboard</a></p>';
				$data .= displayWeeklySpreadPicks($_REQUEST['week']);
			}else
			{
				$data = $contest_image . '<p> <a href="'.$mosConfig_live_site.'&tab=pick">'.MAKEPICKS.'</a> | ';
				if( isset($_REQUEST['user']) && $_REQUEST['user'] > 0 )
				{
					$data .= '<a href="'.$mosConfig_live_site.'&tab=mypicks">'.MYSUPERPICKS.'</a> | ';
					$your_picks_title = 'Picks by '.$_REQUEST['username'];
				}else
				{
					$data .= ' '.MYSUPERPICKS.' | ';
					$your_picks_title = 'Your Picks';
				}
				$data .= '<a href="'.$mosConfig_live_site.'&tab=leaderboard">Leaderboard</a></p>';
				$picks = getContestUserPicks($id);//get the weekly survivor picks, max(21), 17 reg season and 4 playoffs
				
				if(count($picks) > 0)
				{
				
					$data .= '<table class="contest_table">
							  <thead>
							  <tr>
							  <th align="center" width="10%">GameWeek</th>
							  <th align="center" width="60%">'.$your_picks_title.'</th>
							  <th align="center" width="20%">Result</th>
							  <th align="center" width="10%">Points</th>
							  </tr>
							  </thead>
							  <tbody>';
					//overall total counters
					$pw = 0;//wins 
					$pl = 0;//losses
					$pt = 0;//ties
					$tot_points = 0;
					foreach($picks as $pick){
						$pick_pei = explode('-',$pick['winn_team']);
						$pw += $pick_pei[0];
						$pl += $pick_pei[1];
						$pt += $pick_pei[2];
						$p_points = $pick_pei[0] + ($pick_pei[2]*0.5);
						$tot_points += $p_points;
						$anch = '<a href="'.$mosConfig_live_site.'&tab=mypicks&week='.$pick['week_num'].'&result='.$pick['winn_team'].'">';
						if( $_REQUEST['user'] > 0 && $_REQUEST['username'] != '')
						{
							/*$anch = '<a href="javascript:void(0);" onclick="javascript:window.open(\''.$mosConfig_live_site.'&tab=leaderboard&week='.$pick['week_num'].
									'&user='.$_REQUEST['user'].'&username='.$_REQUEST['username'].'&result='.$pick['winn_team'].
									'\',\'Contest Picks\',\'width=650,height=720,scrollbars=1,toolbar=0,resizable=1\');" '.
									'title="View Week '.$pick['week_num'].' Picks for '.$_REQUEST['username'].'" '.
									'style="text-decoration:none;">';*/
							$anch = '<a href="'.$mosConfig_live_site.'&tab=leaderboard&week='.$pick['week_num'].
									'&user='.$_REQUEST['user'].'&username='.$_REQUEST['username'].'&result='.$pick['winn_team'].
									'" title="View Week '.$pick['week_num'].' Picks for '.$_REQUEST['username'].'" '.
									'style="text-decoration:none;">';
						}
						//add link to result for past weeks
						if($pick['week_num'] < $curr_week){
							$picks_result = $anch . $pick['winn_team'] . '</a>';
						}else
						{
							$picks_result = $pick['winn_team'];
						}

						$team_arr = explode(',', $pick['user_picks']);
						$team_out = array();
						foreach ($team_arr as $key => $value) {
							$team_out[] = $value . ' ' . getHandicap($value, $pick['week_num']);
						}


						if( $_REQUEST['user'] > 0 && $_REQUEST['user'] != $user->ID)
						{
							if($pick['week_num'] < $curr_week)
							{
							  $data .= '<tr>
										<td align="center">'.$pick['week_num'].'</td>
										<td align="left">'. implode(', ',$team_out).'</td>
										<td align="center">'.$picks_result.'</td>
										<td align="center">'.$p_points.'</td>
									  </tr>';
							}
						}else
						{
							$data .= '<tr>
										<td align="center">'.$pick['week_num'].'</td>
										<td align="left">'.implode(', ',$team_out).'</td>
										<td align="center">'.$picks_result.'</td>
										<td align="center">'.$p_points.'</td>
									  </tr>';
						}
					}
					//overall total
					//if( $_REQUEST['user'] > 0 ){
						$data .= '
								  <tr>
									<td align="right" colspan="2">
									Overall Totals: 
									</td>
									<td align="center">
									'.$pw.'-'.$pl.'-'.$pt.'
									</td>
									<td align="center">
									'.$tot_points.'
									</td>
								  </tr>';
					//}
					
					$data .= '</tbody>
							  </table>';
				
				}else
				{
				
					$data .= '<p>Currently, you don\'t have any picks.</p>';
				
				}
			}//end of else if( $_REQUEST['week'] > 0 )
		}
		
		if($_REQUEST['tab'] == 'pick')
		{
			$data = $contest_image.'<p> '.MAKEPICKS.' | <a href="'.$mosConfig_live_site.'&tab=mypicks">'.MYSUPERPICKS.'</a> | <a href="'.$mosConfig_live_site.'&tab=leaderboard">Leaderboard</a></p>';
			$picks = getSoccerSchedule('1');//get all the possible picks for user in the current week
			$sel_week = $curr_week;
			//if($_REQUEST['week']>0)$sel_week = $_REQUEST['week'];
			$upicks = getContestUserPicks($id, $sel_week);
			$end_date = getCurrentExpiry('2');

			$first_match_date_timestamp = strtotime($picks[0]['game_date']);
			$now_date_timestamp = strtotime($now_date);

			//close 30 mins before 1st match

			$allowed_to_pick = $first_match_date_timestamp - $now_date_timestamp - (30*60);
		//	echo $picks[0]['game_date'] .'<br/>';
		//	echo date('Y-m-d h:i', $first_match_date_timestamp ) .'<br/>';
		//	echo date('Y-m-d h:i');
		//	echo $allowed_to_pick;

			if($allowed_to_pick>0){
				if( count($upicks) > 0)	{
				  $team_date = $upicks[0]['end_date'];
				  if($team_date < $end_date)$end_date = $team_date;
				}
			if(count($picks) > 0 && count($upicks) == 0 && $now_date < $end_date && $curr_week < 18)
			{
				$data .= '
				<script>
					var max = '.count($picks).';
					function countChecks() 
					{
						var total = 0;
						for (var idx = 0; idx < max; idx++) 
						{
							if ( document.getElementById(\'team_a_\'+idx).checked == true || 
								 document.getElementById(\'team_h_\'+idx).checked == true ) 
							{
							    total += 1;
							}
						}
						return total;
					}
					function validateForm()
					{
						var cCount = countChecks();
						if(cCount < 5)
						{
							alert("Please select at least 5 games.");
							return false;
						}else if(cCount > 5)
						{
							alert("Please select only 5 games.");
							return false;
						}else{
							var items = ""; 
							jQuery(".contest_table input:checked").each(function(){
								items += jQuery(this).val() + "\n";
							});


							return confirm("Are you sure you want these 5 picks?\n"+items);
						}
					}
					function toggleChecks(ah, id)
					{
						var bh = "h";
						if(ah == "h")bh = "a";
						if( document.getElementById("team_"+ah+"_"+id).checked == true )
						{
							document.getElementById("team_"+bh+"_"+id).checked = false;
						}
					}

					jQuery(function(){
						jQuery(\'.gameTime\').each(function(){
							cell_id = jQuery(this).attr(\'id\');

							jQuery(this).find(\'span\').html(localTimes(jQuery(this).find(\'input\').val()));


						});

					});

					function localTimes(unix_timestamp){

						// create a new javascript Date object based on the timestamp
						// multiplied by 1000 so that the argument is in milliseconds, not seconds

						var date = new Date(unix_timestamp*1000);
						var year = date.getFullYear();
						var month = date.getMonth()+1;
						var get_date = date.getDate();

						var hours = date.getHours();
						var minutes = date.getMinutes();

						// will display time in 10:30 format
					//	var formattedTime = year + "-" + month + "-" + get_date + " " + hours + ":" + minutes;
						return date.toLocaleDateString() + " " + date.toLocaleTimeString();

					}

					
				</script>';
				$data .= '<form name="my_picks" action="" method="post" onsubmit="return validateForm();">
						  <input type="hidden" name="p_contest" value="1">
						  <input type="hidden" name="contest_id" value="'.$id.'">
						  <input type="hidden" name="week_id" value="'.$sel_week.'">';
				$data .= '<table class="contest_table">
						  <thead>
						  <tr><th colspan="5" align="center">GameWeek '.$sel_week.'</th></tr>
						  <tr><th>Game Date Time (Local)</th><th>&nbsp;</th><th>Home</th><th>&nbsp;</th><th>Away</th></tr>
						  </thead>
						  <tbody>';
				$c = 0;
				$picked_teams = '';
				if( count($upicks) > 0)$picked_teams = $upicks[0]['user_picks'];
				$p_teams = array();
				if( $picked_teams != '')
				{
					$teams_pei = explode(',',$picked_teams);
					foreach($teams_pei as $te)
					{
						$te = trim($te);
						$p_teams[$te] = $te;
					}
				}
				
				foreach($picks as $pick)
				{
					$bg = '';
					if($c%2 == 1)$bg = ' bgcolor="#dfe6de"';
					$cval = '';
					$aval = ''; $ateam = trim($pick['away_team']);
					$hval = ''; $hteam = trim($pick['home_team']);
					if( isset($p_teams[$ateam]) ){
						$aval = ' checked';
						$hval = '';
						$cval = ' checked';
					}
					if( isset($p_teams[$hteam]) ){
						$hval = ' checked';
						$cval = ' checked';
					}
					
					//disable lines 
					$dsa = ($pick['ps_away'] == 'OFF') ? ' disabled' : '';
					$dsh = ($pick['ps_home'] == 'OFF') ? ' disabled' : '';
					//set point spreads
					$psa = ($pick['ps_away'] != '') ? ' '.$pick['ps_away'] : '';
					$psh = ($pick['ps_home'] != '') ? ' '.$pick['ps_home'] : '';
					
					$data .= '<tr'.$bg.' id="trow_'.$c.'">
								  <td id="gameTime'.$pick['game_id'].'" class="gameTime">
								  <span>'.date('Y-m-d g:i A',strtotime($pick['game_date'])).'</span>
								  <input type="hidden" name="gametime['.$pick['game_id'].']" value="'.strtotime($pick['game_date']).'">
								  </td>
								  <td align="center"><input type="checkbox" id="team_h_'.$c.'" name="team['.$pick['game_id'].']" value="'.$pick['home_team'].'"'.$hval.' onclick="javascript:toggleChecks(\'h\',\''.$c.'\');"'.$dsh.'></td>
								  <td align="left">'.$pick['home_team'].$psh.'</td>
								  <td align="center"><input type="checkbox" id="team_a_'.$c.'" name="team['.$pick['game_id'].']" value="'.$pick['away_team'].'"'.$aval.' onclick="javascript:toggleChecks(\'a\',\''.$c.'\');"'.$dsa.'></td>
								  <td align="left">'.$pick['away_team'].$psa.'</td>
							  </tr>';
					$c++;
				}
				$data .= '
						  <tr><td colspan="6">&nbsp;</td></tr>
						  <tr>
						  	  <td colspan="6">
							  &nbsp;<input type="reset" value="Reset">
							  &nbsp;<input type="submit" value="Submit" class="button">
							  </td>
						  </tr>
						  </tbody>
						  </table>
						  </form>';
				
			
			}else
				{
					if(count($upicks) > 0){
						$data .= '<p>You can only make picks once.</p>';

					}
			
					if($now_date > $end_date)
					{
						$data .= '<p>Now: '.$now_date.'<br> End: '.$end_date.'</p>';
						$data .= '<p>Deadline for week '.$curr_week.' has expired.</p>';
					}
					elseif($curr_week > 18)
					{
						$data .= '<p>No more picks.</p>';
					}
					elseif( count($picks) == 0 )
					{
						$data .= '<p>There are no GameWeek matches at the moment. Please check back later and check our twitter feed for updates.</p>';
					}
			
				}
				
			} else{
				$data .= '<p>There are no GameWeek matches at the moment. Please check back later and check our twitter feed for updates.</p>';
			}

		}//end of if($_REQUEST['tab'] == 'pick')
		
		
		if($_REQUEST['tab'] == 'leaderboard')
		{
			$data = $contest_image.'<p> <a href="'.$mosConfig_live_site.'&tab=pick">'.MAKEPICKS.'</a> | <a href="'.$mosConfig_live_site.'&tab=mypicks">'.MYSUPERPICKS.'</a> | Leaderboard</p>';
			
			//by week links
			$wl_text = 'By GameWeek: ';
			$ic_week = 18;
			if($curr_week < $ic_week)$ic_week = $curr_week;
			for($i=1;$i<$ic_week;$i++)
			{
				if($i > 1)$wl_text .= ' | ';
				if( isset($_REQUEST['week']) && $_REQUEST['week'] == $i)
				{
					$wl_text .= $i;
				}else
				{
				  $wl_text .= '<a href="'.$mosConfig_live_site.'&tab=leaderboard&week='.$i.'">'.$i.'</a>';
				}
			}
			
			//overall link
			$cur_text = ' | Overall Standings';
			if( isset($_REQUEST['week']) && $_REQUEST['week']>0)$cur_text = ' | <a href="'.$mosConfig_live_site.'&tab=leaderboard">Overall Standings</a>';
			$data .= '<p> '.$wl_text.$cur_text.'</p>';
			
			//display current picks if deadline expired
			if(( date('Y-m-d')=='2012-12-24' && date('G') >= 13) || (date('l') == 'Saturday' && date('G') >= 22) || (date('l') == 'Sunday') || (date('l') == 'Monday') )
			{
			  $data .= '<p><a href="'.$mosConfig_live_site.'&tab=leaderboard&week='.$curr_week.'">View Picks for GameWeek '.$curr_week.'</a></p>';
			}
			$user_week = $curr_week-1;
			
			//update contest, calculates points for user picks
			if( isset($_REQUEST['updateContest']) )
			{
				$picks = updateContestLeaderboard($id);//update leaderboard
			}else
			{
				$picks_title = '';
				if( isset($_REQUEST['week']) && $_REQUEST['week'] > 0 )
				{
					$picks = getContestLeaderboard($id, $_REQUEST['week']);//get leaderboard by week
					$picks_title = 'Leaders for Week '.$_REQUEST['week'];
					if($curr_week == $_REQUEST['week'])$picks_title = 'Picks for Week '.$_REQUEST['week'];
					$user_week = $_REQUEST['week'];
				}else
				{
					$past_week = $curr_week-1;
					$picks = getContestLeaderboard($id);//get the latest leaderboard
					$picks_title = 'Overall Standings';
				}
			}
			
			
			if(count($picks) > 0)
			{
			
				if($curr_week == $_REQUEST['week'])
				{
					$data .= '<div align="center">
						  <table class="contest_table">
						  <thead>
						  <tr><th colspan="4" align="center">'.$picks_title.'</th></tr>
						  <tr>
						  <th align="center" width="10%">#</th>
						  <th align="center" width="20%">MEMBER</th>
						  <th align="center" widht="70%">PICKS</th>
						  </tr>
						  </thead>
						  <tbody>';
				}else
				{
					$data .= '<div align="center">
						  <table class="contest_table">
						  <thead>
						  <tr><th colspan="4" align="center">'.$picks_title.'</th></tr>
						  <tr>
						  <th align="center">#</th>
						  <th align="center">MEMBER</th>
						  <th align="center">RECORD</th>
						  <th align="center">POINTS</th>
						  </tr>
						  </thead>
						  <tbody>';
				}
				$s=1;
				foreach($picks as $pick)
				{
					$bg = '';
					if($s%2 == 1)$bg = ' bgcolor="#dfe6de"';
					$bb = ''; $be ='';
					if($user->ID == $pick['user_id'])
					{
						$bb = '<b>'; $be = '</b>';
						$bg = ' bgcolor="#A2D246"';
					}
					$con_user_picks = getContestUserPicks('3', $user_week, $pick['user_id']);
					$user_picks = $con_user_picks[0]['user_picks'];
					$user_anc = '';
					$user_enc = '';
					if( isset($_GET['week']) && $_GET['week'] <= $curr_week )
					{
						/*$user_anc = '<a href="javascript:void(0);" onclick="javascript:window.open(\''.$mosConfig_live_site.'&tab=leaderboard&week='.$user_week.
									'&user='.$pick['user_id'].'&username='.$pick['name'].'&result='.$pick['record'].
									'\',\'Contest Picks\',\'width=650,height=720,scrollbars=1,toolbar=0,resizable=1\');" '.
									'title="View Picks for '.$pick['name'].'" '.
									'style="text-decoration:none;">';*/
						$user_anc = '<a href="'.$mosConfig_live_site.'&tab=leaderboard&week='.$user_week.
									'&user='.$pick['user_id'].'&username='.$pick['name'].'&result='.$pick['record'].
									'" title="View Picks for '.$pick['name'].'" '.
									'style="text-decoration:none;">';
						$user_enc = '</a>';
					}else
					{
						$user_href = $mosConfig_live_site.'&tab=mypicks';
						if($user->ID != $pick['user_id'])$user_href .= '&user='.$pick['user_id'].'&username='.$pick['name'];
						$user_anc = '<a href="'.$user_href.'" title="View Complete Seasonal Picks for '.$pick['name'].'" style="text-decoration:none;">';
						$user_enc = '</a>';
					}
					if($curr_week == $_REQUEST['week'])
					{
						$data .= '<tr'.$bg.'>
									<td align="center">'.$bb.$s.$be.'</td>
									<td align="center">'.$user_anc.$bb.$pick['name'].$be.$user_enc.'</td>
									<td align="center">'.$user_picks.'</td>
								  </tr>';
					}else
					{
						$data .= '<tr'.$bg.'>
									<td align="center">'.$bb.$s.$be.'</td>
									<td align="center">'.$user_anc.$bb.$pick['name'].$be.$user_enc.'</td>
									<td align="center">'.$bb.$pick['record'].$be.'</td>
									<td align="center">'.$bb.$pick['points'].$be.'</td>
								  </tr>';
					}
					$s++;
				}
				$data .= '</tbody>
						  </table>
						  </div>';
			
			}else
			{
			
				$data .= '<p>No results found.</p>';
			
			}
			
		}
		
	}
	return $data;
}


function displayWeeklySpreadPicks($week)
{
		if($week == getCurrentWeek())
		{
			if( (date('l') == 'Saturday' && date('G') > 22) || (date('l') == 'Sunday') || (date('l') == 'Monday') )
			{
				//do nothing
			}else
			{
				//return '';
			}
		}
		$picks = getSoccerSchedule('1', $week);
		$upicks = getContestUserPicks('3', $week);
		
		$result = '';
		if( trim($_REQUEST['username']) != '')$result .= ' for '.trim($_REQUEST['username']);
		if( isset($_REQUEST['result']) )$result .= ' ('.$_REQUEST['result'].')';
		$tbl_style = ( $_GET['user'] > 0 ) ? ' style="border: 1px solid;"' : '';
		$data .= '<table class="contest_table"'.$tbl_style.'>
				  <thead>
				  <tr><th colspan="6" align="center" class="picks_heading">&nbsp;&nbsp;Week '.$week.' Picks'.$result.'</th></tr>
				  <tr>
					<th>&nbsp;Teams</th>
					<th align="center">Scores</th>
					<th>&nbsp;Picks</th>
					<th>&nbsp;Results</th>
				  </tr>
				  </thead>
				  <tbody>';
		$c = 0;
		$picked_teams = '';
		if( count($upicks) > 0)$picked_teams = $upicks[0]['user_picks'];
		$p_teams = array();
		if( $picked_teams != '')
		{
			$teams_pei = explode(',',$picked_teams);
			foreach($teams_pei as $te)
			{
				$te = trim($te);
				$p_teams[$te] = $te;
			}
		}
		
		foreach($picks as $pick)
		{
			$pdata = '';
			$bg = '';
			if($c%2 == 1)$bg = ' bgcolor="#dfe6de"';
			$your_pick = '';
			$ateam = trim($pick['away_team']);
			$hteam = trim($pick['home_team']);
			if( isset($p_teams[$ateam]) ){
				$your_pick = '<b>'.$ateam.'</b>';
				$bg = ' bgcolor="#A2D246"';
				if($pick['winning_spread_team'] == $ateam || $pick['winning_spread_team'] == 'Tie')
				{
					$pick['winning_spread_team'] = '<b>'.$pick['winning_spread_team'].'</b>';
				}
			}
			if( isset($p_teams[$hteam]) ){
				$your_pick = '<b>'.$hteam.'</b>';
				$bg = ' bgcolor="#A2D246"';
				if($pick['winning_spread_team'] == $hteam || $pick['winning_spread_team'] == 'Tie')
				{
					$pick['winning_spread_team'] = '<b>'.$pick['winning_spread_team'].'</b>';
				}
			}
			//set point spreads
			$psa = ($pick['ps_away'] != '') ? ' ('.$pick['ps_away'].')' : '';
			$psh = ($pick['ps_home'] != '') ? ' ('.$pick['ps_home'].')' : '';
			
			//set scores with spreads added
			//$pick['away_score'] = ($pick['ps_away'] > 0) ? ($pick['away_score']+$pick['ps_away']) : $pick['away_score'];
			//$pick['home_score'] = ($pick['ps_home'] > 0) ? ($pick['home_score']+$pick['ps_home']) : $pick['home_score'];
			
			$pdata = '<tr'.$bg.' id="trow_'.$c.'">
						  <td align="left">'.$pick['away_team'].$psa.'</td>
						  <td align="center">
						  '.$pick['away_score'].' </td>
						  <td rowspan="2">'.$your_pick.'</td>
						  <td rowspan="2">
						  '.$pick['winning_spread_team'].'
						  </td>
					  </tr>
					  <tr'.$bg.' id="trow_'.$c.'1">
					  <td align="left">'.$pick['home_team'].$psh.'</td>
					  <td align="center">'.$pick['home_score'].' </td>
					  </tr>


					  ';
			if( $_GET['user'] > 0 )
			{
				//if($your_pick != '')
				//{
					$data .= $pdata;
					$c++;
				//}
			}else
			{
				$data .= $pdata;
				$c++;
			}
		}
		$data .= '
				  <tr><td colspan="6">&nbsp;</td></tr>
				  </tbody>
				  </table>';
		
		return $data;
}

function getContestTerms($id)
{
	$query = 'SELECT contest_terms
			  FROM `'.MOSCONFIG_BRDPREFIX.'contests`
			  WHERE contest_id = '.$id;
	$result = @mysql_query($query);
	$res = @mysql_fetch_row($result);
	return $res[0];
}

function getContestStartEndDate($id, $stamp = true)
{
	$query = 'SELECT start_date, end_date 
			  FROM `'.MOSCONFIG_BRDPREFIX.'contests`
			  WHERE contest_id = '.$id;
	$result = @mysql_query($query);
	$res = @mysql_fetch_row($result);
	if($stamp) return array('start' => strtotime($res[0]), 'end' => strtotime($res[1]));
	else return array('start' => $res[0], 'end' => $res[1]);
}

function getCurrentWeek()
{
	$query = 'SELECT week_num FROM `'.MOSCONFIG_BRDPREFIX.'contests_soccer` ORDER BY `week_num` DESC LIMIT 1';

	$result = @mysql_query($query);
	$res = @mysql_fetch_row($result);
	return $res[0];


	//$contest_start_day  = date('l',  SOCCER_START_DATE);
	//$now_day_of_week = date('l');
	//$diff = ($now_day_of_week - $contest_start_day) / 604800;
	
	//return ceil($diff) +1;
}

function getCurrentExpiry($id=0)
{
	$diff = getCurrentWeek();
	if($id == 2)
	{
		$dtime = SOCCER_END_DATE + (604800*$diff);
		$date = date('Y-m-d', $dtime).' 22:00:00';
	}
	elseif($id == 1)
	{
		$dtime = SOCCER_START_DATE + (604800*$diff);
		$date = date('Y-m-d', $dtime).' 13:00:00';
	}
	return $date;
}

function setContestCookie($id, $user_id)
{
	setcookie("contest[$id][$user_id]" ,'true', mktime (0, 0, 0, 9, 1, 2014));
}

function getUserSurvivorStatus()
{
	$curr_week = getCurrentWeek();
	$past_week = $curr_week - 1;
	$user	= wp_get_current_user();
	
	$query = 'SELECT user_picks
			  FROM `'.MOSCONFIG_BRDPREFIX.'contests_soccer_picks`
			  WHERE contest_id = 1
			  AND user_id = '.$user->ID.' 
			  AND week_num = '.$past_week;
	$result = @mysql_query($query);
	if(@mysql_num_rows($result) == 0)
	{
		return false;
	}
	else
	{
	  $res = @mysql_fetch_row($result);
	  $que2 = 'SELECT winning_team
	  		   FROM `'.MOSCONFIG_BRDPREFIX.'contests_soccer_picks`
			   WHERE week_num = '.$past_week.' 
			   AND winning_team = \''.$res[0].'\'';
	  $res2 = mysql_query($que2);
	  if(@mysql_num_rows($res2) == 0)return false;
	}
	return true;
}

function getPastSurvivorPicks()
{
	$curr_week = getCurrentWeek();
	$user	= wp_get_current_user();
	
	$teams = array();
	
	$query = 'SELECT week_num, user_picks
			  FROM `'.MOSCONFIG_BRDPREFIX.'contests_soccer_picks`
			  WHERE contest_id = 1
			  AND user_id = '.$user->ID.' 
			  AND week_num < '.$curr_week;
	$result = @mysql_query($query);
	if(@mysql_num_rows($result) > 0)
	{
		while($res = mysql_fetch_assoc($result))
		{
			$pick = $res['user_picks'];
			$teams[$pick] = $res['week_num'];
		}
	}
	return $teams;
}

function getCurrentSurvivors($id)
{
	$curr_week = getCurrentWeek();
	
	$past_week = $curr_week-1;
	
	$surv = array();
	
	$query  =  'SELECT u.ID as id, u.display_name as survivor, u.user_login as username, u.user_email as email, p.user_picks
				FROM br3_contests_soccer_picks p, br3_contests_soccer n, wp_users u
				WHERE p.contest_id = '.$id.'
				AND p.week_num = '.$past_week.'
				AND p.user_picks = n.winning_team
				AND n.week_num = '.$past_week.'
				AND p.user_id = u.ID
				ORDER BY u.display_name';
	$result = @mysql_query($query);
	if(@mysql_num_rows($result) > 0)
	{
		while($res = mysql_fetch_assoc($result))
		{
			$surv[] = $res;
		}
	}
	return $surv;
}

function getContestLeaderboard($contest_id = 2, $week=0)
{
	$curr_week = getCurrentWeek();
	$user  = wp_get_current_user();
	$query = 'SELECT p.user_id, sum( user_points ) AS points, u.display_name as name
			  FROM `br3_contests_soccer_picks` p
			  LEFT JOIN `wp_users` u ON u.ID = p.user_id
			  WHERE p.contest_id = '.$contest_id;
	if($week > 0)$query .= ' AND p.week_num = '.$week;
	$query .= ' GROUP BY p.user_id
			  ORDER BY points DESC , display_name';
	//echo 'query: '.$query;
	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);
	$ret = array();
	if($rows_count > 0)
	{
		while($res = mysql_fetch_assoc($result))
		{
			$user_id = $res['user_id'];
			if( $week > 0 )
			{
				$res['record'] = getUserRecord($user_id, $contest_id, $week);
			}else
			{
				$res['record'] = getUserRecord($user_id, $contest_id);
			}
			$ret[] = $res;
		}
	}
	return $ret;
}

function getUserRecord($user_id, $contest_id = 2, $week=0)
{
	$curr_week = getCurrentWeek() + 1;
	$query = 'SELECT * 
			  FROM `br3_contests_soccer_picks`
			  WHERE contest_id = '.$contest_id.' 
			  AND user_id = '.$user_id;
	if($week > 0)
	{
		if( isset($_REQUEST['uptoweek']) )
		{
			$query .= ' AND week_num <= '.$week;
		}else
		{
		  	$query .= ' AND week_num = '.$week;
		}
	}else
	{
		$query .= ' AND week_num < '.$curr_week;
	}
	$query .= ' ORDER BY user_id, week_num';
	//echo 'query: '.$query;
	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);
	$ret = '0-0-0';
	if($rows_count > 0)
	{

		$wins = 0;//overall wins
		$loss = 0;//overall losses
		$ties = 0;//overall ties





		while($res = mysql_fetch_assoc($result))
		{

			//$check_graded_week = false;
			if(!$check_graded_week){
				$check_graded_query = 'SELECT * FROM `br3_contests_soccer` WHERE `week_num` = '.$res['week_num'] .' LIMIT 1';
				$check = mysql_query($check_graded_query);
				$graded = mysql_fetch_assoc($check);
			//	$check_graded_week = true;
				$is_graded = !empty($graded['winning_spread_team'])? true : false;
			}


			//set blank for winning team
			$res['winn_team'] = '';
			//get results for passed week(s)
			if($res['week_num'] < $curr_week)
			{
			  $team_arr = getTeamsByWeek($res['week_num'], $res['user_picks']);
			  if($contest_id == 3)
			  {

				$w = 0;//wins
				$l = 0;//losses
				$t = 0;//ties

				$user_picks_array = explode(',',$res['user_picks']);
				foreach($team_arr as $team)
				{
					$game_id = $team['game_id'];
					$team['winning_spread_team'];
					if( in_array($team['winning_spread_team'], $user_picks_array))$w++;
					if($team['winning_spread_team'] == 'Tie')$t++;
				}
				$l = 5-($w+$t);
				$res['winn_team'] = $w.'-'.$l.'-'.$t;
				if($is_graded){
					$wins += $w; $loss += $l; $ties += $t;
				}

			  }
			}
		}
		$ret = $wins.'-'.$loss.'-'.$ties;

	}
	return $ret;
}


function updateContestLeaderboard($contest_id = 3, $week=0)
{
	$curr_week = getCurrentWeek();
	$user	= wp_get_current_user();
	$query = 'SELECT * 
			  FROM `br3_contests_soccer_picks`
			  WHERE contest_id = '.$contest_id;
	if($week > 0)$query .= ' AND week_num = '.$week;
	$query .= ' ORDER BY user_id, week_num';
	//echo 'query: '.$query;
	$result = mysql_query($query);
	$rows_count = (integer)@mysql_num_rows($result);
	$ret = array();
	if($rows_count > 0)
	{
		while($res = mysql_fetch_assoc($result))
		{
			//set blank for winning team
			$res['winn_team'] = '';
			//get results for passed week(s)
			if($res['week_num'] < $curr_week)
			{
			  $team_arr = getTeamsByWeek($res['week_num'], $res['user_picks']);
			  if($contest_id == 3)
			  {
				$teams = array();
				$w = 0;//wins
				$l = 0;//losses
				$t = 0;//ties
				$p = 0;//points
				foreach($team_arr as $team)
				{
					$game_id = $team['game_id'];
					$team['winning_spread_team'];
					if( eregi($team['winning_spread_team'],$res['user_picks']))$w++;
					if($team['winning_spread_team'] == 'Tie')$t++;
				}
				$l = 5-($w+$t);//calc losses
				$p = ($w*2) + ($t*1);//cacl points
				$res['winn_team'] = $w.'-'.$l.'-'.$t;
				$res['points'] = $p;
				if( isset($_REQUEST['updateContest']) )
				{
					$upd_que = 'UPDATE `br3_contests_soccer_picks`
								SET `user_points` = '.$p.'
								WHERE `pick_id` = '.$res['pick_id'];
					if($res['pick_id']>0)@mysql_query($upd_que);
				}
			  }
			}
			$ret[] = $res;
		}
	}
	return $ret;
}


//newly added functions
function getWeekFromDate($start_date, $game_date){

	$start = strtotime($start_date);
	$game = strtotime($game_date);

	$diff = ($game - $start) / 604800 ;
	
	return ceil($diff);

}