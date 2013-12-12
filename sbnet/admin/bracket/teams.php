<?php 
ini_set('display_errors',0);
//error_reporting(E_ALL);
//define( 'DOC_ROOT', '/var/www/html/bracket/');
//define( 'DOC_ROOT', '');
require_once(DOC_ROOT.'rounds.php');

/* THIS HAD TO BE CHANGED FOR NEW MORE SIMPLE LOGIC AS THEY NOW WANT ALL ROUNDS TO BE SELECTED AT ONCE */

class InitTeams extends Db_Sqlr_Basic implements ConstInt// stub function -- this will be replace with db functions
{
	protected $matchups = array();
	protected $teams = array();
	protected $games = array();
	protected $rounds = array();
	protected $teamcount = 32;
	
	public function __construct()
	{
		// don't have many privileges so I can't create triggers on any table plus I cannot create tables just rename current ones
		parent::__construct(self::USERNAME,self::PASSWORD);
	}
	protected function getTeamInfo()
	{
		$this->getTeamData();
		$this->getGameData();	 // simpler as we only need one round	
		$this->getRoundData();
		
	}
	
	public function getAllInfoForCID($cid)
	{
		$id = $cid;
		$query = mysql_query("SELECT * FROM `bracket_user` WHERE USERID=".$id);
		$count = @mysql_num_rows($query)+0;
		if ($count>0) {
			$s = mysql_fetch_assoc($query);
			$retval['cid'] = $cid;
			$retval['answers'] = $s['ANSWERS'];	
			//$this->Cache_Lite->save(serialize($retval), 'CONTEST_BRACKET_CID_'.$cid);
			
			return $retval;
		} else
			return false;
	}
	
	public function setAllInfoForCID($cid, $answers)
	{
		$round = 0;
		
			$info = $this->getAllInfoForCID($cid);
			if ($info !== false)
			{
				return self::ROUND_ALREADY_SET;	
			}
			
			$id = $cid;
			$cc = time();
			$pp = 1;
			$oo = 1;
			$an = $answers;
			$rr = $round;
			
			$query = mysql_query("INSERT INTO `bracket_user` 
								 (PRINTID, OPTIONS, CREATED,ROUND,USERID,ANSWERS) 
								 VALUES 
								 ('".$pp."','".$oo."','".$cc."','".$rr."','".$id."','".$an."')");
			if ($query) {
				// remove cache file so that the next time we use it it's retrieved from database
				//$this->Cache_Lite->remove('CONTEST_BRACKET_CID_'.$cid);
				return self::ROUND_VALUES_INSERTED;
			}
			return self::ROUND_INSERT_ERROR;
		
	}
	
	protected function getTeamData()
	{
		if (DEBUG)
		{
			for ($j=1; $j<3; $j++)
				{
					for ($i=1;$i<=$this->teamcount;$i++)
					{
						if ($j==1) $v = 'Left';
						else $v = 'Right';
						$this->teams[] = array($i +($j-1)*$this->teamcount, "T-$v {$i}", $i);
					}
				}	
				
			return;
		}
		
		if ($data = $this->sendQuery('SELECT * FROM `bracket_team`','team1'))
		{
			for ($row=0; $row<@mysql_num_rows($data); $row++) {
				
				$id = mysql_result($data,$row,"ID");
				$na = mysql_result($data,$row,"NAME");
				$ra = mysql_result($data,$row,"RANK");
				
				$this->teams[] = array($id, $na, $ra);
			}
			
			//$this->Cache_Lite->save(serialize($this->teams));
			//$this->teams = $data;
		}
		
	}
	protected function getGameData()
	{
		
		if (DEBUG)
		{
				$count = 1;
				for ($i=0;$i<$this->teamcount*2;$i+=2)
				{
					$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+1][0], 1);
				}
				return;
		}
		
		
		
