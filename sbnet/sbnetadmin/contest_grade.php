<?php 
	include_once('session.php');

	$contest_id = isset($_REQUEST['contest_id'])? (int)$_REQUEST['contest_id'] : 0;

	$contests = get_contests($contest_id);

	if($contest_id>0){
		$contest = $contests[0];

		$templates = get_templates();

		if(class_exists($contest['template'])){

			$contestObject = new $contest['template']($contest);

		} else {
			exit();

		}

	}


	if( isset( $_POST['pval'] ) && $_POST['pval'] == 'process' )
	{
		//echo '<pre>'; print_r($_POST); echo '</pre>';
		extract($_POST);
		if(count($event) > 0)
		{
			@mysql_query("UPDATE `contest_entries`
						  SET `points` = 0
						  WHERE `contest_id` = ".$contestID." 
						  AND `contest_date` = '".$contestDate."'");
			if($contestID == 1)
			{
			  foreach($event as $ek => $ev)
			  {
				@mysql_query("UPDATE `events`
							  SET `event_result` = '".$ev."'
							  WHERE `event_id` = '".$ek."'");
				@mysql_query("UPDATE `contest_entries`
						  	  SET `points` = 1
						  	  WHERE `contest_id` = ".$contestID." 
						  	  AND `contest_date` = '".$contestDate."'
							  AND `entry_value` = ".$ev);
			  }
			}
			else
			{
			  foreach($event as $ek => $ev)
			  {
				@mysql_query("UPDATE `events`
							  SET `event_result` = '".$ev."'
							  WHERE `event_id` = '".$ek."'");
			  }
			  $entry_values = implode(',',$event);
			  @mysql_query("UPDATE `contest_entries`
						  	  SET `points` = 1
						  	  WHERE `contest_id` = ".$contestID." 
						  	  AND `contest_date` = '".$contestDate."'
							  AND `entry_value` = '".$entry_values."'");
			}//end of else if($contestID == 1)
			
			$cinfo_arr = get_contests($contestID);
			$cinfo = $cinfo_arr[0];
			$einfo = get_entries($contestID, $contestDate);
			$message = '<br />Statistics for the <b>'.ucwords($cinfo['contest_name']).'</b> contest ending at '. $contestDate.
					   '<br />'.
					   'Total Entries: <b>' . $einfo['total_count'] . '</b><br />';
			if($contestID != 3)$message .= 'Total Winners: <b>' . count($einfo['entries']) . '</b><br />';
			if( count($einfo['entries']) > 0 )
			{
				if($contestID == 3)
				{
				  $message .= "<br>" .'Users with (20/20) correct answers ('.count($einfo['entries']).'):' . "<br>";
				}else
				{
				  $message .= "<br>" .'Users with all wright answers:' . "<br>";
				}
				
				$message .= '<table border="1" cellpadding="2" cellspacing="2" width="600">
							 <tr>
							   <th width="40%" nowrap>User ID</th>
							   <th width="40%" nowrap>User Email</th>
							   <th nowrap>Web Site</th>
							 </tr>
							 ';
				foreach($einfo['entries'] as $en)
				{
					$message .= '
							 <tr>
							   <td nowrap>'.$en['customer_id'].'</td>
							   <td nowrap>'.$en['customer_email'].'</td>
							   <td nowrap>'.$en['site'].'</td>
							 </tr>';
				}
				$message .= '</table>';
			}
			
			//set up emails for mail
			$cemail  = 'joe@epm3ltd.com';
			$temail  = array('jeff@epm3ltd.com','matt@epm3ltd.com');
			if($contestID == 2 || $contestID == 4)
			{
				$temail[] = 'adam@epm3ltd.com';
				$temail[] = 'antony@epm3ltd.com';
			}
			$temails = implode(',',$temail);
			
			//if super bowl contest, check for additional winners
			if($contestID == 3)
			{
				$start_num = 8;
				$message .= check_additional_winners($contestID, $contestDate, $start_num);
			}
			
			//if streaker contest, run updates
			if($contestID == 1)
			{
				update_streaker_board();//updated the leaderboard for current streakers
				$message .= mail_streaker_board('1');//get mail message for leaderboard
			}
			mail($temails, "Statistics for the ".ucwords($cinfo['contest_name'])." contest ending at ". $contestDate, $message, 'Content-Type: text/html; charset="iso-8859-15"' . "\n" . 'Content-Transfer-Encoding: 8bit' . "\n" . 'From: '.$cemail);
			mail('reports@epm3ltd.com', "Statistics for the ".ucwords($cinfo['contest_name'])." contest ending at ". $contestDate, $message, 'Content-Type: text/html; charset="iso-8859-15"' . "\n" . 'Content-Transfer-Encoding: 8bit' . "\n" . 'From: '.$cemail);
			//echo $message;
			
		}
		
		header('Location: contest_grade.php?Graded=1&date='.$contestDate);
		tep_db_close();
		exit();
	}
	
	//set date defaults
	$curDate = date('Y-m-d');
	$contestDate = $_GET['date'];
	$contest_id = $_GET['contest_id'];
	
	//contest info
	$contest_info = get_contests($contest_id);
	$contest_name = $contest_info[0]['contest_name'];
	
	//get contest data
	$events = $contestObject->getEvents();
	
	$textCol = '#000000';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Grade Contest</title>
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
			#ui-datepicker-div, .ui-datepicker{ font-size: 80%; }
			
			/* css for timepicker */
			.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
			.ui-timepicker-div dl { text-align: left; }
			.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
			.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
			.ui-timepicker-div td { font-size: 90%; }
			.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
			
		</style> 
		
		<link rel="stylesheet" type="text/css" href="css/jform.css" media="all">
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		
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

</head>

<body>

	<div align="center">
		<br>
		<div class="box" style="margin: 10px auto 10px auto;">

		 <a href="home.php?contest_id=<?php echo $contest_id; ?>">&larr;Back</a>
		 <fieldset>
		   <legend style="font-size:12px; font-weight:bold;">GRADE CONTEST: <?php echo $contest_name; ?></legend>
		   <p><strong>Date:</strong> <?php echo date('l, F d, Y', strtotime($contestDate)); ?></p>
		   <?php
		   if( isset($_GET['Graded']) )
		   {
		   	  echo '<p id="contUpd" style="color:green;"><strong>Contest Graded:</strong> ' . date('Y-m-d H:i:s') . '</p>';
		   }elseif( count($events) == 0)
		   {
		   	  echo '<p> NO DATA FOUND! </p>';
		   }else
		   {
		   ?>
		   <form name="gradeForm" id="gradeForm" method="post" action="contest_grade.php">
			<input type="hidden" name="pval" id="pval" value="process" />
			<input type="hidden" id="contestID" name="contestID" value="<?php echo $contest_id; ?>" />
			<input type="hidden" id="contestDate" name="contestDate" value="<?php echo $contestDate; ?>" />
		   <table class="eventTable">
		   
		   <?php
		   $evc = 1;//event counter
		   foreach($events as $ev)
		   {
		   		$ev_id = $ev['event_id'];
				$choices = $contestObject->getChoices($ev_id);
		   ?>
		   
		   <tr>
		     <td width="800">
			   <table width="100%">
			   <tr>
			   	 <td width="100%">
		   		   <?php echo $evc; ?>) <?php echo $ev['event_desc']; ?>
			     </td>
			   </tr>
			   <tr>
			     <td width="100%">
				 <?php
			     if(count($choices)  > 0 )
			     {
				 	echo '<select name="event['.$ev_id.']">'."\n";
					$chc = 1;//choices counter
					foreach($choices as $ch)
					{
						$ch_id = $ch['ec_id'];
						echo '<option value="'.$ch['ec_id'].'">'.$ch['choice'].'</option>'."\n";
					}
					//echo '<option value="-1">PUSH</option>';
					echo '</select>'."\n";
				 }
				 ?>
				 </td>
			   </tr>
			   </table>
			 </td>
		   </tr>
		   
		   <tr><td>&nbsp;</td></tr>
		   
		   <?php
		   		$evc++;
		   }//end of foreach($events as $ev)
		   ?>
		   
		   <tr>
		     <td>
		     <input type="submit" name="submit_button" id="submit_button" value="Submit" style="font-weight:bold;"></td>
			 </td>
		   </tr>
		   
		   </table>
		   </form>
		   <?php
		   }
		   ?>
		 </fieldset>
		 
		 <br><br>
		 <a href="home.php?contest_id=<?php echo $contest_id; ?>">&larr;Back</a>
		</div>
	</div>
</body>

</html>

<?php
mysql_close();
?>
