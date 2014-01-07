<?php

//echo '<pre>'; print_r($_POST); echo'</pre>'; exit();
		extract($_POST);
		if(count($game) > 0 )
		{
			foreach($game as $gid => $gval)
			{
				
				$upd = "UPDATE `br3_contests_nfl`
						SET `ps_home` = '".$home[$gid]."',
							`ps_away` = '".$away[$gid]."'
						WHERE `week_num` = ".$week."
						AND `game_id` = ".$gid;
				@mysql_query($upd);
			}
			if( isset($publish) )
			{
				$pbd = "UPDATE `br3_contests_nfl`
						SET `published` = 'yes'
						WHERE `week_num` = ".$week;
				@mysql_query($pbd);
			}else
			{
				$wk_que = "SELECT *
						   FROM `br3_contests_nfl`
						   WHERE week_num = ".$week."
						   ORDER BY game_date
						   LIMIT 1";
				$wk_query = mysql_query($wk_que);
				$wk_res = mysql_fetch_assoc($wk_query);
				$wk_date = $wk_res['game_date'];
				$cu_date = date('Y-m-d H:i:s');
				if($cu_date < $wk_date)
				{
					$pbd = "UPDATE `br3_contests_nfl`
						    SET `published` = 'no'
							WHERE `week_num` = ".$week;
					@mysql_query($pbd);
				}
			}
		}