<?php

$add = isset($_POST['add']) && $_POST['add']=='true';

$change_dates = isset($_POST['change_dates']) && $_POST['change_dates']=='true';
	
	if($change_dates && isset($_POST['start'],$_POST['end']) ){

		$start_date = date( 'Y-m-d H:i:s', strtotime($_POST['start']) );
		$end_date = date( 'Y-m-d H:i:s', strtotime($_POST['end']) );
		$query = 'UPDATE `br3_contests`
							SET start_date = "'.$start_date.'", end_date = "'.$end_date.'" WHERE contest_id = '.(int)$_GET['contest_id'];
		//echo $query;
		mysql_query($query);
	}


	if($add && isset($_POST['gameinfo']) && sizeof($_POST['gameinfo'])>0){

		$game = $_POST['gameinfo'];

		for($i=0,$j=sizeof($game['time']); $i<$j; $i++){
			$game_date = date( 'Y-m-d H:i:s', strtotime($game['time'][$i]) );

			//$week_num = getWeekFromDate($contests[0]['start_date'], $game_date);

			$week_num = (int)$game['week'][$i];


			$away_team = mysql_real_escape_string($game['away']['name'][$i]);
			$home_team = mysql_real_escape_string($game['home']['name'][$i]);
			$ps_away = mysql_real_escape_string($game['away']['handicap'][$i]);
			$ps_home = mysql_real_escape_string($game['home']['handicap'][$i]);			

			$insert = "INSERT INTO  `br3_contests_soccer` (`game_id` ,	`week_num` , `game_date` , `away_team` , `home_team` , `ps_away` , `ps_home` , `away_score` , `home_score`)
			VALUES ( NULL ,  '".$week_num."',  '".$game_date."',  '".$away_team."',  '".$home_team."',  '".$ps_away."',  '".$ps_home."', 0, 0)";
			$result = mysql_query($insert);
		}

	}else{

		//echo '<pre>'; print_r($_POST); echo'</pre>'; exit();
				extract($_POST);
				if(count($game) > 0 )
				{
					foreach($game as $gid => $gval)
					{
						
						$upd = "UPDATE `br3_contests_soccer`
								SET `ps_home` = '".$home[$gid]."',
									`ps_away` = '".$away[$gid]."'
								WHERE `week_num` = ".$week."
								AND `game_id` = ".$gid;
						@mysql_query($upd);
					}
					if( isset($publish) )
					{
						$pbd = "UPDATE `br3_contests_soccer`
								SET `published` = 'yes', `published_time` = '".date('Y-m-d H:i:s')."'
								WHERE `week_num` = ".$week;
						@mysql_query($pbd);
					}else
					{
						$wk_que = "SELECT *
								   FROM `br3_contests_soccer`
								   WHERE week_num = ".$week."
								   ORDER BY game_date
								   LIMIT 1";
						$wk_query = mysql_query($wk_que);
						$wk_res = mysql_fetch_assoc($wk_query);
						$wk_date = $wk_res['game_date'];
						$cu_date = date('Y-m-d H:i:s');
						if($cu_date < $wk_date)
						{
							$pbd = "UPDATE `br3_contests_soccer`
								    SET `published` = 'no'
									WHERE `week_num` = ".$week;
							@mysql_query($pbd);
						}
					}
				}
	}


