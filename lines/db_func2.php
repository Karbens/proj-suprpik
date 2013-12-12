<?php
// error_reporting(E_ALL); ini_set('display_errors', 1); // uncomment this line for debugging

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

include_once('lines.class.php');
include_once('xmltoarray.class.php');

function tep_db_connect() {
    global $db_link;

	if( eregi("sb.net",$_SERVER['SERVER_NAME']) )
	{
      	$db_link = mysql_connect('localhost', 'root', 'yogster');
      	mysql_select_db('sbnetdb');
	}else
	{
		$db_link = @mysql_connect('192.168.29.102:3306', 'joesbnet', 'dathUhuch8tr');//main site connection
		@mysql_select_db('sbnetdb');
	}
    
    if (!$db_link) 
	{
	  die('Could not connect: ' . mysql_error());
	}

    //return $db_link;
}

function tep_db_close() {
    global $db_link;

    $result = mysql_close($db_link);
    
    return $result;
}

function stripslashes_recursive($var) {
	return (is_array($var) ? array_map('stripslashes_recursive', $var) : stripslashes($var));
}

function getBJFeed($post) 
{ 
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, 'http://www.betjamaica.com/livelines2008/lines.asmx/Load_Latest_Lines'); 
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch); 
	if (curl_errno($ch) > 0) $result = -1;
	curl_close($ch); 
	return $result;
}

