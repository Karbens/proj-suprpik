<?php
session_start();
include_once('sbnetadmin/db_func.php');
include_once('sbnetadmin/contests_func.php');
include_once('functions.php');
tep_db_connect();

	$contest_id = isset($_REQUEST['contest_id'])? (int)$_REQUEST['contest_id'] : 0;

	$contests = get_contests($contest_id);
 
  $customer_id = $_SESSION['user']['signup_id'];
	$selected_choices = get_customer_choices($contest_id,$customer_id);
	
	if($contest_id>0){
		$contest = $contests[0];
    
		$templates = get_templates();

		if(class_exists($contest['template'])){

			$contestObject = new $contest['template']($contest);
      
		} else {
			exit();

		}
	}

  $events = $contestObject->getEvents();
  
	

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
			.event-container .noselect { 
				/*text-align: center; */
				margin: 5px 265px;
				/*margin:5px 0px 5px 0px; */
				font-size:13px; 
				font-weight:bold; 
				color: #000000;
			}
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
		if($('.rdo_class:checked').length<=0){
			alert('Please select choices');
			return false;
		}
    var names = {};
    $('input:radio').each(function() { // find unique names
          names[$(this).attr('name')] = true;
    });
    var count = 0;
    $.each(names, function() { // then count them
          count++;
    });
    if($('input:radio:checked').length != count) {
          alert("Answer all questions");
          return false;
    }
    
    if($('#hdn_choice_cnt').val()>0){
       alert("You have alrady participated in this contest");
       return false;
     }

      $.ajax({
			type: 'post',
			url: 'ajax_participation.php?contest_id=<?php echo $contest_id; ?>',
			async: false,
			data: $('#contestForm').serialize(),
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
					<h2><?php echo $contestObject->contest_name; ?></h2>
				</header>
				<section class="wrapper" id="nfl-game-lines" style="">
				<input type="hidden" id="hdn_choice_cnt" value="<?php echo count($selected_choices); ?>">
					<?php if(!empty($events)){ ?>
						<form id="contestForm" name="contestForm" action="" method="post">
							<?php $count = 1;
								foreach($events as $event){ 

										$choices = $contestObject->getChoices($event['event_id']);
								?>
								<div class="event-container">
									<div class="qNum">
										<p><?php echo $count; ?></p>
									</div>
									<div class="question">
										<p><?php echo $event['event_desc']; ?></p>
									</div>
									<div style="margin: 50px 0px 0px 0px;">


									<?php foreach ($choices as $key => $choice) { 
									  $selected = '';
                    foreach($selected_choices as $sel){
                        if($sel['ec_id']==$choice['ec_id'])
                          $selected="checked";
                    }
                  ?>
                  	<div class="noselect">
											<input <?php echo $selected; ?> class="rdo_class" type="radio" id="rdo_<?php echo $choice['ec_id']; ?>" name="choice[<?php echo $event['event_id']; ?>]" value="<?php echo $choice['ec_id']; ?>"> <?php echo $choice['choice']; ?>
										</div>
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
							<div >
								<p style="color:black;">The Contest is now expired. Please check back for future contests. Good Luck!</p>
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