<?
require_once('contests_func.php');
tep_db_connect();

if( isset($_POST['action']) )
{
	extract($_POST);
	$content = '';
	if($action == 'addnewentry')
	{
	    if( isset($_POST['contestDate']) && isset($_POST['contestID']) )
		{
			//set current date time variables
			$curdate = date('Y-m-d');
			$curtime = date('H:i');
			$curDateTime = $curdate.' '.$curtime;
			$eventTime = $curtime;
			
			//get events start time
			$tque = mysql_query("SELECT e.event_time
								 FROM `events_choices` c, `events` e
								 WHERE c.event_id = e.event_id
								 AND c.ec_id = ".$entryValue);
			$tres = @mysql_fetch_row($tque);
			if($tres[0] != '')$eventTime = $tres[0];
			
			//set contest date time
			$conDateTime = $contestDate.' '.$eventTime;
			$entryDate = date('Y-m-d H:i:s');
			
			//validate contest date time with current date time
			if($conDateTime > $curDateTime)
			{
				$cque = mysql_query("SELECT `entry_id`,`contest_time` FROM `contest_entries`
									 WHERE `contest_id` = ".$contestID."
									 AND `contest_date` = '".$contestDate."'
									 AND `customer_id` = '".trim($cid)."'
									 AND `customer_email` = '".trim($email)."'");
				$ccount = @mysql_num_rows($cque);
				if($ccount > 0)
				{
					$cres = mysql_fetch_row($cque);
					$entry_id = $cres[0];
					$entry_time = $cres[1];
					if($entry_time > date('H:i') )
					{
						@mysql_query("UPDATE `contest_entries`
									  SET `contest_time` = '".$eventTime."',
									  	  `entry_date` = '".date('Y-m-d H:i:s')."',
										  `entry_value` = '".$entryValue."'
									  WHERE `entry_id` = '".$entry_id."'");
						$content = 'You have successfully entered the contest. Good LUCK!';
					}
					else
					{
						$content = 'You have already entered this contest.';
					}//end of if($entry_time > date('H:i') )
				}
				else
				{
					@mysql_query("INSERT INTO `contest_entries` (
								`contest_id` ,
								`contest_date` ,
								`contest_time` ,
								`customer_id` ,
								`customer_email`,
								`entry_date` ,
								`entry_value` ,
								`entry_id`
								)
								VALUES (
								'".$contestID."', 
								'".$contestDate."',
								'".$eventTime."', 
								'".trim($cid)."', 
								'".trim($email)."', 
								'".date('Y-m-d H:i:s')."', 
								'".$entryValue."', 
								NULL
								)");
					$content = 'You have successfully entered the contest. Good LUCK!';
				}
			}
			else
			{
				$content = 'This event has expired, please select another event.';
			}
		}//end of if( isset($_POST['contestDate'])  && isset($_POST['contestID']) )
	}//end of if($action == 'addnewentry')
	
	
	if($action == 'seemypicks')
	{
	    if( isset($_POST['contestDate']) && isset($_POST['contestID']) )
		{
			$content = 'No Data Found!';
			$cque = mysql_query("SELECT `entry_value` FROM `contest_entries`
								 WHERE `contest_id` = ".$contestID."
								 AND `contest_date` = '".$contestDate."'
								 AND `customer_id` = '".trim($cid)."'
								 AND `customer_email` = '".trim($email)."'");
			$ccount = @mysql_num_rows($cque);
			if($ccount > 0)
			{
				$cres = mysql_fetch_row($cque);
				$ec_id = $cres[0];
				$ec_ti = $cres[1];
			    $que = mysql_query("SELECT c.`choice`, e.`event_desc`
								    FROM `events_choices` c
								    LEFT JOIN `events` e ON e.`event_id` = c.`event_id`
								    WHERE `ec_id` = ".$ec_id);
			    if(@mysql_num_rows($que) > 0)
			    {
				    $res = mysql_fetch_assoc($que);
				    $content = $res['event_desc']."\n PICK: ".$res['choice'];
			    }
			}//end of if($ccount > 0)
		}//if( isset($_POST['contestDate']) && isset($_POST['contestID']) )
		
	}//end of if($action == 'seemypicks')
	
	echo $content;
}



mysql_close();
?>