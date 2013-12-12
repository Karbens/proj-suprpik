<?php
//set time zone to eastern time zone
date_default_timezone_set('America/New_York');
include_once('../admin/db_func.php');
tep_db_connect();
$contest_id = 3;//id for superbowl contest
$date = '2012-02-05';//date of the superbowl
$events = get_events($contest_id, $date);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Free Contests - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="http://n1.sbtcdn.com/media/sbtfav2.png" />
	<link rel="stylesheet" type="text/css" href="/css/phoenix.css" />
	<link rel="stylesheet" type="text/css" href="/css/fonts.css" />
	<link rel="stylesheet" type="text/css" href="http://fast.fonts.com/t/1.css?apiType=css&projectid=ad1c2391-5578-473c-95e8-486971f0b8dd" />
	<script type="text/javascript" src="http://www.sportsbetting.com/javascripts/core/head.js"></script>
	<script type="text/javascript">
		  (function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		  function popUp(URL) {
			window.open(URL, 'Terms and Conditions', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=960,height=600,left = 300,top = 100');
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
			.event-container .question { width:628px; height:43px; float:left; background:url('/i/question_bg.jpg') right no-repeat #f00; }
			.event-container p { font-weight: bold; color:#fff;padding:10px; margin:0; }
			.event-container select { border:1px solid #b2b2b2; }
			.event-container option { padding:0 5px; }
			.event-container table { margin:15px; }
			.event-container table input#submit { font-weight:bold; border:none; color:#fff; background:url('/i/submit_bg.jpg') no-repeat; display:block; width:165px; height:30px;}
			.event-container table input#mypicks, .event-container table input#terms { font-weight:bold; border:none; color:#fff; background:url('/i/button_bg.jpg') no-repeat; display:block; width:165px; height:30px; }		
			.event-container > dl dt{ font-weight: bold; height: 20px; }
			.event-container > dl dd{ margin: -20px 0 10px 100px; border-bottom: solid 1px #fff; }
			.event-container .select select { background: transparent; width: 180px; height: 18px; }
			.event-container .select { margin:55px 0 15px 15px; width: 150px; height: 18px; overflow: hidden; background: url('/i/select.jpg') no-repeat right #fff; border: 1px solid #ccc; }
			.clear{ clear: both; }

			#blogFeed { }
			#blogFeed h2 { padding:7px 0; }
			#blogFeed table { width:100%; text-align:center; }
			#blogFeed th { text-align:center; background:#666; line-height:14px; color:#fff; padding:8px 5px; font-weight:bold; }
			#blogFeed td { line-height:14px; margin:0; padding:8px 5px; }
			#blogFeed tr.odd { background:#dcdcdc; }
			#blogFeed article { padding:0; }
			#blogFeed a { text-transform:lowercase!important; color:#BD580A!important; text-transform:capitalize!important;}
			#blogFeed a:hover { text-decoration:underline; }
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
					<h2>Superbowl $100K Contest</h2>
				</header>
				<section class="wrapper" id="nfl-game-lines" style="">
					<img src="big-banner.jpg" alt="Superbowl Contest" style="margin:-10px 0 0 -20px;" />
					<br /><br />
					
					<?php
					if( count($events) > 0 )
					{
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
								</div>
						<?php
							$count++;
						}
						?>
							
							
							<div class="event-container">
								<table cellpadding="2" cellspacing="2">
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
					?>				
					
					<div class="clear"></div>
				</section>
			</section>
		</section>

		<aside id="hpFeeds" class="left">
			<section id="contestFeed">
				<section id="blogFeed">
					<header>
						<h2>Superbowl $100K Payout</h2>
					</header>
					<article>
						<table>
							<tr>
								<th>Questions</th><th>Prize</th>
							</tr>
							<tr>
								<td>20 of 20</td><td><b>$100, 000</b></td>
							</tr>
							<tr class="odd">
								<td>19 of 20</td><td><b>$10,000 account</b></td>
							</tr>
							<tr>
								<td>18 of 20</td><td><b>$5,000 account</b></td>
							</tr>
							<tr class="odd">
								<td>17 of 20</td><td> <b>$2,500 account</b></td>
							</tr>
							<tr>
								<td>16 of 20</td><td><b>$1,000 account</b></td>
							</tr>
							<tr class="odd">
								<td>15 of 20</td><td><b>$500 account</b></td>
							</tr>
							<tr>
								<td>14 of 20</td><td> <b>$250 account</b></td>
							</tr>
							<tr class="odd">
								<td>11, 12, or 13 of 20</td><td><b>$75 account</b></td>
							</tr>
							<tr>
								<td>9 or 10 of 20</td><td><b>$50 account</b></td>
							</tr>
							<tr class="odd">
								<td>8 of 20</td><td><b>$25 account</b></td>
							</tr>
							<tr>
								<td colspan="2"><a href="#" onclick="popUp('terms.html')">See rules and regs for full details</a></td>
							</tr>
						</table>
					</article>
				</section>
				<?php include('../inc/twitter.php'); ?>
				<?php include('../inc/facebook.php'); ?>	
			</section>	
		</aside>

		<div class="clear"></div>

    </div>
</section>

<?php include('../inc/footer.php'); ?>

</body>
<script type="text/javascript" src="../admin/js/jquery-latest.js"></script>
<script type="text/javascript" src="../js/processPromo2.js"></script>
</html>