function getLeagueParams($lg)
{
	$l_que = mysql_query("SELECT league_id, sport FROM `ol_leagues`
						  WHERE league = '".mysql_real_escape_string($lg)."'");
	$ret = array();
	if(@mysql_num_rows($l_que) > 0)
	{
		$ret = mysql_fetch_assoc($l_que);
	}
	return $ret;
}

function getLeagueSport($id)
{
	$l_que = mysql_query("SELECT sport FROM `ol_leagues`
						  WHERE league_id = '".mysql_real_escape_string($id)."'");
	$ret = '';
	if(@mysql_num_rows($l_que) > 0)
	{
		$res = mysql_fetch_row($l_que);
		$ret = $res[0];
	}
	return $ret;
}


function getGames($league_id, $gdate, $short_name='Game')
{
	if($league_id > 3 && $league_id < 8)
	{
		$ntime = strtotime($gdate) + (86400*7);
	}else
	{
	    $ntime = strtotime($gdate) + 86400;
	}
	$ndate = date('Y-m-d', $ntime);
	/*$pregque = "SELECT g. *
				FROM `ol_games` g, `ol_lines_bd` b
				WHERE g.game_id = b.game_id
				AND b.short_name = '".$short_name."'
				AND g.league_id = '".$league_id."'
				AND (
				g.game_date = '".$gdate."'
				OR g.game_date = '".$ndate."'
				)
				GROUP BY rot_visitor
				ORDER BY game_date, rot_visitor, rot_home";*/
	$pregque = "SELECT * FROM `ol_games`
				WHERE league_id = '".$league_id."'
				AND rot_visitor > 0
				AND rot_home > 0
				AND (game_date >= '".$gdate."' AND game_date <= '".$ndate."')
				GROUP BY rot_visitor
				ORDER BY game_date,rot_visitor,rot_home";
	//get short names for MLB
	if($league_id == 1)
	{
		session_start();
		if( !isset($_SESSION['mlb_teams']) || count($_SESSION['mlb_teams']) != 30 )
		{
		  unset($_SESSION['mlb_teams']);
		  $mlb_que = 'SELECT team_name, team_short_name
		  			  FROM `ol_teams`
					  WHERE league_id = 1';
		  $mlb_query = mysql_query($mlb_que);
		  $mlb_array = array();
		  while($mlb_res = mysql_fetch_row($mlb_query))
		  {
		  	$mlb_key = trim($mlb_res[0]);
			$mlb_val = trim($mlb_res[1]);
			$mlb_array[$mlb_key] = $mlb_val;
		  }
		  $_SESSION['mlb_teams'] = $mlb_array;
		}
	}
	$gque = mysql_query($pregque);
	$games = array();
	if(@mysql_num_rows($gque) > 0)
	{
		while($gres = mysql_fetch_assoc($gque))
		{
			//update short names for MLB
			if($league_id == 1)
			{
				$teamh = trim($gres['team_home']);
				$teamv = trim($gres['team_visitor']);
				$gres['team_home'] = $teamh;
				$gres['team_visitor'] = $teamv;
				$gres['team_home_short'] = $teamh;
				$gres['team_visitor_short'] = $teamv;
				if( isset($_SESSION['mlb_teams'][$teamh]) )$gres['team_home_short'] = $_SESSION['mlb_teams'][$teamh];
				if( isset($_SESSION['mlb_teams'][$teamv]) )$gres['team_visitor_short'] = $_SESSION['mlb_teams'][$teamv];
				//remove (R) from right handed pitchers
				$gres['pitcher_home'] = str_replace('(R)', '', $gres['pitcher_home']);
				$gres['pitcher_visitor'] = str_replace('(R)', '', $gres['pitcher_visitor']);
			}
			$games[] = $gres;
		}
	}
	return $games;
}

function getGameByID($gid, $date, $short_name='Game')
{
	$gque = "SELECT g.* 
			 FROM `ol_games` g
			 WHERE g.rot_visitor = '".$gid."'
			 AND g.game_date = '".$date."'
			 GROUP BY g.game_id
			 ";
	$gquery = mysql_query($gque);
	$ret = array();
	if(@mysql_num_rows($gquery) > 0)
	{
		$ret = mysql_fetch_assoc($gquery);
	}
	return $ret;
}

function getAllOpeners($arr)
{
	$openers = array();
	
	$openers = _getSBLine($arr);
	
	return $openers;
}

function getAllLines($arr)
{
	$lines = array();
	
	
	//$lines[1] = _getSBookLine($arr);
	$lines[2] = _getSBLine($arr);//latest line for sportsbetting.com
	$lines[3] = _getSBLineFinal($arr);//bet final for sportsbetting.com
	//$lines[3] = _getBDLine($arr);
	//$lines[4] = _getBJLine($arr);
	//$lines[5] = _getPSLine($arr);
	
	return $lines;
}

function _getSBookLine($arr)
{
	//extract arr params
	extract($arr);
	
	if(trim($home)!= "")
	{
	  $home = str_replace("Los Angeles","LA",$home);
	  $home = str_replace("New York Mets","NY Mets",$home);
	  $home = str_replace("Portland Trail Blazers","Portland Blazers",$home);
	  $home = str_replace(".","",$home);
	  $home = "%".str_replace(" ","%",$home)."%";
	}
	if(trim($away)!= "")
	{
	  $away = str_replace("Los Angeles","LA",$away);
	  $away = str_replace("New York Mets","NY Mets",$away);
	  $away = str_replace("Portland Trail Blazers","Portland Blazers",$away);
	  $away = str_replace(".","",$away);
	  $away = "%".str_replace(" ","%",$away)."%";
	}
	$sel_period = $period;
	if($period == '3')$sel_period = 0;
	
	$que = "SELECT * FROM `ol_lines_sbook` 
			WHERE `league_id` = '".$league_id."'
			and `period` = '".$sel_period."'
			and `date` = '".$date."' 
			and `rot_home` = '".$rot_home."'
			and `rot_away` = '".$rot_away."'
			order by `ol_id` desc
			limit 1";
	$chque = mysql_query($que);
	
	if(@mysql_num_rows($chque) == 0)
	{
	$que = "SELECT * FROM `ol_lines_sbook` 
			WHERE `league_id` = '".$league_id."'
			and `period` = '".$sel_period."'
			and `date` = '".$date."' 
			and `home` like '".$home."'
			and `away` like '".$away."'";
	}else
	{
	$que = "SELECT * FROM `ol_lines_sbook` 
			WHERE `league_id` = '".$league_id."'
			and `period` = '".$sel_period."'
			and `date` = '".$date."' 
			and `rot_home` = '".$rot_home."'
			and `rot_away` = '".$rot_away."'";
	}
	if($opener == '1')
	{
		 if($league_sport == 'Basketball' || $league_sport == 'Football')
		 {
		 	if( $period == '3' )
			{
				$que .= 
			 	  " and `money_home` != 'OFF' and `money_away` != 'OFF'
			 	    order by `ol_id`
				    limit 1";
			}else
			{
				$que .= 
			 	  " and `bet_home` != 'OFF' and `bet_away` != 'OFF'
			 	    order by `ol_id`
				    limit 1";
			}
		 }else
		 {
		    if( $period == '3' )
			{
				$que .= 
			 	  " and `bet_home` != 'OFF' and `bet_away` != 'OFF'
			 	    order by `ol_id`
				    limit 1";
			}else
			{
				$que .= 
			 	  " and `money_home` != 'OFF' and `money_away` != 'OFF'
			 	    order by `ol_id`
				    limit 1";
			}
		 }
	}else
	{
	  $que .= " order by `ol_id` desc
			    limit 1";
	}
	//if($opener == '1')echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	$ret['book_id'] = '1';
	if(@mysql_num_rows($query) > 0)
	{
		$res = mysql_fetch_assoc($query);
		$ret['visitor_seconds'] = 10800 + strtotime('now') - strtotime($res['date_updated']);
		$ret['home_seconds'] = $ret['visitor_seconds'];
		if($period == '3')
		{
		  if($league_sport == 'Basketball' || $league_sport == 'Football')
		  {
			$ret['visitor_value'] = $res['money_away'];
		    $ret['home_value'] = $res['money_home'];
			
		  }else
		  {
			$ret['visitor_value'] = $res['bet_away'];
		    $ret['home_value'] = $res['bet_home'];
		  }
		}
		elseif($period == '4')
		{
		  $ret['visitor_value'] = $res['over'];
		  $ret['home_value'] = $res['under'];
		}
		else
		{
			
			//for the opener, get first over under
			if($opener == 1 )
			{
			   $oque = "SELECT * FROM `ol_lines_sbook` 
						WHERE league_id = '".$league_id."'
						and period = '".$sel_period."'
						and date = '".$date."' 
						and rot_home = '".$rot_home."'
						and rot_away = '".$rot_away."'
						and over != 'OFF'
		 	    		order by ol_id
			    		limit 1";
			   $ochque = mysql_query($oque);
			   if(@mysql_num_rows($ochque) == 0)
			   {
			   $oque = "SELECT * FROM `ol_lines_sbook` 
						WHERE league_id = '".$league_id."'
						and period = '".$sel_period."'
						and date = '".$date."' 
						and home like '".$home."'
						and away like '".$away."'
						and over != 'OFF'
		 	    		order by ol_id
			    		limit 1";
				}
				$oquery = mysql_query($oque);
				$ores = mysql_fetch_assoc($oquery);
				//set totals, over and under
			    $tot_p = explode("(",$ores['over']);
				$tot = trim($tot_p[0]);
				if(count($tot_p) > 1)
				{
					$over_p = explode(")",$tot_p[1]);
					$over_adj = $over_p[0];
				}
				else
				{
					$over_adj = 0;
				}
				$tot_u = explode("(",$ores['under']);
				if(count($tot_u) > 1)
				{
					$under_p = explode(")",$tot_u[1]);
					$under_adj = $under_p[0];
				}
				else
				{
					$under_adj = 0;
				}
			}
			else
			{
				//set totals, over and under
			    $tot_p = explode("(",$res['over']);
				$tot = trim($tot_p[0]);
				if(count($tot_p) > 1)
				{
					$over_p = explode(")",$tot_p[1]);
					$over_adj = $over_p[0];
				}
				else
				{
					$over_adj = 0;
				}
				$tot_u = explode("(",$res['under']);
				if(count($tot_u) > 1)
				{
					$under_p = explode(")",$tot_u[1]);
					$under_adj = $under_p[0];
				}
				else
				{
					$under_adj = 0;
				}
			}//end of  else if($opener == 1 && $res['over'] == "")
			
			if($league_sport == 'Basketball' || $league_sport == 'Football')
		    {
				$ret['visitor_value'] = "";
				$ret['home_value'] = $tot;
				if($res['bet_away'] < $res['bet_home'])
				{
				  $ret['visitor_value'] = $res['bet_home'];
				  $ret['home_value'] = $tot;
				}
				elseif($res['bet_home'] < $res['bet_away'])
				{
					$ret['visitor_value'] = $tot;
				    $ret['home_value'] = $res['bet_away'];
				}
				elseif($res['bet_home'] == $res['bet_away'] && $res['bet_home'] != "" && $res['bet_away'] != "")
				{
					 $ret['visitor_value'] = "pk";
				  	 $ret['home_value'] = $tot;
					 
				}
				elseif($res['bet_home'] == "" && $res['bet_away'] == "" && $res['money_home'] != "" && $res['money_away'] != "")
				{
					if($res['money_home'] < $res['money_away'])
					{
						$ret['visitor_value'] = $tot;
				  	    $ret['home_value'] = "";
					}
					elseif($res['money_away'] < $res['money_home'])
					{
						$ret['visitor_value'] = "";
				  	    $ret['home_value'] = $tot;
					}
					
				}
		    }else//if($league_sport == 'Basketball' || $league_sport == 'Football')
		    {
				//over_adjust 	under_adjust
				if($over_adj < $under_adj)$tot.="o".$over_adj;
				if($under_adj < $over_adj)$tot.="u".$under_adj;
				
				$ret['visitor_value'] = "";
				$ret['home_value'] = $tot;
				if($res['money_home'] != "" && $res['money_away'] != "" && ($res['money_away'] != $res['money_home']) )
				{
					if($res['money_home'] < $res['money_away'])
					{
						$ret['visitor_value'] = $tot;
				  	    $ret['home_value'] = $res['money_home']."/".$res['money_away'];
					}
					elseif($res['money_away'] < $res['money_home'])
					{
						$ret['visitor_value'] = $res['money_away']."/".$res['money_home'];
				  	    $ret['home_value'] = $tot;
					}
					
				}
				elseif( substr($res['bet_away'],0,1) == "-" )
				{
				  $ret['visitor_value'] = $res['money_away']."/".$res['money_home'];
				  $ret['home_value'] = $tot;
				}
				elseif( substr($res['bet_home'],0,1) == "-" )
				{
					$ret['visitor_value'] = $tot;
				    $ret['home_value'] = $res['money_home']."/".$res['money_away'];
				}
				elseif( ($res['bet_home'] == $res['bet_away']) && $res['bet_home'] != "OFF" && $res['bet_away'] != "OFF")
				{
					 $ret['visitor_value'] = "pk";
				  	 $ret['home_value'] = $tot;
				}
				
		    }//end of else if($league_sport == 'Basketball'..)
		}
		$ret['visitor_value'] = str_replace(".0","",$ret['visitor_value']);
		$ret['home_value'] = str_replace(".0","",$ret['home_value']);
		$ret['visitor_value'] = str_replace(".5","&amp;frac12;",$ret['visitor_value']);
		$ret['home_value'] = str_replace(".5","&amp;frac12;",$ret['home_value']);
		$ret['visitor_value'] = str_replace("&frac12;","&amp;frac12;",$ret['visitor_value']);
		$ret['home_value'] = str_replace("&frac12;","&amp;frac12;",$ret['home_value']);
		$ret['visitor_value'] = str_replace("+","", $ret['visitor_value']);
		$ret['home_value'] = str_replace("+","", $ret['home_value']);
		
	}//end of if(@mysql_num_rows($query) > 0)
	
	return $ret;
}

function _getSBLine($arr, $final = 0)
{
	//set array params
	extract($arr);
	
	if(trim($home)!= "")
	{
	  //$home = str_replace("Los Angeles","LA",$home);
	  //$home = str_replace("New York Mets","NY Mets",$home);
	  //$home = str_replace("Portland Trail Blazers","Portland Blazers",$home);
	  $home = str_replace(".","",$home);
	}
	if(trim($away)!= "")
	{
	  //$away = str_replace("Los Angeles","LA",$away);
	  //$away = str_replace("New York Mets","NY Mets",$away);
	  //$away = str_replace("Portland Trail Blazers","Portland Blazers",$away);
	  $away = str_replace(".","",$away);
	}
	
	//set game_line
	$game_line = "Game";
	if($period != '0')
	{
		if($league_id == 1 && $period == '1')
		{
			$game_line = "5inn";
		}
		if( ($league_sport == 'Basketball'  || $league_sport == 'Football') && $period != '3')
		{
			switch($period)
			{
				case '1':
					$game_line = "1H";
					if($league_id == 4)
					{
						$teamh_pei = explode(" ", $home);
						$teama_pei = explode(" ", $away);
						$hc = count($teamh_pei)-1;
						$ac = count($teama_pei)-1;
						$home = $teamh_pei[$hc].' - 1H';
						$away = $teama_pei[$ac].' - 1H';
					}
					if($league_sport == 'Football')$league_id = '6';
					break;
				case '2':
					$game_line = "2H";
					if($league_id == 4)
					{
						$teamh_pei = explode(" ", $home);
						$teama_pei = explode(" ", $away);
						$hc = count($teamh_pei)-1;
						$ac = count($teama_pei)-1;
						$home = $teamh_pei[$hc].' - 2H';
						$away = $teama_pei[$ac].' - 2H';
					}
					if($league_sport == 'Football')$league_id = '6';
					break;
				default:
					$game_line = $period;
			}
		}
	}
	//end of set game_line
	
	if(trim($home)!= "")$home = "%".str_replace(" ","%",$home)."%";
	if(trim($away)!= "")$away = "%".str_replace(" ","%",$away)."%";
	$que = "SELECT * FROM `ol_lines_sb` 
				WHERE league_id = '".$league_id."'
				and game_line = '".$game_line."'
				and date = '".$date."' 
				and home like '".$home."'
				and away like '".$away."'";
	$que_tot = $que;
	if($opener == '1')
	{
		 if($league_sport == 'Basketball' || $league_sport == 'Football')
		 {
		 	if( $period == '3' )
			{
				$que .= 
			 	  " AND `mlhome` != '' AND `mlaway` != ''
			 	    ORDER BY `ol_id`
				    LIMIT 1";
			}else
			{
				$que .= 
			 	  " AND `pshome` != '' AND `psaway` != ''
			 	    ORDER BY `ol_id`
				    LIMIT 1";
			}
		 }else
		 {
		    if( $period == '3' )
			{
				$que .= 
			 	  " AND `pshome` != '' AND `psaway` != ''
			 	    ORDER BY `ol_id`
				    LIMIT 1";
			}else
			{
				$que .= 
			 	  " AND `mlhome` != '' AND `mlaway` != ''
			 	    ORDER BY `ol_id`
				    LIMIT 1";
			}
		 }
	}else
	{
	  	 if($league_sport == 'Basketball' || $league_sport == 'Football')
		 {
		 	if( $period == '3' )
			{
				$que .= 
			 	  " AND `mlhome` != '' AND `mlaway` != ''";
			}else
			{
				$que .= 
			 	  " AND `pshome` != '' AND `psaway` != ''";
			}
		 }else
		 {
		    if( $period == '3' )
			{
				$que .= 
			 	  " AND `pshome` != '' AND `psaway` != ''";
			}else
			{
				$que .= 
			 	  " AND `mlhome` != '' AND `mlaway` != ''";
			}
		 }
		 $que .= " ORDER BY `ol_id` DESC
				LIMIT 1";
	}
	//echo '<br>'.$que."<br>";
	//exit();
	$query = mysql_query($que);
	$ret = array();
	$ret['book_id'] = '2';
	if(@mysql_num_rows($query) > 0)
	{
		$res = mysql_fetch_assoc($query);
		$ret['visitor_seconds'] = 10800 + strtotime('now') - strtotime($res['date_updated']);
		$ret['home_seconds'] = $ret['visitor_seconds'];
		if($period == '3')
		{
		  if($league_sport == 'Basketball'  || $league_sport == 'Football')
		  {
		    $ret['visitor_value'] = $res['mlaway'];
		    $ret['home_value'] = $res['mlhome'];
		  }elseif($res['psaway'] != "" && $res['pshome'] != "")
		  {
			$ret['visitor_value'] = $res['psaway']." (".$res['psawaym'].")";
		    $ret['home_value'] = $res['pshome']." (".$res['pshomem'].")";
		  }
		  
		}
		elseif($period == '4')
		{
		  $ret['visitor_value'] = $res['ouaway']." ".$res['ouawaym'];
		  $ret['home_value'] = $res['ouhome']." ".$res['ouhomem'];
		}
		else
		{
			if($league_sport == 'Basketball' || $league_sport == 'Football')
		    {
		      if( $res['psaway'] != "" &&  $res['pshome'] != "")
			  {
			    if($opener == '1')
			    {
					$que_tot .=   " and `ouhome` != ''
							 	    order by `ol_id`
								    limit 1";
					//echo $que_tot; exit();
					$query_tot = mysql_query($que_tot);
					$res_tot = mysql_fetch_assoc($query_tot);
				
					$tot_p = explode("U",$res_tot['ouhome']);
					$tot = trim($tot_p[1]);
					if($res_tot['ouawaym'] < $res_tot['ouhomem'])$tot.="o".$res_tot['ouawaym'];
					if($res_tot['ouhomem'] < $res_tot['ouawaym'])$tot.="u".$res_tot['ouhomem'];
			    }else
			    {
				
					$tot_p = explode("U",$res['ouhome']);
					$tot = trim($tot_p[1]);//." ".$res['ouawaym']."/".$res['ouhomem'];
					if($res['ouawaym'] < $res['ouhomem'])$tot.="o".$res['ouawaym'];
					if($res['ouhomem'] < $res['ouawaym'])$tot.="u".$res['ouhomem'];
				
			    }
				
				if($res['psaway'] < $res['pshome'])
				{
				  $ret['visitor_value'] = $res['pshome'];
				  if($res['pshomem'] != '-110')$ret['visitor_value'] .= ' ('.$res['psawaym'].')';
				  $ret['home_value'] = $tot;
				}
				elseif($res['pshome'] < $res['psaway'])
				{
					$ret['visitor_value'] = $tot;
				    $ret['home_value'] = $res['psaway'];
					if($res['psawaym'] != '-110')$ret['home_value'] .= ' ('.$res['pshomem'].')';
				}
				elseif($res['pshome'] == $res['psaway'] && $res['pshome'] == "" && $res['psaway'] == "")
				{
					 $ret['visitor_value'] = "OFF";
				  	 $ret['home_value'] = $tot;
				}
				elseif($res['pshome'] == $res['psaway'] && $res['pshome'] != "" && $res['psaway'] != "")
				{
					 $ret['visitor_value'] = "pk";
				  	 $ret['home_value'] = $tot;
				}
				else
				{
					 $ret['visitor_value'] = "";
				  	 $ret['home_value'] = $tot;
				}
			  }
		    }else//if($league_sport == 'Basketball'...)
		    {
		  	  /*if( $res['psaway'] != "OFF" &&  $res['pshome'] != "OFF")
			  {*/
			  	
			 if($opener == '1')
			 {
				$que_tot .=   " and `ouhome` != ''
						 	    order by `ol_id`
							    limit 1";
				//echo $que_tot; exit();
				$query_tot = mysql_query($que_tot);
				$res_tot = mysql_fetch_assoc($query_tot);
				
				$tot_p = explode("U",$res_tot['ouhome']);
				$tot = trim($tot_p[1]);
				if($res_tot['ouawaym'] < $res_tot['ouhomem'])$tot.="o".$res_tot['ouawaym'];
				if($res_tot['ouhomem'] < $res_tot['ouawaym'])$tot.="u".$res_tot['ouhomem'];
				$ret['visitor_value'] = "";
				$ret['home_value'] = $tot; 
			 }else
			 {
				$tot_p = explode("U",$res['ouhome']);
				$tot = trim($tot_p[1]);//." ".$res['ouawaym']."/".$res['ouhomem'];
				if($res['ouawaym'] < $res['ouhomem'])$tot.="o".$res['ouawaym'];
				if($res['ouhomem'] < $res['ouawaym'])$tot.="u".$res['ouhomem'];
				$ret['visitor_value'] = "";
				$ret['home_value'] = $tot; 
			 }
				if($res['mlhome'] != "" && $res['mlaway'] != "" && ($res['mlhome']!=$res['mlaway']) )
				{
					if($res['mlaway'] < $res['mlhome'])
					{
						$ret['visitor_value'] = $res['mlaway']."/".$res['mlhome'];
						$ret['home_value'] = $tot;
					}
					elseif($res['mlhome'] < $res['mlaway'])
					{
						$ret['visitor_value'] = $tot;
				    	$ret['home_value'] = $res['mlhome']."/".$res['mlaway'];
					}
				}
				elseif($res['psaway'] < $res['pshome'])
				{
				  $ret['visitor_value'] = $res['mlaway']."/".$res['mlhome'];
				  $ret['home_value'] = $tot;
				}
				elseif($res['pshome'] < $res['psaway'])
				{
					$ret['visitor_value'] = $tot;
				    $ret['home_value'] = $res['mlhome']."/".$res['mlaway'];
				}
				elseif($res['pshome'] == $res['psaway'] && $res['pshome'] == "" && $res['psaway'] == "")
				{
					 $ret['visitor_value'] = "OFF";
				  	 $ret['home_value'] = $tot;
				}
				elseif( ($res['pshome'] == $res['psaway']) && $res['pshome'] != "" && $res['psaway'] != "")
				{
					 $ret['visitor_value'] = "pk";
				  	 $ret['home_value'] = $tot;
				}
				
			  //}
			  
		    }//end of else if($league_sport == 'Basketball'...)
		}
		$ret['visitor_value'] = str_replace(".5","&amp;frac12;",$ret['visitor_value']);
		$ret['home_value'] = str_replace(".5","&amp;frac12;",$ret['home_value']);
		$ret['visitor_value'] = str_replace("+","", $ret['visitor_value']);
		$ret['home_value'] = str_replace("+","", $ret['home_value']);
	}
	return $ret;
}

function _getSBLineFinal($arr)
{
	//set array params
	$ret = array();
	$ret['book_id'] = '3';
	$ret['visitor_value'] = '';
	$ret['home_value'] = '';
	$ret['visitor_slide'] = '';
	$ret['home_slide'] = '';
	$ret['visitor_total'] = '';
	$ret['home_total'] = '';
	
	//extract passed array parameters
	extract($arr);
	if($game_status != 'Final')
	{
		$ret['visitor_seconds'] = 10800 + strtotime($date);
		$ret['home_seconds'] = $ret['visitor_seconds'];
		return $ret;
	}
	$total_score = $home_score + $away_score;
	
	if(trim($home)!= "")
	{
	  //$home = str_replace("Los Angeles","LA",$home);
	  //$home = str_replace("New York Mets","NY Mets",$home);
	  //$home = str_replace("Portland Trail Blazers","Portland Blazers",$home);
	  $home = str_replace(".","",$home);
	}
	if(trim($away)!= "")
	{
	  //$away = str_replace("Los Angeles","LA",$away);
	  //$away = str_replace("New York Mets","NY Mets",$away);
	  //$away = str_replace("Portland Trail Blazers","Portland Blazers",$away);
	  $away = str_replace(".","",$away);
	}
	
	//set game_line
	$game_line = "Game";
	if($period != '0')
	{
		if($league_id == 1 && $period == '1')
		{
			$game_line = "5inn";
		}
		if( ($league_sport == 'Basketball'  || $league_sport == 'Football') && $period != '3')
		{
			switch($period)
			{
				case '1':
					$game_line = "1H";
					if($league_id == 4)
					{
						$teamh_pei = explode(" ", $home);
						$teama_pei = explode(" ", $away);
						$hc = count($teamh_pei)-1;
						$ac = count($teama_pei)-1;
						$home = $teamh_pei[$hc].' - 1H';
						$away = $teama_pei[$ac].' - 1H';
					}
					if($league_sport == 'Football')$league_id = '6';
					break;
				case '2':
					$game_line = "2H";
					if($league_id == 4)
					{
						$teamh_pei = explode(" ", $home);
						$teama_pei = explode(" ", $away);
						$hc = count($teamh_pei)-1;
						$ac = count($teama_pei)-1;
						$home = $teamh_pei[$hc].' - 2H';
						$away = $teama_pei[$ac].' - 2H';
					}
					if($league_sport == 'Football')$league_id = '6';
					break;
				default:
					$game_line = $period;
			}
		}
	}
	//end of set game_line
	
	if(trim($home)!= "")$home = "%".str_replace(" ","%",$home)."%";
	if(trim($away)!= "")$away = "%".str_replace(" ","%",$away)."%";
	$que = "SELECT * FROM `ol_lines_sb` 
				WHERE league_id = '".$league_id."'
				and game_line = '".$game_line."'
				and date = '".$date."' 
				and home like '".$home."'
				and away like '".$away."'";
	$que_tot = $que;
	if($opener == '1')
	{
		 if($league_sport == 'Basketball' || $league_sport == 'Football')
		 {
		 	if( $period == '3' )
			{
				$que .= 
			 	  " AND `mlhome` != '' AND `mlaway` != ''
			 	    ORDER BY `ol_id`
				    LIMIT 1";
			}else
			{
				$que .= 
			 	  " AND `pshome` != '' AND `psaway` != ''
			 	    ORDER BY `ol_id`
				    LIMIT 1";
			}
		 }else
		 {
		    if( $period == '3' )
			{
				$que .= 
			 	  " AND `pshome` != '' AND `psaway` != ''
			 	    ORDER BY `ol_id`
				    LIMIT 1";
			}else
			{
				$que .= 
			 	  " AND `mlhome` != '' AND `mlaway` != ''
			 	    ORDER BY `ol_id`
				    LIMIT 1";
			}
		 }
	}else
	{
	  	 if($league_sport == 'Basketball' || $league_sport == 'Football')
		 {
		 	if( $period == '3' )
			{
				$que .= 
			 	  " AND `mlhome` != '' AND `mlaway` != ''";
			}else
			{
				$que .= 
			 	  " AND `pshome` != '' AND `psaway` != ''";
			}
		 }else
		 {
		    if( $period == '3' )
			{
				$que .= 
			 	  " AND `pshome` != '' AND `psaway` != ''";
			}else
			{
				$que .= 
			 	  " AND `mlhome` != '' AND `mlaway` != ''";
			}
		 }
		 $que .= " ORDER BY `ol_id` DESC
				LIMIT 1";
	}
	//echo '<br>'.$que."<br>";
	//exit();
	$query = mysql_query($que);
	if(@mysql_num_rows($query) > 0)
	{
		$res = mysql_fetch_assoc($query);
		$ret['visitor_seconds'] = 10800 + strtotime('now') - strtotime($res['date_updated']);
		$ret['home_seconds'] = $ret['visitor_seconds'];
		$ret['visitor_value'] = '';
		$ret['home_value'] = '';
			if($league_sport == 'Basketball' || $league_sport == 'Football')
		    {
		      if( $res['psaway'] != "" &&  $res['pshome'] != "")
			  {
			      $tot_p = explode("U",$res['ouhome']);
				  $tot = trim($tot_p[1]);
				  //set up over/under/tie
				  $tot_text = '';
				  if($total_score > $tot)
				  {
				  	$tot_text = 'Over: '.$total_score;
					$ret['visitor_total'] = 'Over';
				  }
				  elseif($total_score < $tot)
				  {
				  	$tot_text = 'Under: '.$total_score;
					$ret['visitor_total'] = 'Under';
				  }
				  elseif($total_score == $tot)
				  {
				  	$tot_text = ($total_score > 0) ? 'Tie: '.$total_score : '';
					$ret['visitor_total'] = 'Tie';
				  }
				  
				  
				  //set up cover/push
				  $away_score_s = $away_score;
				  $home_score_s = $home_score;
				  if($res['psaway'] > 0)
				  {
				  	$away_score_s = $away_score + $res['psaway'];
				  }
				  elseif($res['pshome'] > 0)
				  {
				  	$home_score_s = $home_score + $res['pshome'];
				  }
				  
				  if($home_score_s > $away_score_s)
				  {
				  	$ret['visitor_value'] = $tot_text;
				    $ret['home_value'] = 'Cover: +'.($home_score_s-$away_score_s);
					if($res['psaway'] > 0)
					{
						$ret['visitor_slide'] = 'Favorite';
					}
					else
					{
						$ret['visitor_slide'] = 'Dog';
					}
				  }
				  elseif($away_score_s > $home_score_s)
				  {
				  	$ret['visitor_value'] = 'Cover: +'.($away_score_s-$home_score_s);
				    $ret['home_value'] = $tot_text;
					if($res['pshome'] > 0)
					{
						$ret['visitor_slide'] = 'Favorite';
					}
					else
					{
						$ret['visitor_slide'] = 'Dog';
					}
				  }
				  elseif($away_score_s == $home_score_s)
				  {
				  	$ret['visitor_value'] = $tot_text;
				    $ret['home_value'] = 'Push';
					$ret['visitor_slide'] = 'Push';
				  }
			  }
		    }else//if($league_sport == 'Basketball'...)
		    {
		  	  	  $tot_p = explode("U",$res['ouhome']);
				  $tot = trim($tot_p[1]);
				  //set up over/under/tie
				  $tot_text = '';
				  if($total_score > $tot)
				  {
				  	$ret['visitor_value'] = 'Over';
					$ret['visitor_total'] = 'Over';
				    $ret['home_value'] = $tot;
				  }
				  elseif($total_score < $tot)
				  {
				  	$ret['visitor_value'] = 'Under';
					$ret['visitor_total'] = 'Under';
				    $ret['home_value'] = $tot;
				  }
				  elseif($total_score == $tot)
				  {
				  	$ret['visitor_value'] = 'Tie';
					$ret['visitor_total'] = 'Tie';
				    $ret['home_value'] = $tot;
				  }
				  
				  
				  
				  //set up cover/push
				  $away_score_s = $away_score;
				  $home_score_s = $home_score;
				  if($res['psaway'] > 0)
				  {
				  	$away_score_s = $away_score + $res['psaway'];
				  }
				  elseif($res['pshome'] > 0)
				  {
				  	$home_score_s = $home_score + $res['pshome'];
				  }
				  
				  if($home_score_s > $away_score_s)
				  {
					if($res['psaway'] > 0)
					{
						$ret['visitor_slide'] = 'Favorite';
					}
					else
					{
						$ret['visitor_slide'] = 'Dog';
					}
				  }
				  elseif($away_score_s > $home_score_s)
				  {
				  	if($res['pshome'] > 0)
					{
						$ret['visitor_slide'] = 'Favorite';
					}
					else
					{
						$ret['visitor_slide'] = 'Dog';
					}
				  }
				  elseif($away_score_s == $home_score_s)
				  {
				  	$ret['visitor_slide'] = 'Push';
				  }
				  
				  
			  
		    }//end of else if($league_sport == 'Basketball'...)
		
		//echo '<br><pre>'; print_r($ret); echo '</pre>';
		//echo '<br><pre>'; print_r($arr); echo '</pre>';
		$ret['visitor_value'] = str_replace(".5","&amp;frac12;",$ret['visitor_value']);
		$ret['home_value'] = str_replace(".5","&amp;frac12;",$ret['home_value']);
		$ret['visitor_value'] = str_replace("+","", $ret['visitor_value']);
		$ret['home_value'] = str_replace("+","", $ret['home_value']);
	}
	//exit();
	return $ret;
}

function _getBDLine($arr)
{
	//set array params
	foreach($arr as $key => $val)
	{
		$$key = $val;
	}
	
	//set short_name
	$short_name = "Game";
	if($period != '0')
	{
		if($league_id == 1 && $period == '1')
		{
			$short_name = "5inn.";
		}
		if( ($league_sport == 'Basketball' || $league_sport == 'Football') && $period != '3')
		{
			switch($period)
			{
				case '1':
					$short_name = "1H";
					break;
				case '2':
					$short_name = "2H";
					break;
				default:
					$short_name = $period;
			}
		}
	}
	//end of set short_name
	$que = "SELECT visitor_value, visitor_seconds, home_value, home_seconds 
			FROM `ol_lines_bd` 
			WHERE game_id = '".$game_id."'
			AND short_name = '".$short_name."'
			order by ol_id desc
			limit 1";
	//echo $que."<br>"; exit();
	$query = mysql_query($que);
	$ret = array();
	$ret['book_id'] = '3';
	$now = strtotime('now');
	if(@mysql_num_rows($query) > 0)
	{
		if($period == '3')
		{
		  if($league_sport == 'Basketball' || $league_sport == 'Football')
		  {
		  	$ret['visitor_value'] = '';
		    $ret['home_value'] = '';
			$ret['visitor_seconds'] = '';
			$ret['home_seconds'] = '';
			$bque = "SELECT visitor_value, visitor_seconds, home_value, home_seconds 
					FROM `ol_lines_bd` 
					WHERE game_id = '".$game_id."'
					AND short_name = '".$short_name."'
					and period = '0'
					order by ol_id desc
					limit 1";
			//echo $bque.";<br>";
			$bquery = mysql_query($bque);
			if(@mysql_num_rows($bquery) > 0)
			{
				$bres = mysql_fetch_assoc($bquery);
				$ret['visitor_value'] = $bres['visitor_value'];
		    	$ret['home_value'] = $bres['home_value'];
				$ret['visitor_seconds'] = 10800 + $now - $bres['visitor_seconds'];
				$ret['home_seconds'] = 10800 + $now - $bres['home_seconds'];
			}
		  }else
		  {
		    $que = "SELECT visitor_value, visitor_seconds, home_value, home_seconds 
					FROM `ol_lines_bd` 
					WHERE game_id = '".$game_id."'
					AND short_name = '".$short_name."'
					AND period = '3'
					order by ol_id desc
					limit 1";
			//echo $que; exit();
			$query = mysql_query($que);
			$res = mysql_fetch_assoc($query);
			$ret['visitor_value'] = $res['visitor_value'];
		    $ret['home_value'] = $res['home_value'];
			$ret['visitor_seconds'] = 10800 + $now - $res['visitor_seconds'];
			$ret['home_seconds'] = 10800 + $now - $res['home_seconds'];
		  }
		}
		else
		{
			//get spread values
			$sque = "SELECT visitor_value, home_value
					 FROM `ol_lines_bd` 
					 WHERE game_id = '".$game_id."'
					 AND short_name = '".$short_name."'";
			if($league_sport == 'Basketball' || $league_sport == 'Football')
			{
				$sque .= " and period = '3'";
			}else
			{
			    $sque .= " and period = '".$period."'";
			}
			$sque .= " order by ol_id desc limit 1";
			//echo $sque; exit();
			$squery = mysql_query($sque);
			$sres = mysql_fetch_assoc($squery);
			$sv = $sres['visitor_value'];
			$sh = $sres['home_value'];
			
			//get total plus over under
			$tque = "SELECT visitor_value, home_value
					 FROM `ol_lines_bd` 
					 WHERE game_id = '".$game_id."'
					 AND short_name = '".$short_name."'
					 and period = '4'
					 order by ol_id desc
					 limit 1";
			$tquery = mysql_query($tque);
			$tres = mysql_fetch_assoc($tquery);
			
			$ret['visitor_seconds'] = 10800 + $now - $tres['visitor_seconds'];
			$ret['home_seconds'] = 10800 + $now - $tres['home_seconds'];
			
			$tot = $tres['visitor_value'];
			$tou = explode("/",$tres['home_value']);
			$over = $tou[0];
			$under = $tou[1];
			if($league_sport == 'Basketball' || $league_sport == 'Football')
			{
				if(substr($sv,0,1) == "-")
				{
					$ret['visitor_value'] = $sh;
					$ret['home_value'] = $tot;
				}
				elseif(substr($sh,0,1) == "-")
				{
					$ret['visitor_value'] = $tot;
					$ret['home_value'] = $sv;
				}
				else
				{
					$ret['visitor_value'] = "pk";
					$ret['home_value'] = $tot;
				}
			}else
			{
				$que = "SELECT visitor_value, visitor_seconds, home_value, home_seconds 
						FROM `ol_lines_bd` 
						WHERE game_id = '".$game_id."'
					    AND short_name = '".$short_name."'
						AND period = '".$period."'
						order by ol_id desc
						limit 1";
				$query = mysql_query($que);
				$res = mysql_fetch_assoc($query);
				
				$ret['visitor_seconds'] = 10800 + $now - $res['visitor_seconds'];
				$ret['home_seconds'] = 10800 + $now - $res['home_seconds'];
				
				if($over < $under)
				{
					$tot = $tot . "o".$over;
				}elseif($under < $over)
				{
					$tot = $tot . "u".$under;
				}
				if(substr($sv,0,1) == "-")
				{
					  $ret['visitor_value'] = $res['visitor_value']."/".$res['home_value'];
					  $ret['home_value'] = $tot;
				}
				elseif(substr($sh,0,1) == "-")
				{
					$ret['visitor_value'] = $tot;
					$ret['home_value'] = $res['home_value']."/".$res['visitor_value'];
				}
				elseif($sh == $sv && $sh != "" && $sv != "")
				{
					$ret['visitor_value'] = "pk";
					$ret['home_value'] = $tot;
				}
				else
				{
					$ret['visitor_value'] = "";
					$ret['home_value'] = $tot;
				}
			}
		}
		
		$ret['visitor_value'] = str_replace("½","&amp;frac12;", $ret['visitor_value']);
		$ret['home_value'] = str_replace("½","&amp;frac12;", $ret['home_value']);
		$ret['visitor_value'] = str_replace("+","", $ret['visitor_value']);
		$ret['home_value'] = str_replace("+","", $ret['home_value']);
	}
	//echo "<pre>".print_r($ret)."</pre>"; exit();
	return $ret;
}


function _getBJLine($arr)
{
	//set array params
	foreach($arr as $key => $val)
	{
		$$key = $val;
	}
	
	if(trim($home)!= "")$home = "%".ereg_replace(" ","%",$home)."%";
	if(trim($away)!= "")$away = "%".ereg_replace(" ","%",$away)."%";
	$que = "SELECT * FROM `ol_lines_bj` 
			WHERE league_id = '".$league_id."'
			and GameDate = '".$date."' 
			and Team1Rot = '".$rot_away."'
			and Team2Rot = '".$rot_home."'
			order by ol_id desc
			limit 1";
	$chque = mysql_query($que);
	if(@mysql_num_rows($chque) == 0)
	{
		$que = "SELECT * FROM `ol_lines_bj` 
				WHERE league_id = '".$league_id."'
				and GameDate = '".$date."' 
				and Team1 like '".$away."'
				and Team2 like '".$home."'
				order by ol_id desc
				limit 1";
	}
	//echo $que."<br>"; exit();
	$query = mysql_query($que);
	$ret = array();
	$ret['book_id'] = '4';
	if(@mysql_num_rows($query) > 0)
	{
		$res = mysql_fetch_assoc($query);
		$res['Team1'] = trim($res['Team1']);
		$res['Team2'] = trim($res['Team2']);
		$res['FavoredTeamID'] = trim($res['FavoredTeamID']);
		$ret['visitor_seconds'] = 10800 + strtotime('now') - strtotime($res['date_updated']);
		$ret['home_seconds'] = $ret['visitor_seconds'];
		if($period == '3')
		{
		  //set the spread values
		  $spread = $res['Spread'];
		  $spread2 = 0;
		  if($spread != 0)$spread2 = ($spread * (-1));
		  if($league_sport == 'Basketball' || $league_sport == 'Football')
		  {
			$ret['visitor_value'] = $res['MoneyLine1'];
		    $ret['home_value'] = $res['MoneyLine2'];
		  }
		  else
		  {
		  	if($res['Team1'] == $res['FavoredTeamID'])
			{
				$ret['visitor_value'] = $spread . " (" . $res['SpreadAdj1'].")";
		        $ret['home_value'] = $spread2 . " (" . $res['SpreadAdj2'].")";
			}elseif($res['Team2'] == $res['FavoredTeamID'])
			{
				$ret['visitor_value'] = $spread2 . " (" . $res['SpreadAdj1'].")";
		        $ret['home_value'] = $spread . " (" . $res['SpreadAdj2'].")";
			}
		  }
		  
		}
		elseif($period == '4')
		{
		  $ret['visitor_value'] = $res['ouaway']." ".$res['ouawaym'];
		  $ret['home_value'] = $res['ouhome']." ".$res['ouhomem'];
		}
		else
		{
			if($league_sport == 'Basketball' || $league_sport == 'Football')
		    {
		      
			  $tot = $res['TotalPoints'];
			  //set the spread values
		     $spread = $res['Spread'];
		 	 $spread2 = "pk";
		  	 if($spread != 0)$spread2 = ($spread * (-1));
			  if($res['Team1'] == $res['FavoredTeamID'])
			  {
				$ret['visitor_value'] = $spread2;
		        $ret['home_value'] = $tot;
			  }elseif($res['Team2'] == $res['FavoredTeamID'])
			  {
				$ret['visitor_value'] = $tot;
		        $ret['home_value'] = $spread2;
			  }
		    }else//if($league_sport == 'Basketball'...)
		    {
		  	  //TotalPoints 	TotalAdj1 	TotalAdj2
			  $tot = $res['TotalPoints'];
			  if($res['TotalAdj1'] < $res['TotalAdj2'])$tot.="o".$res['TotalAdj1'];
			  if($res['TotalAdj2'] < $res['TotalAdj1'])$tot.="u".$res['TotalAdj2'];
			  $ret['visitor_value'] = "";
		      $ret['home_value'] = $tot;
			  if($res['Team1'] == $res['FavoredTeamID'])
			  {
				$ret['visitor_value'] = $res['MoneyLine1'] . "/". $res['MoneyLine2'];
		        $ret['home_value'] = $tot;
			  }
			  elseif($res['Team2'] == $res['FavoredTeamID'])
			  {
				$ret['visitor_value'] = $tot;
		        $ret['home_value'] = $res['MoneyLine2'] . "/". $res['MoneyLine1'];
			  }
			  elseif($res['FavoredTeamID'] == "" && $res['MoneyLine1'] != "" && $res['MoneyLine2'] != "")
			  {
			  	if($res['MoneyLine1'] < $res['MoneyLine2'])
				{
					$ret['visitor_value'] = $res['MoneyLine1'] . "/". $res['MoneyLine2'];
		        	$ret['home_value'] = $tot;
				}
				elseif($res['MoneyLine2'] < $res['MoneyLine1'])
				{
					$ret['visitor_value'] = $tot;
		        	$ret['home_value'] = $res['MoneyLine2'] . "/". $res['MoneyLine1'];
				}
			  }
		    }//end of else if($league_sport == 'Basketball'...)
		}
		$ret['visitor_value'] = str_replace(".5","&amp;frac12;",$ret['visitor_value']);
		$ret['home_value'] = str_replace(".5","&amp;frac12;",$ret['home_value']);
		$ret['visitor_value'] = str_replace("+","", $ret['visitor_value']);
		$ret['home_value'] = str_replace("+","", $ret['home_value']);
	}
	return $ret;
}//end of function _getBJLine(.....)



function _getPSLine($arr)
{
	//extract arr params
	extract($arr);
	
	if(trim($home)!= "")$home = "%".ereg_replace(" ","%",$home)."%";
	if(trim($away)!= "")$away = "%".ereg_replace(" ","%",$away)."%";
	$sel_period = $period;
	if($period == '3' || $period == '4')$sel_period = 0;
	if( eregi('Q',$period) )$sel_period = 3;
	$que = "SELECT * FROM `ol_lines_ps` 
			WHERE league_id = '".$league_id."'
			and game_date = '".$date."' 
			and period = '".$sel_period."'
			and rot_visitor = '".$rot_away."'
			and rot_home = '".$rot_home."'
			order by ol_id desc
			limit 1";
	$chque = mysql_query($que);
	$que_tot = '';
	if(@mysql_num_rows($chque) == 0)
	{
		$que = "SELECT * FROM `ol_lines_ps` 
				WHERE league_id = '".$league_id."'
				and game_date = '".$date."' 
				and period = '".$sel_period."'
				and home like '".$home."'
				and vistor like '".$away."'";
		$que_tot = $que;
	}else
	{
		$que = "SELECT * FROM `ol_lines_ps` 
				WHERE league_id = '".$league_id."'
				and game_date = '".$date."' 
				and period = '".$sel_period."'
				and rot_visitor = '".$rot_away."'
				and rot_home = '".$rot_home."'";
		$que_tot = $que;
	}
	
	if($opener == '1')
	{
		 if($league_sport == 'Basketball' || $league_sport == 'Football')
		 {
		 	if( $period == '3' )
			{
				$que .= 
			 	  " and `moneyline_home` != '' and `moneyline_visiting` != ''
			 	    order by `ol_id`
				    limit 1";
			}else
			{
				$que .= 
			 	  " and `spread_home` != '' and `spread_visiting` != ''
			 	    order by `ol_id`
				    limit 1";
			}
		 }else
		 {
		    if( $period == '3' )
			{
				$que .= 
			 	  " and `spread_home` != 'OFF' and `spread_visiting` != ''
			 	    order by `ol_id`
				    limit 1";
			}else
			{
				$que .= 
			 	  " and `moneyline_home` != '' and `moneyline_visiting` != ''
			 	    order by `ol_id`
				    limit 1";
			}
		 }
	}else
	{
	  $que .= " order by `ol_id` desc
			    limit 1";
	}
	
	//echo $que."<br>"; exit();
	$query = mysql_query($que);
	$ret = array();
	$ret['book_id'] = '5';
	if(@mysql_num_rows($query) > 0)
	{
		$res = mysql_fetch_assoc($query);
		$ret['visitor_seconds'] = 10800 + strtotime('now') - strtotime($res['date_updated']);
		$ret['home_seconds'] = $ret['visitor_seconds'];
		if($period == '3')
		{
		  if($league_sport == 'Basketball' || $league_sport == 'Football')
		  {
			$ret['visitor_value'] = $res['moneyline_visiting'];
		    $ret['home_value'] = $res['moneyline_home'];
		  }
		  else
		  {
		  	$ret['visitor_value'] = $res['spread_visiting']." (".$res['spread_adjust_visiting'].")";
		    $ret['home_value'] = $res['spread_home']." (".$res['spread_adjust_home'].")";
			
		  }
		  
		}
		elseif($period == '4')
		{
		  $ret['visitor_value'] = $res['ouaway']." ".$res['ouawaym'];
		  $ret['home_value'] = $res['ouhome']." ".$res['ouhomem'];
		}
		else
		{
			if($opener == '1')
			{
				$que_tot .=   " and `total_points` != ''
						 	    order by `ol_id`
							    limit 1";
				//echo $que_tot; exit();
				$query_tot = mysql_query($que_tot);
				$res_tot = mysql_fetch_assoc($query_tot);
				$tot = $res_tot['total_points'];
				$res['over_adjust'] = $res_tot['over_adjust'];
				$res['under_adjust'] = $res_tot['under_adjust'];
			}else
			{
			  	$tot = $res['total_points'];
			}
			
			if($league_sport == 'Basketball' || $league_sport == 'Football')
		    {
				 $ret['visitor_value'] = "";
				 $ret['home_value'] = $tot;
				if($res['spread_visiting'] < $res['spread_home'])
				{
				  $ret['visitor_value'] = $res['spread_home'];
				  if($res['spread_adjust_home'] != '-110')$ret['visitor_value'] .= ' ('.$res['spread_adjust_visiting'].')';
				  $ret['home_value'] = $tot;
				}
				elseif($res['spread_home'] < $res['spread_visiting'])
				{
					$ret['visitor_value'] = $tot;
				    $ret['home_value'] = $res['spread_visiting'];
					if($res['spread_adjust_visiting'] != '-110')$ret['home_value'] .= ' ('.$res['spread_adjust_home'].')';
				}
				elseif($res['spread_home'] == $res['spread_visiting'] && $res['spread_home'] != "" && $res['spread_visiting'] != "")
				{
					 $ret['visitor_value'] = "pk";
				  	 $ret['home_value'] = $tot;
				}
				elseif($res['spread_home'] == "" && $res['spread_visiting'] == "" && $res['moneyline_visiting'] != "" && $res['moneyline_home'] != "")
				{
					if($res['moneyline_visiting'] < $res['moneyline_home'])
					{
						$ret['visitor_value'] = $res['spread_home'];
						$ret['home_value'] = $tot;
					}
					elseif($res['moneyline_home'] < $res['moneyline_visiting'])
					{
						$ret['visitor_value'] = $tot;
						$ret['home_value'] = $res['spread_home'];
					}
				}
				
		    }else
		    {
				//over_adjust 	under_adjust
				if($res['over_adjust'] < $res['under_adjust'])$tot.="o".$res['over_adjust'];
				if($res['under_adjust'] < $res['over_adjust'])$tot.="u".$res['under_adjust'];
				$ret['visitor_value'] = "";
				$ret['home_value'] = $tot;
				
				
				if($res['spread_visiting'] < $res['spread_home'])
				{
				  $ret['visitor_value'] = $res['moneyline_visiting']."/".$res['moneyline_home'];
				  $ret['home_value'] = $tot;
				}
				elseif($res['spread_home'] < $res['spread_visiting'])
				{
					$ret['visitor_value'] = $tot;
				    $ret['home_value'] = $res['moneyline_home']."/".$res['moneyline_visiting'];
				}
				elseif( ($res['spread_home'] == $res['spread_visiting']) && $res['spread_home'] != "" && $res['spread_visiting'] != "")
				{
					 $ret['visitor_value'] = "pk";
				  	 $ret['home_value'] = $tot;
				}
				elseif($res['moneyline_visiting'] != "" && $res['moneyline_home'] != "")
				{
					if($res['moneyline_visiting'] < $res['moneyline_home'])
					{
						$ret['visitor_value'] = $res['moneyline_visiting']."/".$res['moneyline_home'];
				  		$ret['home_value'] = $tot;
					}
					elseif($res['moneyline_home'] < $res['moneyline_visiting'])
					{
						$ret['visitor_value'] = $tot;
				    	$ret['home_value'] = $res['moneyline_home']."/".$res['moneyline_visiting'];
					}
					elseif( ($res['moneyline_home'] == $res['moneyline_visiting']) && $res['spread_home'] == "" && $res['spread_visiting'] == "" )
					{
						$ret['visitor_value'] = $tot;
				    	$ret['home_value'] = $res['moneyline_home']."/".$res['moneyline_visiting'];
					}
				}
				
		    }//end of else if($league_sport == 'Basketball'...)
		}
		$ret['visitor_value'] = str_replace(".5","&amp;frac12;",$ret['visitor_value']);
		$ret['home_value'] = str_replace(".5","&amp;frac12;",$ret['home_value']);
		$ret['visitor_value'] = str_replace("+","", $ret['visitor_value']);
		$ret['home_value'] = str_replace("+","", $ret['home_value']);
	}
	return $ret;
}//end of function _getPSLine(.....)



function getBookLogs($arr = array())
{
	/*if($arr['period'] == '1' || $arr['period'] == '1Q' || $arr['period'] == '2')
	{
		return getBookHalfLogs($arr);
	}*/
	switch($arr['bid'])
	{
		case "1":
			return _getSBookLogs($arr);
			break;
		case "2":
			return _getSBLogs($arr);
			break;
		case "3":
			return _getBDLogs($arr);
			break;
		case "4":
			return _getBJLogs($arr);
			break;
		case "5":
			return _getPSLogs($arr);
			break;
		default:
			return;
	}
}

function getBookHalfLogs($arr = array())
{
	switch($arr['bid'])
	{
		case "1":
			return _getSBookLogs($arr);
			break;
		case "5":
			return _getPSLogs($arr);
			break;
		default:
			return;
	}
}

function _getSBookLogs($arr = array())
{
	$league_id = $arr['league_id'];
	$league_sport = $arr['league_sport'];
	$period = $arr['period'];
	$date = $arr['date'];
	$home = $arr['home'];
	$away = $arr['away'];
	$rot_home = $arr['hid'];
	$rot_away = $arr['vid'];
	if(trim($home)!= "")
	{
	  $home = str_replace("Los Angeles","LA",$home);
	  $home = str_replace("New York Mets","NY Mets",$home);
	  $home = str_replace("Portland Trail Blazers","Portland Blazers",$home);
	  $home = str_replace(".","",$home);
	  $home = "%".str_replace(" ","%",$home)."%";
	}
	if(trim($away)!= "")
	{
	  $away = str_replace("Los Angeles","LA",$away);
	  $away = str_replace("New York Mets","NY Mets",$away);
	  $away = str_replace("Portland Trail Blazers","Portland Blazers",$away);
	  $away = str_replace(".","",$away);
	  $away = "%".str_replace(" ","%",$away)."%";
	}
	$sel_period = $period;
	if($period == '3')$sel_period = 0;
	
	$que = "SELECT * FROM `ol_lines_sbook` 
			WHERE `league_id` = '".$league_id."'
			and `period` = '".$sel_period."'
			and `date` = '".$date."' 
			and `rot_home` = '".$rot_home."'
			and `rot_away` = '".$rot_away."'
			ORDER BY `ol_id` DESC";
	
	$chque = mysql_query($que);
	
	if(@mysql_num_rows($chque) == 0)
	{
	$que = "SELECT * FROM `ol_lines_sbook` 
			WHERE `league_id` = '".$league_id."'
			and `period` = '".$sel_period."'
			and `date` = '".$date."' 
			and `home` like '".$home."'
			and `away` like '".$away."'
			ORDER BY `ol_id` DESC";
	}
	
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		$i = 0;
		while($res = mysql_fetch_assoc($query))
		{
		  $r = array();
		  $j = $i - 1;
		  if($period == '3')
		  {
		  	  if($league_sport == 'Basketball' || $league_sport == 'Football')
			  {
				$r['vvalue'] = $res['money_away'];
			    $r['hvalue'] = $res['money_home'];
				
			  }else
			  {
				$r['vvalue'] = $res['bet_away'];
			    $r['hvalue'] = $res['bet_home'];
			  }
		  }else
		  {
		      if($league_sport == 'Basketball' || $league_sport == 'Football')
			  {
				$r['vvalue'] = $res['bet_away'];
			    $r['hvalue'] = $res['bet_home'];
			  }else
			  {
				$r['vvalue'] = $res['money_away'];
			    $r['hvalue'] = $res['money_home'];
			  }
			  
		  }
		  $date_time = strtotime($res['date_updated']." EST");
		  $r['datetime'] = date('m/d/y h:i:sa', $date_time );
		  $r['vvalue'] = str_replace(".5","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace(".5","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("+","",$r['vvalue']);
		  $r['hvalue'] = str_replace("+","",$r['hvalue']);
		  if($j < 0)
		  {
			if(trim($r['vvalue']) != "" && trim($r['hvalue']) != "")
			{
			  $ret[$i] = $r;
			  $i++;
			}
		  }else
		  {
			if(trim($r['vvalue']) != "" && trim($r['hvalue']) != "")
			{
				if($r['vvalue'] != $ret[$j]['vvalue'] || $r['hvalue'] != $ret[$j]['hvalue'])
				{
				  $ret[$i] = $r;
				  $i++;
				}
			}
		  }//end of else if($j < 0)
		}
	}
	return $ret;
}


function _getSBLogs($arr = array())
{
	//extract arr params
	extract($arr);
	
	if(trim($home)!= "")
	{
	  $home = str_replace(".","",$home);
	  $home = ereg_replace("&nbsp;","",$home);
	  $home = "%".str_replace(" ","%",$home)."%";
	}
	if(trim($away)!= "")
	{
	  $away = str_replace(".","",$away);
	  $away = ereg_replace("&nbsp;","",$away);
	  $away = "%".str_replace(" ","%",$away)."%";
	}
	$que = "SELECT * FROM `ol_lines_sb` 
			WHERE league_id = '".$league_id."'
			and date = '".$date."' 
			and home like '".$home."'
			and away like '".$away."'";
	if($league_sport == 'Basketball' || $league_sport == 'Football')
	{
	 	if( $period == '3' )
		{
			$que .= 
		 	  " AND `mlhome` != '' AND `mlaway` != ''";
		}else
		{
			$que .= 
		 	  " AND `pshome` != '' AND `psaway` != ''";
		}
	}else
	{
	    if( $period == '3' )
		{
			$que .= 
		 	  " AND `pshome` != '' AND `psaway` != ''";
		}else
		{
			$que .= 
		 	  " AND `mlhome` != '' AND `mlaway` != ''";
		}
	}
	$que .= " ORDER BY `ol_id` DESC";
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		$i = 0;
		while($res = mysql_fetch_assoc($query))
		{
		  $r = array();
		  $j = $i - 1;
		  if($period == '3')
		  {
			if($league_sport == 'Basketball' || $league_sport == 'Football')
			{
			  $r['vvalue'] = $res['mlaway'];
		      $r['hvalue'] = $res['mlhome'];
			}else
			{
			  $r['vvalue'] = $res['psaway'].' ('.$res['psawaym'].')';
		      $r['hvalue'] = $res['pshome'].' ('.$res['pshomem'].')';
			}
			
		  }else
		  {
		    if($league_sport == 'Basketball' || $league_sport == 'Football')
			{
			  $r['vvalue'] = $res['psaway'].' ('.$res['psawaym'].')';
		      $r['hvalue'] = $res['pshome'].' ('.$res['pshomem'].')';
			}else
			{
			  $r['vvalue'] = $res['mlaway'];
		      $r['hvalue'] = $res['mlhome'];
			}
		  }
		  $date_time = strtotime($res['date_updated']." EST");
		  $r['datetime'] = date('m/d/y h:i:sa', $date_time );
		  $r['vvalue'] = str_replace(".5","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace(".5","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("+","",$r['vvalue']);
		  $r['hvalue'] = str_replace("+","",$r['hvalue']);
		  if($j < 0)
		  {
			if(trim($r['vvalue']) != "" && trim($r['hvalue']) != "")
			{
			  $ret[$i] = $r;
			  $i++;
			}
		  }else
		  {
			if(trim($r['vvalue']) != "" && trim($r['hvalue']) != "")
			{
				if($r['vvalue'] != $ret[$j]['vvalue'] || $r['hvalue'] != $ret[$j]['hvalue'])
				{
				  $ret[$i] = $r;
				  $i++;
				}
			}
		  }
		}
	}
	return $ret;
}



function _getBJLogs($arr = array())
{
	$league_id = $arr['league_id'];
	$league_sport = $arr['league_sport'];
	$period = $arr['period'];
	$date = $arr['date'];
	$home = $arr['home'];
	$away = $arr['away'];
	$rot_away = $arr['vid'];
	$rot_home = $arr['hid'];
	if(trim($home)!= "")$home = "%".ereg_replace(" ","%",$home)."%";
	if(trim($away)!= "")$away = "%".ereg_replace(" ","%",$away)."%";
	$que = "SELECT * FROM `ol_lines_bj` 
			WHERE league_id = '".$league_id."'
			and GameDate = '".$date."' 
			and Team1Rot = '".$rot_away."'
			and Team2Rot = '".$rot_home."'
			ORDER BY `ol_id` DESC";
	$chque = mysql_query($que);
	if(@mysql_num_rows($chque) == 0)
	{
		$que = "SELECT * FROM `ol_lines_bj` 
				WHERE league_id = '".$league_id."'
				and GameDate = '".$date."' 
				and Team1 like '".$away."'
				and Team2 like '".$home."'
				ORDER BY `ol_id` DESC";
	}
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		$i = 0;
		while($res = mysql_fetch_assoc($query))
		{
		  $r = array();
		  $j = $i - 1;
		  if($period == '3')
		  {
		  	if($league_sport == 'Basketball' || $league_sport == 'Football')
			{
			  $r['vvalue'] = $res['MoneyLine1'];
		      $r['hvalue'] = $res['MoneyLine1'];
			}else
			{
			  // 	Spread 	SpreadAdj1 	SpreadAdj2
			  if($res['FavoredTeamID'] == $res['Team1'])
			  {
			    $spread1 = $res['Spread'];
				$spread2 = $res['Spread'] * -1;
				$r['vvalue'] = $spread1 . " (" . $res['SpreadAdj1'] . ")";
		        $r['hvalue'] = $spread2 . " (" . $res['SpreadAdj2'] . ")";
				if($res['Spread'] == 0){$r['vvalue'] = 0; $r['hvalue'] = 0;}
			  }else
			  {
			    $spread2 = $res['Spread'];
				$spread1 = $res['Spread'] * -1;
				$r['vvalue'] = $spread1 . " (" . $res['SpreadAdj1'] . ")";
		        $r['hvalue'] = $spread2 . " (" . $res['SpreadAdj2'] . ")";
				if($res['Spread'] == 0){$r['vvalue'] = 0; $r['hvalue'] = 0;}
			  }
			}
		  }else
		  {
		    if($league_sport == 'Basketball' || $league_sport == 'Football')
			{
			  if($res['FavoredTeamID'] == $res['Team1'])
			  {
			    $spread1 = $res['Spread'];
				$spread2 = $res['Spread'] * (-1);
				$r['vvalue'] = $spread1 . " (" . $res['SpreadAdj1'] . ")";
		        $r['hvalue'] = $spread2 . " (" . $res['SpreadAdj2'] . ")";
				if($res['Spread'] == 0){$r['vvalue'] = 0; $r['hvalue'] = 0;}
			  }else
			  {
			    $spread2 = $res['Spread'];
				$spread1 = $res['Spread'] * (-1);
				$r['vvalue'] = $spread1 . " (" . $res['SpreadAdj1'] . ")";
		        $r['hvalue'] = $spread2 . " (" . $res['SpreadAdj2'] . ")";
				if($res['Spread'] == 0){$r['vvalue'] = 0; $r['hvalue'] = 0;}
			  }
			}else
			{
			  $r['vvalue'] = $res['MoneyLine1'];
		      $r['hvalue'] = $res['MoneyLine2'];
			}
		  }
		  $date_time = strtotime($res['date_updated']." EST");
		  $r['datetime'] = date('m/d/y h:i:sa', $date_time );
		  $r['vvalue'] = str_replace(".5","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace(".5","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("+","",$r['vvalue']);
		  $r['hvalue'] = str_replace("+","",$r['hvalue']);
		  if($j < 0)
		  {
			if(trim($r['vvalue']) != "" && trim($r['hvalue']) != "")
			{
			  $ret[$i] = $r;
			  $i++;
			}
		  }else
		  {
			if(trim($r['vvalue']) != "" && trim($r['hvalue']) != "")
			{
				if($r['vvalue'] != $ret[$j]['vvalue'] || $r['hvalue'] != $ret[$j]['hvalue'])
				{
				  $ret[$i] = $r;
				  $i++;
				}
			}
		  }
		}
	}
	return $ret;
}


function _getBDLogs($arr = array())
{
	$league_id = $arr['league_id'];
	$league_sport = $arr['league_sport'];
	$game_id = $arr['gid'];
	$period = $arr['period'];
	
	//set short_name
	$short_name = "Game";
	if($period != '0')
	{
		if($league_id == 1 && $period == '1')
		{
			$short_name = "5inn.";
		}
		if( ($league_sport == 'Basketball' || $league_sport == 'Football') && $period != '3')
		{
			switch($period)
			{
				case '1':
					$short_name = "1H";
					break;
				case '2':
					$short_name = "2H";
					break;
				default:
					$short_name = $period;
			}
		}
	}
	//end of set short_name
	
	$que = "SELECT visitor_value, visitor_seconds, home_value, home_seconds 
			FROM `ol_lines_bd` 
			WHERE game_id = '".$game_id."'
			AND short_name = '".$short_name."'";
	if($league_sport == 'Basketball' || $league_sport == 'Football')
	{
		if($period == '3')$que .= " AND period = '0'";
		if($period == '0')$que .= " AND period = '3'";
	}else
	{
		$que .= " AND period = '".$period."'";
	}
	$que .= " ORDER BY `ol_id` DESC";
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		
		while($res = mysql_fetch_assoc($query))
		{
		  $res['vvalue'] = str_replace("½","&amp;frac12;",$res['visitor_value']);
		  $res['hvalue'] = str_replace("½","&amp;frac12;",$res['home_value']);
		  $res['vvalue'] = str_replace("+","",$res['vvalue']);
		  $res['hvalue'] = str_replace("+","",$res['hvalue']);
		  $res['datetime'] = ($res['visitor_seconds'] > $res['home_seconds']) ? date('m/d/y h:i:sa',$res['visitor_seconds']) : date('m/d/y h:i:sa',$res['home_seconds']);
		  $ret[] = $res;
		}
	}
	return $ret;
}

function _getPSLogs($arr = array())
{
	$league_id = $arr['league_id'];
	$league_sport = $arr['league_sport'];
	$period = $arr['period'];
	$date = $arr['date'];
	$home = $arr['home'];
	$away = $arr['away'];
	$rot_away = $arr['vid'];
	$rot_home = $arr['hid'];
	if(trim($home)!= "")$home = "%".ereg_replace(" ","%",$home)."%";
	if(trim($away)!= "")$away = "%".ereg_replace(" ","%",$away)."%";
	$sel_period = $period;
	if($period == '3')$sel_period = 0;
	if( eregi('Q',$period) )$sel_period = 3;
	$que = "SELECT * FROM `ol_lines_ps` 
			WHERE league_id = '".$league_id."'
			and game_date = '".$date."' 
			and period = '".$sel_period."'
			and rot_visitor = '".$rot_away."'
			and rot_home = '".$rot_home."'
			ORDER BY `ol_id` DESC";
	$chque = mysql_query($que);
	if(@mysql_num_rows($chque) == 0)
	{
		$que = "SELECT * FROM `ol_lines_ps` 
				WHERE league_id = '".$league_id."'
				and game_date = '".$date."' 
				and period = '".$sel_period."'
				and home like '".$home."'
				and vistor like '".$away."'
				ORDER BY `ol_id` DESC";
	}
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		$i = 0;
		while($res = mysql_fetch_assoc($query))
		{
		  $j = $i - 1;
		  $r = array();
		  if($period == '3')
		  {
		    if($league_sport == 'Basketball' || $league_sport == 'Football')
			{
			  $r['vvalue'] = $res['moneyline_visiting'];
		      $r['hvalue'] = $res['moneyline_home'];
			}else
			{
			  //spread_visiting 	spread_adjust_visiting 	spread_home 	spread_adjust_home
			  $r['vvalue'] = $res['spread_visiting'] . " (". $res['spread_adjust_visiting'] . ")";
		      $r['hvalue'] = $res['spread_home'] . " (". $res['spread_adjust_home'] . ")";
			}
		  }else//end of if($period == "3")
		  {
		    if($league_sport == 'Basketball' || $league_sport == 'Football')
			{
				$r['vvalue'] = $res['spread_visiting'] . " (". $res['spread_adjust_visiting'] . ")";
		        $r['hvalue'] = $res['spread_home'] . " (". $res['spread_adjust_home'] . ")"; 
			}else
			{
				$r['vvalue'] = $res['moneyline_visiting'];
		        $r['hvalue'] = $res['moneyline_home'];
			}
		  }//end of else if($period == "3")
		  
		  $date_time = strtotime($res['date_updated']." EST");
		  $r['datetime'] = date('m/d/y h:i:sa', $date_time );
		  $r['vvalue'] = str_replace(".5","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace(".5","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("()","",$r['vvalue']);
		  $r['hvalue'] = str_replace("()","",$r['hvalue']);
		  $r['vvalue'] = str_replace("+","",$r['vvalue']);
		  $r['hvalue'] = str_replace("+","",$r['hvalue']);
		  if($j < 0)
		  {
			if(trim($r['vvalue']) != "" && trim($r['hvalue']) != "")
			{
			  $ret[$i] = $r;
			  $i++;
			}
		  }else
		  {
			if(trim($r['vvalue']) != "" && trim($r['hvalue']) != "")
			{
				if($r['vvalue'] != $ret[$j]['vvalue'] || $r['hvalue'] != $ret[$j]['hvalue'])
				{
				  $ret[$i] = $r;
				  $i++;
				}
			}
		  }
		}
	}//end of if(@mysql_num_rows($query) > 0)
	return $ret;
}

function getBookTotals($arr = array())
{
	/*if($arr['period'] == '1' || $arr['period'] == '1Q' || $arr['period'] == '2')
	{
		return getBookHalfTotals($arr);
	}*/
	switch($arr['bid'])
	{
		case "1":
			return _getSBookTotals($arr);
			break;
		case "2":
			return _getSBTotals($arr);
			break;
		case "3":
			return _getBDTotals($arr);
			break;
		case "4":
			return _getBJTotals($arr);
			break;
		case "5":
			return _getPSTotals($arr);
			break;
		default:
			return;
	}
}


function getBookHalfTotals($arr = array())
{
	switch($arr['bid'])
	{
		case "1":
			return _getSBookTotals($arr);
			break;
		case "5":
			return _getPSTotals($arr);
			break;
		default:
			return;
	}
}


function _getSBookTotals($arr = array())
{
	$league_id = $arr['league_id'];
	$period = $arr['period'];
	$date = $arr['date'];
	$home = $arr['home'];
	$away = $arr['away'];
	$rot_away = $arr['vid'];
	$rot_home = $arr['hid'];
	if(trim($home)!= "")
	{
	  $home = str_replace("Los Angeles","LA",$home);
	  $home = str_replace("New York Mets","NY Mets",$home);
	  $home = str_replace("Portland Trail Blazers","Portland Blazers",$home);
	  $home = str_replace(".","",$home);
	  $home = "%".str_replace(" ","%",$home)."%";
	}
	if(trim($away)!= "")
	{
	  $away = str_replace("Los Angeles","LA",$away);
	  $away = str_replace("New York Mets","NY Mets",$away);
	  $away = str_replace("Portland Trail Blazers","Portland Blazers",$away);
	  $away = str_replace(".","",$away);
	  $away = "%".str_replace(" ","%",$away)."%";
	}
	$sel_period = $period;
	if($period == '3')$sel_period = 0;
	
	$que = "SELECT * FROM `ol_lines_sbook` 
			WHERE `league_id` = '".$league_id."'
			and `period` = '".$sel_period."'
			and `date` = '".$date."' 
			and `rot_home` = '".$rot_home."'
			and `rot_away` = '".$rot_away."'
			ORDER BY `ol_id` DESC";
	
	$chque = mysql_query($que);
	
	if(@mysql_num_rows($chque) == 0)
	{
	$que = "SELECT * FROM `ol_lines_sbook` 
			WHERE league_id = '".$league_id."'
			and `period` = '".$sel_period."'
			and `date` = '".$date."' 
			and `home` like '".$home."'
			and `away` like '".$away."'
			ORDER BY `ol_id` DESC";
	}
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		$i = 0;
		while($res = mysql_fetch_assoc($query))
		{
		  $r = array();
		  $j = $i - 1;
		  $r['vvalue'] = "Over ".$res['over'];
		  $r['hvalue'] = "Under ".$res['under'];
		  $date_time = strtotime($res['date_updated']." EST");
		  //replace decimals with fractions
		  $r['vvalue'] = str_replace(".0","",$r['vvalue']);
		  $r['hvalue'] = str_replace(".0","",$r['hvalue']);
		  $r['vvalue'] = str_replace(".5","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace(".5","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("&frac12;","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace("&frac12;","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("+","",$r['vvalue']);
		  $r['hvalue'] = str_replace("+","",$r['hvalue']);
		  $r['datetime'] = date('m/d/y h:i:sa', $date_time );
		  if($j < 0)
		  {
			if($res['over'] != "" || $res['under'] != "")
			{
			  $ret[$i] = $r;
			  $i++;
			}
		  }else
		  {
			if($res['over'] != "" || $res['under'] != "")
			{
			  if($r['vvalue'] != $ret[$j]['vvalue'] || $r['hvalue'] != $ret[$j]['hvalue'])
			  {
				$ret[$i] = $r;
			    $i++;
			  }
			}
		  }
		}
	}
	return $ret;
}

function _getSBTotals($arr = array())
{
	$league_id = $arr['league_id'];
	$league_sport = $arr['league_sport'];
	$period = $arr['period'];
	$date = $arr['date'];
	$home = $arr['home'];
	$away = $arr['away'];
	if(trim($home)!= "")
	{
	  $home = str_replace(".","",$home);
	  $home = "%".str_replace(" ","%",$home)."%";
	}
	if(trim($away)!= "")
	{
	  $away = str_replace(".","",$away);
	  $away = "%".str_replace(" ","%",$away)."%";
	}
	
	//set game_line
	$game_line = "Game";
	if($period != '0')
	{
		if($league_id == 1 && $period == '1')
		{
			$game_line = "5inn";
		}
		if( ($league_sport == 'Basketball' || $league_sport == 'Football') && $period != '3')
		{
			switch($period)
			{
				case '1':
					$game_line = "1H";
					if($league_id == 4)
					{
						$teamh_pei = explode(" ", $home);
						$teama_pei = explode(" ", $away);
						$hc = count($teamh_pei)-1;
						$ac = count($teama_pei)-1;
						$home = $teamh_pei[$hc].' - 1H';
						$away = $teama_pei[$ac].' - 1H';
					}
					if($league_sport == 'Football')$league_id = '6';
					break;
				case '2':
					$game_line = "2H";
					if($league_id == 4)
					{
						$teamh_pei = explode(" ", $home);
						$teama_pei = explode(" ", $away);
						$hc = count($teamh_pei)-1;
						$ac = count($teama_pei)-1;
						$home = $teamh_pei[$hc].' - 2H';
						$away = $teama_pei[$ac].' - 2H';
					}
					if($league_sport == 'Football')$league_id = '6';
					break;
				default:
					$game_line = $period;
			}
		}
	}
	//end of set game_line
	
	$que = "SELECT * FROM `ol_lines_sb` 
			WHERE league_id = '".$league_id."'
			and game_line = '".$game_line."'
			and date = '".$date."' 
			and home like '".$home."'
			and away like '".$away."'
			order by ol_id desc";
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		$i = 0;
		while($res = mysql_fetch_assoc($query))
		{
		  //ouhome 	ouhomem 	ouaway 	ouawaym
		  $r = array();
		  $j = $i - 1;
		  $r['vvalue'] = str_replace("O ","Over ",$res['ouaway'])." ".$res['ouawaym'];
		  $r['hvalue'] = str_replace("U ","Under ",$res['ouhome'])." ".$res['ouhomem'];
		  $date_time = strtotime($res['date_updated']." EST");
		  $r['datetime'] = date('m/d/y h:i:sa', $date_time );
		  //replace decimals with fractions
		  $r['vvalue'] = str_replace(".5","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace(".5","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("+","",$r['vvalue']);
		  $r['hvalue'] = str_replace("+","",$r['hvalue']);
		  if($j < 0)
		  {
			if($res['ouaway'] != "" || $res['ouhome'] != "")
			{
			  $ret[$i] = $r;
			  $i++;
			}
		  }else
		  {
			if($res['ouaway'] != "" || $res['ouhome'] != "")
			{
			  if($r['vvalue'] != $ret[$j]['vvalue'] || $r['hvalue'] != $ret[$j]['hvalue'])
			  {
			    $ret[$i] = $r;
			    $i++;
			  }
			}
		  }
		}
	}
	return $ret;
}


function _getBDTotals($arr)
{
	$league_id = $arr['league_id'];
	$league_sport = $arr['league_sport'];
	$game_id = $arr['gid'];
	$period = $arr['period'];
	
	//set short_name
	$short_name = "Game";
	if($period != '0')
	{
		if($league_id == 1 && $period == '1')
		{
			$short_name = "5inn.";
		}
		if( ($league_sport == 'Basketball' || $league_sport == 'Football') && $period != '3')
		{
			switch($period)
			{
				case '1':
					$short_name = "1H";
					break;
				case '2':
					$short_name = "2H";
					break;
				default:
					$short_name = $period;
			}
		}
	}
	//end of set short_name
	
	$que = "SELECT visitor_value, visitor_seconds, home_value, home_seconds 
			FROM `ol_lines_bd` 
			WHERE game_id = '".$game_id."'
			AND short_name = '".$short_name."'
			AND period = '4'
			ORDER BY ol_id DESC";
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		while($res = mysql_fetch_assoc($query))
		{
		  $res['vvalue'] = str_replace("½","&amp;frac12;",$res['visitor_value']);
		  $res['hsvalue'] = str_replace("½","&amp;frac12;",$res['home_value']);
		  $res['vvalue'] = str_replace("+","",$res['vvalue']);
		  $res['hsvalue'] = str_replace("+","",$res['hsvalue']);
		  $res['hvalue'] = "Over ".str_replace("/","  Under ",$res['hsvalue']);
		  $res['datetime'] = ($res['visitor_seconds'] > $res['home_seconds']) ? date('m/d/y h:i:sa',$res['visitor_seconds']) : date('m/d/y h:i:sa',$res['home_seconds']);
		  $ret[] = $res;
		}
	}
	return $ret;
}


function _getBJTotals($arr = array())
{
	$league_id = $arr['league_id'];
	$league_sport = $arr['league_sport'];
	$period = $arr['period'];
	$date = $arr['date'];
	$home = $arr['home'];
	$away = $arr['away'];
	$rot_away = $arr['vid'];
	$rot_home = $arr['hid'];
	if(trim($home)!= "")$home = "%".ereg_replace(" ","%",$home)."%";
	if(trim($away)!= "")$away = "%".ereg_replace(" ","%",$away)."%";
	$que = "SELECT * FROM `ol_lines_bj` 
			WHERE league_id = '".$league_id."'
			and GameDate = '".$date."' 
			and Team1Rot = '".$rot_away."'
			and Team2Rot = '".$rot_home."'
			order by ol_id desc";
	$chque = mysql_query($que);
	if(@mysql_num_rows($chque) == 0)
	{
		$que = "SELECT * FROM `ol_lines_bj` 
				WHERE league_id = '".$league_id."'
				and GameDate = '".$date."' 
				and Team1 like '".$away."'
				and Team2 like '".$home."'
				order by ol_id desc";
	}
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		$i = 0;
		while($res = mysql_fetch_assoc($query))
		{
		  //TotalPoints 	TotalAdj1 	TotalAdj2 	
		  $r = array();
		  $j = $i - 1;
		  $r['vvalue'] = $res['TotalPoints'];
		  $r['hvalue'] = " Over ".$res['TotalAdj1']." Under ".$res['TotalAdj2'];
		  $date_time = strtotime($res['date_updated']." EST");
		  //replace decimals with fractions
		  $r['vvalue'] = str_replace(".5","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace(".5","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("+","",$r['vvalue']);
		  $r['hvalue'] = str_replace("+","",$r['hvalue']);
		  $r['datetime'] = date('m/d/y h:i:sa', $date_time );
		  if($j < 0)
		  {
			if(trim($res['TotalAdj1']) != "" || trim($res['TotalAdj2']) != "")
			{
			  $ret[$i] = $r;
			  $i++;
			}
		  }else
		  {
			if(trim($res['TotalAdj1']) != "" || trim($res['TotalAdj2']) != "")
			{
			  if($r['vvalue'] != $ret[$j]['vvalue'] || $r['hvalue'] != $ret[$j]['hvalue'])
			  {
			    $ret[$i] = $r;
			    $i++;
			  }
			}
		  }
		}
	}
	return $ret;
}


function _getPSTotals($arr = array())
{
	$league_id = $arr['league_id'];
	$league_sport = $arr['league_sport'];
	$period = $arr['period'];
	$date = $arr['date'];
	$home = $arr['home'];
	$away = $arr['away'];
	$rot_away = $arr['vid'];
	$rot_home = $arr['hid'];
	if(trim($home)!= "")$home = "%".ereg_replace(" ","%",$home)."%";
	if(trim($away)!= "")$away = "%".ereg_replace(" ","%",$away)."%";
	$sel_period = $period;
	if($period == '3')$sel_period = 0;
	if( eregi('Q',$period) )$sel_period = 3;
	$que = "SELECT * FROM `ol_lines_ps` 
			WHERE league_id = '".$league_id."'
			and game_date = '".$date."' 
			and period = '".$sel_period."'
			and rot_visitor = '".$rot_away."'
			and rot_home = '".$rot_home."'
			order by ol_id desc";
	$chque = mysql_query($que);
	if(@mysql_num_rows($chque) == 0)
	{
		$que = "SELECT * FROM `ol_lines_ps` 
				WHERE league_id = '".$league_id."'
				and game_date = '".$date."' 
				and period = '".$sel_period."'
				and home like '".$home."'
				and vistor like '".$away."'
				order by ol_id desc";
	}
	//echo $que; exit();
	$query = mysql_query($que);
	$ret = array();
	if(@mysql_num_rows($query) > 0)
	{
		$i = 0;
		while($res = mysql_fetch_assoc($query))
		{
		  //total_points 	over_adjust 	under_adjust
		  $r = array();
		  $j = $i - 1;
		  $r['vvalue'] = $res['total_points'];
		  $r['hvalue'] = " Over ".$res['over_adjust']." Under ".$res['under_adjust'];
		  $date_time = strtotime($res['date_updated']." EST");
		  //replace decimals with fractions
		  $r['vvalue'] = str_replace(".5","&amp;frac12;",$r['vvalue']);
		  $r['hvalue'] = str_replace(".5","&amp;frac12;",$r['hvalue']);
		  $r['vvalue'] = str_replace("+","",$r['vvalue']);
		  $r['hvalue'] = str_replace("+","",$r['hvalue']);
		  $r['datetime'] = date('m/d/y h:i:sa', $date_time );
		  if($j < 0)
		  {
			if(trim($res['over_adjust']) != "" && trim($res['under_adjust']) != "")
			{
			  $ret[$i] = $r;
			  $i++;
			}
		  }else
		  {
			if(trim($res['over_adjust']) != "" && trim($res['under_adjust']) != "")
			{
			  if($r['vvalue'] != $ret[$j]['vvalue'] || $r['hvalue'] != $ret[$j]['hvalue'])
			  {
			    $ret[$i] = $r;
			    $i++;
			  }
			}
		  }
		}
	}
	return $ret;
}


function update_final_scores($feed_url)
{
	$league_array = array('NFL'=>4, 'CFB'=>7, 'NBA'=>2, 'CBB'=>3, 'MLB'=>1, 'NHL'=>8);
	$feed_data = file_get_contents($feed_url);
	$sp_data = explode("<th class='headergrey' width=268 nowrap>", $feed_data);
	$c = 0;
	foreach($sp_data as $sp)
	{
		if($c>0)
		{
		  $leags = explode('</th>', $sp);
		  $teams = explode("<td align='left'>", $sp);
		  $leag = explode('&nbsp;&nbsp;', $leags[0]);
		  $league = $leag[0];
		  $leagd = str_replace('&nbsp;', ' ', $leag[1]);
		  $ldate = date('Y-m-d', strtotime($leagd));
		  $t=0;
		  foreach($teams as $team)
		  {
		  	 if($t > 0)
			 {
			 	$scor1 = explode("<td class='sbfinal' align='center' width='75'>", $team);
				$scor2 = explode("</td>", $scor1[1]);
				$score = strip_tags($scor2[0]);
				$teamn = explode('</td>', $team);
				$team_names = strip_tags($teamn[0]);
				$troe = explode(':', $team_names);
				$aroe = $troe[0];
				$roep = explode('&nbsp;', $troe[1]);
				$roec = count($roep)-1;
				$hroe = $roep[$roec];
				if( eregi('final', $score) )
				{
					$league_id = $league_array[$league];
					$fscore = explode('Final', $score);
					$array = array('aroe'   => $aroe, 
					               'hroe'   => $hroe, 
								   'ascore' => $fscore[0],
								   'hscore' => $fscore[1],
								   'league' => $league_id,
								   'g_date' => $ldate);
					_update_final_scores($array);
				}
			 }
			 $t++;
		  }
		}
		$c++;
	}
}


function _update_final_scores($arr)
{
	extract($arr);
	/*
	$array = array('aroe'   => $aroe, 
	               'hroe'   => $hroe, 
				   'ascore' => $fscore[0],
				   'hscore' => $fscore[1],
				   'league' => $league_id,
				   'g_date' => $game_date);
	*/
	//echo '<pre>'; print_r($arr); echo '</pre>';
	if($league > 0 && $g_date > '2012-01-01' && $aroe > 0 && $hroe > 0)
	{
		$sel_que = "SELECT `visitor_score`, `home_score`
					FROM `ol_games`
					WHERE `league_id` = '".$league."'
					AND `game_date` = '".$g_date."'
					AND `rot_visitor` = '".$aroe."'
					AND `rot_home` = '".$hroe."'";
		$sel_query = mysql_query($sel_que);
		if(@mysql_num_rows($sel_query) > 0)
		{
			while($sel_res = mysql_fetch_assoc($sel_query))
			{
				if($hscore != $sel_res['home_score'] || $ascore != $sel_res['visitor_score'])
				{
					$upd_que = "UPDATE `ol_games`
								SET `visitor_score` = '".$ascore."',
									`home_score`    = '".$hscore."',
									`game_status`   = 'Final',
									`bet_status` 	= 'Final'
								WHERE `league_id` = '".$league."'
								AND `game_date` = '".$g_date."'
								AND `rot_visitor` = '".$aroe."'
								AND `rot_home` = '".$hroe."'";
					@mysql_query($upd_que);
				}
			}
		}
	}
}


//this function removes data older than 15 days from lines db
function remove_old_data()
{
	$old_ts = strtotime('-15 days');
	$fol_ts = strtotime('-2 days');
	$fl_ts = $fol_ts."000";
	$old_date = date( 'Y-m-d', $old_ts );
	$upd_date = date( 'Y-m-d H:i:s' , $old_ts );
	$que1 = "DELETE FROM `ol_lines_sb` 
			 WHERE `date` < '".$old_date."'
			 AND `league_id` < 4
			 AND `league_id` > 6";
	@mysql_query($que1);
}

function ordinal($cdnl){
    $test_c = abs($cdnl) % 10;
    $ext = ((abs($cdnl) %100 < 21 && abs($cdnl) %100 > 4) ? 'th'
            : (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1)
            ? 'th' : 'st' : 'nd' : 'rd' : 'th'));
    return $cdnl.$ext;
} 


