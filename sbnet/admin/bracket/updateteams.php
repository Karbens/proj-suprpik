<?php
require_once('session.php');
if (isset($_POST['collection']))
{
	$info = array();
	$rounds = array();
	for ($i=0; $i<11;$i++)
	{
		if (isset($_POST['round'.$i]))
		{
			$round = $_POST['round'.$i];
			foreach ($round as $r)		
			{
				$v = explode(',',$r);
				if ($i==1)
				{
					//$rank = $info[0];	$name = $info[1];	$id= $info[2];
					$info[ $v[1] ] = $v; // update team data
				} else
				{
					// should have added the id to the list but since this is a last minute feature, I'll leave it at just name resolution
					// update team matchups
					if ( array_key_exists($v[1],$info ) )  // name of the team
					{	
						$rounds[$i][$v[0]] = $info[$v[1]]; // position in edit list
					}
				}
			}
		}
	}
	
	
	if (isset($_POST['roundchampion']))
	{
		$v = explode(',', $_POST['roundchampion']);
		if ( array_key_exists($v[1],$info ) )  // name of the team
		{
			$v = $info[$v[1]];
		}
		$rounds[7][1] = $v;
	}
	
	if (!empty($info))
	{
		//@mysql_connect('localhost', 'root', 'yogster');
		//@mysql_select_db('sb_contests');
		
		// UPDATE ranks and team names
		foreach ($info as $i)
		{
		   	$name = $i[1];
			$rank = $i[0];
			$id	  = $i[2];
			
			@mysql_query("UPDATE `bracket_team` SET NAME='".$name."', RANK=".$rank." WHERE ID=".$id);
		}
		
			
		if (!empty($rounds))
		{
			
			$cquery = mysql_query("SELECT * FROM `bracket_game`");
			for ($row=0; $row<@mysql_num_rows($cquery); $row++) {
			
				$id = mysql_result($cquery,$row,"ID");
				$t1 = mysql_result($cquery,$row,"TID1");
				$t2 = mysql_result($cquery,$row,"TID2");
				$rd = mysql_result($cquery,$row,"ROUND");
				$cm = mysql_result($cquery,$row,"COLUMN1");
				
				$sround[$id] = array($rd, $t1, $t2, $cm, false);
			}
			
			foreach ($rounds as $r => $val)
			{
				$round = $r; 
				$gameid = 0;
				switch($round)
				{
					case 2: $gameid= 32;break;
					case 3: $gameid= 48;break;
					case 4: $gameid= 56;break;
					case 5: $gameid= 60;break;
					case 6: $gameid= 62;break;
					case 7: $gameid= 63;break;
				}
				
				foreach ($val as $gid => $update)
				{
					$gidn =(int)ceil($gid/2.0);
					$roundid = ($gidn+$gameid);
					
					if (($gid%2) == 1) {
						$sround[$roundid][1]= $update[2];
						$sround[$roundid][4]=true;
					}
					else {
						$sround[$roundid][2]= $update[2];	
						$sround[$roundid][4]=true;
					}
				}
			}
			
			//sqlrcon_debugOn($con);		
			foreach ($sround as $aid => $values)
			{
				if ($values[4] == true)
				{
					
					$t1 = $values[1];
					$t2 = $values[2];
					$id = (int)$aid;
					$rd = (int)$values[0];
					@mysql_query("UPDATE `bracket_game` SET TID1=".$t1.", TID2=".$t2.", COLUMN1=1 WHERE ROUND=".$rd." AND ID=".$id);
				}
			}
		}
	    @mysql_close();
	}
	echo 'Teams updated';
}
?>