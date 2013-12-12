<?php
error_reporting (E_ALL ^ E_NOTICE);
//error_reporting(null);
ini_set('display_errors',1);
//set the time zone
date_default_timezone_set('America/Vancouver');
define('_VALID_MOS', '1');//for accessing db_func.php
$nowtime = strtotime("now");//set current time stamp
$tomorrow = strtotime("+2 day");
//if there is a get parameter for league
if($_GET['league'] && trim($_GET['league']) != "")
{
  include_once('db_func.php');
  tep_db_connect();
  $league_params = getLeagueParams($_GET['league']);
  $league_id = $league_params['league_id'];
  $league_sport = $league_params['sport'];
  if($league_sport == 'Football')$tomorrow = strtotime("+14 days");//weekly lines for football
  if($league_id > 0)
  {
  		$xml = '';
		$period = 0;
		if($_GET['period'] != '0')$period = $_GET['period'];
		$league_name = strtoupper(trim($_GET['league']));
		//set game date
		$gdate = date('Y-m-d',$nowtime);
		if($_GET['date'])
		{
			$gtime = strtotime($_GET['date']);
			if($gtime <= $tomorrow)$gdate = date('Y-m-d', $gtime);//force lines up to tomorrow only
		}
		
		//set short_name
		$short_name = "Game";
		if($period != '0')
		{
			if($league_id == 1 && $period == '1')
			{
				$short_name = "5inn.";
			}
			if($league_id == 2 && $period != '3')
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
		
		//get the games based on league and game date
		$games = getGames($league_id, $gdate, $short_name);
		//echo "gamecount: ".count($games);
		//exit();
		$gxml = '';
		foreach($games as $gm)
		{
			$gm['team_visitor'] = trim($gm['team_visitor']);
			$gm['team_home'] = trim($gm['team_home']);
			
			if( ($gm['rot_visitor']>0 && $gm['rot_home']>0) ||  ($gm['team_visitor']!="" && $gm['team_home']!="") )
			{
				$game_date = date('Ymd',$gm['game_seconds']);
				$game_time = date("Hi",$gm['game_seconds']);
				
				//set starting pitchers for baseball games
				$v_pitcher = ''; $h_pitcher = '';
				if( trim($gm['pitcher_visitor']) != "" || trim($gm['pitcher_home']) != "" )
				{
					$v_pitcher = (trim($gm['pitcher_visitor']) != "" ) ? ' pitcher="'.trim($gm['pitcher_visitor']).'"' : ' pitcher=""'; 
					$h_pitcher = (trim($gm['pitcher_home']) != "" ) ? ' pitcher="'.trim($gm['pitcher_home']).'"' : ' pitcher=""';
					$v_pitcher .= (trim($gm['hand_visitor']) != "" ) ? ' hand="'.$gm['hand_visitor'].'"' : ''; 
					$h_pitcher .= (trim($gm['hand_home']) != "" ) ? ' hand="'.$gm['hand_home'].'"' : '';
				}
				
				//scores for games in progress
				$vs = '';
				$hs = '';
				$gmp_a = '';
				if( trim($gm['visitor_score']) != "" || trim($gm['home_score']) != "" )
				{
					$gmp = explode(" ",$gm['game_status']);
					$gmp_b = '';
					if(count($gmp) > 1)
					{
						for($g=1;$g<count($gmp);$g++)
						{
							if($g>1)$gmp_b .= " ";
							$gmp_b .= ( intval($gmp[$g]) > 0 ) ? ordinal($gmp[$g]) :  $gmp[$g];
						}
					}
					if( $gmp[0] == 'Top' || $gmp[0] == 'Bottom' )$gmp[0] .= ' Of';
					$gmp_a = (intval($gmp[0]) > 0) ? ordinal($gmp[0]) : $gmp[0];
					$vs = ' score="'.$gm['visitor_score'].'" status="'.$gmp_a.'"';
					$hs = ' score="'.$gm['home_score'].'" status="'.$gmp_b.'"';
				}
				
				//set the array parameters to pass
				$open_array = array(
									 'league_id' 	=> $league_id,
									 'league_sport' => $league_sport,
									 'date' 	 	=> $gm['game_date'],
									 'game_id'	 	=> $gm['game_id'],
									 'period'	 	=> $period,
									 'away' 	 	=> $gm['team_visitor'],
									 'home'		 	=> $gm['team_home'],
									 'rot_away'	 	=> $gm['rot_visitor'],
									 'rot_home'	 	=> $gm['rot_home'],
									 'away_score'   => $gm[visitor_score],
									 'home_score'   => $gm[home_score],
									 'game_status'  => $gmp_a,
									 'opener'	 	=> '1'
									);
				//get the openers from SportsBook
				$openers = getAllOpeners($open_array);
				
				$open_array['opener'] = '0';//reset opener for all lines
				
				//get all the lines for the game
				$lines = getAllLines($open_array);
				
				//create teamname for statfox
				$teamhome = createStatFoxTeam($gm['team_home'], $league_id);
				$stathome = createStatFoxTeam($gm['team_home'], $league_id, '1');
				$stataway = createStatFoxTeam($gm['team_visitor'], $league_id, '1');
				
				//use short names for baseball
				if($league_id == 1)
				{
					$gm['team_visitor'] = $gm['team_visitor_short'];
					$gm['team_home'] = $gm['team_home_short'];
				}
				
				$gxml .= '<GAME id="'.$gm['game_id'].'" date="'.$game_date.'" time="'.$game_time.'" seconds="'.$gm['game_seconds'].'" teamid="'.$game_date.$teamhome.'">
				      <TEAM number="'.$gm['rot_visitor'].'" statid="'.$stataway.'" name="'.htmlspecialchars($gm['team_visitor']).'"'.$vs.$v_pitcher.'>
				        <OPENER value="'.$openers['visitor_value'].'" />';
				foreach($lines as $vln)
				{
				        if(trim($vln['visitor_value']) != "" && trim($vln['visitor_value']) != "\\" && trim($vln['visitor_value']) != "-")
						{
						  $vval = 'value="'.$vln['visitor_value'].'" seconds="'.$vln['visitor_seconds'].'"';
						}else
						{
						  $vval = 'value=""';
						}
						
						if( trim($vln['visitor_total']) != "" )
							$vval .= ' total="'.$vln['visitor_total'].'"';
						else
							$vval .= ' total=""';
							
						if( trim($vln['visitor_slide']) != "" )
							$vval .= ' slide="'.$vln['visitor_slide'].'"';
						else
							$vval .= ' slide=""';
						
						
						$gxml .= '<LINE book="'.$vln['book_id'].'" '.$vval.' />';
				}
				$gxml .='
				      </TEAM>
				      <TEAM number="'.$gm['rot_home'].'" statid="'.$stathome.'" name="'.htmlspecialchars($gm['team_home']).'"'.$hs.$h_pitcher.'>
				        <OPENER value="'.$openers['home_value'].'" />';
				foreach($lines as $hln)
				{
				        if(trim($hln['home_value']) != "" && trim($hln['home_value']) != "\\" && trim($hln['home_value']) != "-")
						{
						  $hval = 'value="'.$hln['home_value'].'" seconds="'.$hln['home_seconds'].'"';
						}else
						{
						  $hval = 'value=""';
						}
						
						if( trim($hln['total']) != "" )
							$hval .= ' total="'.$hln['total'].'"';
						else
							$hval .= ' total=""';
							
						if( trim($hln['slide']) != "" )
							$hval .= ' slide="'.$hln['slide'].'"';
						else
							$hval .= ' slide=""';
						
						$gxml .= '<LINE book="'.$hln['book_id'].'" '.$hval.' />';
				}
				$gxml .='
				      </TEAM>
				    </GAME>';
			}
		}//end of foreach($games as $gm)
		  //exit();
		  header('Content-type: text/xml');
	  	  echo '<ODDS>
				<SCHEDULE value="1301348277" />
				<TIME value="'.$nowtime.'" GMT_offset="8" />
				<STARTED value="Servlet started at (1299771477)" />
				<LOADED value="Schedule loaded at (1301348277)(1301348277)" />
				  <LEAGUE number="'.$league_id.'" name="'.$league_name.'">
				    '.$gxml.'
				  </LEAGUE>'."\n".'
				</ODDS>'."\n";
  		
  }else//if($lague_id > 0)
  {
  	  header('Content-type: text/xml');
  	  echo '<ODDS>
			<SCHEDULE value="1301348277" />
			<TIME value="'.$nowtime.'" GMT_offset="8" />
			<STARTED value="Servlet started at (1299771477)" />
			<LOADED value="Schedule loaded at (1301348277)(1301348277)" />
			  <LEAGUE number="2" name="NBA">
			    <GAME date="" time="" seconds="">
			      <TEAM number="1" name="No Data">
			        <OPENER value="" />
			      </TEAM>
			      <TEAM number="2" name="No Data">
			        <OPENER value="" />
			      </TEAM>
			    </GAME>
			  </LEAGUE>
			</ODDS>';
  }//end of else if($league_id > 0)

  tep_db_close();
}//end of if($_GET['league'] && trim($_GET['league']) != "")
else
{
header('Content-type: text/xml');
echo '<ODDS>
	<SCHEDULE value="1301348277" />
	<TIME value="'.$nowtime.'" GMT_offset="8" />
	<STARTED value="Servlet started at (1299771477)" />
	<LOADED value="Schedule loaded at (1301348277)(1301348277)" />
	  <LEAGUE number="2" name="NBA">
	    <GAME date="" time="" seconds="">
	      <TEAM number="1" name="No Data">
	        <OPENER value="" />
	      </TEAM>
	      <TEAM number="2" name="No Data">
	        <OPENER value="" />
	      </TEAM>
	    </GAME>
	  </LEAGUE>
	</ODDS>';
}
?>