<?php
extract($_POST);//set post variables as globals

//login session expired, then redirect
if($user->ID == 0)
{
	if( isset($_REQUEST['terms_and_conditions']) )
	{
		echo '<p><br><br></b>You must login to participate in the contests.</p>';
		exit();
	}else
	{
		header('Location: '.$mosConfig_live_site.'?option=com_contests&contest_id='.$contest_id.'&tab=mypicks');
		exit();
	}
}

if( isset($_POST['s_contest']) )
{
	$res = getContestUserPicks('1',$week_id);
	
	//set dates for check points
	$now_date = date('Y-m-d H:i:s');
	$end_date = getCurrentExpiry('1');
	$teams_w = getTeamsByWeek($week_id, $team);
	$team_date = $teams_w[0]['game_date'];
	if($team_date < $end_date)$end_date = $team_date;
	
	$upd_que = '';
	if( count($res) > 0)
	{
		$upicks = $res[0];
		if($upicks['user_picks'] == $team || $upicks['end_date'] < $now_date )
		{
			header('Location: '.$mosConfig_live_site.'?option=com_contests&contest_id='.$contest_id.'&tab=mypicks');
			exit();
		}else
		{
			$upd_que = 'UPDATE `'.$mosConfig_brdbprefix.'contests_picks`
				    	SET user_picks = \''.$team.'\',
							entry_date = \''.$now_date.'\',
							end_date = \''.$end_date.'\'
						WHERE pick_id = '.$upicks['pick_id'];
		}
	}else
	{
		$upd_que = "INSERT INTO `".$mosConfig_brdbprefix."contests_picks` 
					(
					`pick_id` , `user_id` , `contest_id` , `week_num` , `user_picks`, `entry_date`, `end_date`
					)
					VALUES 
					(
					NULL , '".$user->ID."', '".$contest_id."', '".$week_id."', '".$team."', '".$now_date."', '".$end_date."'
					)";
	}
	if($upd_que != '')
	{
		@mysql_query($upd_que);
	}
	header('Location: '.$mosConfig_live_site.'?option=com_contests&contest_id='.$contest_id.'&tab=mypicks');
}

if( isset($_POST['p_contest']) )
{
	//echo '<pre>'; print_r($_POST); echo'</pre>';exit();
	$res = getContestUserPicks('2',$week_id);
	
	//set dates for check points
	$now_date = date('Y-m-d H:i:s');
	$end_date = getCurrentExpiry('2');
	$min_key = min(array_keys($team));
	$team_date = date('Y-m-d H:i:s',$gametime[$min_key]);
	if($team_date < $end_date)$end_date = $team_date;
	//concatenate teams array into list
	$teams_list = implode(',',$team);
	
	$upd_que = '';
	if( count($res) > 0)
	{
		$upicks = $res[0];
		if($upicks['user_picks'] == $teams_list)
		{
			header('Location: '.$mosConfig_live_site.'?option=com_contests&contest_id='.$contest_id.'&tab=mypicks');
			exit();
		}else
		{
			$upd_que = "UPDATE `".$mosConfig_brdbprefix."contests_picks`
				    	SET user_picks = '".$teams_list."',
							entry_date = '".$now_date."',
							end_date = '".$end_date."'
						WHERE pick_id = ".$upicks['pick_id'];
		}
	}else
	{
		$upd_que = "INSERT INTO `".$mosConfig_brdbprefix."contests_picks` 
					(
					`pick_id` , `user_id` , `contest_id` , `week_num` , `user_picks`, `entry_date`, `end_date`
					)
					VALUES 
					(
					NULL , '".$user->ID."', '".$contest_id."', '".$week_id."', '".$teams_list."', '".$now_date."', '".$end_date."'
					)";
	}
	if($upd_que != '')
	{
		@mysql_query($upd_que);
	}
	header('Location: '.$mosConfig_live_site.'?option=com_contests&contest_id='.$contest_id.'&tab=mypicks');
}

if( isset($_REQUEST['terms_and_conditions']) )
{
	echo'
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	
	<html>
	<head>
		<title>'.getContestName($_REQUEST['contest_id']).' Contest - Terms &amp; Conditions</title>
	</head>
	
	<body style="background:none;text-align:left;">';

	echo getContestTerms($_REQUEST['contest_id']);
	
	echo '
	</body>
	</html>';
}
?>