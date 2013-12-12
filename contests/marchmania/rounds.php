<?php 
//set time zone to eastern time zone
include_once('../admin/db_func.php');
tep_db_connect();
require_once(DOC_ROOT.'dbsql.php');
interface ConstInt {
	
	const USERNAME = '';//'scorecard';
	const PASSWORD = '';//'G3tUPNg0';
	
	const LINE_COUNT = 32;
	const TOTAL_ROUNDS = 6;
	const ROUND_VALUES_INSERTED=1;
	const ROUND_INSERT_ERROR=0;
	const ROUND_ALREADY_SET=2;
}

class SetupData extends Db_Sqlr_Basic implements ConstInt
{
	protected $teams = array();
	protected $games = array();
	private $rounds = array();
	private $teamcount = 32;

	public function __construct()
	{
		// don't have many privileges so I can't create triggers on any table plus I cannot create tables just rename current ones
		parent::__construct(self::USERNAME,self::PASSWORD);
	}
	public function setupData()
	{
		$this->setTeams();
		$this->setGames();
		$this->setRounds();
	}
	
	private function setTeams()
	{
		for ($j=1; $j<3; $j++)
		{
			for ($i=1;$i<=$this->teamcount;$i++)
			{
				if ($j==1) $v = 'Left';
				else $v = 'Right';
				//$this->teams[] = array($i +($j-1)*$this->teamcount, "T-$v {$i}", $i);
				$id   = $i +($j-1)*$this->teamcount;
				$rank = $i;
				$name = "T-$v {$i}";
				@mysql_query("INSERT INTO `bracket_team` (ID,RANK,NAME) VALUES ('".$id."', '".$rank."', '".$name."')");
			}
		}
	}
	
	
	private function setGames()
	{
		// SET ALL GAMES FOR TESTING
/********************/		
		$count = 1;
		for ($i=0;$i<$this->teamcount*2;$i+=2)
		{
			$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+1][0], 1);
		}
		for ($i=0;$i<$this->teamcount*2;$i+=4)
		{
			$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+2][0], 2);
		}
		for ($i=0;$i<$this->teamcount*2;$i+=8)
		{
			$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+4][0], 3);
		}
		for ($i=0;$i<$this->teamcount*2;$i+=16)
		{
			$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+8][0], 4);
		}
		for ($i=0;$i<$this->teamcount*2;$i+=32)
		{
			$this->games[] = array($count++, $this->teams[$i][0], $this->teams[$i+16][0], 5);
		}
		$this->games[] = array($count++, $this->teams[0][0], $this->teams[32][0], 6);
/********************/	

		
		$c = count($this->games);
		for ($j=0; $j<$c;$j++)
		{
			$id    = $this->games[$j][0];
			$tid1  = $this->games[$j][1];
			$tid2  = $this->games[$j][2];
			$round = $this->games[$j][3];
			$city  = '-';
			$c1	   = '-';
			@mysql_query("INSERT INTO `bracket_game` (ID,TID1,TID2,ROUND,CITY,COLUMN1) VALUES ('".$id."','".$tid1."','".$tid2."','".$round."','".$city."','".$c1."')");
		}
	}
	private function setRounds()
	{
		$theTime = time() - 1;
		/*for ($i=1;$i<=5;$i++)
		{
			//past
			$this->rounds[] = array($i, $i, $theTime-3600, $theTime);
		}*/
		for ($i=1;$i<=6;$i++)
		{
			$this->rounds[] = array($i, $i, $theTime, $theTime+3600);
			$theTime+=3600;
		}
	
	/********************/	

		
		$c = count($this->rounds);
		for ($j=0; $j<$c;$j++)
		{
			$id = $this->rounds[$j][0];
			$ci = $this->rounds[$j][2];
			$ee = $this->rounds[$j][3];
			$pp = $this->rounds[$j][1];
			
			@mysql_query("INSERT INTO `bracket_round` (PANELID,CREATED,END,POINTS) VALUES ('".$id."','".$ci."','".$ee."','".$pp."')");
		}
	}
}