<?php include_once('session.php'); ?>
<?php
	if( isset( $_GET['updateCurrentStreakers'] ) && $_GET['updateCurrentStreakers'] == 1 )
	{
		update_streaker_board();//updated the leaderboard for current streakers
		
		mail_streaker_board();//mails the leaderboard for Streaker to jeff@epm3ltd.com
		
		header('Location: contest_streakers.php?Updated=1');
		tep_db_close();
		exit();
	}
	
	
	//get contest streakers
	$streakers = get_streakers();
	
	$textCol = '#000000';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Contest Streakers</title>
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
		<script type="text/javascript" src="js/jquery-latest.js"></script>
		
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
		 <fieldset>
		   <legend style="font-size:12px; font-weight:bold;">CONTEST STREAKERS</legend>
		   <p><strong>Last Updated:</strong> 
		   <?php 
		   $dque = mysql_query("SELECT `last_updated` FROM `current_streakers` ORDER BY `last_updated` DESC LIMIT 1");
		   $dres = mysql_fetch_row($dque);
		   echo date('l, F d, Y H:i:s', strtotime($dres[0])); 
		   ?></p>
		   <?php
		   
		   if( !isset($_GET['Updated']) )
		   {
			 echo '<p> <a href="contest_streakers.php?updateCurrentStreakers=1" style="color:green;font-weight:bold;">UPDATE CURRENT STREAKERS</a> </p>';
		   }
		   
		   if( isset($_GET['Updated']) )
		   {
		   	  echo '<p id="contUpd" style="color:green;"><strong>CURRENT STREAKERS UPDATED:</strong> ' . date('Y-m-d H:i:s') . '</p>';
		   }
		   elseif( count($streakers) == 0)
		   {
		   	  echo '<p> NO DATA FOUND! </p>';
		   }else
		   {
		   ?>
		   <table cellpadding="2" cellspacing="2" style="border: 1px solid #000000;">
		   
		   <tr bgcolor="#808080" style="color:#ffffff;">
		   	 <th width="50" align="center"> # </th>
		     <th width="200" align="center"> Customer ID </th>
			 <th width="50" align="center"> Streak </th>
		   </tr>
		   
		   <?php
		   $skc = 1;//event counter
		   foreach($streakers as $sk => $sv)
		   {
				/*if($sk > 0)
				{
					$pk = $sk -1;
					if($streakers[$pk]['streak'] != $streakers[$pk]['streak'])
					{
					  $skc++;
					}
				}*/
				$bcol = '';
				if( ($skc%2) == 0 )
				{
					$bcol = ' bgcolor="#dcdcdc"';
				}
		   ?>
		   
		   <tr<?php echo $bcol; ?>>
		   	 <td align="center"><?php echo $skc; ?></td>
		     <td align="center" nowrap><?php echo $sv['customer_id']; ?></td>
			 <td align="center"><?php echo $sv['streak']; ?></td>
		   </tr>
		   
		   <?php
		   		$skc++;
		   }//end of foreach($events as $ev)
		   ?>
		   
		   </table>
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
