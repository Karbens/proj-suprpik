<?php
header('Location: index.php');exit();
$contest_id = $_GET['contest_id'];
$username = $_GET['username'];
define('_VALID_MOS', '1');//for accessing contests_func.php
include_once('../contests/contests_func.php');
tep_db_connect();
$userdata  = get_pick_history($contest_id, $username);
$datacount = get_pick_history_count($contest_id, $username);
$next_count = 30;
tep_db_close();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Free Contests - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	
	  <!-- Grey Box Code -->
	<script type="text/javascript">
		var GB_ROOT_DIR = "../greybox/";
	</script>
	<script type="text/javascript" src="../greybox/AJS.js"></script>
	<script type="text/javascript" src="../greybox/AJS_fx.js"></script>
	<script type="text/javascript" src="../greybox/gb_scripts.js"></script>
	<link href="../greybox/gb_styles.css" rel="stylesheet" type="text/css" />
	<!-- End of Grey Box Code -->
	
	<link rel="shortcut icon" href="http://n1.sbtcdn.com/media/sbtfav2.png" />
	<link rel="stylesheet" type="text/css" href="../css/phoenix.css" />
	<link rel="stylesheet" type="text/css" href="../css/fonts.css" />
	<link rel="stylesheet" type="text/css" href="http://fast.fonts.com/t/1.css?apiType=css&projectid=ad1c2391-5578-473c-95e8-486971f0b8dd" />
	<script type="text/javascript" src="/js/jquery-latest.js"></script>
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
			
			function loadMoreHistory(pagination)
			{
				var servData = 'more_history='+pagination+'&contest_id=<?php echo $contest_id; ?>&username=<?php echo $username; ?>';
				$.ajax({
			        url: "more_pick_history.php",
			        type: "POST",
			        data: servData,
			        cache: false,
					success: function (html) {
						//replace the content with new content
						$('.moreHistory').replaceWith(html);
			        }
			    });
			}
			//end of functions for payments
		
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
			.moreHistory { 
				text-align:center;
				font-size:12px;
				color:#fff;
				font-weight: bold;
				padding:5px 14px;
				background:#111;
				background:-moz-linear-gradient(top, #111 0%, #1e1e1e 100%);
				background:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #111), color-stop(100%, #1e1e1e));
				background:-webkit-linear-gradient(top, #111 0%, #1e1e1e 100%);
				background:-o-linear-gradient(top, #111 0%, #1e1e1e 100%);
				background:-ms-linear-gradient(top, #111 0%, #1e1e1e 100%);
				filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#FF111111', endColorstr='#FF1E1E1E',GradientType=0 );
				background:linear-gradient(top, #111111 0%,#1e1e1e 100%);
			}
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
					<h2>$1 Million "The Streaker" - Pick History for <?php echo $username; ?></h2>
				</header>
				
				<section class="wrapper" id="nfl-game-lines" style="">
				<style type="text/css">
				.leaderClass table { text-align:center;}
				.leaderClass td { padding:5px 14px; text-align:left;  font-size:12px; }
				.leaderClass th { background:#666; color:#fff; padding:5px 14px; text-align:left;  font-size:12px; font-weight:bold;}
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
				.moreHistory a, a:visited
				{
					color: #FFF;
					text-decoration: none;
				}
				.moreHistory a:hover
				{
					color: #FFF;
					text-decoration: underline;
				}
				</style>
				<?php
				$today = date('Y-m-d');
				if( count($userdata) > 0 )
				{
						$content .= '<table cellpadding="2" cellspacing="2" class="leaderClass" width="100%" style="font-size: 12px !important; border: 1px solid #000000 !important;">
									   
									   <tr>
									   	 <th> Date </th>
									     <th> Event </th>
										 <th> Pick </th>
										 <th> Result </th>
									   </tr>
									 ';
						$skc = 1;
						foreach($userdata as $sk => $sv)
						{
							$bcol = '';
							if( ($skc%2) == 0 )
							{
								$bcol = ' background:#dcdcdc !important;';
							}
							//figure out the result
							$result = '';
							if($sv['points'] > 0 || $sv['event_result'] > 0)
							{
								$result = ( $sv['points'] > 0 || ($sv['event_result'] == $sv['entry_value']) ) ? 'Won' : 'Loss';
							}
							else
							{
								$result = '<a rel="gb_page_center[600, 200]" href="pick_pending.php?contest_id=1&username='.$username.'">Pending</a>';
							}
							
							
							$bstyle = ' style="font-weight: normal;'.$bcol.'"';
							if($result == 'Won')$bstyle = ' style="font-weight: bold;'.$bcol.'"';
							
							$content .= '
									 <tr'.$bstyle.'>
								   	   <td'.$bstyle.'>'.$sv['contest_date'].'</td>
									   <td'.$bstyle.'>'.$sv['event_desc'].'</td>
									   <td'.$bstyle.'>'.$sv['choice'].'</td>
									   <td'.$bstyle.'>'.$result.'</td>
								     </tr>';
							$skc++;
						}
						if( $next_count < $datacount )
						{
							$content .= '<tr class="moreHistory">
										  <td colspan="4" style="text-align:center;">
										  	<a href="javascript:void(\'0\');" onclick="loadMoreHistory(\'30\')">View More...</a>
										  </td>
										</tr>';
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