function convert_fractions(&$numer, &$denom)
{
    if( $numer < 100 && $denom < 100 )
	{
		$nMod = 100 % $numer;
		$nMul = 100 / $numer;
		$dMod = 100 % $denom;
		$dMul = 100 / $denom;
		if($nMod == 0)
		{
			$numer = $numer * $nMul;
			$denom = $denom * $nMul;
		}elseif($dMod == 0)
		{
			$numer = $numer * $dMul;
			$denom = $denom * $dMul;
		}
	}
}




    //encrypt password
	function _encode($password)
  	{
		$encryption_key = "H6PjFEGVWWBRaUrEO1tEYYO0d7J0hWt3";
		$majorsalt=null;
		
		//if you set your encryption key let's use it
		if ($encryption_key != '')
		{
			//conctenates the encryption key and the password
			$_password = $encryption_key.$password;
		}
		else {$_password=$password;}
		
		//if PHP5
		if (function_exists('str_split'))
		{
		    $_pass = str_split($_password);       
		}
		//if PHP4
		else
		{
			$_pass = array();
		    if (is_string($_password))
		    {
		    	for ($i = 0; $i < strlen($_password); $i++)
		    	{
		        	array_push($_pass, $_password[$i]);
		        }
		     }
		}
		
		//encrypts every single letter of the password
		foreach ($_pass as $_hashpass) 
		{
			$majorsalt .= md5($_hashpass);
		}
		
		//encrypts the string combinations of every single encrypted letter
		//and finally returns the encrypted password 
		return $password=md5($majorsalt);
		
  	}

