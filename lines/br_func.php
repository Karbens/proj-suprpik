<?php
// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

function br_db_connect() {
    global $ln_link, $br_link;
	
	/*if( eregi("local",$_SERVER['SERVER_NAME']) )
	{
		$ln_link = @mysql_connect('localhost', 'root', 'yogster');//local host connection
		$br_link = @mysql_connect('localhost', 'root', 'yogster');//local host connection
	}
	elseif( eregi("192.168.65.153",$_SERVER['SERVER_NAME']) )
	{
		$ln_link = @mysql_connect('localhost', 'br_lines_admin', 'RscmkBSy3p');//pre-pub connection
		$br_link = @mysql_connect('localhost', 'joom_admin', '9acugUFr');//pre-pub connection
	}
	else
	{*/
      	$ln_link = @mysql_connect('localhost', 'super100_dbmain', 'FgDvr436oy');//main site connection
		$br_link = @mysql_connect('localhost', 'super100_wpmain', 'st127com!!');//main site connection
	//}
    @mysql_select_db('super100_wp');
    if (!$ln_link || !$br_link) 
	{
	  die('Could not connect: ' . mysql_error());
	}

    //return $db_link;
}

function br_db_close() {
    global $ln_link, $br_link;

    @mysql_close($ln_link);
	@mysql_close($br_link);
	
}

//runs scripts that update the Betrepublic Contests
function updateBRContests()
{
	$day  = date('l');
	$hour = date('G');
	if( ($day == 'Tuesday' && $hour > 3) || ($day == 'Wednesday') )
	{
		_updateBRSpreads();
	}
	if($day == 'Tuesday' && $hour == 3)
	{
		_updateNFLSchedule();
		_updateContestLeaderboard();
	}
}

function _updateBRSpreads()
{
	global $ln_link, $br_link;
	
	$cur_week = getCurrentWeek();
	
	$b_date = date('Y-m-d').' 00:00:00';
	$e_date = date('Y-m-d',strtotime('+6 days')).' 00:00:00';
	$que = "SELECT * FROM `super100_wp`.`br3_contests_nfl`
			WHERE `week_num` = ".$cur_week."
			AND ps_away = '' AND ps_home = ''";
	$query = mysql_query($que, $br_link);
	$count = @mysql_num_rows($query);
	//echo 'count: '.$count.'<br>';
	if($count > 0 )
	{
		while($res = mysql_fetch_assoc($query))
		{
			//$res['home_team'] = str_replace('.','',$res['home_team']);
			//$res['away_team'] = str_replace('.','',$res['away_team']);
			$que2 = "SELECT spread_home, spread_visiting FROM `super100_lines`.`ol_lines_ps`
					 WHERE league_id = '4'
					 AND period = '0'
					 AND `game_date` = '".substr($res['game_date'],0,10)."'
					 AND `home` = '".$res['home_team']."'
					 AND `vistor` = '".$res['away_team']."'
					 ORDER BY `ol_id` DESC LIMIT 1";
			//echo '<br> que2 '.$que2;
			$query2 = mysql_query($que2,$ln_link);
			if(@mysql_num_rows($query2) > 0)
			{
				$pres = mysql_fetch_assoc($query2);
				$ps_away = ($pres['spread_visiting'] > 0) ? '+'.$pres['spread_visiting'] : $pres['spread_visiting'];
				$ps_home = ($pres['spread_home'] > 0) ? '+'.$pres['spread_home'] : $pres['spread_home'];
				$upd_que = "UPDATE `super100_wp`.`br3_contests_nfl` 
							SET ps_away = '".$ps_away."', 
								ps_home = '".$ps_home."' 
							WHERE game_id = '".$res['game_id']."'";
				//echo '<br>upd spreads: '.$upd_que.'<br>';
				@mysql_query($upd_que, $br_link);
			}
		}
	}
}

