<?php
// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );
include_once('table.class.php');
/**
* Lines class, updates local lines database using feeds from different books
* Handles following feeds:
	
	Sportsbetting
	http://oddsfeed.gamingsys.net/new_lines/liveodds.xml
	
	Sportsbook
	http://www.sportsbook.ag/rss/
	
	Bodog
	http://sportsfeeds.bodoglife.com/basic/NFL.xml, ..NHL.xml, ..NBA.xml, etc.
	
	Pinnacle Sports
	Old: http://xml.pinnaclesports.com/pinnacleFeed.aspx?sportType=Hockey&last=1196336407641&contest=no&sportSubType=NHL%20Reg%20Time
	New: http://api.pinnaclesports.com/v1/feed?sportid=15&leagueid=889&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0
	
	Bet Jamaica
	http://www.betjamaica.com/livelines2008/lines.asmx
	
	Sports Interaction
	//nothing yet
*/
class lines {

	/** @var int Internal variable to hold book id number */
	var $book_id		= 0;
	/** @var int Internal variable to hold the league id number */
	var $league_id		= 0;
	/** @var string Internal variable to hold the book's xml feed, listed above */
	var $feed			= '';
	/** @var string Internal variable to hold the period, 0=game, 1=1st half/period, 2=2nd half, 3=moneyline/runline */
	var $period			= '';
	
	/**
	* Database object constructor
	* @param string Book ID
	* @param string League ID
	* @param string Feed
	*
	function lines( $book_id, $league_id, $feed ) {
		
		//set the local variables
		$this->book_id 		= $book_id;
		$this->league_id 	= $league_id;
		$this->feed 		= $feed;
	}*/
	
	/**
	* Function to overide constructor, for books with multiple feeds
	* @param string Book ID
	* @param string League ID
	* @param string Feed
	*/
	public function setLines( $book_id, $league_id, $feed , $period = 0) {
		
		//set the local variables
		$this->book_id 		= $book_id;
		$this->league_id 	= $league_id;
		$this->feed 		= $feed;
		$this->period 		= $period;
	}
	
	/**
	* Function to set values for sportsbetting
	* @param string Book ID
	* @param string Feed
	*/
	public function setSBLines( $book_id, $feed ) {
		
		//set the local variables
		$this->book_id 		= $book_id;
		$this->feed 		= $feed;
	}
	
	/**
	* function to get the books
	*/
	public function get_books()
	{
		$que = 	"SELECT * FROM ol_books";
		$query = mysql_query($que);
		$rows = array();
		while($res = mysql_fetch_array($query, MYSQL_ASSOC))
		{
			$rows[] = $res;
		}
		return $rows;
	}
	
	/**
	* Public function to update the lines
	* Calls the appropriate private function based on book id
	*/
	public function updateLines( ) {
		
		switch($this->book_id)
		{
			case 1:
				//$this->updateSBookLines( );//sportsbook.ag/rss
				//$this->updateSportsBookLines( );//sportsbook.ag
				break;
			case 2:
				//$this->updateSBLines( );//SportsBetting.com
				break;
			case 3:
				$this->updateBodogLines( );//Bodog.com
				break;
			case 4:
				//$this->updateBJLines( );//BetJamaica.com
				break;
			case 5:
				$this->updatePSLines( );//PinnacleSports.com
				break;
			default:
				return '1';//default do noting
		}
	}
	
	
	