# EXAMPLE USAGE:
# $where = 'user_id='.$user_id.' and page_id='.$page_id;
# $query = $this->_delete('customers', $where);
# $result = mysql_query($query);
function _delete($table, $where)
{

	return "DELETE FROM "._escape_table($table)." WHERE ".$where;

}

function _select($table, $where)
{

	return "SELECT FROM "._escape_table($table)." WHERE ".$where;

}

# EXAMPLE USAGE:
# $this->_insert('fa_vendor_emails', array('minisite_id' => $minisite_id));
function _insert($table, $params)
{	
	
	while (list($key, $val) = each($params))
	 
	{
 	 
		  $keys[] = $key;
		  
		  $values[] = "'".$val."'";
	 
	}

	$query = "INSERT INTO "._escape_table($table)." (".implode(', ', $keys).

			 ") VALUES (".implode(', ', $values).")";

	return $query;

}

# EXAMPLE USAGE:
# $values = array('item_name' => $args['item_name'], 'item_desc' => $args['item_desc']);
# $where = 'item_id='.$item_id;
# $query = $this->_update('custom_pages_items', $values, $where);
function _update($table, $values, $where)
{

	foreach($values as $key => $val)
	 
	{
	 
		$valstr[] = $key." = '".$val."'";
	 
	}



	return "UPDATE "._escape_table($table)." SET ".implode(', ', $valstr)." WHERE ".$where;

}

function _escape_table($table)
{
	if (stristr($table, '.'))
	{
		$table = preg_replace("/\./", "`.`", $table);
	}
	
	return $table;
}
?>
