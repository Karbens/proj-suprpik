<?php
//set time zone to eastern time zone
date_default_timezone_set('America/New_York');
include_once('sbnetadmin/db_func.php');
tep_db_connect();

$contest_id = $_GET['contest'];
$today = date('Y-m-d');
$today1 = date("Y-m-d", strtotime($today));
$curtime = date("G");
/* AND `event_time` > '".$time."'"; */
$que = "SELECT * FROM `events` 
		WHERE event_desc != '' AND `contest_id` = ".$contest_id."
		AND `event_date` >= '".$today."' ORDER BY `event_order`, `event_id`";
//echo $que;
$query = mysql_query($que);
if(@mysql_num_rows($query) > 0)
{
	$events = array();
	while ($row = mysql_fetch_array($query)) {
	  $events[] = $row;
	}
}
//print_r($events);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Free Contests - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="i/sbtfav2.png" />
	<link rel="stylesheet" type="text/css" href="css/phoenix.css" />
	<link rel="stylesheet" type="text/css" href="css/fonts.css" />
	<link href="css/frontend/style.css" rel="stylesheet" type="text/css">
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
			.event-container .qNum {float:left; background:url('i/question_bg.jpg') no-repeat #434343; width:47px; text-align:center; height:39px; padding:4px 0 0 3px;}
			.event-container .qNum p { color:#000; font-size:1.2em; }
			.event-container .question { width:628px; height:43px; float:left; background:url('i/question_bg.jpg') right no-repeat; }
			.event-container p { font-weight: bold; color:#fff;padding:10px; margin:0; }
			.event-container select { border:1px solid #b2b2b2; }
			.event-container option { padding:0 5px; }
			.event-container table { margin:15px; }
			.event-container table input#submit { cursor:pointer; font-weight:bold; border:none; color:#fff; background:url('i/submit_bg.jpg') no-repeat; display:block; width:165px; height:30px;}
			.event-container table input#mypicks, .event-container table input#terms { cursor:pointer; font-weight:bold; border:none; color:#fff; background:url('i/button_bg.jpg') no-repeat; display:block; width:165px; height:30px; }		
			.event-container > dl dt{ font-weight: bold; height: 20px; }
			.event-container > dl dd{ margin: -20px 0 10px 100px; border-bottom: solid 1px #fff; }
			.event-container .select select { background: transparent; width: 180px; height: 18px; }
			.event-container .select { margin:55px 0 15px 15px; width: 150px; height: 18px; overflow: hidden; background: url('i/select.jpg') no-repeat right #fff; border: 1px solid #ccc; }
			.event-container .noselect { text-align: center; margin:5px 0px 5px 0px; font-size:13px; font-weight:bold; color: #000000;}
			.clear{ clear: both; }
			.event-container .checkClass { margin:55px 0 20px 15px; width: 700px; height: 18px; }
			.event-container .checkClass .checkbox {
			    width: 20px;
			    height: 20px;
			    padding: 0px;
			    background: url("i/checkbox.png") no-repeat;
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
	<script type="text/javascript" src="admin/js/jquery-latest.js"></script>
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
	
	function save_choices(){
		var count = 0;
		var rdo_arr =[];
		$('.rdo_class').each(function () {
		
		if($(this).is(':checked')){
			rdo_arr[count] = $(this).val();
			count=count+1;
		}
        });
		$.ajax({
			type: 'post',
			url: 'ajax_participation.php',
			async: false,
			data: { choices: rdo_arr},
			success: function (msg) {
			  if(msg==1){
				 $('#error_div').addClass('error_class');   
				 $('#error_div').html("Invalid username and password");   
				 return false;
			  }
			  else if(msg==0){
				window.location.href ="\contest.php";
			  }
			}
		});
	}
	</script>
</head>

<body class="en home">

<?php include('inc/header.php'); ?>

<section id="mainContainer">
	<br />
	<div class="innerWidth">
		<section id="hpMain" class="left">
			<section id="featuredBets" class="tabs">
				<header>
					<h2>Penalty Kicks 2012 Contest</h2>
				</header>
				<section class="wrapper" id="nfl-game-lines" style="">
					<?php if(!empty($events)){ ?>
						<form id="contestForm" name="contestForm" action="" method="post">
							<?php $count = 1;
								foreach($events as $ev){ 
									$event_id = $ev['event_id']; ?>
								<div class="event-container">
									<div class="qNum">
										<p><?php echo $count; ?></p>
									</div>
									<div class="question">
										<p><?php echo $ev['event_desc']; ?></p>
									</div>
									<div style="margin: 50px 0px 0px 0px;">
										<?php $que = 'SELECT * FROM `events_choices` WHERE event_id = '.$event_id.' ORDER BY `ec_order`, `ec_id`';
												$query = mysql_query($que);
												$ret = array();
												if(@mysql_num_rows($query) > 0)
												{
													while($res = mysql_fetch_assoc($query))
													{
														$ret[] = $res;
													}?>
												<div class="noselect">
													<input class="rdo_class" type="radio" id="rdo_<?php echo $ret[0]['ec_id']; ?>" name="rdo_<?php echo $ret[0]['ec_id']; ?>" value="<?php echo $ret[0]['ec_id']."_1"; ?>"> <?php echo $ret[0]['choice']; ?> </div>
												<div style="margin-right: 3px;" class="noselect">
													<input class="rdo_class" type="radio" id="rdo_<?php echo $ret[0]['ec_id']; ?>" name="rdo_<?php echo $ret[0]['ec_id']; ?>" value="<?php echo  $ret[0]['ec_id']."_0"; ?>"> <?php echo $ret[1]['choice']; ?></div>
												<?php } ?>
									</div>
								</div>
							<?php $count++;
							}  ?>
						 </form>
					<div class="clear"></div>
					<div style="float: left; width: 83px; margin: 10px 47px 6px 222px;">
						<input style="width: 86px;" id="register" class="button_class" type="button" name="btn_submit" value="Submit" onclick=" save_choices();">
					</div>
					<div style="float: left; width: 86px; margin: 10px 0px 6px;">
						<input onclick="location.href='contest.php'" style="width: 86px;" id="cancel" class="button_class" type="submit" style="float: right;" name="cancel" value="Cancel">
					</div>
					<?php }
					else{ ?>
						<div class="event-container">
							<div class="question">
								<p>The Contest is now expired. Please check back for future contests. Good Luck!</p>
							</div>
						</div>
					<?php } ?>
					<div class="clear"></div>
				</section>
			</section>
		</section>

		<aside id="hpFeeds" class="left">
			<?php include('inc/twitter.php'); ?>
			<?php include('inc/facebook.php'); ?>	
		</aside>

		<div class="clear"></div>

    </div>
</section>

<?php include('inc/footer.php'); ?>

</body>
<script type="text/javascript" src="js/processPromo2.js?version=20120404"></script>
</html>
<?php tep_db_close(); ?>