	private function updateBodogLines( ) {
	
		$xml_data = file_get_contents($this->feed);
		$array =  $this->xml2array($xml_data, 1, 'attribute');
		
		/*
		echo "<pre>";
		print_r($array);
		echo "</pre>";
		exit();
		*/
		
		$date_updated = date('Y-m-d H:i:s');
		
		$bookname = trim($array[Schedule][attr][BOOKNAME]);
		$pub_date = trim($array[Schedule][attr][PUBLISH_DATE]);
		$pub_time = trim($array[Schedule][attr][PUBLISH_TIME]);
		$pub_ts	  = trim($array[Schedule][attr][TS]);
		/*
		echo "<br>bookname : " . $bookname;
		echo "<br>";
		echo "pub_date : " . $pub_date;
		echo "<br>";
		echo "pub_time : " . $pub_time;
		echo "<br>";
		echo "pub_ts : " . $pub_ts;
		echo "<br>";
		*/
		
		//check for updated feed
		$que = "SELECT id FROM `ol_feeds_log`
				WHERE feed_url = '".$this->feed."'
				AND book_name = '".$bookname."'
				AND pub_date = '".$pub_date."'
				AND	pub_time = '".$pub_time."'
				AND	pub_ts = '".$pub_ts."'";
		//echo $que; exit();
		
		$query = mysql_query($que);
		$count = @mysql_num_rows($query);
		if($count == 0)
		{
			@mysql_query("INSERT INTO `ol_feeds_log` (
							`id` , `feed_url` , `book_name` , `pub_date` , `pub_time` , `pub_ts`
							)
							VALUES (
							NULL , '".$this->feed."', '".$bookname."', '".$pub_date."', '".$pub_time."', '".$pub_ts."'
							);");
		}
		
		  if($array[Schedule][EventType]['Date'])
		  {
			$eventType_date = $array[Schedule][EventType]['Date'];
			//echo "<br>evtydate count: ".count($eventType_date)."<br>";
			foreach($eventType_date as $et_key => $et_date)
			{
				$event = array();
				if(isset($eventType_date[0]))
				{
				  $event = $et_date['Event'];
				  $eventType_ts = strtotime($et_date['attr'][DTEXT]);
				}
				elseif($et_key == "Event")
				{
				  $event = $eventType_date['Event'];
				  $eventType_ts = strtotime($eventType_date['attr'][DTEXT]);
				}
				
				
				//echo "etkey: ".$et_key."<br>";
				//echo "event count: ".count($event)."<BR>"; //exit();
				if(count($event) > 0)
				{
					$game_date = date('Y-m-d',$eventType_ts);
					$game_time = date('H:i',$eventType_ts);
					$game_que = mysql_query("SELECT * FROM `ol_games`
											 WHERE `league_id` = '".$this->league_id."'
											 AND `game_date` = '".$game_date."'");
					$game_count = @mysql_num_rows($game_que);
					$ol_games_ids = array();
					$ol_games_array = array();
					if($game_count > 0)
					{
						while($game_res = mysql_fetch_assoc($game_que))
						{
							$ol_id = $game_res['game_id'];
							$ol_games_ids[] = $ol_id;
							$ol_games_array[$ol_id] = $game_res;
						}
					}
					//echo '<pre>'; print_r($ol_games_ids); echo'</pre>';$ol_games_ids; exit();
					$ev_ins_que = "INSERT INTO `ol_games` 
								  (`game_id` , `league_id` , `game_date` , `game_time` , `game_seconds` , 
								   `rot_visitor`,  `rot_home`, `team_visitor` , `team_home`, `pitcher_visitor`, `pitcher_home`,
								   `visitor_score`, `home_score`, `game_clock`, `game_status`, `bet_status`)
							      VALUES ";
					$ev_ins_array = array();//inserting new events array
					$ev_upd_array = array();//updating existing events array, for games score, game clock, bet status, etc.
					$lines_array = array();//inserting new lines array, always inserted, no updates, better for lines history
					//echo "eventcount: ".count($event)."<br>";
					if( !isset($event[0]) )$event[0] = $event;
					if(isset($event[0]))
					{
						
					 foreach($event as $ev)
					 {
						//echo "ID: " . $ev['attr'][ID] . "<BR>";
						$short_name = $ev[SEGMENT]['attr'][SHORT_NAME];
						$event_ts = $ev[Time]['attr'][TS]/1000;
						$event_date = date('Y-m-d',$event_ts);
						$event_time = date('H:i',$event_ts);
						$vis = $ev[Competitor][0];
						$hom = $ev[Competitor][1];
						$rot_vis = $vis['attr'][ROT];
						$rot_home = $hom['attr'][ROT];
						$team_vis = addslashes($vis['attr'][NAME]);
						$team_home = addslashes($hom['attr'][NAME]);
						$pitcher_vis = ''; $pitcher_home = '';
						if($this->league_id == '1' && $short_name == 'Game')//check for MLB, set pitchers for game
						{
						  $pitcher_vis = addslashes($vis[Condition]['attr'][VALUE]);
						  $pitcher_home = addslashes($hom[Condition]['attr'][VALUE]);
						}
						$vis_score = $vis['attr'][SCORE];
						$home_score = $hom['attr'][SCORE];
						$game_clock = $ev['attr'][GAME_CLOCK];
						$game_status = $ev['attr'][GAME_STATUS];
						$bet_status = $ev['attr'][STATUS];
						$ev_id = $ev['attr'][ID];
						if($ev['attr'][MASTER_ID] > 0)
						{
							$ev_id = $ev['attr'][MASTER_ID];
						}
						$event_id = $ev_id;
						
						if($short_name == 'Game')//only update "ol_games" table for game lines
						{
							//check if game (event) already in database
							if( in_array($event_id,$ol_games_ids) )
							{
								//update games changes, if there are any to following:
								$gameN = array();
								$gameN['game_date'] = $event_date;
								$gameN['game_time'] = $event_time;
								$gameN['game_seconds'] = $event_ts;
								$gameN['rot_visitor'] = $rot_vis;
								$gameN['rot_home'] = $rot_home;
								$gameN['team_visitor'] = $team_vis;
								$gameN['team_home'] = $team_home;
								$gameN['pitcher_visitor'] = $pitcher_vis;
								$gameN['pitcher_home'] = $pitcher_home;
								$gameN['visitor_score'] = $vis_score;
								$gameN['home_score'] = $home_score;
								$gameN['game_clock'] = $game_clock;
								$gameN['game_status'] = $game_status;
								$gameN['bet_status'] = $bet_status;
								$ev_upd = array();
								foreach( $gameN as $gnKey => $gnVal )
								{
									if($ol_games_array[$ev_id][$gnKey] != stripslashes($gnVal) && $rot_home > 0 && $rot_vis > 0 )
									{
										$ev_upd[] = "`".$gnKey."`" . " = '".$gnVal."'";
									}
								}
								if(count($ev_upd) > 0)
								{
								  $ev_upd_array[] = "UPDATE `ol_games` SET " . implode(", ", $ev_upd) . 
												    " WHERE `game_id` = '".$ev_id."' 
													AND `league_id` = '".$this->league_id."'";
								 
								}
							}//if( in_array($event_id,$ol_games_ids) )
							else
							{
								//if game (event) not in db, create insert query;
								if($event_date > '2011-01-01')//date error check
								{
									$ev_ins_array[] = "('".$ev_id."' , '".$this->league_id."', '".$event_date."', '".$event_time."', '".$event_ts."', '"
													  .$rot_vis."', '".$rot_home."', '".$team_vis."', '".$team_home."', '".$pitcher_vis."', '".$pitcher_home."', '"
													  .$vis_score."', '".$home_score."', '".$game_clock."', '".$game_status."', '".$bet_status."')";
								}
							}//end of else for if( in_array($event_id,$ol_games_ids) )
						}//end if($short_name == 'Game')
						//set array for totals
						$t_lines_array = array();
						
						//lines for total (over under)
						$tot_line = $ev[Line];
						if($tot_line['attr'][TYPE] == "Total")
						{
							//if there are choices, loop through them
							if( count($tot_line[Choice]) > 0 && $tot_line[Choice][0]['attr'][VALUE] && $tot_line[Choice][1]['attr'][VALUE] )
							{
								$o_choice = $tot_line[Choice][0];
								$u_choice = $tot_line[Choice][1];
								
								//update array for totals
								$t_lines_array['period'] = '4';
								$t_lines_array['visitor_value'] = $o_choice['attr'][NUMBER];//put total value in vistor_value
								$t_lines_array['home_value'] = $o_choice[Odds]['attr'][Line]."/".$u_choice[Odds]['attr'][Line];//put over/under in home_value
								$t_lines_array['visitor_seconds'] = round($o_choice['attr'][TS]/1000);//over line change seconds
								$t_lines_array['home_seconds'] = round($u_choice['attr'][TS]/1000);//under line change seconds
								$t_lines_array['date_updated'] = $date_updated;
							}
							
						}//end of if($tot_line['attr'][TYPE] == "Total")
						//end of lines for total (over under)
						
						//set lines arrays
						$m_lines_array  = array();//money line array
						$c_lines_array = array();//canadian line array
						$s_lines_array = array();//spread line array
						
						//lines for visitor
						if(count($vis[Line]) > 0)
						{
						  foreach($vis[Line] as $line)
						  {
						  	$line_type = $line['attr'][TYPE];
							if($line_type == "Moneyline" && $line[Choice][Odds]['attr'][Line] )
							{
								$m_lines_array['visitor_line'] = $line[Choice][Odds]['attr'][Line];
								$m_lines_array['visitor_seconds'] = round($line[Choice]['attr'][TS]/1000);
								$m_lines_array['visitor_value'] = $line[Choice]['attr'][VALUE];
							}
							if( ($line_type == "Canadian Line" || $line_type == "Runline") && $line[Choice][Odds]['attr'][Line] )
							{
								$c_lines_array['visitor_line'] = $line[Choice][Odds]['attr'][Line];
								$c_lines_array['visitor_seconds'] = round($line[Choice]['attr'][TS]/1000);
								$c_lines_array['visitor_value'] = $line[Choice]['attr'][VALUE];
							}
							if($line_type == "Pointspread" && $line[Choice][Odds]['attr'][Line] )
							{
								$s_lines_array['visitor_line'] = $line[Choice][Odds]['attr'][Line];
								$s_lines_array['visitor_seconds'] = round($line[Choice]['attr'][TS]/1000);
								$s_lines_array['visitor_value'] = $line[Choice]['attr'][VALUE];
							}
						  }
						}//end of if(count($vis[Line]) > 0)
						//end of lines for visitor
						
						//lines for home
						if(count($hom[Line]) > 0)
						{
						  foreach($hom[Line] as $line)
						  {
						  	$line_type = $line['attr'][TYPE];
							if($line_type == "Moneyline" && $line[Choice][Odds]['attr'][Line] )
							{
								$m_lines_array['home_line'] = $line[Choice][Odds]['attr'][Line];
								$m_lines_array['home_seconds'] = round($line[Choice]['attr'][TS]/1000);
								$m_lines_array['home_value'] = $line[Choice]['attr'][VALUE];
							}
							if( ($line_type == "Canadian Line" || $line_type == "Runline") && $line[Choice][Odds]['attr'][Line] )
							{
								$c_lines_array['home_line'] = $line[Choice][Odds]['attr'][Line];
								$c_lines_array['home_seconds'] = round($line[Choice]['attr'][TS]/1000);
								$c_lines_array['home_value'] = $line[Choice]['attr'][VALUE];
							}
							if($line_type == "Pointspread" && $line[Choice][Odds]['attr'][Line] )
							{
								$s_lines_array['home_line'] = $line[Choice][Odds]['attr'][Line];
								$s_lines_array['home_seconds'] = round($line[Choice]['attr'][TS]/1000);
								$s_lines_array['home_value'] = $line[Choice]['attr'][VALUE];
							}
						  }
						}//end of if(count($vis[Line]) > 0)
						//end of lines for visitor
						
						
						//check the lines array for values
						
							//update database if there are changes for: (total: over/under line) (period = 4)
							if( count($t_lines_array) > 0 )
							{
								//check for changes, and update lines array
								$t_lines_value = $this->checkBodogLines($t_lines_array, $ev_id, '4', $short_name);
								if($t_lines_value != '')$lines_array[] = $t_lines_value;
							
							}//end of if( count($t_lines_array) > 0 )
							
							
							//update database if there are changes for: (money line) (period = 0)
							if( count($m_lines_array) > 0 )
							{
								//check for changes, and update lines array
								$m_lines_value = $this->checkBodogLines($m_lines_array, $ev_id, '0', $short_name);
								if($m_lines_value != '')$lines_array[] = $m_lines_value;
							
							}//end of if( count($m_lines_array) > 0 )
							
							
							//update database if there are changes for: (puck line) (period = 3)
							if( count($c_lines_array) > 0 )
							{
								//check for changes, and update lines array
								$c_lines_value = $this->checkBodogLines($c_lines_array, $ev_id, '3', $short_name);
								if($c_lines_value != '')$lines_array[] = $c_lines_value;
							
							}//end of if( count($c_lines_array) > 0 )
							
							
							//update database if there are changes for: (pointspread) (period = 3)
							if( count($s_lines_array) > 0 )
							{
								//check for changes, and update lines array
								$s_lines_value = $this->checkBodogLines($s_lines_array, $ev_id, '3', $short_name);
								if($s_lines_value != '')$lines_array[] = $s_lines_value;
							
							}//end of if( count($s_lines_array) > 0 )
						
						//end of check the lines array for values
						
						
					 }//end of foreach($event as $ev)
					 
					}//end of if(isset($event[0]))
					
					//insert new games into database
					if( count($ev_ins_array) > 0 )
					{
						$ev_ins_query =  $ev_ins_que . implode(",",$ev_ins_array).";";
						//echo "<br>".$ev_ins_query."<br>";
						@mysql_query($ev_ins_query);
					}
					
					//update existing games in database
					if( count($ev_upd_array) > 0 )
					{
						$ev_upd_query = implode(" limit 1; ", $ev_upd_array).";";
						//echo "<br>".$ev_upd_query."<br>";
						foreach($ev_upd_array as $ev_upd)
						{
						  @mysql_query($ev_upd);
						}
						
					}
					
					//update lines for the games
					if( count($lines_array) > 0)
					{
						$line_ins_query = "INSERT INTO `ol_lines_bd` 
										  (`ol_id` , `game_id` , `book_id` , `short_name`, `period` , `visitor_value` , 
										   `visitor_seconds` , `home_value` , `home_seconds` , `date_updated`
										  )
										  VALUES " . implode(", ",$lines_array) . ";";
						//echo $line_ins_query."<BR>";
						@mysql_query($line_ins_query);
					}
					
				}//end of if(count($event) > 0)
				
			}//end of foreach($eventType_date as $et_date)
		  }//end of if($array[Schedule][EventType]['Date'])
		
		//echo "count: " . $count."<br>";
	
	}//end of private function updateBodogLines( )
	
	
	//local function to check database for bodog lines changes, used in function updateBodogLines()
	private function checkBodogLines($t_lines_array = array(), $ev_id, $period , $short_name)
	{
		$date_updated = date('Y-m-d H:i:s');
		$t_que = mysql_query("SELECT visitor_value, visitor_seconds, home_value, home_seconds 
							  FROM `ol_lines_bd` 
							  WHERE game_id = '".$ev_id."' 
							  AND book_id = '".$this->book_id."'
							  AND short_name = '".$short_name."'
							  AND period = '".$period."'
							  ORDER BY `ol_id` DESC LIMIT 1");
		if(@mysql_num_rows($t_que) > 0)
		{
			$t_res = mysql_fetch_assoc($t_que);
			$t_ins = 0;
			foreach( $t_res as $tKey => $tVal )
			{
				if($tVal != $t_lines_array[$tKey])
				{
					$t_ins = 1;
					break;
				}
			}
			if($t_ins == 1)
			{
				return	"('' , '".$ev_id."', '".$this->book_id."', '".$short_name."', '".$period."', '".$t_lines_array['visitor_value']."', '"
			  			.$t_lines_array['visitor_seconds']."', '".$t_lines_array['home_value']."', '"
						.$t_lines_array['home_seconds']."', '".$date_updated."')";
			}
		}else
		{
				return  "('' , '".$ev_id."', '".$this->book_id."', '".$short_name."', '".$period."', '".$t_lines_array['visitor_value']."', '"
			  			.$t_lines_array['visitor_seconds']."', '".$t_lines_array['home_value']."', '"
						.$t_lines_array['home_seconds']."', '".$date_updated."')";
		}
		return '';
	}//end of function private function checkBodogLines...
	
	
	
	
	
	//function to update all lines for SportsBetting.com
	private function updateSBLines( )
	{
		$xml_data = file_get_contents($this->feed);
		$array =  $this->xml2array($xml_data, 1, 'attribute');
		/*
		echo "<pre>";
		print_r($array);
		echo "</pre>";
		exit();
		*/
		$date_updated = date('Y-m-d H:i:s');
		$stype = array();
		$stype = $array[odds][sport];
		if( !isset($stype[0]) )
		{
			$stype = array();
			$stype[0] = $array[odds][sport];
		}
		if($array[odds] && count($stype) > 0)
		{
			$lines_array = array();
			$league_array = array();
			$league_id = 0;
			foreach($stype as $st)
			{
				//stype id and desc (ex. <id>20000</id> is <desc>NBA - Game Lines</desc>)
				$st_id = $st['attr']['sportid'];
				$st_desc = $st['attr']['name'];
				$game_line = 'Game';
				if($st_desc == 'American Football' || $st_desc == 'Basketball' || $st_desc == 'Ice Hockey NHL' || $st_desc == 'Baseball')
				{
					$region = $st['region'];
					if( !isset($region[0]) )$region[0] = $st['region'];
					
					foreach($region as $rg)
					{
						if($rg['attr']['name'] == 'NFL'  || 
						   $rg['attr']['name'] == 'NBA'  || 
						   $rg['attr']['name'] == 'NHL'  || 
						   $rg['attr']['name'] == 'NCAA' || 
						   $rg['attr']['name'] == 'MLB')
						{
							$group = $rg['group'];
							if( !isset($group[0]) )$group[0] = $rg['group'];
							
							foreach($group as $gp)
							{
								$league_id = 0;
								if($st_desc == 'Baseball' && preg_match('/MLB/',$gp['attr']['name']) && preg_match('/Game Lines/',$gp['attr']['name']) )$league_id = 1;
								if($rg['attr']['name'] == 'NFL' && $gp['attr']['name'] == 'Game Lines')$league_id = 4;
								if($st_desc == 'American Football' && $gp['attr']['name'] == 'NCAA Game Lines')$league_id = 7;
								if($st_desc == 'Basketball' && preg_match('/NCAA/',$gp['attr']['name']) && preg_match('/Game Lines/',$gp['attr']['name']) )$league_id = 3;
								if($gp['attr']['name'] == 'NBA Game Lines')$league_id = 2;
								if($gp['attr']['name'] == 'NHL Game Lines')$league_id = 8;
								
								if($league_id > 0)
								{
									$league_array[$league_id] = $gp['event'];
								}
								
							}//end of foreach($group as $gp)
						}//end of if($rg['attr']['name'] == 'USA')
					}//end of foreach($region as $rg)
					
					
				}//end of if($st_desc == 'American Football' ... )
				
				
			}//end of foreach($stype as $st)
				
				
				if( count($league_array) > 0 )
				{
					//echo '<pre>'; print_r($league_array); echo '</pre>'; exit();
					foreach($league_array as $league_id => $lr)
					{
						
						//echo '<pre>'; print_r($lr); echo '</pre>'; exit();
						//if no array for event, then create it
					    if( !isset($lr[0]) )$lr[0] = $lr;
						foreach($lr as $ev)
						{
							$game_id = 0;
							$bet_end_date = $ev['attr']['betend'].' CET';
							$date_time = strtotime($bet_end_date);
							$game_date = date('Y-m-d', $date_time);
							$game_time = date('H:i:s', $date_time);
							$bet_team_names = $ev['attr']['name'];
							$bet_team_pei = explode(" @ ", $bet_team_names);
							$rnhome = '';
							$rnaway = '';
							$home = trim($bet_team_pei[1]);
							$away = trim($bet_team_pei[0]);
							
							//set defaults for bet values
							$evv  =  array( 'ctime' => $date_time,
											'status' => '',
											'date' => $game_date,
											'time' => $game_time,
											'home' => $home,
											'away' => $away,
											'rnhome' => '',
											'rnaway' => '',
											'mlhome' => '',
											'mlaway' => '',
											'pshome' => '',
											'pshomem' => '',
											'psaway' => '',
											'psawaym' => '',
											'ouhome' => '',
											'ouhomem' => '',
											'ouaway' => '',
											'ouawaym' => '' );
							//go through bets and override defaults, if applicable
							$bets = $ev['bet'];
							//if no array for bets, then create it
					    	if( !isset($bets[0]) )$bets[0] = $bets;
							foreach($bets as $bet)
							{
								//bet selections
								$bet_home = $bet['selection'][0]['attr'];//first row is home team
								$bet_away = $bet['selection'][1]['attr'];//second row is away team
								
								//convert fractions to american system
								convert_fractions($bet_home['priceup'], $bet_home['pricedown']);
								convert_fractions($bet_away['priceup'], $bet_away['pricedown']);
								
								if($bet['attr']['name'] == 'Money Line')
								{
									$evv['mlhome'] = ($bet_home['priceup'] > $bet_home['pricedown']) ? $bet_home['priceup'] : '-'.$bet_home['pricedown'];
									$evv['mlaway'] = ($bet_away['priceup'] > $bet_away['pricedown']) ? $bet_away['priceup'] : '-'.$bet_away['pricedown'];
								}
								
								if($bet['attr']['name'] == 'Point Spread' || $bet['attr']['name'] == 'Puck Line' || $bet['attr']['name'] == 'Run Line')
								{
									$evv['pshome']  = $bet_home['handicap'];
									$evv['psaway']  = $bet_away['handicap'];
									$evv['pshomem'] = ($bet_home['priceup'] > $bet_home['pricedown']) ? $bet_home['priceup'] : '-'.$bet_home['pricedown'];
									$evv['psawaym'] = ($bet_away['priceup'] > $bet_away['pricedown']) ? $bet_away['priceup'] : '-'.$bet_away['pricedown'];
								}
								//Total Runs Over/Under
								if( substr($bet['attr']['name'], 0, 12) == 'Total Points' || 
								    substr($bet['attr']['name'], 0, 11) == 'Total Goals'  || 
									substr($bet['attr']['name'], 0, 10) == 'Total Runs')
								{
									$beth_pei = explode("(", $bet_home['name']);
									if( count($beth_pei) > 1 )
									{
										$bet_home['name'] = trim($beth_pei[0]);
									}
									
									$beta_pei = explode("(", $bet_away['name']);
									if( count($beta_pei) > 1 )
									{
										$bet_away['name'] = trim($beta_pei[0]);
									}
									$evv['ouaway']  = str_replace('Over','O',$bet_home['name']);
									$evv['ouhome']  = str_replace('Under','U',$bet_away['name']);
									$evv['ouawaym'] = ($bet_home['priceup'] > $bet_home['pricedown']) ? $bet_home['priceup'] : '-'.$bet_home['pricedown'];
									$evv['ouhomem'] = ($bet_away['priceup'] > $bet_away['pricedown']) ? $bet_away['priceup'] : '-'.$bet_away['pricedown'];
								}
							}//end of foreach($bets as $bet)
							
							//$team_visitor = preg_replace ("/(?(?=[^a-zA-Z])[^.]|(.))/i", '$1%', $away);
							//echo '<br>Home: '.$home.' | Away: '.$away;
							if($home != '' &&  $away != '' && ($home != $away) )
							{
							  $g_que  = "SELECT `mlhome` ,
												`mlaway` ,
												`pshome` ,
												`pshomem` ,
												`psaway` ,
												`psawaym` ,
												`ouhome` ,
												`ouhomem` ,
												`ouaway` ,
												`ouawaym`  
										FROM `ol_lines_sb`
										WHERE league_id = '".$league_id."'
										AND game_line = '".$game_line."'
										AND date = '".$game_date."'
										AND home = '".addslashes($home)."'
										AND away = '".addslashes($away)."'
										ORDER BY ol_id DESC";
							  //echo '<br>gque: '.$g_que.'<br>';
							  $g_query = mysql_query($g_que);
							  if(@mysql_num_rows($g_query) > 0)
							  {
							  	$g_res = mysql_fetch_assoc($g_query);
								$upd = 0;
								foreach($g_res as $gk => $gv)
								{
									if($evv[$gk] != $gv)
									{
										$upd = 1;
										break;
									}
								}
								
								//if changes, then insert into db
								if($upd == 1)
								{
									$key_arr = array();
									foreach($evv as $ky => $kv)
									{
										$key_arr[] = "'".addslashes($evv[$ky])."'";
									}
									$lines_array[] = "(NULL , '".$league_id."', '".$game_line."', ".implode(", ",$key_arr).", '".$date_updated."')";
								}
								
							  }else
							  {
								
								$key_arr = array();
								foreach($evv as $ky => $kv)
								{
									$key_arr[] = "'".addslashes($evv[$ky])."'";
								}
								$lines_array[] = "(NULL , '".$league_id."', '".$game_line."', ".implode(", ",$key_arr).", '".$date_updated."')";
							  }
							}//end of if($ru_home > 0 && $ru_away > 0)
						}//end of foreach($event as $ev)
						//echo "<br>";
					}//end of foreach($league_array as $lr)
				}//end of if( count($league_array) > 0 )
			echo "<br>New Lines: ".count($lines_array);
			//update lines for the games
			if( count($lines_array) > 0)
			{
				$line_ins_query = "INSERT INTO `ol_lines_sb` 
															(
															`ol_id` ,
															`league_id` ,
															`game_line` ,
															`ctime` ,
															`status` ,
															`date` ,
															`time` ,
															`home` ,
															`away` ,
															`rnhome` ,
															`rnaway` ,
															`mlhome` ,
															`mlaway` ,
															`pshome` ,
															`pshomem` ,
															`psaway` ,
															`psawaym` ,
															`ouhome` ,
															`ouhomem` ,
															`ouaway` ,
															`ouawaym` ,
															`date_updated`
															)
															VALUES " 
								. implode(", ",$lines_array) . ";";
								echo '<br>'.$line_ins_query;
								@mysql_query($line_ins_query);
			}
			
		}//end of if($array[sbk] && count($array[sbk][stype]) > 0)
		
	}//end of private function updateSBLines( )
	
	
	
	//function to update all lines for SportsBetting.ag
	private function updateSBLinesAG( )
	{
		$xml_data = file_get_contents($this->feed);
		$array =  $this->xml2array($xml_data, 1, 'attribute');
		/*
		echo "<pre>";
		print_r($array[sbk][stype]);
		echo "</pre>";
		exit();
		*/
		$date_updated = date('Y-m-d H:i:s');
		$stype = array();
		$stype = $array[sbk][stype];
		if( !isset($stype[0]) )
		{
			$stype = array();
			$stype[0] = $array[sbk][stype];
		}
		if($array[sbk] && count($stype) > 0)
		{
			$lines_array = array();
			foreach($stype as $st)
			{
				//stype id and desc (ex. <id>20000</id> is <desc>NBA - Game Lines</desc>)
				$st_id = $st['id']['value'];
				$st_desc = $st['desc']['value'];
				
				$league_id = 0;
				$game_line = 'Game';
				
				
				/*
				if( preg_match("/football_lines.xml/",$this->feed) )
				{
					switch($st_id)
					{
						//NFL pre-season
						case '10007':
							$league_id = 4;//nfl league id
							$game_line = 'Game';
							break;
						
						//NFL 1st Quarter Lines
						case '10018':
							$league_id = 4;//nfl league id
							$game_line = '1Q';
							break;
						
						//NFL 2nd Quarter Lines
						case '10019':
							$league_id = 4;//nfl league id
							$game_line = '2Q';
							break;
						
						//NFL 3rd Quarter Lines
						case '10020':
							$league_id = 4;//nfl league id
							$game_line = '3Q';
							break;
						
						//NFL 4th Quarter Lines
						case '10021':
							$league_id = 4;//nfl league id
							$game_line = '4Q';
							break;
						
						//CFL Game Lines
						case '16000':
							$league_id = 6;
							$game_line = 'Game';
							break;
						
						//Football First Half Lines (CFL, NFL, NCAAF, AFL)
						case '17000':
							$league_id = 6;//Use CFL league id
							$game_line = '1H';
							break;
						
						//Football 2nd Half Lines (CFL, NFL, NCAAF, AFL)
						case '17001':
							$league_id = 6;//Use CFL league id
							$game_line = '2H';
							break;
					}
				}*/
				
				
				if( preg_match("/all-liveodds.xml/",$this->feed) )
				{
					switch($st_id)
					{
						
						//WNBA Game lines
						case '26000':
							$league_id = 9;//
							$game_line = 'Game';
							break;
							
						case '27000':
							$league_id = 9;//2; //if NBA in season it's 2, if WNBA season then it's 9
							$game_line = '1H';
							break;
							
						case '27001':
							$league_id = 9;//2; //if NBA in season it's 2, if WNBA season then it's 9
							$game_line = '2H';
							break;
						
						//Football First Half Lines (CFL, NFL, NCAAF, AFL)
						case '17000':
							$league_id = 6;//Use CFL league id
							$game_line = '1H';
							break;
						
						//Football 2nd Half Lines (CFL, NFL, NCAAF, AFL)
						case '17001':
							$league_id = 6;//Use CFL league id
							$game_line = '2H';
							break;
						
					}
				}
				else
				{
					//set league_id for internal use relative to leagues table
					if(preg_match("/MLB/",$st_desc))$league_id = 1;
					if(preg_match("/NBA/",$st_desc))$league_id = 2;
					if(preg_match("/NCAA Bk/",$st_desc))$league_id = 3;
					if(preg_match("/NFL/",$st_desc))$league_id = 4;
					if(preg_match("/NCAA Football/",$st_desc))$league_id = 7;
					if(preg_match("/NHL/",$st_desc))$league_id = 8;
				}
				if( !isset($st[event][0]) )$st[event][0] = $st[event];
				if($league_id > 0 && count($st[event]))
				{
					//echo "leagueid: ".$league_id."<br>";
					$event = $st[event];
					foreach($event as $ev)
					{
						$game_id = 0;
						$date = $ev['date']['value'];
						$date_p = array();
						$date_p = explode("-",$date);
						$date_q = $date_p[2].'-'.$date_p[0].'-'.$date_p[1];
						$date_time = strtotime($date_q);
						$game_date = date('Y-m-d', $date_time);
						$rnhome = $ev['rnhome']['value'];
						$rnaway = $ev['rnaway']['value'];
						$home = $ev['home']['value'];
						$away = $ev['away']['value'];
						//$team_visitor = preg_replace ("/(?(?=[^a-zA-Z])[^.]|(.))/i", '$1%', $away);
						if($rnhome > 0 && $rnaway > 0 && ($rnhome != $rnaway) )
						{
						  $g_que  = "SELECT `mlhome` ,
											`mlaway` ,
											`pshome` ,
											`pshomem` ,
											`psaway` ,
											`psawaym` ,
											`ouhome` ,
											`ouhomem` ,
											`ouaway` ,
											`ouawaym`  
									FROM `ol_lines_sb`
									WHERE league_id = '".$league_id."'
									AND game_line = '".$game_line."'
									AND date = '".$game_date."'
									AND rnaway = '".$rnaway."'
									AND rnhome = '".$rnhome."'
									ORDER BY ol_id DESC";
						  $g_query = mysql_query($g_que);
						  if(@mysql_num_rows($g_query) > 0)
						  {
						  	$g_res = mysql_fetch_assoc($g_query);
							$upd = 0;
							foreach($g_res as $gk => $gv)
							{
								if($ev[$gk]['value'] != $gv)
								{
									$upd = 1;
									break;
								}
							}
							
							//if changes, then insert into db
							if($upd == 1)
							{
								$key_arr = array();
								foreach($ev as $ky => $kv)
								{
									if($ky == "date")$ev[$ky]['value']=$game_date;
									if($ky != "desc")$key_arr[] = "'".addslashes($ev[$ky]['value'])."'";
								}
								$lines_array[] = "(NULL , '".$league_id."', '".$game_line."', ".implode(", ",$key_arr).", '".$date_updated."')";
							}
							
						  }else
						  {
						  	$key_arr = array();
							foreach($ev as $ky => $kv)
							{
								if($ky == "date")$ev[$ky]['value']=$game_date;
								if($ky != "desc")$key_arr[] = "'".addslashes($ev[$ky]['value'])."'";
							}
							$lines_array[] = "(NULL , '".$league_id."', '".$game_line."', ".implode(", ",$key_arr).", '".$date_updated."')";
						  }
						}//end of if($ru_home > 0 && $ru_away > 0)
					}
					//echo "<br>";
				}//end of if($league_id > 0)
				
			}//end of foreach($stype as $st)
			
			//update lines for the games
			if( count($lines_array) > 0)
			{
				$line_ins_query = "INSERT INTO `ol_lines_sb` 
															(
															`ol_id` ,
															`league_id` ,
															`game_line` ,
															`ctime` ,
															`status` ,
															`date` ,
															`time` ,
															`home` ,
															`away` ,
															`rnhome` ,
															`rnaway` ,
															`mlhome` ,
															`mlaway` ,
															`pshome` ,
															`pshomem` ,
															`psaway` ,
															`psawaym` ,
															`ouhome` ,
															`ouhomem` ,
															`ouaway` ,
															`ouawaym` ,
															`date_updated`
															)
															VALUES " 
								. implode(", ",$lines_array) . ";";
								//echo $line_ins_query;
								@mysql_query($line_ins_query);
			}
			
		}//end of if($array[sbk] && count($array[sbk][stype]) > 0)
		
	}//end of private function updateSBLinesAG( )
	
	
	
	
	
	//function to update all lines for Bet Jamaica
	private function updateBJLines( )
	{
		$feed_p = explode("?",$this->feed);
		$post = $feed_p[1];
		$xml_data = $this->getBJFeed($post);
		$array =  $this->xml2array($xml_data, 1, 'attribute');
		
		$date_updated = date('Y-m-d H:i:s');
		
		if($array[lines] && count($array[lines][line]) > 0)
		{
			$lines_array = array();
			$line = $array[lines][line];
			foreach($line as $ev)
			{
				$date = $ev['attr']['GameDateTime'];
				$date_time = strtotime($date);
				$game_date = date('Y-m-d', $date_time);
				$rnaway = $ev['attr']['Team1Rot'];
				$rnhome = $ev['attr']['Team2Rot'];
				$home = $ev['attr']['Team2'];
				$away = $ev['attr']['Team1'];
				//$team_visitor = preg_replace ("/(?(?=[^a-zA-Z])[^.]|(.))/i", '$1%', $away);
				if($rnhome > 0 && $rnaway > 0)
				{
				  $g_que  = "SELECT `ListedPitcher1` ,
									`ListedPitcher2` ,
									`FavoredTeamID` ,
									`Spread` ,
									`SpreadAdj1` ,
									`SpreadAdj2` ,
									`TotalPoints` ,
									`TotalAdj1` ,
									`TotalAdj2` ,
									`MoneyLine1` ,
									`MoneyLine2` ,
									`Team1TotalPoints` ,
									`Team2TotalPoints` ,
									`Team1TotalPointsAdj1` ,
									`Team1TotalPointsAdj2` ,
									`Team2TotalPointsAdj1` ,
									`Team2TotalPointsAdj2` ,
									`PuckLine` 
							FROM `ol_lines_bj`
							WHERE league_id = '".$this->league_id."'
							AND GameDate = '".$game_date."'
							AND Team1Rot = '".$rnaway."'
							AND Team2Rot = '".$rnhome."'
							ORDER BY ol_id DESC";
				  //echo $g_que."<br>";
				  $g_query = mysql_query($g_que);
				  if(@mysql_num_rows($g_query) > 0)
				  {
				  	$g_res = mysql_fetch_assoc($g_query);
					$upd = 0;
					foreach($g_res as $gk => $gv)
					{
						if($ev['attr'][$gk] != $gv)
						{
							$upd = 1;
							break;
						}
					}
					
					//if changes, then insert into db
					if($upd == 1)
					{
						$key_arr = array();
						foreach($ev['attr'] as $ky => $kv)
						{
							$key_arr[] = "'".addslashes($ev['attr'][$ky])."'";
						}
						$lines_array[] = "(NULL , '".$this->league_id."', '".$game_date."', ".implode(", ",$key_arr).", '".$date_updated."')";
					}
					
				  }else
				  {
				  	$key_arr = array();
					foreach($ev['attr'] as $ky => $kv)
					{
						$key_arr[] = "'".addslashes($ev['attr'][$ky])."'";
					}
					$lines_array[] = "(NULL , '".$this->league_id."', '".$game_date."', ".implode(", ",$key_arr).", '".$date_updated."')";
				  }
				}//end of if($ru_home > 0 && $ru_away > 0)
			}
			//echo "<br>";
			
			//update lines for the games
			if( count($lines_array) > 0)
			{
				$line_ins_query  = "INSERT INTO `ol_lines_bj` (
																`ol_id` ,
																`league_id` ,
																`GameDate` ,
																`GameDateTime` ,
																`SportType` ,
																`SportSubType` ,
																`Team1Rot` ,
																`Team2Rot` ,
																`Team1` ,
																`Team2` ,
																`ListedPitcher1` ,
																`ListedPitcher2` ,
																`FavoredTeamID` ,
																`Spread` ,
																`SpreadAdj1` ,
																`SpreadAdj2` ,
																`TotalPoints` ,
																`TotalAdj1` ,
																`TotalAdj2` ,
																`MoneyLine1` ,
																`MoneyLine2` ,
																`Team1TotalPoints` ,
																`Team2TotalPoints` ,
																`Team1TotalPointsAdj1` ,
																`Team1TotalPointsAdj2` ,
																`Team2TotalPointsAdj1` ,
																`Team2TotalPointsAdj2` ,
																`PuckLine` ,
																`PeriodNumber` ,
																`PeriodDescription` ,
																`date_updated`
																)
																VALUES" 
								. implode(", ",$lines_array) . ";";
								//echo $line_ins_query;
								@mysql_query($line_ins_query);
			}
			
		}//end of if($array[sbk] && count($array[sbk][stype]) > 0)
		
	}//end of private function updateBJLines( )
	
	
	//local function to get Bet Jamaica xml feed, used by function updateBJLines
	private function getBJFeed($post) 
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
	}//end of private function getBJFeed($post) 
	
	
	//local function for updating SportsBook lines
	private function updateSBookLines( ) {
	
		$xml_data = file_get_contents($this->feed);
		$array =  $this->xml2array($xml_data, 1, 'attribute');
		
		/*
		echo "<pre>";
		print_r($array);
		echo "</pre>";
		exit();
		*/
		
		$date_updated = date('Y-m-d H:i:s');
		
		if($array[rss][channel] && count($array[rss][channel][item]) > 0)
		{
			$item = $array[rss][channel][item];
			if( !isset($item[0]) )$item[0] = $item;//if only one item found, set array
			$lines_array = array();
			
			 foreach($item as $it)
			 {
				if(preg_match("/ @ /",$it[title][value]))
				{
					$title_p = explode(" @ ",$it[title][value]);
					$away = trim($title_p[0]);
					$teamp = explode(" (",$title_p[1]);
					$home = trim($teamp[0]);
					$desc_date = explode(" Bet on ", $it[description][value]);
					$gdatep = explode(" ", trim($desc_date[0]) );
					$gdate = trim($gdatep[0]);
					$gtime = $gdatep[1]." ".$gdatep[2];
					$datep = explode("-",$gdate);
					$datef = $datep[2]."-".$datep[0]."-".$datep[1]." ".$gtime;
					$game_date = date('Y-m-d', strtotime($datef));
					$game_time = date('H:i', strtotime($datef)).'ET';
					if($game_date > '2011-01-01')
					{
						$desc = $it[description][value];
						$dp = explode("Bet on",$desc);
						//if lines found, check and update
						if(count($dp) > 0)
						{
							//set sb array defaults
							$sb = array();
							$sb['date'] = $game_date;
							$sb['time'] = $game_time;
							$sb['home'] = $home;
							$sb['away'] = $away;
							$sb['bet_home'] = '';
							$sb['bet_away'] = '';
							$sb['over'] = '';
							$sb['under'] = '';
							$sb['money_home'] = '';
							$sb['money_away'] = '';
							//check for totals
							if( preg_match("/Totals:/",$dp[1]) )
							{
								//get over under values
								$totp1 = explode(". Totals:",$dp[1]);
								$totp2 = explode("Under",$totp1[1]);
								$tot_ov = trim(str_replace("Over",'',$totp2[0]));
								$tot_un = trim($totp2[1]);
								//set over under values
								$sb['over'] = strip_tags($tot_ov);
							    $sb['under'] = strip_tags($tot_un);
								
								//get bet/money lines
								$betp = explode(" or ",$totp1[0]);
								$betp1 = trim($betp[0]);
								$betp2 = trim($betp[1]);
								//set bet/money lines for away
								if(preg_match("/Money:/",$betp1))
								{
									$betm = explode("Money:",$betp1);
									$sb['bet_away'] = strip_tags( trim( str_replace($away,'',$betm[0]) ) );
									$sb['money_away'] = strip_tags( trim($betm[1]) );
								}else
								{
									$sb['bet_away'] = strip_tags( trim( str_replace($away,'',$betp1) ) );
									$sb['money_away'] = '';
								}
								//set bet/money lines for home
								if(preg_match("/Money:/",$betp2))
								{
									$betm = explode("Money:",$betp2);
									$sb['bet_home'] = strip_tags( trim( str_replace($home,'',$betm[0]) ) );
									$sb['money_home'] = strip_tags( trim($betm[1]) );
								}else
								{
									$sb['bet_away'] = strip_tags( trim( str_replace($home,'',$betp2) ) );
									$sb['money_away'] = '';
								}
								
							}else//if no totals found
							{
								//set over under values
								$sb['over'] = '';
							    $sb['under'] = '';
								
								//get bet/money lines
								$betp = explode(" or ",$dp[1]);
								$betp1 = trim($betp[0]);
								$betp2 = trim($betp[1]);
								//set bet/money lines for away
								if(preg_match("/Money:/",$betp1))
								{
									$betm = explode("Money:",$betp1);
									$sb['bet_away'] = strip_tags( trim( str_replace($away,'',$betm[0]) ) );
									$sb['money_away'] = strip_tags( trim($betm[1]) );
								}else
								{
									$sb['bet_away'] = strip_tags( trim( str_replace($away,'',$betp1) ) );
									$sb['money_away'] = '';
								}
								//set bet/money lines for home
								if(preg_match("/Money:/",$betp2))
								{
									$betm = explode("Money:",$betp2);
									$sb['bet_home'] = strip_tags( trim( str_replace($home,'',$betm[0]) ) );
									$sb['money_home'] = strip_tags( trim($betm[1]) );
								}else
								{
									$sb['bet_away'] = strip_tags( trim( str_replace($home,'',$betp2) ) );
									$sb['money_away'] = '';
								}
							}//end of else if no totals found
							
							//query to check changes in lines
							$g_que   =  "SELECT `bet_home` ,
												`bet_away` ,
												`over` ,
												`under` ,
												`money_home` ,
												`money_away`
										FROM `ol_lines_sbook`
										WHERE league_id = '".$this->league_id."'
										AND period = '".$this->period."'
										AND date = '".$game_date."'
										AND home = '".$home."'
										AND away = '".$away."'
										ORDER BY ol_id DESC";
							  //echo $g_que."<br>";
							  $g_query = mysql_query($g_que);
							  if(@mysql_num_rows($g_query) > 0)
							  {
							  	$g_res = mysql_fetch_assoc($g_query);
								$upd = 0;
								$upval = "";
								foreach($g_res as $gk => $gv)
								{
									if($sb[$gk] != $gv)
									{
										$upd = 1;
										break;
									}
								}
								//if changes, then insert into db
								if($upd == 1)
								{
									$key_arr = array();
									foreach($sb as $ky => $kv)
									{
										$key_arr[] = "'".addslashes($sb[$ky])."'";
									}
									$lines_array[] = "(NULL , '".$this->league_id."', '".$this->period."', ".implode(", ",$key_arr).", '".$date_updated."')";
								}
								
							  }else
							  {
							  	$key_arr = array();
								foreach($sb as $ky => $kv)
								{
									$key_arr[] = "'".addslashes($sb[$ky])."'";
								}
								$lines_array[] = "(NULL , '".$this->league_id."', '".$this->period."', ".implode(", ",$key_arr).", '".$date_updated."')";
							  }
							
						}
						//echo "<br>";
					}
				}//end of if(preg_match("/ @ /",$it[title][value]))
			 }//end of foreach($item as $it)
			
			//update lines for the games
			if( count($lines_array) > 0)
			{
				$line_ins_query = "INSERT INTO `ol_lines_sbook` (
																`ol_id` ,
																`league_id` ,
																`period` ,
																`date` ,
																`time` ,
																`home` ,
																`away` ,
																`bet_home` ,
																`bet_away` ,
																`over` ,
																`under` ,
																`money_home` ,
																`money_away` ,
																`date_updated`
																)
																VALUES"
								. implode(", ",$lines_array) . ";";
				//echo $line_ins_query;
				@mysql_query($line_ins_query);
			}
			
		}//end of if($array[rss][channel]..
	}//end of private function updateSBookLines( )
	
	
	
	
	
	
	
	//local function to get sportbook lines, used by function updateSportsBookLines
	private function getSBookFeed($category) 
	{ 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://sportsbook.gamingsystem.net/sportsbook4/www.sportsbook.ag/getodds.xgi?categoryId='.$category); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
		$result = curl_exec($ch); 
		if (curl_errno($ch) > 0) $result = -1;
		curl_close($ch); 
		return $result; 
	}//end of private function getSBookFeed($post) 
	
	
	
	
	
	//local function for updating SportsBook lines
	private function updateSportsBookLines( ) {
	
		$_data = $this->getSBookFeed($this->feed);
		
		//echo $_data; exit();
		
		$tbody = explode('<tbody id="betOdds">', $_data);
		if(count($tbody) > 1)
		{
			$tab1 = explode('<!-- load page footer comes from header -->', $_data);
			$tab2 = explode('<!--  START BODY -->', $tab1[0]);
			$tab3 = explode('<tr valign=middle class="odd">', $tab2[1]);
			if(count($tab3) > 1)
			{
				$tab3[0] = '<table class="border" border=0 cellspacing=0 cellpadding=0 id="wagerTable">
		<tbody id="betOdds">
				
				<tr>
				<th>Date</th>
				<th>Num</th>
				<th>Team</th>
				<th>Money</th>
				<th>Blank_1</th>
				<th>Line</th>
				<th>Blank_2</th>
				<th>OverUnder</th>
				<th>Blank_3</th>
				</tr>';
				$table = implode('<tr valign=middle class="odd">', $tab3);
				//echo $table;
				$tbl = new tableExtractor; 
				$tbl->source = $table; 
				// Set the HTML Document 
				$tbl->anchor = ''; 
				// Set an anchor that is unique and occurs before the Table 
				$tpl->anchorWithin = true; 
				// To use a unique anchor within the table to be retrieved 
				$d = $tbl->extractTable(); // The array
				
				//echo "<pre>"; print_r($d); echo "</pre>";
				
				//check for changes in lines and update
				if(count($d) > 0)
				{
					$sb = array();
					$i = 1;
					while($i<count($d))
					{
						//echo "<br>I: ".$i;
						if( isset($d[$i]['Team']) && trim($d[$i]['Team']) != "")
						{
							$j = $i+2;
							
							//set the game date
							$date_updated = date('Y-m-d H:i:s');
							$datep = $d[$i]['Date'];
							$datef = substr(date('Y'),0,2) . substr($datep,6,2). "-" . substr($datep,0,2) . "-" . substr($datep,3,2);
							$game_date = date('Y-m-d', strtotime($datef));
							$game_time = $d[$j]['Date'];
							
							$sb['date'] = $game_date;
							$sb['time'] = $game_time;
							$sb['rot_away'] = $d[$i]['Num'];
							$sb['rot_home'] = $d[$j]['Num'];
							if(trim($sb['rot_home']) == "")$sb['rot_home'] = $sb['rot_away']+1;
							$sb['home'] = strip_tags($d[$j]['Team']);
							$sb['away'] = strip_tags($d[$i]['Team']);
							$sb['bet_home'] = strip_tags($d[$j]['Line']);
							$sb['bet_away'] = strip_tags($d[$i]['Line']);
							$sb['over'] = str_replace("Over", "", strip_tags($d[$i]['OverUnder']));
							$sb['under'] = str_replace("Under", "", strip_tags($d[$j]['OverUnder']));
							$sb['money_home'] = strip_tags($d[$j]['Money']);
							$sb['money_away'] = strip_tags($d[$i]['Money']);
							
							//check the db for changes
							$g_que   =  "SELECT `bet_home` ,
												`bet_away` ,
												`over` ,
												`under` ,
												`money_home` ,
												`money_away`
										FROM `ol_lines_sbook`
										WHERE `league_id` = '".$this->league_id."'
										AND `period` = '".$this->period."'
										AND `date` = '".$game_date."'
										AND `rot_home` = '".$sb['rot_home']."'
										AND `rot_away` = '".$sb['rot_away']."'
										ORDER BY ol_id DESC";
							  //echo "<br>".$g_que."<br>";
							  $g_query = mysql_query($g_que);
							  if(@mysql_num_rows($g_query) > 0)
							  {
							  	$g_res = mysql_fetch_assoc($g_query);
								$upd = 0;
								$upval = "";
								foreach($g_res as $gk => $gv)
								{
									if($sb[$gk] != $gv)
									{
										$upd = 1;
										break;
									}
								}
								//if changes, then insert into db
								if($upd == 1)
								{
									$key_arr = array();
									foreach($sb as $ky => $kv)
									{
										$key_arr[] = "'".addslashes($sb[$ky])."'";
									}
									$lines_array[] = "(NULL , '".$this->league_id."', '".$this->period."', ".implode(", ",$key_arr).", '".$date_updated."')";
								}
								
							  }else
							  {
							  	$key_arr = array();
								foreach($sb as $ky => $kv)
								{
									$key_arr[] = "'".addslashes($sb[$ky])."'";
								}
								$lines_array[] = "(NULL , '".$this->league_id."', '".$this->period."', ".implode(", ",$key_arr).", '".$date_updated."')";
							  }
							  
							//echo "<pre>"; print_r($sb); echo "</pre>";
							$i += 3;
						}else
						{
							$i++;
						}
						
						
					}//end of while($i<count($d))
					
					
				}//end of if(count($d) > 0)
			}
		}
			
			//update lines for the games
			if( count($lines_array) > 0)
			{
				$line_ins_query = "INSERT INTO `ol_lines_sbook` (
																`ol_id` ,
																`league_id` ,
																`period` ,
																`date` ,
																`time` ,
																`rot_away`,
																`rot_home`,
																`home` ,
																`away` ,
																`bet_home` ,
																`bet_away` ,
																`over` ,
																`under` ,
																`money_home` ,
																`money_away` ,
																`date_updated`
																)
																VALUES"
								. implode(", ",$lines_array) . ";";
				//echo $line_ins_query;
				@mysql_query($line_ins_query);
			}
			
	}//end of private function updateSportsBookLines( )
	
	
	
	
	//local function for updating PinnacleSports lines
	private function updatePSLines( ) {
	
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Authorization: Basic UEIxODgyOTI6YXNoZTQ0")
			);
		$context = stream_context_create($opts);

		$xml_data = file_get_contents($this->feed, false, $context);
		//	$xml_data = file_get_contents($this->feed);
		$array =  $this->xml2array($xml_data, 1, 'attribute');
		/*
		echo "<pre>";
		echo print_r($array);
		echo "</pre>";
		exit();
		*/
		$date_updated = date('Y-m-d H:i:s');
		
		if($array[rsp][fd][fdTime] && count($array[rsp][fd][sports][sport][leagues][league][events][event]) > 0)
		{
			$lines_array = array();
			$event = $array[rsp][fd][sports][sport][leagues][league][events][event];
			foreach($event as $ev)
			{
				
				$date = str_replace( array('T','Z'), " ", $ev['startDateTime']['value']) . " GMT";
				$date_time = strtotime($date);
				$game_date = date('Y-m-d', $date_time);
				$game_time = date('H:i', $date_time);
				$gamenumber = $ev[id][value];
				
				
				//set participant values
				$visitor = $ev[awayTeam][name][value];
				$home = $ev[homeTeam][name][value];
				$rot_visitor = $ev[awayTeam][rotNum][value];
				$rot_home = $ev[homeTeam][rotNum][value];
				
				//values for periods 0=game, 1=1st half or 1st period
				if(  $ev['periods']['period'] && $rot_visitor > 0 && $rot_home > 0 && $game_date > '2012-01-01')
				{
				   $period = array();
				   if( isset($ev['periods']['period']['0']) )
				   {
				   	   $period = $ev['periods']['period'];
				   }
				   else
				   {
				   	   $period[0] = $ev['periods']['period'];
				   }
					   foreach($period as $pe)
					   {
						  $period_number = $pe[number][value];
						  $per_array = array();
						  $per_array[game_date] = $game_date;
						  $per_array[game_time] = $game_time;
						  $per_array[rot_visitor] = $rot_visitor;
						  $per_array[rot_home] = $rot_home;
						  $per_array[vistor] = $visitor;
						  $per_array[home] = $home;
						  $per_array[period] = $period_number;
						  $per_array[moneyline_visiting] = (isset($pe[moneyLine])) ? $pe[moneyLine][awayPrice][value] : '';
						  $per_array[moneyline_home] = (isset($pe[moneyLine])) ? $pe[moneyLine][homePrice][value] : '';
						  if( !isset($pe[spreads][spread][0]) )$pe[spreads][spread][0] = $pe[spreads][spread];
						  $per_array[spread_visiting] = $pe[spreads][spread][0][awaySpread][value];
						  $per_array[spread_adjust_visiting] = $pe[spreads][spread][0][awayPrice][value];
						  $per_array[spread_home] = $pe[spreads][spread][0][homeSpread][value];
						  $per_array[spread_adjust_home] = $pe[spreads][spread][0][homePrice][value];
						  if( !isset($pe[totals][total][0]) )$pe[totals][total][0] = $pe[totals][total];
						  $per_array[total_points] = $pe[totals][total][0][points][value];
						  $per_array[over_adjust] = $pe[totals][total][0][overPrice][value];
						  $per_array[under_adjust] = $pe[totals][total][0][underPrice][value];
						  $g_que  = "SELECT `moneyline_visiting` ,
											`moneyline_home` ,
											`spread_visiting` ,
											`spread_adjust_visiting` ,
											`spread_home` ,
											`spread_adjust_home` ,
											`total_points` ,
											`over_adjust` ,
											`under_adjust`
									FROM `ol_lines_ps`
									WHERE league_id = '".$this->league_id."'
									AND game_date = '".$game_date."'
									AND rot_visitor = '".$rot_visitor."'
									AND rot_home = '".$rot_home."'
									AND period = '".$period_number."'
									ORDER BY ol_id DESC";
						 // echo $pecount."/".$period_number . ": ".$visitor."/".$home."<br>";
						  //echo $g_que.'<br><br>';
						  $g_query = mysql_query($g_que);
						  if(@mysql_num_rows($g_query) > 0)
						  {
						  	$g_res = mysql_fetch_assoc($g_query);
							$upd = 0;
							foreach($g_res as $gk => $gv)
							{
								if($per_array[$gk] != $gv)
								{
									$upd = 1;
									break;
								}
							}
							
							//if changes, then insert into db
							if($upd == 1)
							{
								$key_arr = array();
								foreach($per_array as $pv)
								{
									$key_arr[] = "'".addslashes($pv)."'";
								}
								$lines_array[] = "(NULL , '".$this->league_id."', ".implode(", ",$key_arr).", '".$date_updated."')";
							}
							
						  }else
						  {
						  	$key_arr = array();
							foreach($per_array as $pv)
							{
								$key_arr[] = "'".addslashes($pv)."'";
							}
							$lines_array[] = "(NULL , '".$this->league_id."', ".implode(", ",$key_arr).", '".$date_updated."')";
						  }
					   }//end of foreach($period as $pe)
				   
				}//end of if( count($period) > 0 && $rot_visitor > 0 && $rot_home > 0)
			}
			
			//update lines for the games
			if( count($lines_array) > 0)
			{
				$line_ins_query  = "INSERT INTO `ol_lines_ps` (
																`ol_id` ,
																`league_id` ,
																`game_date` ,
																`game_time` ,
																`rot_visitor` ,
																`rot_home` ,
																`vistor` ,
																`home` ,
																`period` ,
																`moneyline_visiting` ,
																`moneyline_home` ,
																`spread_visiting` ,
																`spread_adjust_visiting` ,
																`spread_home` ,
																`spread_adjust_home` ,
																`total_points` ,
																`over_adjust` ,
																`under_adjust` ,
																`date_updated`
																)
																VALUES" 
								. implode(", ",$lines_array) . ";";
								//echo '<br>lines: '.$line_ins_query;
								@mysql_query($line_ins_query);
			}
			
		}//end of if($array[pinnacle_line_feed] && count($array[pinnacle_line_feed][events][event]) > 0)
		
	}//end of private function updatePSLines( )
	
	
	
	
	
	//local function for updating PinnacleSports lines(old)
	private function updateOldPSLines( ) {
	
		$xml_data = file_get_contents($this->feed);
		$array =  $this->xml2array($xml_data, 1, 'attribute');
		//echo "<pre>";
		//echo print_r($array);
		//echo "</pre>";
		$date_updated = date('Y-m-d H:i:s');
		
		if($array[pinnacle_line_feed] && count($array[pinnacle_line_feed][events][event]) > 0)
		{
			$lines_array = array();
			$event = $array[pinnacle_line_feed][events][event];
			foreach($event as $ev)
			{
				$date = $ev['event_datetimeGMT']['value']." GMT";
				$date_time = strtotime($date);
				$game_date = date('Y-m-d', $date_time);
				$game_time = date('H:i', $date_time);
				$gamenumber = $ev[gamenumber][value];
				//set participant values
				$part1 = $ev[participants][participant][0];
				$part2 = $ev[participants][participant][1];
				$visitor = $part1[participant_name][value];
				$home = $part2[participant_name][value];
				$rot_visitor = $part1[rotnum][value];
				$rot_home = $part2[rotnum][value];
				//if 1st participant not visitor, reset participant values
				if($part1[visiting_home_draw] == "Home")
				{
					$visitor = $part2[participant_name][value];
					$home = $part1[participant_name][value];
					$rot_visitor = $part2[rotnum][value];
					$rot_home = $part1[rotnum][value];
				}
				//values for periods 0=game, 1=1st half or 1st period
				if(  $ev['periods']['period'] && $rot_visitor > 0 && $rot_home > 0 && $game_date > '2011-01-01')
				{
				   if($ev['periods']['period']['0'])
				   {
					   $period = $ev['periods']['period'];
					   foreach($period as $pe)
					   {
						  $period_number = $pe[period_number][value];
						  $per_array = array();
						  $per_array[game_date] = $game_date;
						  $per_array[game_time] = $game_time;
						  $per_array[rot_visitor] = $rot_visitor;
						  $per_array[rot_home] = $rot_home;
						  $per_array[vistor] = $visitor;
						  $per_array[home] = $home;
						  $per_array[period] = $period_number;
						  $per_array[moneyline_visiting] = $pe[moneyline][moneyline_visiting][value];
						  $per_array[moneyline_home] = $pe[moneyline][moneyline_home][value];
						  $per_array[spread_visiting] = $pe[spread][spread_visiting][value];
						  $per_array[spread_adjust_visiting] = $pe[spread][spread_adjust_visiting][value];
						  $per_array[spread_home] = $pe[spread][spread_home][value];
						  $per_array[spread_adjust_home] = $pe[spread][spread_adjust_home][value];
						  $per_array[total_points] = $pe[total][total_points][value];
						  $per_array[over_adjust] = $pe[total][over_adjust][value];
						  $per_array[under_adjust] = $pe[total][under_adjust][value];
						  $g_que  = "SELECT `moneyline_visiting` ,
											`moneyline_home` ,
											`spread_visiting` ,
											`spread_adjust_visiting` ,
											`spread_home` ,
											`spread_adjust_home` ,
											`total_points` ,
											`over_adjust` ,
											`under_adjust`
									FROM `ol_lines_ps`
									WHERE league_id = '".$this->league_id."'
									AND game_date = '".$game_date."'
									AND rot_visitor = '".$rot_visitor."'
									AND rot_home = '".$rot_home."'
									AND period = '".$period_number."'
									ORDER BY ol_id DESC";
						  //echo $pecount."/".$period_number . ": ".$visitor."/".$home."<br>";
						  $g_query = mysql_query($g_que);
						  if(@mysql_num_rows($g_query) > 0)
						  {
						  	$g_res = mysql_fetch_assoc($g_query);
							$upd = 0;
							foreach($g_res as $gk => $gv)
							{
								if($per_array[$gk] != $gv)
								{
									$upd = 1;
									break;
								}
							}
							
							//if changes, then insert into db
							if($upd == 1)
							{
								$key_arr = array();
								foreach($per_array as $pv)
								{
									$key_arr[] = "'".addslashes($pv)."'";
								}
								$lines_array[] = "(NULL , '".$this->league_id."', ".implode(", ",$key_arr).", '".$date_updated."')";
							}
							
						  }else
						  {
						  	$key_arr = array();
							foreach($per_array as $pv)
							{
								$key_arr[] = "'".addslashes($pv)."'";
							}
							$lines_array[] = "(NULL , '".$this->league_id."', ".implode(", ",$key_arr).", '".$date_updated."')";
						  }
					   }//end of foreach($period as $pe)
				   }else//if($ev['periods']['period']['0'])
				   {
						  $pe = $ev['periods']['period'];
						  $period_number = $pe[period_number][value];
						  $per_array = array();
						  $per_array[game_date] = $game_date;
						  $per_array[game_time] = $game_time;
						  $per_array[rot_visitor] = $rot_visitor;
						  $per_array[rot_home] = $rot_home;
						  $per_array[vistor] = $visitor;
						  $per_array[home] = $home;
						  $per_array[period] = $period_number;
						  $per_array[moneyline_visiting] = $pe[moneyline][moneyline_visiting][value];
						  $per_array[moneyline_home] = $pe[moneyline][moneyline_home][value];
						  $per_array[spread_visiting] = $pe[spread][spread_visiting][value];
						  $per_array[spread_adjust_visiting] = $pe[spread][spread_adjust_visiting][value];
						  $per_array[spread_home] = $pe[spread][spread_home][value];
						  $per_array[spread_adjust_home] = $pe[spread][spread_adjust_home][value];
						  $per_array[total_points] = $pe[total][total_points][value];
						  $per_array[over_adjust] = $pe[total][over_adjust][value];
						  $per_array[under_adjust] = $pe[total][under_adjust][value];
						  $g_que  = "SELECT `moneyline_visiting` ,
											`moneyline_home` ,
											`spread_visiting` ,
											`spread_adjust_visiting` ,
											`spread_home` ,
											`spread_adjust_home` ,
											`total_points` ,
											`over_adjust` ,
											`under_adjust`
									FROM `ol_lines_ps`
									WHERE league_id = '".$this->league_id."'
									AND game_date = '".$game_date."'
									AND rot_visitor = '".$rot_visitor."'
									AND rot_home = '".$rot_home."'
									AND period = '".$period_number."'
									ORDER BY ol_id DESC";
						  //echo $pecount."/".$period_number . ": ".$visitor."/".$home."<br>";
						  $g_query = mysql_query($g_que);
						  if(@mysql_num_rows($g_query) > 0)
						  {
						  	$g_res = mysql_fetch_assoc($g_query);
							$upd = 0;
							foreach($g_res as $gk => $gv)
							{
								if($per_array[$gk] != $gv)
								{
									$upd = 1;
									break;
								}
							}
							
							//if changes, then insert into db
							if($upd == 1)
							{
								$key_arr = array();
								foreach($per_array as $pv)
								{
									$key_arr[] = "'".addslashes($pv)."'";
								}
								$lines_array[] = "(NULL , '".$this->league_id."', ".implode(", ",$key_arr).", '".$date_updated."')";
							}
							
						  }else
						  {
						  	$key_arr = array();
							foreach($per_array as $pv)
							{
								$key_arr[] = "'".addslashes($pv)."'";
							}
							$lines_array[] = "(NULL , '".$this->league_id."', ".implode(", ",$key_arr).", '".$date_updated."')";
						  }
				   }//end of else if($ev['periods']['period']['0'])
				}//end of if( count($period) > 0 && $rot_visitor > 0 && $rot_home > 0)
			}
			
			//update lines for the games
			if( count($lines_array) > 0)
			{
				$line_ins_query  = "INSERT INTO `ol_lines_ps` (
																`ol_id` ,
																`league_id` ,
																`game_date` ,
																`game_time` ,
																`rot_visitor` ,
																`rot_home` ,
																`vistor` ,
																`home` ,
																`period` ,
																`moneyline_visiting` ,
																`moneyline_home` ,
																`spread_visiting` ,
																`spread_adjust_visiting` ,
																`spread_home` ,
																`spread_adjust_home` ,
																`total_points` ,
																`over_adjust` ,
																`under_adjust` ,
																`date_updated`
																)
																VALUES" 
								. implode(", ",$lines_array) . ";";
								//echo $line_ins_query;
								//@mysql_query($line_ins_query);
			}
			
		}//end of if($array[pinnacle_line_feed] && count($array[pinnacle_line_feed][events][event]) > 0)
		
	}//end of private function updateOldPSLines( )
	
	
	
	/**
	 * xml2array() will convert the given XML text to an array in the XML structure.
	 * Link: http://www.bin-co.com/php/scripts/xml2array/
	 * Arguments : $contents - The XML text
	 *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
	 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
	 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
	 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
	 *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
	 */
	private function xml2array($contents, $get_attributes=1, $priority = 'tag') {
	    if(!$contents) return array();
	
	    if(!function_exists('xml_parser_create')) {
	        //print "'xml_parser_create()' function not found!";
	        return array();
	    }
	
	    //Get the XML parser of PHP - PHP must have this module for the parser to work
	    $parser = xml_parser_create('');
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, trim($contents), $xml_values);
	    xml_parser_free($parser);
	
	    if(!$xml_values) return;//Hmm...
	
	    //Initializations
	    $xml_array = array();
	    $parents = array();
	    $opened_tags = array();
	    $arr = array();
	
	    $current = &$xml_array; //Refference
	
	    //Go through the tags.
	    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	    foreach($xml_values as $data) {
	        unset($attributes,$value);//Remove existing values, or there will be trouble
	
	        //This command will extract these variables into the foreach scope
	        // tag(string), type(string), level(int), attributes(array).
	        extract($data);//We could use the array by itself, but this cooler.
	
	        $result = array();
	        $attributes_data = array();
	        
	        if(isset($value)) {
	            if($priority == 'tag') $result = $value;
	            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
	        }
	
	        //Set the attributes too.
	        if(isset($attributes) and $get_attributes) {
	            foreach($attributes as $attr => $val) {
	                if($priority == 'tag') $attributes_data[$attr] = $val;
	                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
	            }
	        }
	
	        //See tag status and do the needed.
	        if($type == "open") {//The starting of the tag '<tag>'
	            $parent[$level-1] = &$current;
	            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
	                $current[$tag] = $result;
	                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
	                $repeated_tag_index[$tag.'_'.$level] = 1;
	
	                $current = &$current[$tag];
	
	            } else { //There was another element with the same tag name
	
	                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	                    $repeated_tag_index[$tag.'_'.$level]++;
	                } else {//This section will make the value an array if multiple tags with the same name appear together
	                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
	                    $repeated_tag_index[$tag.'_'.$level] = 2;
	                    
	                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
	                        unset($current[$tag.'_attr']);
	                    }
	
	                }
	                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
	                $current = &$current[$tag][$last_item_index];
	            }
	
	        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
	            //See if the key is already taken.
	            if(!isset($current[$tag])) { //New Key
	                $current[$tag] = $result;
	                $repeated_tag_index[$tag.'_'.$level] = 1;
	                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
	
	            } else { //If taken, put all things inside a list(array)
	                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
	
	                    // ...push the new element into that array.
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	                    
	                    if($priority == 'tag' and $get_attributes and $attributes_data) {
	                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                    }
	                    $repeated_tag_index[$tag.'_'.$level]++;
	
	                } else { //If it is not an array...
	                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
	                    $repeated_tag_index[$tag.'_'.$level] = 1;
	                    if($priority == 'tag' and $get_attributes) {
	                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                            
	                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
	                            unset($current[$tag.'_attr']);
	                        }
	                        
	                        if($attributes_data) {
	                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                        }
	                    }
	                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
	                }
	            }
	
	        } elseif($type == 'close') { //End of tag '</tag>'
	            $current = &$parent[$level-1];
	        }
	    }
	    
	    return($xml_array);
	}

}// end of class lines

?>