<?php   
/**  
 * Template Name: Odds 
 */   

get_header(); ?>

  <link type="text/css" rel="stylesheet" href="/css/fonts.css" />
  <link type="text/css" rel="stylesheet" href="/css/styles.css" />
  <!-- Grey Box Code -->
	<script type="text/javascript">
		var GB_ROOT_DIR = "/greybox/";
	</script>
	<script type="text/javascript" src="/greybox/AJS.js"></script>
	<script type="text/javascript" src="/greybox/AJS_fx.js"></script>
	<script type="text/javascript" src="/greybox/gb_scripts.js"></script>
	<link href="/greybox/gb_styles.css" rel="stylesheet" type="text/css" />
	<!-- Start of Calendar files -->
	<style>
	#datepicker {
		width: 80px;
	}
	#odds_table td {
		font-size:		13px;
		color:			#FFF;
	}
	#odds_table input {
		color: #000;
		padding-left: 4px;
	}
	</style>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
	<script type="text/javascript" src="/lines/js/jquery-ui-1.8.7.custom.min.js"></script>
	<link type="text/css" href="/lines/css/redmond/jquery-ui-1.8.7.custom.css" rel="stylesheet" />	
	<!-- End of Calendar files -->

<div id="container" class="contain">
	
	<section id="lines">
	<header style="margin-bottom:0;padding:25px 0 0;">
	<?php
		//set the time zone
		date_default_timezone_set('America/Vancouver');
		define('_VALID_MOS', '1');//for accessing db_func.php
		include_once('lines/db_func.php');
		tep_db_connect();
		$league_params = getLeagueParams($league);
		
		$leagues_array   =  getAllLeagues();
		$sports_settings =  get_sports_settings();
		$def_league      =  $sports_settings['default'];
		
		tep_db_close();
		
		//set the date value
		$cur_date = date('m/d/Y');
		if(isset($_GET['date']))$cur_date = $_GET['date'];
		
		//set the sport value
		$league = ( isset($leagues_array[$def_league]) ) ? $leagues_array[$def_league] : "nfl";
		if(isset($_GET['league']))$league = $_GET['league'];
		
		//set the period value
		$period = 0;
		if(isset($_GET['period']))$period = $_GET['period'];
		
		if($league != '')
		{
		  $league_sport = $league_params['sport'];
		  $league_id = $league_params['league_id'];
		  if($league_sport == 'Football')
		  {
		  	$minDate = '"-15D"';
			$maxDate = '"+2W"';
		  }else
		  {
		  	$minDate = '"-15D"';
			$maxDate = '"+1W"';
		  }
		}
		?>
		<table width="100%" id="odds_table" border=0>
		  <tr>
		    <td width="10">&nbsp;</td>
		    <td width="380" align="left" nowrap>
				 SPORT: 
				<?php echo '<a href="javascript:void(0);" onClick="change_odds(\'league\', \'nfl\')" class="lines" title="National Football League">NFL</a>'; ?> | 
				<?php echo '<a href="javascript:void(0);" onClick="change_odds(\'league\', \'nhl\')" class="lines" title="National Hockey League">NHL</a>'; ?> | 
				<?php echo '<a href="javascript:void(0);" onClick="change_odds(\'league\', \'nba\')" class="lines" title="National Basketball Association">NBA</a>'; ?> | 
				<?php echo '<a href="javascript:void(0);" onClick="change_odds(\'league\', \'ncaab\')" class="lines" title="NCAA College Basketball">NCAAB</a>'; ?> |
			    <?php echo '<a href="javascript:void(0);" onClick="change_odds(\'league\', \'mlb\')" class="lines" title="Major League Baseball">MLB</a>'; ?> |  
			    <?php echo '<a href="javascript:void(0);" onClick="change_odds(\'league\', \'ncaaf\')" class="lines" title="NCAA College Football">NCAAF</a>'; ?> | 
				<?php echo '<a href="javascript:void(0);" onClick="change_odds(\'league\', \'cfl\')" class="lines" title="Canadian Football League">CFL</a>'; ?> 
			</td>
			<td align="left" nowrap>
				 LINES: 
				<?php echo '<a href="javascript:void(0);" onClick="change_odds(\'period\', \'0\')" class="lines" class="lines" title="Game Lines">Game</a>'; ?> | 
				<?php echo '<a href="javascript:void(0);" onClick="change_odds(\'period\', \'3\')" class="lines" class="lines" title="Money Lines">ML/RL</a>'; ?> 
			</td>
			<td align="center" nowrap>
			 <a href="javascript:void(0);" onclick="javascript:top.frames[0].toggle_settings();" style="color:#ffffff;">SETTINGS</a>
			</td>
			<td align="right" nowrap>
				 DATE:
				<input type="text" id="datepicker" value="<?php echo $cur_date;?>" >
				<input type="hidden" id="league" value="<?php echo $league; ?>">
				<input type="hidden" id="period" value="<?php echo $period; ?>">
				<script type="text/javascript" language="javascript">
				  var thedate = <?php echo strtotime('today'); ?>;
				  
				 jQuery(document).ready(function()
					{
						jQuery('#datepicker').datepicker({
							minDate: <?=$minDate?>,
							maxDate: <?=$maxDate?>,
							inline: false,
							onSelect: function(dateText, inst) 
							{ 
								load_odds(dateText);
							}
						});
					});
					
					function load_odds(dateText)
					{
						thedate = new Date(dateText);
						var curr_date = thedate.getDate();
						var curr_month = thedate.getMonth()+1;
						var curr_year = thedate.getFullYear();
						if(curr_date<10){curr_date='0'+curr_date}
						if(curr_month<10){curr_month='0'+curr_month}
						var league = $( "#league" ).val();
						var period = $( "#period" ).val();
						document.getElementById("lines_frame").src = '/lines/index.php?date=' + curr_year + "-" + curr_month + "-" + curr_date + '&league=' + league + '&period='+period;		
					}
					
					function change_odds(ovar, oval)
					{
						$( "#"+ovar ).val(oval);
						var dateText = $( "#datepicker" ).val();
						load_odds(dateText);
					}
					
					function auto_height()
					{
						
						var linesFrame = document.getElementById("lines_frame");
						var linesHeight = 0;
						if(linesFrame.contentDocument){
							linesHeight = linesFrame.contentDocument.body.offsetHeight+35;
							linesFrame.height = (linesHeight < 350) ? 350 : linesHeight;
						} else {
							linesHeight = linesFrame.contentWindow.document.body.scrollHeight+35;
							linesFrame.height = (linesHeight < 350) ? 350 : linesHeight;
						}
					}

				</script>
		
			</td>
			<td width="10">&nbsp;</td>
		  </tr>
		</table>
		</header>
		<article>
			<iframe src="/lines/" name="lines_frame" id="lines_frame" scrolling="no" frameborder="0"></iframe>
		</article>
		<footer>

		</footer>
	</section>

</div><!-- end container -->

<?php get_footer(); ?>