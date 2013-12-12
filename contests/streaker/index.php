<?php
//set time zone to eastern time zone
date_default_timezone_set('America/New_York');
include_once('../admin/db_func.php');
tep_db_connect();
$contest_id = 1;
$date = date('Y-m-d');
$time = date('H:i');
$events = set_events($contest_id, $date, $time);
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
	<link rel="stylesheet" media="all" type="text/css" href="../css/jquery-ui-1.8.16.custom.css" />
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
			.event-container .select select { background: transparent; width: 180px; height: 18px; }
			.event-container .select { margin:55px 0 15px 15px; width: 150px; height: 18px; overflow: hidden; background: url('/i/select.jpg') no-repeat right #fff; border: 1px solid #ccc; }
			.clear{ clear: both; }
			iframe { width:225px; height:350px; overflow:hidden;}
			.event-container .radio { float:left; margin:15px 5px 0 20px; }
			.event-container .checkClass { margin:55px 0 20px 15px; width: 700px; height: 18px; }
			.event-container .checkClass .checkbox {
			    width: 20px;
			    height: 20px;
			    padding: 0px;
			    background: url("/i/checkbox.png") no-repeat;
			    display: block;
			    clear: left;
			    float: left;
				margin-right: 3px;
			 }
			.event-container .checkClass .checked {
			     background-position: 0px -40px;   
			}
			.event-container .disabClass { margin:55px 0 15px 15px; width: 700px; height: 18px; }
			.event-container .disabClass .checkbox {
			    width: 20px;
			    height: 20px;
			    padding: 0px;
			    background: url("/i/checkbox.png") no-repeat;
				background-position: 0px -20px;
			    display: block;
			    clear: left;
			    float: left;
				margin-right: 3px;
			 }
			.event-container .disabClass .checked {
			     background-position: 0px -60px;
			}
			.checkClass table td, .disabClass table td { vertical-align: middle; }
	</style>
	<script type="text/javascript" src="../admin/js/jquery-latest.js"></script>
	<script type="text/javascript">
	$(function() {

	    $('input.styled').each(function() {
	        var span = $('<span class="' + $(this).attr('type') + ' ' + $(this).attr('class') + '"></span>').click(doCheck).mousedown(doDown).mouseup(doUp);
	        if ($(this).is(':checked')) {
	            span.addClass('checked');
	        }
	        $(this).wrap(span).hide();
	    });
	
	    function doCheck() {
			if ($(this).hasClass('checked')) {
	            $(this).removeClass('checked');
	            $(this).children().prop("checked", false);
	        } else {
	            $("input.styled").attr('checked', false);
				$("span.checked").removeClass('checked');
				$(this).addClass('checked');
	            $(this).children().prop("checked", true);
	        }
	    }
	
	    function doDown() {
	        $(this).addClass('clicked');
	    }
	
	    function doUp() {
	        $(this).removeClass('clicked');
	    }
	});
	</script>
</head>

<body class="en home">

<?php include('../inc/header.php'); ?>

<section id="mainContainer">
<br />
	<div class="innerWidth">
   		<section id="hpMain" class="left">
			<section id="featuredBets" class="tabs">
				<header>
					<h2>$1 Million "The Streaker" Contest</h2>
				</header>
				<section class="wrapper" id="nfl-game-lines" style="">
					<img src="streaker_contest_suspended.jpg" alt="The Streaker Contest" style="margin:-10px 0 0 -20px;" />
					<!--
					<br /><br />
					<center><a href="https://www.sportsbetting.com/en/signup/"><img src="/i/member.jpg" alt="Join Now!" /></a></center>
					-->
					<br />
								
					<?php
					if( count($events) > 0 )
					{
					?>
						<form id="contestForm" name="contestForm" action="" method="post">
							<input type="hidden" id="contestid" name="contestid" value="<?php echo $contest_id; ?>" />
							<input type="hidden" id="contestdate" name="contestdate" value="<?php echo $date; ?>" />
						<?php
						$count = 1;
						$j = 1;
						foreach($events as $eve)
						{
							$sel = '';
							if( count($events) == 1)$sel = ' checked';
							$choices = get_choices($eve['event_id']);
							$cClas = 'checkClass';
						?>
								<div class="event-container">
									<input type="hidden" name="eventtime[<?php echo $eve['event_id']; ?>]" id="eventtime_<?php echo $eve['event_id']; ?>" value="<?php echo $eve['event_time']; ?>" />									
									<div class="qNum">
										<p><?php echo $count;?></p>
									</div>
									<div class="question">
										<p><?php echo $eve['event_desc']; ?></p>
									</div>
									<div class="<?php echo $cClas; ?>">
									  <table>
									  <tr>
									  <?php
									  foreach($choices as $ch)
									  {
										echo '<td><input type="checkbox" name="eventchoice" '.
											 'id="eventchoice_'.$j.'" value="'.$ch['ec_id'].'" class="styled"/></td>'.
											 '<td>'.$ch['choice'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>';
										$j++;
									  }
									  ?>
									  </tr>
									  </table>
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
								  <input type="button" id="mypicks" name="mypicks" value="See My Pick" onclick="processPicks();"/>
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
					else
					{
					?>
							<!--
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
							</div>-->
					<?php
				    }
					?>					
					<div class="clear"></div>
				</section>
			</section>
		</section>

		<aside id="hpFeeds" class="left">
				<!--
				<section id="blogFeed">
					<header>
						<h2>THE STREAKER LEADERBOARD</h2>
					</header>
					<iframe src="/contests/leaderboard.php"></iframe>
				</section>-->
				<?php					
					include('../inc/twitter.php');
					include('../inc/facebook.php');
				?>	
		</aside>

		<div class="clear"></div>

    </div>
</section>

<?php include('../inc/footer.php'); ?>

</body>
<script type="text/javascript" src="../js/processPromo.js?version=20120404"></script>
</html>
<?php
tep_db_close();//close the database connection
?>