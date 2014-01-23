<?
require_once('session.php');

if( isset($_POST['servType']) )
{
	extract($_POST);
	$content = '';
	if($action == 'add')
	{
			/*
				   'action=add&servType=newentry' +
				   '&contestDate=' + contestDate +
				   '&eventTime=' + eventTime +
				   '&contestID=' + contestID + 
				   '&cid=' + cid +
				   '&entry=' + eventID + 
				   '&entryValue=' + eventValue;
			*/
		//add new event to the contest
		if($servType == 'newentry')
		{
		    if( isset($_POST['contestDate']) )
			{
				$curDateTime = date('Y-m-d H:i');
				$conDateTime = $contestDate.' '.$eventTime;
				$entryDate = date('Y-m-d H:i:s');
				if($conDateTime > $curDateTime)
				{
					$cque = mysql_query("SELECT `entry_id` FROM `contest_entries`
										 WHERE `contest_id` = ".$contestID."
										 AND `contest_date` = '".$contestDate."'
										 AND `customer_id = '".$cid."'");
					$ccount = @mysql_num_rows($cque);
					if($ccount > 0)
					{
						$cres = mysql_fetch_row($cque);
						$entry_id = $cres[0];
						@mysql_query("UPDATE `contest_entries`
									  SET `contest_time` = '".$eventTime."',
									  	  `entry_date` = '".date('Y-m-d H:i:s')."'
										  `entry_value` = '".$entryValue."'
									  WHERE entry_id = '".$entry_id."'");
					}
					else
					{
						@mysql_query("INSERT INTO `contest_entries` (
									`contest_id` ,
									`contest_date` ,
									`contest_time` ,
									`customer_id` ,
									`entry_date` ,
									`entry_value` ,
									`entry_id`
									)
									VALUES (
									'".$contestID."', 
									'".$contestDate."', 
									'".$eventTime."', 
									'".$cid."', 
									'".date('Y-m-d H:i:s')."', 
									'".$entryValue."', 
									NULL
									)");
					}
					$content = 'You have successfully entered the contest. Good LUCK!';
				}
				else
				{
					$content = 'This event has expired, please select another event.';
				}
			}
		}
		
		//add new event to the contest
		if($servType == 'event')
		{
		    if( isset($_POST['contestDate']) )
			{
				$contestDate = $_POST['contestDate'];
				$contestID = $_POST['contestID'];
				$curDate = date('Y-m-d');
				$cque = mysql_query("SELECT `event_id` FROM `events`
									 WHERE `contest_id` = ".$contestID."
									 AND `event_date` = '".$contestDate."'");
				$ccount = @mysql_num_rows($cque);
				if($contestDate >= $curDate)
				{
					$ins1 = "INSERT INTO `events` 
							(
							`event_id` ,  `contest_id` , `event_date` , `event_desc` , `event_order`
							)
							VALUES 
							(
							NULL , '".$contestID."', '".$contestDate."', '', ''
							)";
					if( @mysql_query($ins1) )
					{
					   $event_id = @mysql_insert_id();
					   $ccount++;
					   $ins2 = "INSERT INTO `events_choices` 
					  			(
								`ec_id` , `event_id` , `choice` , `ec_order`
								)
								VALUES 
								(
								NULL , '".$event_id."', '', ''
								), (
								NULL , '".$event_id."', '', ''
								)";
					   if(mysql_query($ins2))
					   {
					     $choices = get_choices($event_id);
					     $content = '<tr id="eveRow_'.$event_id.'">
						 			 <td width="800" colspan="2">
									   <table width="100%">
									     <tr>
											 <td width="100">
										   		<a name="eventname_'.$event_id.'"></a>Event '.$ccount.':
										     </td>
										     <td width="700">
											 	<input type="text" name="event['.$event_id.']" value="" class="required eventInput" />
											 </td>
										 </tr>';
						 $contest_info = get_contests($contestID);
						 if($contest_info['contest_daily'] == 'Yes')
						 {
						 	$content .= '
										 <tr>
											 <td width="100">
										   		Time:
										     </td>
										     <td width="700">
											 	<input type="text" name="eventtime['.$event_id.']" value="" class="eventtime required" />
											 </td>
										 </tr>';
						}
							$content .= '
									   </table>
									 </td>
									     
									 <td class="tdDel" nowrap>
										<a href="#" class="button_remove" onclick="loadService(\'remove\',\'event\', \''.$event_id.'\');" title="Remove This Event">&nbsp;</a>
									 </td>
								   </tr>
								   
								   <tr id="eceRow_'.$event_id.'">
								     <td>&nbsp;</td>
									 <td colspan="2">
									 
										<table width="100%" style="font-size: 12px;" id="eventTable_'.$event_id.'">
										
										  <tr>
										    <td width="80">Choice 1:</td>
										    <td width="350">
											<input type="text" name="choice['.$choices[0]['ec_id'].']" value="" class="required choiceInput" />
											</td>
											<td nowrap></td>
										  </tr>
										  
										  <tr>
										    <td width="80">Choice 2:</td>
										    <td width="350">
											<input type="text" name="choice['.$choices[1]['ec_id'].']" value="" class="required choiceInput" />
											</td>
											<td nowrap>
											<a href="#eventname_'.$event_id.'" class="button_plus" onclick="loadService(\'add\', \'choice\', \''.$event_id.'\');" title="Add New Choice">&nbsp;</a>
											</td>
										  </tr>
										  
										</table>
										
									</td>
								   </tr>
								   
								   <tr id="bleRow_'.$event_id.'">
								   	<td colspan="3">
									  <table width="100%">
									    <tr><td>&nbsp;</td></tr>
										<tr><td><hr></td></tr>
										<tr><td>&nbsp;</td></tr>
									  </table>
									</td>
								   </tr>
								   ';
					   }//end of if(mysql_query($ins2))
					}// end of if( @mysql_query($ins1) )
			    }//end of if($contestDate > $curDate)
			}//end of if( isset($_POST['contestDate']) )
		}//end of if($servType == 'event')
		
		
		//add new choice to event
		if($servType == 'choice')
		{
			  $event_id = $servValue;
			  $ins = "INSERT INTO `events_choices` 
			  			(
						`ec_id` , `event_id` , `choice` , `ec_order`
						)
						VALUES 
						(
						NULL , '".$event_id."', '', ''
						)";
			  if(mysql_query($ins))
			  {
			  	$ec_id = mysql_insert_id();
				$choices = get_choices($event_id);
				$ch_count = count($choices);
				$content = '<tr id="chRow_'.$ec_id.'"><td width="80">Choice '.$ch_count.':</td><td width="350"><input type="text" class="required choiceInput" value="" name="choice['.$ec_id.']"></td><td nowrap><a href="#eventname_'.$event_id.'" class="button_minus" onclick="loadService(\'remove\', \'choice\', \''.$ec_id.'\');">&nbsp;</a></td></tr>';
			  }
		}
	}
	elseif($action == 'remove')
	{
		
		//remove choice for an event, choice is unique, so use that
		if($servType == 'choice')
		{
			  $ch_id = $servValue;
			  $del = "DELETE FROM `events_choices`
			  		  WHERE `ec_id` = ".$ch_id;
			  if(mysql_query($del))
			  {
			  	$content = 'removechoice';
			  }
		}
		
		//remove event and choices for that event
		if($servType == 'event')
		{
			  $event_id = $servValue;
			  $del = "DELETE FROM `events_choices`
			  		  WHERE `event_id` = ".$event_id;
			  if(mysql_query($del))
			  {
			  	$del2 = "DELETE FROM `events`
			  		  	 WHERE `event_id` = ".$event_id;
				if(mysql_query($del2))
				{
				  $content = 'removeevent';
				}
			  }
		}
		
	}
	
	echo $content;
}



mysql_close();
?>