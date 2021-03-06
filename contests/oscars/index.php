<?php
//set time zone to eastern time zone
date_default_timezone_set('America/New_York');
include_once('../admin/db_func.php');
tep_db_connect();
$contest_id = 5;//id for oscars contest
$date = '2012-02-26';//end date for current contest
$events = get_events($contest_id, $date);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Free Contests - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="../i/sbtfav2.png" />
	<link rel="stylesheet" type="text/css" href="../css/phoenix.css" />
	<link rel="stylesheet" type="text/css" href="../css/fonts.css" />
	<link rel="stylesheet" type="text/css" href="http://fast.fonts.com/t/1.css?apiType=css&projectid=ad1c2391-5578-473c-95e8-486971f0b8dd" />
	<script type="text/javascript" src="http://www.sportsbetting.com/javascripts/core/head.js"></script>
	<script type="text/javascript">
		  (function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();

		  function popUp(URL) {
			window.open(URL, 'Terms', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=960,height=600,left = 300,top = 100');
		}
	</script>
	<style type="text/css"> 
			pre{ padding: 20px; background-color: #ffffcc; border: solid 1px #fff; }
			.wrapper { color:#000; padding:10px 20px!important; font-size:13px; text-align:left!important; }
			.wrapper h1 { padding:0 0 10px; }
			.wrapper p { margin:5px 0 10px; }
			.event-container { background:#e5e5e5; border:1px solid #b2b2b2; margin:0 0 10px; }
			.event-container .qNum {float:left; background:url('/i/question_bg.jpg') no-repeat #434343; width:47px; text-align:center; height:39px; padding:4px 0 0 3px;}
			.event-container .qNum p { color:#000; font-size:1.2em; }
			.event-container .question { width:628px; height:43px; float:left; background:url('/i/question_bg.jpg') right no-repeat; }
			.event-container p { font-weight: bold; color:#fff;padding:10px; margin:0; }
			.event-container select { border:1px solid #b2b2b2; }
			.event-container option { padding:0 5px; }
			.event-container table { margin:15px; }
			.event-container table input#submit { cursor:pointer; font-weight:bold; border:none; color:#fff; background:url('/i/submit_bg.jpg') no-repeat; display:block; width:165px; height:30px;}
			.event-container table input#mypicks, .event-container table input#terms { cursor:pointer; font-weight:bold; border:none; color:#fff; background:url('/i/button_bg.jpg') no-repeat; display:block; width:165px; height:30px; }		
			.event-container > dl dt{ font-weight: bold; height: 20px; }
			.event-container > dl dd{ margin: -20px 0 10px 100px; border-bottom: solid 1px #fff; }
			.event-container .select select { background: transparent; width: 270px; height: 18px; }
			.event-container .select { margin:55px 0 15px 15px; width: 240px; height: 18px; overflow: hidden; background: url('/i/select.jpg') no-repeat right #fff; border: 1px solid #ccc; }
			.event-container .noselect { text-align: center; margin:5px 0px 5px 0px; font-size:13px; font-weight:bold; color: #000000;}
			.clear{ clear: both; }
	</style>
</head>

<body class="en home">

<?php include('../inc/header.php'); ?>

<section id="mainContainer">
	<br />
	<div class="innerWidth">
		<section id="hpMain" class="left">
			<section id="featuredBets" class="tabs">
				<header>
					<h2>Oscar Pool 2012</h2>
				</header>
				<section class="wrapper" id="nfl-game-lines" style="">
					<img src="oscars-large.jpg" alt="Oscar Pool 2012 Contest" style="margin:-10px 0 0 -20px;" />
					<br /><br />
					<center><a href="https://www.sportsbetting.com/en/signup/"><img src="/i/member.jpg" alt="Join Now!" /></a></center>
					<br />
					<?php
					if( count($events) > 0 )
					{
						//current date time
						$now_date_time = strtotime( date('Y-m-d H:i') );
						
						//set expiry date
						$expiry_date_time = strtotime($date.' '.$events[0]['event_time']);
						$expiry_date = date('l g:i A T F jS, Y', $expiry_date_time);
					?>
						<form id="contestForm" name="contestForm" action="" method="post">
							<input type="hidden" id="contestid" name="contestid" value="<?php echo $contest_id; ?>" />
							<input type="hidden" id="contestdate" name="contestdate" value="<?php echo $date; ?>" />
						<?php
						$count = 1;
						foreach($events as $eve)
						{
							$choices = get_choices($eve['event_id']);
						?>
								<div class="event-container">
									<input type="hidden" name="eventtime[<?php echo $eve['event_id']; ?>]" id="eventtime_<?php echo $eve['event_id']; ?>" value="<?php echo $eve['event_time']; ?>" />
									<div class="qNum">
										<p><?php echo $count;?></p>
									</div>
									<div class="question">
										<p><?php echo $eve['event_desc']; ?></p>
									</div>
									<?php
									if($expiry_date_time > $now_date_time)
									{
									?>
									<div class="select">
									  <select name="eventchoice_<?php echo $eve['event_id']; ?>" id="eventchoice_<?php echo $eve['event_id']; ?>" class="contestSelection">
									  <?php
									  foreach($choices as $ch)
									  {
										echo '<option value="'.$ch['ec_id'].'">'.$ch['choice'].'</option>';
									  }
									  ?>
									  </select>
									</div>
									<?php
									}
									else
									{
										echo '<div style="margin: 50px 0px 0px 0px;">';
										foreach($choices as $ch)
									    {
										  echo '<div class="noselect">'.$ch['choice'].'</div>';
									    }
										echo '</div>';
									}
									?>
								</div>
						<?php
							$count++;
						}
						?>
						
							<div class="event-container">
								<table cellpadding="2" cellspacing="2">
							<?php
							if($expiry_date_time > $now_date_time)
							{
							?>
								<tr>
								  <td>Your User Name:&nbsp;</td>
								  <td><input type="text" id="cid" name="cid" /></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
								  <td>Your Email:&nbsp;</td>
								  <td><input type="text" id="email" name="email" /></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
								  <td>&nbsp;</td>
								  <td>
								  <input type="button" id="submit" name="submit" value="Enter the Contest" onclick="processPromo();"/>
								  </td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
								  <td>&nbsp;</td>
								  <td>
								  <input type="button" id="mypicks" name="mypicks" value="See My Picks" onclick="processPicks();"/>
								  </td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
							<?php
							}else
							{
							?>
								<tr>
								<td colspan="2">&nbsp;
								<p style="color:#000000;">
								The Contest is now expired. Please check back for future contests. Good Luck!
								</p>
								</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
								  <td>Your User Name:&nbsp;</td>
								  <td><input type="text" id="cid" name="cid" /></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
								  <td>Your Email:&nbsp;</td>
								  <td><input type="text" id="email" name="email" /></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
								  <td>&nbsp;</td>
								  <td>
								  <input type="button" id="mypicks" name="mypicks" value="See My Picks" onclick="processPicks();"/>
								  </td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
							<?php
							}
							?>
								<tr>
								  <td>&nbsp;</td>
								  <td>
									<input type="button" id="terms" name="terms" value="Rules and Regulations" onclick="popUp('terms.html')"/>
								  </td>
								</tr>

								</table>
							</div>
							
						 </form>
					<?php
					}
					else
					{
					?>
							<div class="event-container">
							  <table cellpadding="2" cellspacing="2" width="95%">
								<tr><td>&nbsp;</td></tr>
								<tr>
								  <td align="center">
									<input type="button" id="terms" name="terms" value="Rules and Regulations" onclick="popUp('terms.html')"/>
								  </td>
								</tr>
								<tr><td>&nbsp;</td></tr>
							  </table>
							</div>
					<?php
				    }
					?>
					<div class="clear"></div>
				</section>
			</section>
		</section>

		<aside id="hpFeeds" class="left">
			<?php include('../inc/twitter.php'); ?>
			<?php include('../inc/facebook.php'); ?>	
		</aside>

		<div class="clear"></div>

    </div>
</section>

<?php include('../inc/footer.php'); ?>

</body>
<script type="text/javascript" src="../admin/js/jquery-latest.js"></script>
<script type="text/javascript" src="../js/processPromo2.js"></script>
</html>
<?php tep_db_close(); ?>