function _updateNFLSchedule()
{
	global $ln_link, $br_link;
	
	$cur_week = getCurrentWeek();
	$pas_week = $cur_week - 1;
	
	$b_date = date('Y-m-d').' 00:00:00';
	$e_date = date('Y-m-d',strtotime('+7 days')).' 00:00:00';
	
	/*******************************************************
	update past weeks scores
	*******************************************************/
	$que = "SELECT * FROM `super100_wp`.`br3_contests_nfl`
			WHERE `week_num` = ".$pas_week."
			AND away_score = '0' AND home_score = '0'";
	//echo '<br>past week que: '.$que.'<br>';
	$query = mysql_query($que, $br_link);
	$count = @mysql_num_rows($query);
	if($count > 0)
	{
		while($res = mysql_fetch_assoc($query))
		{
			$que2 = "SELECT visitor_score, home_score 
					 FROM `super100_lines`.`ol_games`
					 WHERE league_id = '4'
					 AND `game_date` = '".substr($res['game_date'],0,10)."'
					 AND `team_home` = '".$res['home_team']."'
					 AND `team_visitor` = '".$res['away_team']."'
					 AND (`visitor_score` > 0 OR `home_score` > 0)";
			$query2 = mysql_query($que2,$ln_link);
			if(@mysql_num_rows($query2) > 0)
			{
				$pres = mysql_fetch_assoc($query2);
				$away_score = $pres['visitor_score'];
				$home_score = $pres['home_score'];
				$ps_away = ($res['ps_away'] > 0) ? $res['ps_away'] : 0;
				$ps_home = ($res['ps_home'] > 0) ? $res['ps_home'] : 0;
				$psa_score = $away_score + $ps_away;
				$psh_score = $home_score + $ps_home;
				$winn_team = ($away_score > $home_score) ? $res['away_team'] : $res['home_team'];
				if($away_score == $home_score)$winn_team = 'Tie';//if straight tie, override winning team
				$wins_team = ($psa_score > $psh_score) ? $res['away_team'] : $res['home_team'];
				if($psa_score == $psh_score)$wins_team = 'Tie';//if spread tie, override winning spread team
				$upd_que = "UPDATE `super100_wp`.`br3_contests_nfl` 
							SET away_score = '".$away_score."', 
								home_score = '".$home_score."', 
								winning_team = '".$winn_team."',
								winning_spread_team = '".$wins_team."'
							WHERE game_id = '".$res['game_id']."'";
				//echo "<br>update past week scores: ".$upd_que.'<br>';
				@mysql_query($upd_que, $br_link);
			}
		}//end of while($res...
	}//end of if($count > 0)
	
	
	/*******************************************************
	update current week schedule
	*******************************************************/
	$cque = "SELECT * FROM `super100_wp`.`br3_contests_nfl`
			 WHERE `week_num` = ".$cur_week."";
	$cquery = mysql_query($cque, $br_link);
	$ccount = @mysql_num_rows($cquery);
	//echo '<br>update current week ccount: '.$ccount.'<br>';
	if($ccount == 0)
	{
		$sque = "SELECT CONCAT( 'NULL' ) , CONCAT( '".$cur_week."' ) , CONCAT( game_date, ' ', game_time, ':00' ) , team_visitor, team_home
				 FROM `super100_lines`.`ol_games`
				 WHERE `league_id` = 4
				 AND `game_date` > '".$b_date."'
				 AND `game_date` < '".$e_date."'
				 ORDER BY `ol_games`.`game_id` ASC";
		//echo '<br>sque: '.$sque;
		$squery = mysql_query($sque, $ln_link);
		$srows = array();
		if(@mysql_num_rows($squery) > 0)
		{
			while($sres = mysql_fetch_row($squery))
			{
				$values = array();
				while (list($key, $val) = each($sres))
				{
				  $values[] = "'".$val."'";
				}
				$srows[] = "(".implode(', ', $values).")";
			}
			$ins_que = "INSERT INTO super100_wp.br3_contests_nfl
						(`game_id`, `week_num`, `game_date`, `away_team`, `home_team`)
						VALUES ".implode(', ',$srows);
			@mysql_query($ins_que, $br_link);
		}
	}
}

//update contests leaders, default contest 2 (NFL spreads)
function _updateContestLeaderboard($contest_id = 2)
{
	global $ln_link, $br_link;
	$curr_week = getCurrentWeek();
	$week = $curr_week-1;
	
	$query = 'SELECT * 
			  FROM `super100_wp`.`br3_contests_picks`
			  WHERE contest_id = '.$contest_id;
	if($week > 0)$query .= ' AND week_num = '.$week;
	$query .= ' ORDER BY user_id, week_num';
	//echo 'query: '.$query.'<br>';
	$result = mysql_query($query, $br_link);
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
			  if($contest_id == 1)
			  {
			  	$res['winn_team'] = $team_arr[0]['winning_team'];
			  }
			  if($contest_id == 2)
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
				if($res['pick_id']>0 && ($p != $res['user_points']) )
				{
					$upd_que = 'UPDATE `super100_wp`.`br3_contests_picks`
								SET `user_points` = '.$p.'
								WHERE `pick_id` = '.$res['pick_id'];
					//echo $upd_que.'<br>';
					@mysql_query($upd_que, $br_link);
				}
			  }
			}
		}
	}
}

function getTeamsByWeek($week, $team='')
{
	global $br_link;
	
	$query = 'SELECT * 
			  FROM `super100_wp`.`br3_contests_nfl`
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
	echo '<br>teamsByWeek que: '.$query;
	$result = mysql_query($query, $br_link);
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

function getCurrentWeek()
{
	$fdate = strtotime('2013-08-27');//last tuesday from week 1
	$dayofweek = date('l');
	if($dayofweek == 'Tuesday')
	{
		$cdate = strtotime( date('Y-m-d') );
	}else
	{
		$cdate = strtotime('last Tuesday');
	}
	$diff = round(($cdate - $fdate) / 604800);
	
	return $diff;
}
?>