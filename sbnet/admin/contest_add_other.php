<?php include_once('session.php'); ?>
<?php
	if( isset( $_POST['pval'] ) && $_POST['pval'] == 'process' )
	{
		$contestID 	 = $_POST['contestID'];
		$contestDate = $_POST['contestDate'];
		$event 		 = $_POST['event'];
		$eventTime 	 = $_POST['eventtime'];
		$choice 	 = $_POST['choice'];
		$delaytime   = trim($_POST['delaytime']);
		
		//get contest info, current_contest_date
		$contest_info = get_contests($contestID);
		$current_contest_date = $contest_info[0]['current_contest_date'];
		
		//check if current live contest and make it live, or else unset it
		if( isset( $_POST['current_contest_check']) )
		{
			$delay_timestamp = ($delaytime != '') ? strtotime($delaytime) : '';
			@mysql_query("UPDATE `contests` 
						  SET `current_contest_date` = '".$contestDate."',
						  	  `delay_timestamp` = '".$delay_timestamp."'
						  WHERE `contest_id` =".$contestID." LIMIT 1");
		}
		else
		{
			if($current_contest_date == $contestDate)
			{
				@mysql_query("UPDATE `contests` 
						  	  SET `current_contest_date` = '',
							  	  `delay_timestamp` = ''
						  	  WHERE `contest_id` =".$contestID." LIMIT 1");
			}
		}
		
		foreach($event as $ke => $pe)
		{
			$et = $eventTime;
			@mysql_query("UPDATE `events`
						  SET `event_desc` = '".$pe."',
						  	  `event_time` = '".$et."'
						  WHERE `event_id` = ".$ke);
		}
		foreach($choice as $kc => $pc)
		{
			@mysql_query("UPDATE `events_choices`
						  SET `choice` = '".$pc."'
						  WHERE `ec_id` = ".$kc);
		}
		if($_POST['pred'] > 0)
		{
			$pre_event = 0;
			$pre_que = mysql_query("SELECT `event_id` FROM `events` 
									WHERE `contest_id` = ".$contestID."
									AND `event_date` = '".$contestDate."'
									AND `event_id` < ".$_POST['pred']."
									ORDER BY `event_id` DESC LIMIT 1");
			if( @mysql_num_rows($pre_que) > 0 )
			{
				$pre_res = mysql_fetch_row($pre_que);
				$pre_event = $pre_res[0];
			}
			header('Location: contest_add_other.php?contest_id='.$contestID.'&contestDate='.$contestDate.'#eventname_'.$pre_event);
		}
		elseif($_POST['pred'] == 'newevent')
		{
			header('Location: contest_add_other.php?contest_id='.$contestID.'&contestDate='.$contestDate.'#newevent');
		}
		else
		{
		  header('Location: contest_add_other.php?updated=1&contest_id='.$contestID.'&contestDate='.$contestDate);
		}
		tep_db_close();
		exit();
	}
	
	//set date defaults
	$curDate = date('Y-m-d');
	$contestDate = $_GET['contestDate'];
	$contest_id = $_GET['contest_id'];
	
	
	if( isset($_GET['contestDate'],$_GET['createNew']) && $contest_id > 1 && $_GET['createNew'] == 1)
	{
		$contestDate = $_GET['contestDate'];
		$cque = mysql_query("SELECT `event_id` FROM `events`
							 WHERE `contest_id` = ".$contest_id."
							 AND `event_date` = '".$contestDate."'");
		if(@mysql_num_rows($cque) == 0 && $contestDate >= $curDate)
		{
			$ins1 = "INSERT INTO `events` 
					(
					`event_id` ,  `contest_id` , `event_date` , `event_desc` , `event_order`
					)
					VALUES 
					(
					NULL , '".$contest_id."', '".$contestDate."', '', ''
					)";
			if( @mysql_query($ins1) )
			{
			  $event_id = @mysql_insert_id();
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
			  @mysql_query($ins2);
			}
	  }//end of if(@mysql_query($cque) > 0)
	}//end of if( isset($_GET['contestDate']) )
	
	$contest_info = get_contests($contest_id);
	$current_contest_date = $contest_info[0]['current_contest_date'];
	$delay_timestamp = $contest_info[0]['delay_timestamp'];
	$delayTime = ''; $checkVal = '';
	if($contestDate == $current_contest_date)
	{
		if($delay_timestamp > 0)$delayTime = date('l, F d, Y h:i a',$delay_timestamp);
		$checkVal = ' checked="checked"';
	}
	
	//get contest events
	$events = get_events($contest_id, $contestDate);
	
	//get entry count
	$entry_count = get_entry_count($contest_id, $contestDate);
	
    //set contest date time and current date time
    $contestDateTime = strtotime($contestDate.' '.$events[0]['event_time']);
    $curDateTime = strtotime( date('Y-m-d H:i') );
	
	$textCol = '#777';
	//set overrides
	if( $contestDateTime >= $curDateTime && count($events) > 0)
	{
		$textCol = '#000';
	}
	
	//calculate max days
	$difTime = ceil(($contestDateTime-$curDateTime)/86400);
	$maxDays = ($difTime > 0) ? $difTime : 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Add Contest</title>
		<link rel="stylesheet" media="all" type="text/css" href="css/ui-lightness/jquery-ui-1.8.16.custom.css" />
		<style type="text/css"> 
			body,img,p,h1,h2,h3,h4,h5,h6,form,table,td,ul,li,dl,dt,dd,pre,blockquote,fieldset,label{
				margin:0;
				padding:0;
				border:0;
			}
			h1,h2{ margin: 10px 0; }
			p{ margin: 10px 0; }
			
			pre{ padding: 20px; background-color: #ffffcc; border: solid 1px #fff; }
			.wrapper{ background-color: #ffffff; width: 800px; border: solid 1px #eeeeee; padding: 20px; margin: 0 auto; }
			.example-container{ background-color: #f4f4f4; border-bottom: solid 2px #777777; margin: 0 0 40px 0; padding: 20px; }
			.example-container p{ font-weight: bold; }
			.example-container > dl dt{ font-weight: bold; height: 20px; }
			.example-container > dl dd{ margin: -20px 0 10px 100px; border-bottom: solid 1px #fff; }
			.example-container input{ width: 150px; }
			.clear{ clear: both; }
			#ui-datepicker-div, .ui-datepicker{ font-size: 90%; }
			
			/* css for timepicker */
			.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
			.ui-timepicker-div dl { text-align: left; }
			.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
			.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
			.ui-timepicker-div td { font-size: 90%; }
			.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
			
		</style> 
		
		<link rel="stylesheet" type="text/css" href="css/jform.css" media="all">
		<script type="text/javascript" src="js/jquery-latest.js"></script>
		<script type="text/javascript" src="js/jquery.validate.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
		<script type="text/javascript" src="js/jquery-ui-sliderAccess.js"></script>
		
<style type="text/css">
* { font-family: Verdana; font-size: 96%; }
label { width: 10em; float: left; }
label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
p { clear: both; }
.submit { margin-left: 12em; }
em { font-weight: bold; padding-right: 1em; vertical-align: top; }
.eventTable td {
    font-size: 11px;
	font-weight: bold;
}
.eventInput {
	width: 700px;
	height: 24px;
	border: 1px solid #000000;
	background-color: #EAF3FB;
	padding: 2px 0 2px 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
	line-height: 23px;
}

.eventtime {
	width: 50px;
	height: 24px;
	border: 1px solid #000000;
	background-color: #EAF3FB;
	padding: 2px 0 2px 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
	line-height: 23px;
}

.delaytime {
	width: 300px;
	height: 24px;
	border: 1px solid #000000;
	background-color: #EAF3FB;
	padding: 2px 0 2px 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
	line-height: 23px;
}

.choiceInput {
	width: 338px;
	height: 24px;
	border: 1px solid #000000;
	background-color: #FFFFDD;
	padding: 2px 0 2px 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
	line-height: 23px;
}

.tdDel {
	vertical-align:top;
	padding-top:10px;
}

textarea {
	width: 300px;
	height: 150px;
	font-family: Arial;
	border: 1px solid #000000;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	padding: 2px 0 2px 5px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
}

.button_plus {
	background: url("img/button_plus.png") no-repeat scroll 0 0 #FFFFFF;
    display: inline;
    padding: 5px 10px 5px 10px;
    text-decoration: none;
	width: 25px;
	height: 25px;
    background-image: 25px 25px;
}

.button_minus {
	background: url("img/button_minus.png") no-repeat scroll 0 0 #FFFFFF;
    display: inline;
    padding: 5px 10px 5px 10px;
    text-decoration: none;
	width: 25px;
	height: 25px;
    background-image: 25px 25px;
}

.button_remove {
	background: url("img/button_close.png") no-repeat scroll 0 0 #FFFFFF;
    display: inline;
    padding: 5px 10px 5px 10px;
    text-decoration: none;
	width: 25px;
	height: 25px;
    background-image: 25px 25px;
}

</style>

  <script>
  $(document).ready(function() {
   // put all your jQuery goodness in here.
   $(".eventtime").timepicker({
   		addSliderAccess: true,
		sliderAccessArgs: { touchonly: false }
   });
   $(".delaytime").datetimepicker({
   		dateFormat: "DD, MM d, yy",
		ampm: true,
		minDate: 0,
        maxDate: <?php echo $maxDays; ?>,
		addSliderAccess: true,
		sliderAccessArgs: { touchonly: false }
   });
 });
  
  function validate_form()
  {
    $("#eventForm").validate();
  }
  
  function submit_form()
  {
	$("#eventForm").validate();
	$('#eventForm').submit();
	return false;
  }
  
  function show_confirm()
  {
	var r = confirm("Are you sure you want to delete this event and its choices.");
	return r;
  }
  
  function loadService(servAction, servType, servValue) {
  
    //confirm when deleting event
	/*
	if(servAction == 'remove' && servType == 'event')
	{
		show_confirm();
	}
	*/
	
	//get hidden fields values
    var contestID = $("#contestID").val();
	var contestDate = $("#contestDate").val();
	
	var servData = 'action=' + servAction +
				   '&servType=' + servType + 
				   '&servValue=' + servValue + 
				   '&contestDate=' + contestDate +
				   '&contestID=' + contestID;
	//alert(servData);
	//ajax the servData
	$.ajax({
	       url: "ajax_loader.php",
	       type: "POST",
	       data: servData,
	       cache: false,
	       success: function (html) {
		     if(servAction == 'add')
		     {
			   if(servType=='event')
			   {
			     $('#addNewEvent').before(html);
				 $("#pred").val('newevent');
				 $('#eventForm').submit();
				 return false;
			   }
		   	   if(servType=='choice')$('#eventTable_'+servValue).append(html);
	         }
			 if(servAction == 'remove')
			 {
			 	if(html == 'removechoice')
				{
					//alert("chRow_"+servValue);
					$("#chRow_"+servValue).remove();
				}
				if(html == 'removeevent')
				{
					$("#eveRow_"+servValue).remove();
					$("#eceRow_"+servValue).remove();
					$("#bleRow_"+servValue).remove();
					$("#pred").val(servValue);
					$('#eventForm').submit();
					return false;
				}
			 }
		   }
	});
	
  }
  </script>
</head>

<body>

	<div align="center">
		<br>
		<div class="box" style="width: 950px !important; margin: 10px auto 10px auto;">
		 <fieldset>
		   <legend style="font-size:12px; font-weight:bold;"><?php echo $contest_info[0]['contest_name']; ?></legend>
		   <?php
		   $now_date = date('l, F d, Y H:i:s'). ' EST';
		   $table_header = '
		   <table width="100%">
 			  <tr>
			    <td width="75%" valign="top">
				<p style="font-size:13px;">
				<strong>End Date:</strong> '.date('l, F d, Y', strtotime($_GET['contestDate'])).'
				</p>';
		   
		   if( isset($_GET['updated']) )
		   {
		   	  $table_header .= 
			  		'<p id="contUpd" style="color:green;"><strong>Contest Updated:</strong> ' . $now_date . '</p>'.
			  		'<script>
						setTimeout( hideNow, 3000);
						function hideNow()
						{
							$("#contUpd").hide("slow");
						}
			  	 	</script>';
		   }
		   
		   //default texts
		   $sub_dis = '';
		   $text_read = '';
		   $view_entries_text = '';
		   if($entry_count > 0)
		   {
		   	$view_entries_text = '<p> <a href="contest_entries.php?contest_id='.$contest_id.'&date='.$contestDate.'" style="color:green;font-weight:bold;">VIEW ENTRIES</a> </p>';
		   }
		   
		   if( $contestDateTime < $curDateTime && count($events) > 0)
		   {
		   	 $table_header .= 
			 	  '<p style="color:red;"> This contest has expired, so you can\'t modify it.</p>'.
				  '</td>
				   <td valign="top">'.
				     '<p> <a href="contest_grade.php?contest_id='.$contest_id.'&date='.$contestDate.'" style="color:green;font-weight:bold;">GRADE THIS CONTEST</a> </p>'.
					 $view_entries_text;
			 $sub_dis = ' DISABLED="DISABLED"';
			 $text_read = ' READONLY="READONLY"';
		   }else
		   {
		   	 $table_header .= 
			 	  '</td>
				   <td valign="top">'.$view_entries_text;
		   }
		   
		   if( count($events) == 0)
		   {
			  if($curDate < $_GET['contestDate'])
			  {
			  	$table_header .= 
					'<p> <a href="contest_add_other.php?contest_id='.$_GET['contest_id'].'&contestDate='.$_GET['contestDate'].'&createNew=1" style="font-weight:bold;color:green;">Create New</a> contest for this End Date.</p><br>';
			  }
		   	  $table_header .= '<p> NO DATA FOUND! </p>';
			  $table_header .= '</td></tr></table>';
			  echo $table_header;
		   }else
		   {
		   	  $table_header .= '</td></tr></table>';
			  echo $table_header;
		   ?>
		   <form name="eventForm" id="eventForm" method="post" action="contest_add_other.php">
			<input type="hidden" name="pval" id="pval" value="process" />
			<input type="hidden" name="pred" id="pred" value="" />
			<input type="hidden" id="contestID" name="contestID" value="<?php echo $contest_id; ?>" />
			<input type="hidden" id="contestDate" name="contestDate" value="<?php echo $contestDate; ?>" />
			
		   <p style="font-size:13px;"><strong>End Time:</strong> <input type="text" name="eventtime" value="<?php echo $events[0]['event_time']; ?>" class="eventtime" <?php echo $sub_dis; ?>/></p>
		   <p><hr></p>
			<a name="eventname_0"></a>
		   <table class="eventTable">
		   
		   <?php
		   $evc = 1;//event counter
		   foreach($events as $ev)
		   {
		   		$ev_id = $ev['event_id'];
				$choices = get_choices($ev_id);
		   ?>
		   
		   <tr id="eveRow_<?php echo $ev_id; ?>">
		     <td width="800" colspan="2">
			   <table width="100%">
			   <tr>
			   	 <td width="100">
		   		    <a name="eventname_<?php echo $ev_id; ?>"></a>Event <?php echo $evc; ?>:
			     </td>
			     <td width="700">
				 	<input type="text" name="event[<?php echo $ev_id; ?>]" value="<?php echo $ev['event_desc']; ?>" class="required eventInput"<?php echo $text_read;?> />
				 </td>
			   </tr>
			   </table>
			 </td>
			 <td class="tdDel" nowrap>
			 <?php 
			 if($evc > 1 && $contestDate >= $curDate)
			 {
			   echo  '<a href="#" class="button_remove" onclick="loadService(\'remove\',\'event\', \''.$ev_id.'\');" title="Remove This Event">&nbsp;</a>';
			 }else echo '&nbsp;';
			 ?>
			 </td>
		   </tr>
		   
			   <?php
			   $ch_count = count($choices);
			   if($ch_count  > 0 )
			   {
			   ?>
			   <tr id="eceRow_<?php echo $ev_id; ?>">
			     <td>&nbsp;</td>
				 <td colspan="2">
					<table width="100%" style="font-size: 12px;" id="eventTable_<?php echo $ev_id; ?>">
					<?php
					$chc = 1;//choices counter
					foreach($choices as $ch)
					{
						$ch_id = $ch['ec_id']
					?>
					  <tr id="chRow_<?php echo $ch_id; ?>">
					    <td width="80">Choice <?php echo $chc; ?>:</td>
					    <td width="350">
						<input type="text" name="choice[<?php echo $ch_id; ?>]" value="<?php echo $ch['choice']; ?>" class="required choiceInput"<?php echo $text_read;?> />
						</td>
						<td nowrap><?php
						if($chc == 2 && $contestDate >= $curDate)
						{
							echo '<a href="#eventname_'.$ev_id.'" class="button_plus" onclick="loadService(\'add\', \'choice\', \''.$ev_id.'\');" title="Add New Choice">&nbsp;</a>';
						}
						if($chc > 2 && $contestDate >= $curDate )
						{
							echo '<a href="#eventname_'.$ev_id.'" class="button_minus"  onclick="loadService(\'remove\', \'choice\', \''.$ch_id.'\');" title="Remove This Choice">&nbsp;</a>';
						}
						?>
						</td>
					  </tr>
					<?php
						$chc++;//increment choices counter
					}//end of foreach($choices as $ch)
					?>
					</table>
				</td>
			   </tr>
			   <?php
			   }//end of if( count($choices) > 0 )
			   ?>
		   
		   <tr id="bleRow_<?php echo $ev_id; ?>">
		   	<td colspan="3">
			  <table width="100%">
			    <tr><td>&nbsp;</td></tr>
				<tr><td><hr></td></tr>
				<tr><td>&nbsp;</td></tr>
			  </table>
			</td>
		   </tr>
		   
		   <?php
		   	$evc++;//increment event counter
		   }//end of foreach($events as $ev)
		   
		   
		   if( $contestDate >= $curDate )
		   {
		   ?>
		   <tr id="addNewEvent">
		   	<td>&nbsp;</td>
			<td align="center"><a name="newevent">&nbsp;</a>&nbsp;<a href="#newevent" style="font-size:12px; font-weight:bold; color:green;" onclick="loadService('add','event', '<?php echo $contestDate; ?>');">Add Another Event</a></td>
			<td>&nbsp;</td>
		   </tr>
		   <?php
		   }
		   ?>
		   
		   <tr>
		   	<td>&nbsp;</td>
			<td><br>
			<input type="checkbox" name="current_contest_check" id="current_contest_check" value="1" style="font-weight:bold;" <?php echo $checkVal.$sub_dis; ?>>
			&nbsp;Make this contest live.&nbsp;
			<input type="text" name="delaytime" value="<?php echo $delayTime; ?>" class="delaytime" readonly="readonly" <?php echo $sub_dis; ?>/>
			</td>
			<td>&nbsp;</td>
		   </tr>
		   
		   <tr>
		   	<td colspan="3">&nbsp;</td>
		   </tr>
		   
		   <tr>
		   	<td>&nbsp;</td>
			<td><input type="button" name="submit_button" id="submit_button" value="Save" style="font-weight:bold;" onclick="submit_form();" <?php echo $sub_dis; ?>></td>
			<td>&nbsp;</td>
		   </tr>
		   
		   </table>
		   </form>
		   <?php
		   }
		   ?>
		 </fieldset>
		 
		 <br><br>
		</div>
	</div>
</body>

</html>

<?php
mysql_close();
?>
