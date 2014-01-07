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

if( isset($_POST['p_contest']) )
{

	$picks = getSoccerSchedule('1');//get all the possible picks for user in the current week
	$now_date = date('Y-m-d H:i:s');

	$first_match_date_timestamp = strtotime($picks[0]['game_date']);
	$now_date_timestamp = strtotime($now_date);

	//close 30 mins before 1st match

	$allowed_to_pick = $first_match_date_timestamp - $now_date_timestamp - (30*60);
	
	//echo $allowed_to_pick; die();

	if($allowed_to_pick>0){

		$week_id = (int)$_POST['week_id'];
		$res = getContestUserPicks('3',$week_id);
		
		//set dates for check points
		$now_date = date('Y-m-d H:i:s');
		$end_date = getCurrentExpiry('2');
		$min_key = min(array_keys($team));
		$team_date = date('Y-m-d H:i:s',$gametime[$min_key]);
		if($team_date < $end_date)$end_date = $team_date;
		//concatenate teams array into list
		$teams_list = implode(',',$team);

		
		$upd_que = '';
		if( count($res) > 0){
			$upicks = $res[0];
			if($upicks['user_picks'] == $teams_list){
				header('Location: '.$mosConfig_live_site.'?option=com_contests&contest_id='.$contest_id.'&tab=mypicks');
				exit();
			}else{
				$upd_que = "UPDATE `".MOSCONFIG_BRDPREFIX."contests_soccer_picks`
					    	SET user_picks = '".$teams_list."',
								entry_date = '".$now_date."',
								end_date = '".$end_date."'
							WHERE pick_id = ".$upicks['pick_id'];
			}
		}else{
			$upd_que = "INSERT INTO `".MOSCONFIG_BRDPREFIX."contests_soccer_picks` 
						(
						`pick_id` , `user_id` , `contest_id` , `week_num` , `user_picks`, `entry_date`, `end_date`
						)
						VALUES 
						(
						NULL , '".$user->ID."', '".$contest_id."', '".$week_id."', '".$teams_list."', '".$now_date."', '".$end_date."'
						)";
		}
		if($upd_que != ''){
			@mysql_query($upd_que);
		}

	header('Location: '.$mosConfig_live_site.'?option=com_contests&contest_id='.$contest_id.'&tab=mypicks');
	}else{
	header('Location: '.$mosConfig_live_site.'?option=com_contests&contest_id='.$contest_id.'&tab=pick');
	}
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