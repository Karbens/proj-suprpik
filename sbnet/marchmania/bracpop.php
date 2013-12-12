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
		
		
		
		
		if ( $data=$this->sendQuery('SELECT * FROM `bracket_game` WHERE `ROUND` = 1','gamesall') )
		{
			for ($row=0; $row<@mysql_num_rows($data); $row++) {
			
				$id = mysql_result($data,$row,"ID");
				$t1 = mysql_result($data,$row,"TID1");
				$t2 = mysql_result($data,$row,"TID2");
				$rd = mysql_result($data,$row,"ROUND");
				$cm = mysql_result($data,$row,"COLUMN1");
				
				$this->games[] = array($id, $t1, $t2, $rd, $cm);
			}
			$USERID = urldecode($_REQUEST['userid']);
			$USEREM = urldecode($_REQUEST['userem']);
			$nque = mysql_query("SELECT `ANSWERS`
								 FROM `bracket_user`
								 WHERE `USERID` = '".$USERID."'
								 AND `EMAIL` = '".$USEREM."'");
			if( @mysql_num_rows($nque) > 0 )
			{
				$nres = mysql_fetch_row($nque);
				$ans = explode(';',$nres[0]);
				$ans_arr = array();
				foreach($ans as $k => $an)
				{
					$j = $k + 1;
					$r = $j + 1;
					$rnd_str = 'Round '.$j.':';
					$ans_str = str_replace($rnd_str, '', $an);
					$ans_arr[$r] = explode(',',$ans_str);
				}
				if( count($ans_arr) > 0 )
				{
					foreach($ans_arr as $ak => $av)
					{
						
						$round = $ak;
						$count = count($av);
						for($i = 0; $i<$count; $i+=2)
						{
							$j = $i+1;
							$id++;
							$t1 = $av[$i];
							$t2 = ( isset($av[$j]) ) ? $av[$j] : '0';
							$rd = $round;
							$cm = '1';
							$this->games[] = array($id, $t1, $t2, $rd, $cm);
						}
					}
				}
			}//end of if( @mysql_num_rows($mque) > 0 )
			
			//$this->Cache_Lite->save(serialize($this->games));
		}
		//else $this->games = $data;
		
	}
	
	public function setRoundPositions($teams=NULL)
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
			
			//get graded bracket results
			$brac_res = bracket_graded_result('All');
			$completed_rounds = count($brac_res);
			for($b=0; $b<7; $b++)
			{
				if(!isset($brac_res[$b]))$brac_res[$b] = array();//create empty array for incomplete rounds
			}
			$brac_style = ' style="background: none repeat scroll 0 0 #C00C1A; color: white; font-weight: normal;"';//for correct pick
			$brak_style = ' style="background: none repeat scroll 0 0 white; color: #999999; font-weight: normal;"';//for incorrect pick
			
			//echo '<pre>'; print_r($brac_res); echo '</pre>';exit();
				for ($j=0;$j<self::LINE_COUNT*2;$j++)
				{
					if ($j == $start)
					{
						if ($i == 1 || $i== 11)
						{
							$ranking = '';
							
							if ($i==1)
							{
								$s[$i][$j] = '<td>'.$ranking.'
												<input type="text"  value="('.$lists[$current][$id][2].') '.$lists[$current][$id][1].'" id="1-'.$i.'-'.$lists[$current][$id][0].'"/>
												&nbsp;&nbsp;
											  </td>';
							}
							else
							{
								$s[$i][$j] = '<td>
												&nbsp;&nbsp;
												<input type="text"  value="('.$lists[$current][$id][2].') '.$lists[$current][$id][1].'" id="1-'.$i.'-'.$lists[$current][$id][0].'"/>'.$ranking.'
											  </td>';
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
								$stl = $i-1;
								$value1 = ($lists[$ss+1][3] != 0 ) ? '('.$lists[$ss+1][$id][2].') '.$lists[$ss+1][$id][1] : '';
								$style1 = ( in_array($lists[$ss+1][$id][1],$brac_res[6]) ) ? $brac_style : (($completed_rounds >= $i) ? $brak_style : '');
								$value2 = ($lists[$ss][3] != 0 ) ? '('.$lists[$ss][$id][2].') '.$lists[$ss][$id][1] : '';
								$style2 = ( in_array($lists[$ss][$id][1],$brac_res[5]) ) ? $brac_style : (($completed_rounds >= $stl) ? $brak_style : '');
								$value3 = ($lists[$ss][3] != 0 ) ? '('.$lists[$ss][$id+1][2].') '.$lists[$ss][$id+1][1] : '';
								$style3 = ( in_array($lists[$ss][$id+1][1],$brac_res[5]) ) ? $brac_style : (($completed_rounds >= $stl) ? $brak_style : '');
								
								$s[6][10]= '<td><ins>Champion:</ins></td>';
								$s[6][12]='<td><input id="champion" value="'.$value1.'" readonly="readonly" '.$style1.'/></td>';
									$s[$i][$j] = '<td><ins><input id="1-'.$i.'-1" type="text" readonly="readonly" value="'.$value2.'" class="disabled" '.$style2.'>
									
									</ins><br /><ins><input type="text" readonly="readonly" class="disabled" id="1-'.$i.'-2" value="'.$value3.'" '.$style3.'>
									
									</ins></td>';
								$start += $step;
							}
							else
							{
								if ($i<7)
								{
									$stl = $i-1;
									$value1 = ($lists[$ss + $current][3] != 0 ) ? '('.$lists[$ss + $current][$id][2].') '.$lists[$ss + $current][$id][1] : '';
									$style1 = ( in_array($lists[$ss + $current][$id][1] ,$brac_res[$stl]) ) ? $brac_style : (($completed_rounds >= $stl) ? $brak_style : '');
									$s[$i][$j] = '<td>
												  <input type="text" id="1-'.$i.'-'.$lists[$current][$id][0].'" readonly="readonly" value="'.$value1.'" class="disabled" '.$style1.'>
												  &nbsp;&nbsp;
												  </td>';	
								} else
								{
									$stl = 11-$i;
									$value1 = ($lists[$ss + $current][3] != 0 ) ? '('.$lists[$ss + $current][$id][2].') '.$lists[$ss + $current][$id][1] : '';
									$style1 = ( in_array($lists[$ss + $current][$id][1],$brac_res[$stl]) ) ? $brac_style : (($completed_rounds >= $stl) ? $brak_style : '');
									/*if ($i==10)	$textid='1-'.$i.'-'.($lists[$current][$id][0]-16);
									else if ($i==9)	$textid='1-'.$i.'-'.($lists[$current][$id][0]-8);
									else if ($i==8)	$textid='1-'.$i.'-'.($lists[$current][$id][0]-4);
									else if ($i==7)	$textid='1-'.$i.'-'.($lists[$current][$id][0]-2);
									*/
									
									if ($i==10)	$textid=2;
									else if ($i==9)	$textid=3;
									else if ($i==8)	$textid=4;
									else if ($i==7)	$textid=5;
									
									
									$s[$i][$j] = '<td>
												  &nbsp;&nbsp;
												  <input type="text" readonly="readonly" value="'.$value1.'" class="disabled"  id="1-'.$textid.'-'.($lists[$current][$id][0]).'"'.$style1.'>
												 </td>';	
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
	<tr>
		<td colspan="11" style="padding:3px;">&nbsp;</td>
	</tr>';
	
	
$c->setRoundPositions(0);
	echo '</table>';

	
}
?>