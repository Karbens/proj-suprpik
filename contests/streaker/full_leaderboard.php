<?php
header('Location: index.php');exit();
//set time zone to eastern time zone
date_default_timezone_set('America/New_York');
include_once('../admin/db_func.php');
tep_db_connect();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Free Contests - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="http://n1.sbtcdn.com/media/sbtfav2.png" />
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
			.event-container{ background-color: #f4f4f4; border-bottom: solid 2px #777777; margin: 0 0 0 0; padding: 20px; }
			.event-container p{ font-weight: bold; }
			.event-container > dl dt{ font-weight: bold; height: 20px; }
			.event-container > dl dd{ margin: -20px 0 10px 100px; border-bottom: solid 1px #fff; }
			.clear{ clear: both; }
			#ui-datepicker-div, .ui-datepicker{ font-size: 80%; }
			
			/* css for timepicker */
			.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
			.ui-timepicker-div dl { text-align: left; }
			.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
			.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
			.ui-timepicker-div td { font-size: 90%; }
			.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
			iframe { width:225px; height:310px; overflow:hidden;}
	</style>
	
	  <!-- Grey Box Code -->
	<script type="text/javascript">
		var GB_ROOT_DIR = "../greybox/";
	</script>
	<script type="text/javascript" src="../greybox/AJS.js"></script>
	<script type="text/javascript" src="../greybox/AJS_fx.js"></script>
	<script type="text/javascript" src="../greybox/gb_scripts.js"></script>
	<link href="../greybox/gb_styles.css" rel="stylesheet" type="text/css" />
	<!-- End of Grey Box Code -->
	
</head>

<body class="en home">

<?php include('../inc/header.php'); ?>

<section id="mainContainer">
<br />
	<div class="innerWidth">
   		<section id="hpMain" class="left">
			<section id="featuredBets" class="tabs">
				<header>
					<h2>$1 Million "The Streaker" Full Leaderbord</h2>
				</header>
				
				<section class="wrapper" id="nfl-game-lines" style="">
				<style type="text/css">
				.leaderClass table { text-align:center; }
				.leaderClass td { padding:5px 14px; text-align:center; }
				.leaderClass th { background:#666; color:#fff; padding:5px 14px; text-align:center; }
				a, a:visited
				{
					color: #0000FF;
					text-decoration: none;
				}
				
				a:hover
				{
					color: #0000FF;
					text-decoration: underline;
				}
				</style>
				<?php
				$streakers = get_full_streakers();
				$today = date('Y-m-d');
				if( count($streakers > 0) )
				{
						$content .= '<table cellpadding="0" cellspacing="1" class="leaderClass" width="100%" style="border: 1px solid #000000 !important;">
									   
									   <tr>
									   	 <th> # </th>
									     <th> Streaker </th>
										 <th> Streak </th>
										 <th> Best </th>
										 <th> Last Pick </th>
										 <th> Deadline </th>
										 <th> Status </th>
										 <th> History </th>
									   </tr>
									 ';
						$skc = 1;
						foreach($streakers as $sk => $sv)
						{
							$bcol = '';
							if( ($skc%2) == 0 )
							{
								$bcol = ' style="background:#dcdcdc !important;"';
							}
							$content .= '
									 <tr'.$bcol.'>
								   	   <td'.$bcol.'>'.$skc.'</td>
								       <td'.$bcol.'>'.$sv['customer_id'].'</td>
									   <td'.$bcol.'>'.$sv['streak'].'</td>
									   <td'.$bcol.'>'.$sv['best_streak'].'</td>
									   <td'.$bcol.'>'.$sv['last_pick'].'</td>
									   <td'.$bcol.'>'.$sv['deadline'].'</td>
									   <td'.$bcol.'><a rel="gb_page_center[600, 200]" href="pick_pending.php?contest_id=1&username='.$sv['customer_id'].'">'.$sv['status'].'</a></td>
									   <td'.$bcol.'> <a href="pick_history.php?contest_id=1&username='.$sv['customer_id'].'">View</a> </td>
								     </tr>';
							$skc++;
						}
						$content .= '</table>';
				}//end of if( count($streakers > 0) )
				echo $content;
				?>
									
					<div class="clear"></div>
				</section>
			</section>
		</section>

		<aside id="hpFeeds" class="left">
			<section id="contestFeed">
				<!--<section id="blogFeed">
					<header>
						<h2>STREAKERS LEADERBOARD</h2>
					</header>
					<iframe src="../contests/leaderboard.php"></iframe>
				</section>-->
				<?php					
					include('../inc/twitter.php');
					include('../inc/facebook.php');
				?>	
			</section>	
		</aside>

		<div class="clear"></div>

    </div>
</section>

<?php include('../inc/footer.php'); ?>

</body>
</html>
<?php
tep_db_close();//close the database connection
?>