<?php   
/**  
 * Template Name: SuperPicks Soccer
 */ 
//error_reporting (-1);
error_reporting (E_ALL ^ E_NOTICE);

//set the time zone to eastern, cause all gametimes use that
//date_default_timezone_set('America/New_York');
date_default_timezone_set('America/Los_Angeles');

$slug = $post->post_name;

define('MOSCONFIG_BRDPREFIX', 'br3_');
define('MOSCONFIG_LIVE_SITE', site_url('/'.$slug.'/?option=com_contests&contest_id=%s'));
define('MAKEPICKS', 'Make Picks');
define('MYSUPERPICKS', 'My SuperPicks');

$contest_id = 3;


$mosConfig_live_site = sprintf(MOSCONFIG_LIVE_SITE, $contest_id);
require_once('soccer/cc_functions.php');

$contest_dates = getContestStartEndDate($contest_id);

define('SOCCER_START_DATE', $contest_dates['start']);
define('SOCCER_END_DATE', $contest_dates['end']);


//get current user
$user	= wp_get_current_user();
//user attributes: user_login, user_email, user_firstname, user_lastname, display_name, ID
//example: $user->user_login

//check T&C
if(is_user_logged_in()){
	if(!isset($_COOKIE['contest'][$contest_id][$user->ID]) || $_COOKIE['contest'][$contest_id][$user->ID]!='true'){
		if(isset($_REQUEST['conditions']) && $_REQUEST['conditions']=='accepted'){
			setContestCookie($contest_id, $user->ID);
			wp_redirect(site_url('/'.$slug));
		}
	}

}


	//if contest picks posted
	if( isset($_POST['s_contest']) || isset($_POST['p_contest']) || isset($_REQUEST['terms_and_conditions']) ) 
	{
		require_once('soccer/cc_data.php');
		exit();
	}

get_header(); ?>

<div id="container" class="contain">

	</br></br>
	<div id="main_page_ad"><?php $_banner = 'main'; include('banners_inc.php'); ?></div>

	<div id="main" role="main">

	<?php
	if( isset($_GET['user']) && $_GET['tab'] == 'leaderboard' && $_GET['week']>0)
	{
		echo '
			<div id="contests">
			';
		echo '<p> <a href="'.$mosConfig_live_site.'&tab=pick">'.$makePicksConfig.'</a> | <a href="'.$mosConfig_live_site.'&tab=mypicks">'.$myPicksConfig.'</a> | <a href="'.$mosConfig_live_site.'&tab=leaderboard">LEADERBOARD</a></p>';
		echo displayWeeklySpreadPicks($_GET['week']);
		echo '
			</div>';
	}else
	{
	
		echo '<div id="contests">';
		//echo '<h1>BR Contests'.$contest_name.'</h1>';
		
			require_once('soccer/cc_content.php');
		
		echo '</div>';
	}
	?>
	</div><!-- end main -->

	<?php get_sidebar(); ?>

</div><!-- end container -->

<?php get_footer(); ?>