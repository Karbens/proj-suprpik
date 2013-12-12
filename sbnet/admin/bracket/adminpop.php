<?php
if (isset($_REQUEST['populate']))
{

define( 'DOC_ROOT', '');
require_once(DOC_ROOT.'rounds.php');
require_once(DOC_ROOT.'teams.php');
class Round_Admin extends InitTeams 
{
	protected $roundsX = array(16,8,4,2,1);
	protected $sides = 2;
	public function __construct()
	{
		parent::__construct();
	}
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
				$this->matchups[] = array($g[0], $t0, $t1, $g[4]);
			}
			return array_values($this->matchups);
		}
		$this->matchups = array();
		return false;
	}
	
	protected function getGameData()
	{
		
		if (DEBUG)
		{
				$count = 1;
				for ($i=0;$i<$this->teamcount*2;$i+=2)
				{
					$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+1][0], 1,1);
				}
				for ($i=0;$i<$this->teamcount*2;$i+=4)
				{
					$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+2][0], 2,0);
				}
				for ($i=0;$i<$this->teamcount*2;$i+=8)
				{
					$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+4][0], 3,0);
				}
				for ($i=0;$i<$this->teamcount*2;$i+=16)
				{
					$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+8][0], 4,0);
				}
				for ($i=0;$i<$this->teamcount*2;$i+=32)
				{
					$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+16][0], 5,0);
				}
				$this->games[] = array($count++, $this->teams[0][0], $this->teams[32][0], 6,0);
				$this->games[] = array($count++, $this->teams[32][0], $this->teams[0][0], 6,0);
					return;
		}
		
		
		
		if ( $data=$this->sendQuery('SELECT * FROM `bracket_game`','gamesall') )
		{
			//echo 'rows: '.@mysql_num_rows($data); exit();
			for ($row=0; $row<@mysql_num_rows($data); $row++) {
			
				$id = mysql_result($data,$row,"ID");
				$t1 = mysql_result($data,$row,"TID1");
				$t2 = mysql_result($data,$row,"TID2");
				$rd = mysql_result($data,$row,"ROUND");
				$cm = mysql_result($data,$row,"COLUMN1");
				
				$this->games[] = array($id, $t1, $t2, $rd, $cm);
			}
			//$this->Cache_Lite->save(serialize($this->games));
		}
		//else $this->games = $data;
		
	}
	
	public function setRoundPositions($teams)
	{	
		$this->getMatchUps();
		
		$ranking = '<select name="ranking">';
		for ($i=0;$i<22;$i++) $ranking .= '<option value="'.$i.'">'.$i.'</option>';
		$ranking .='</select>';
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
				case 11:$current = 16; $class = 'checkit2';
				case 1: $start = 0; $step = 2; $listing = 1;	
				break;	
				case 10:$current = 8;$class = 'checkit2';
				case 2: $start = 1; $step = 4;$listing = 2;$ss = 32;
				break;
				case 9:$current = 4;$class = 'checkit2'; 
				case 3:$start = 3; $step = 8;$listing = 3;$ss = 48;
				break;
				case 8:$current = 2;$class = 'checkit2';
				case 4: $start = 7; $step = 16;$listing = 4;$ss = 56;
				break;
				case 7:$current = 1;$class = 'checkit2';
				case 5:$start = 15; $step = 32;$listing = 5;$ss = 60;
				break;
				case 6:$start = 31; $step = 64; $listing = 6;$ss = 62; $current = 0;break;
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
							$ranking = '<select name="ranking" class="ranking" id="rankings">';
								for ($p=0;$p<=32;$p++) {
									if ($p==$lists[$current][$id][2])	$ranking .= '<option value="'.$p.'" selected="selected">'.$p.'</option>';
									else $ranking .= '<option value="'.$p.'">'.$p.'</option>';		
								}
								$ranking .='</select>';
							
							if ($i==1)
							{
								$s[$i][$j] = '<td>'.$ranking.'<input type="text"  value="'.$lists[$current][$id][1].'" id="1-'.$i.'-'.$lists[$current][$id][0].'"/>
								<input type="radio" id="rt-'.$i.'-'.$lists[$current][0].'-'.$id.'" name="round-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'" value="'.$lists[$current][$id][0].' "  /></td>';
							}
							else
							{
								$s[$i][$j] = '<td><input type="radio" id="rt-'.$i.'-'.$lists[$current][0].'-'.$id.'" name="round-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'" value="'.$lists[$current][$id][0].' " selected="false" /><input type="text"  value="'.$lists[$current][$id][1].'" id="1-'.$i.'-'.$lists[$current][$id][0].'"/>'.$ranking.'</td>';
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
								
								$value1 = ($lists[$ss+1][3] != 0 ) ? $lists[$ss+1][$id][1] : '';
								$value2 = ($lists[$ss][3] != 0 ) ? $lists[$ss][$id][1] : '';
								$value3 = ($lists[$ss][3] != 0 ) ?$lists[$ss][$id+1][1] : '';
								
								$s[6][10]= '<td><ins>Champion:</ins></td>';
								$s[6][12]='<td><input id="champion" value="'.$value1.'" readonly="readonly" /></td>';
									$s[$i][$j] = '<td><ins><input id="1-'.$i.'-1" type="text" readonly="readonly" value="'.$value2.'" class="disabled" ><input type="radio"  name="final-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'"  selected="false" id="rt-'.$i.'-'.$lists[$current][0].'-1"/></ins><br /><ins><input type="text" readonly="readonly" class="disabled" id="1-'.$i.'-2" value="'.$value3.'" ><input type="radio"  name="final-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'"  selected="false" id="rt-'.$i.'-'.$lists[$current][0].'-2"/></ins></td>';	
								$start += $step;
							}
							else
							{
								if ($i<7)
								{
									
									$value1 = ($lists[$ss + $current][3] != 0 ) ? $lists[$ss + $current][$id][1] : '';
									
									$s[$i][$j] = '<td><input type="text" id="1-'.$i.'-'.$lists[$current][$id][0].'" readonly="readonly" value="'.$value1.'" class="disabled" ><input type="radio" name="round-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'"  selected="false" id="rt-'.$i.'-'.$lists[$current][0].'-'.$id.'" /></td>';	
								} else
								{
									
									$value1 = ($lists[$ss + $current][3] != 0 ) ? $lists[$ss + $current][$id][1] : '';
									
									/*if ($i==10)	$textid='1-'.$i.'-'.($lists[$current][$id][0]-16);
									else if ($i==9)	$textid='1-'.$i.'-'.($lists[$current][$id][0]-8);
									else if ($i==8)	$textid='1-'.$i.'-'.($lists[$current][$id][0]-4);
									else if ($i==7)	$textid='1-'.$i.'-'.($lists[$current][$id][0]-2);
									*/
									
									if ($i==10)	$textid=2;
									else if ($i==9)	$textid=3;
									else if ($i==8)	$textid=4;
									else if ($i==7)	$textid=5;
									
									
									$s[$i][$j] = '<td><input type="radio" name="round-'.$listing.'-'.$lists[$current][0].'" class="'.$class.'" selected="false"  id="rt-'.$i.'-'.$lists[$current][0].'-'.$id.'"/><input type="text" readonly="readonly" value="'.$value1.'" class="disabled"  id="1-'.$textid.'-'.($lists[$current][$id][0]).'"></td>';	
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
}
$c = new Round_Admin;
echo '<table cellpadding="0" cellspacing="0" width="100%"><tr class="round">
	<td>Round One</td>
	<td>Round Two</td>
	<td>Sweet Sixteen</td>
	<td>Elite Eight</td>
	<td>Final Four</td>
	<td>Championship</td>
	<td>Final Four</td>
	<td>Elite Eight</td>
	<td>Sweet Sixteen</td>
	<td>Round Two</td>
	<td>Round One</td>
	</tr>
	<tr class="rounddate">
	<td>March 15-16, 2012</td>
	<td>March 17, 18, 2012</td>
	<td>March 22, 23, 2012</td>
	<td>March 24, 25, 2012</td>
	<td>March 31, 2012</td>
	<td>April 2, 2012</td>
	<td>March 31, 2012</td>
	<td>March 24, 25, 2012</td>
	<td>March 22, 23, 2012</td>
	<td>March 17, 18, 2012</td>
	<td>March 15-16, 2012</td>
	</tr>
	<tr><td colspan="5" style="padding:20px">&nbsp;</td><td><input type="button" name="Update" value="Update" id="update" style="height:20px;font-weight:bold;text-align:center"/></td><td colspan="5" style="text-align:right;padding-right:10px">&nbsp;</td></tr>
	<tr><td colspan="5" style="padding:5px">Rank &nbsp;Team</td><td><input type="button" name="Clear Rounds" value="Clear" id="clear" style="height:20px;font-weight:bold;text-align:center"/></td><td colspan="5" style="text-align:right;padding-right:10px">Team &nbsp;Rank</td></tr>';
	
	
$c->setRoundPositions(0);
	echo '</table>';

	
}
?>