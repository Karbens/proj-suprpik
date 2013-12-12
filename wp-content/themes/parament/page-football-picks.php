<?php   
/**  
 * Template Name: Football Picks
 */   

get_header(); ?>
<?php
//set time zone to eastern time zone
date_default_timezone_set('America/New_York');
include_once('./contests/admin/db_func.php');
tep_db_connect();
$contest_id = 7;//id for slap shot contest
$date = get_current_contest_date($contest_id);//end date for current contest
//uncomment below and change date value to manually override date
$date = '2013-08-26';
$events = set_events($contest_id, $date);
?>
	

    <link rel="stylesheet" type="text/css" href="http://superpicks.com/contests/css/phoenix.css" />
	<link rel="stylesheet" type="text/css" href="http://superpicks.com/contests/css/fonts.css" />
	<link rel="stylesheet" type="text/css" href="http://fast.fonts.com/t/1.css?apiType=css&projectid=ad1c2391-5578-473c-95e8-486971f0b8dd" />
	<script type="text/javascript">
		  function popUp(URL) {
			window.open(URL, 'Terms', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=960,height=600,left = 300,top = 100');
		}
	</script>
	<style type="text/css"> 
			body { 
				background: none repeat scroll 0 0 #2C2C2C;
			    color: #5A6466;
			    font-family: Kreon,serif;
			    font-size: 11.5pt;
			    line-height: 1.75em;
			}
			pre{ padding: 20px; background-color: #ffffcc; border: solid 1px #fff; }
			.wrapper { color:#000; padding:10px 20px!important; font-size:13px; text-align:left!important; }
			.wrapper h1 { padding:0 0 10px; }
			.wrapper p { margin:5px 0 10px; }
			.event-container { background:#e5e5e5; border:1px solid #b2b2b2; margin:0 0 10px; }
			.event-container .qNum {float:left; background:url('http://superpicks.com/contests/i/question_bg.jpg') no-repeat #434343; width:47px; text-align:center; height:39px; padding:4px 0 0 3px;}
			.event-container .qNum p { color:#000; font-size:1.2em; }
			.event-container .question { width:628px; height:43px; float:left; background:url('http://superpicks.com/contests/i/question_bg.jpg') right no-repeat; }
			.event-container p { font-weight: bold; color:#fff;padding:10px; margin:0; }
			.event-container select { border:1px solid #b2b2b2; }
			.event-container option { padding:0 5px; }
			.event-container table { margin:15px; width: 500px;}
			.event-container table input#submit { cursor:pointer; font-weight:bold; border:none; color:#fff; background:url('http://superpicks.com/contests/i/submit_bg.jpg') no-repeat; display:block; width:165px; height:30px;}
			.event-container table input#mypicks, .event-container table input#terms { cursor:pointer; font-weight:bold; border:none; color:#fff; background:url('http://superpicks.com/contests/i/button_bg.jpg') no-repeat; display:block; width:165px; height:30px; }		
			.event-container > dl dt{ font-weight: bold; height: 20px; }
			.event-container > dl dd{ margin: -20px 0 10px 100px; border-bottom: solid 1px #fff; }
			.event-container .select select { background: transparent; width: 180px; height: 18px; }
			.event-container .select { margin:55px 0 15px 15px; width: 150px; height: 18px; overflow: hidden; background: url('http://superpicks.com/contests/i/select.jpg') no-repeat right #fff; border: 1px solid #ccc; }
			.event-container .noselect { text-align: center; margin:5px 0px 5px 0px; font-size:13px; font-weight:bold; color: #000000;}
			.clear{ clear: both; }
			.event-container .checkClass { margin:55px 0 20px 15px; width: 700px; height: 18px; }
			.event-container .checkClass .checkbox {
			    width: 20px;
			    height: 20px;
			    padding: 0px;
			    background: url("http://superpicks.com/contests/i/checkbox.png") no-repeat;
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
			    background: url("http://superpicks.com/contests/i/checkbox.png") no-repeat;
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
			#event_entry_table td {
				padding: 5px;
			}
	</style>
	<script type="text/javascript" src="http://superpicks.com/contests/admin/js/jquery-latest.js"></script>
	<script type="text/javascript">
	var eventCount = <?php echo count($events); ?>;
	$(function() {

	    $('input.styled').each(function() {
	        var span = $('<span class="' + $(this).attr('type') + ' ' + $(this).attr('class') + '" group="' + $(this).attr('group') + 
						 '"></span>').click(doCheck).mousedown(doDown).mouseup(doUp);
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
				var grp = $(this).children().attr('group');
				$('input:checkbox[group='+grp+']').attr('checked', false);
				$("span.checked[group="+grp+"]").removeClass('checked');
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

<div id="container" class="contain">

	</br></br>
	
	<section id="mainContainer">
	<br />
	<div class="innerWidth">
		<section id="hpMain" class="left">
			<section id="featuredBets" class="tabs">
				<header>
					<h2>Super Picks Football</h2>
				</header>
				<section class="wrapper" id="nfl-game-lines" style="">
					<br /><br />
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
						$j = 1;
						$cClas = 'checkClass';
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
										<!--
										<div class="select">
										  <select name="eventchoice_<?php echo $eve['event_id']; ?>" id="eventchoice_<?php echo $eve['event_id']; ?>" class="contestSelection">
										  <?php
										  /*foreach($choices as $ch)
										  {
											echo '<option value="'.$ch['ec_id'].'">'.$ch['choice'].'</option>';
										  }*/
										  ?>
										  </select>
										
										</div>
										-->
										<div class="<?php echo $cClas; ?>">
										  <table>
										  <tr>
										  <?php
										  foreach($choices as $ch)
										  {
											echo '<td><input type="checkbox" name="eventchoice" group="event'.$eve['event_id'].'" '.
												 'id="eventchoice_'.$j.'" value="'.$ch['ec_id'].'" class="styled"/></td>'.
												 '<td>'.$ch['choice'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>';
											$j++;
										  }
										  ?>
										  </tr>
										  </table>
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
							<table id="event_entry_table" cellpadding="2" cellspacing="2">
							<?php
							if($expiry_date_time > $now_date_time)
							{
							?>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
								  <td align="right"><strong>Your User Name:</strong>&nbsp;</td>
								  <td><input type="text" id="cid" name="cid" /></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
								  <td align="right"><strong>Your Email:</strong>&nbsp;</td>
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
								<!--
								<tr>
								  <td>&nbsp;</td>
								  <td>
									<input type="button" id="terms" name="terms" value="Rules and Regulations" onclick="popUp('terms.html')"/>
								  </td>
								</tr>
								-->

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
			<?php include('./contests/inc/twitter.php'); ?>
			<?php include('./contests/inc/facebook.php'); ?>	
		</aside>

		<div class="clear"></div>

    </div>
</section>

</div><!-- end container -->
<script type="text/javascript" src="http://superpicks.com/contests/js/processPromo2.js?version=20130804"></script>
<?php get_footer(); ?>