		if ( $data = $this->sendQuery('SELECT * FROM `bracket_game` WHERE ROUND=1','games1') )
		{
			for ($row=0; $row<@mysql_num_rows($data); $row++) {
				
				$id = mysql_result($data,$row,"ID");
				$t1 = mysql_result($data,$row,"TID1");
				$t2 = mysql_result($data,$row,"TID2");
				$rd = mysql_result($data,$row,"ROUND");
				
				$this->games[] = array($id, $t1, $t2, $rd);
			}
			//$this->Cache_Lite->save(serialize($this->games));
			//$this->games = $data;
		}
		
	}
	protected function getRoundData()
	{
		if (DEBUG)
		{
			$theTime = time() - 1;
				/*for ($i=1;$i<=5;$i++)
				{
					//past
					$this->rounds[] = array($i, $i, $theTime-3600, $theTime);
				}*/
				for ($i=1;$i<=6;$i++)
				{
					$rounds[] = array($i, $i, $theTime, $theTime+3600);
					$theTime+=3600;
				}
				$this->rounds= $rounds[0];
				return;
		}
		
		if ( $data=$this->sendQuery('SELECT * FROM `bracket_round`','round1') )
		{
			$s = mysql_fetch_assoc($data);
			$this->rounds = array($s['PANELID'], $s['POINTS'], $s['CREATED'], $s['END']);
			//$this->Cache_Lite->save(serialize($this->rounds));
			//$this->rounds = $data;
		}
	}
	
	
	protected function getCurrentRounds()
	{
		$currentTime = time();
		$round = $this->rounds;
		//if ($round[2] >= $currentTime &&  $round[3] <= $currentTime)		
		if (1)// $round[3] <= $currentTime)		
		{
			return	$round;
		}
		return false;
	}
	protected function getTeam($id)
	{
		foreach ($this->teams as $t) 
		{
			if ($t[0] == $id){	return $t;	}
		}
		return false;
	}
	// this function is unnecessary now
	//private function getGames($round){$ret = array();foreach ($this->games as $g){if ($g[3] == $round)	$ret[] = $g;}return $ret;}
	
	public function getMatchUps()
	{
		$this->getTeamInfo();
		$rounds = $this->getCurrentRounds();
		if ($rounds !== false)
		{
			$games = $this->games ;
			foreach ($games as $g)
			{
				$t0 = $this->getTeam($g[1]);
				$t1 = $this->getTeam($g[2]);
				// we need gameid, teamid0 and 1 to create options
				// come back here	
				$this->matchups[] = array($g[0], $t0, $t1);
			}
			return array_values($this->matchups);
		}
		$this->matchups = array();
		return false;
	}
	
	
	public function setRoundPositions()
	{
		$groups = self::LINE_COUNT;
		$class = 'checkit';
		$s = array();
		for ($i=1; $i<self::TOTAL_ROUNDS*2;$i++)
		{
			$count = 0;
			$current = 0;
			$id = 1;
			switch($i)
			{
				case 11:	$current = 16; $class = 'checkit2';
				case 1:  $start = 0; $step = 2; $listing = 1;	
				break;	
				case 10: $current = 8;$class = 'checkit2';
				case 2:  $start = 1; $step = 4;$listing = 2;
				break;
				case 9: $current = 4;$class = 'checkit2';
				case 3:   $start = 3; $step = 8;$listing = 3;
				break;
				case 8: $current = 2;$class = 'checkit2';
				case 4:  $start = 7; $step = 16;$listing = 4;
				break;
				case 7: $current = 1;$class = 'checkit2';
				case 5: $start = 15; $step = 32;$listing = 5;
				break;
				case 6: $start = 31; $step = 64; $listing = 6; break;
				default:break;
			}
			if (isset($this->matchups))
			{
				$lists = $this->matchups;
				$index = $i;
				$counter = count($lists)-1; // because indexes start at 1 but counter starts at 0				
				$groups = (int)($counter/2);
				$total = self::TOTAL_ROUNDS*2;
			}
			
			for ($j=0;$j<self::LINE_COUNT*2;$j++)
			{
				if ($j == $start)
				{
					if ($i == 1 || $i== 11)
					{
						if ($i==1)
						{
							$s[$i][$j] = '<td><input type="text" readonly="readonly" value="('.$lists[$current][$id][2].') '.$lists[$current][$id][1].'" id="team-'.$i.'-'.$lists[$current][$id][0].'"/>
							<input type="radio" id="rt-'.$i.'-'.$lists[$current][0].'-'.$id.'" name="round-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'" value="'.$lists[$current][$id][0].' "  /></td>';
						}
						else
						{
							$s[$i][$j] = '<td><input type="radio" id="rt-'.$i.'-'.$lists[$current][0].'-'.$id.'" name="round-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'" value="'.$lists[$current][$id][0].' " selected="false" /><input type="text" readonly="readonly" value="('.$lists[$current][$id][2].') '.$lists[$current][$id][1].'" id="team-'.$i.'-'.$lists[$current][$id][0].'"/></td>';
						}
						if ($id==2) {								
							$current++;											
							$id = 1;
						} else 	if ($id == 1)	$id=2;
						$start += $step;
					}
					else
					{
						if ($i==6 ) {
							$s[6][10]= '<td><ins>Champion:</ins></td>';
							$s[6][12]='<td><input id="champion" value="" readonly="readonly"/></td>';
							
							$s[$i][$j] = '<td><ins><input id="text-'.$i.'-'.$lists[$current][0].'-1" type="text" readonly="readonly" class="disabled" ><input type="radio" style="visibility:hidden" name="final-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'" value="'.$lists[$current][$id][0].' " selected="false" id="rt-'.$i.'-'.$lists[$current][0].'-1"/></ins><br /><ins><input type="text" readonly="readonly" class="disabled" id="text-'.$i.'-'.$lists[$current][0].'-2"><input type="radio" style="visibility:hidden" name="final-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'" value="'.$lists[$current][$id][0].' " selected="false" id="rt-'.$i.'-'.$lists[$current][0].'-2"/></ins></td>';	
							$start += $step;
						}
						else
						{
							if ($i<7)
							{
								$s[$i][$j] = '<td><input type="text" readonly="readonly" value="" class="disabled" ><input type="radio" name="round-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'"  selected="false" style="visibility:hidden" id="rt-'.$i.'-'.$lists[$current][0].'-'.$id.'" /></td>';	
							} else
							{
								$s[$i][$j] = '<td><input type="radio" name="round-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'" selected="false" style="visibility:hidden" id="rt-'.$i.'-'.$lists[$current][0].'-'.$id.'"/><input type="text" readonly="readonly" value="" class="disabled"></td>';	
							}
							if ($id==2) {
								$current++;
								$id = 1;
							} else 	if ($id == 1)	$id=2;
								$start += $step;
							}
					}
				}
				else $s[$i][$j] ='<td>&nbsp;</td>';
			}
		}
		$this->setRoundPositionsFormat($s);
	}
	
	private function setRoundPositionsFormat($s)
	{
		$options ='';
		for ($j=0;$j<=self::LINE_COUNT*2-1;$j++)
		{
			$options .= '<tr>';
			for ($i=1; $i<self::TOTAL_ROUNDS*2;$i++)
			{

				$options .= $s[$i][$j];
			}
			$options .= '</tr>';
		}
		echo $options;
	}

	
	public function createRoundTeams()
	{
		if (!empty($this->matchups))
			$this->setRoundPositions();
	}
}
