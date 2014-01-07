<?php   
/**  
 * Template Name: Pro Picks
 */   
?>
<?php
error_reporting (E_ALL ^ E_NOTICE);

//set the time zone to eastern, cause all gametimes use that
date_default_timezone_set('America/New_York');


$slug = $post->post_name;

define('MOSCONFIG_BRDPREFIX', 'br3_');
define('MOSCONFIG_LIVE_SITE', '/'.$slug.'/?option=com_contests&contest_id=%s');

$contest_id = 2;
$mosConfig_live_site = sprintf(MOSCONFIG_LIVE_SITE, $contest_id);
require_once('cc_functions.php');

//get current user
$user	= wp_get_current_user();
//user attributes: user_login, user_email, user_firstname, user_lastname, display_name, ID
//example: $user->user_login


	//if contest picks posted
	if( isset($_POST['s_contest']) || isset($_POST['p_contest']) || isset($_REQUEST['terms_and_conditions']) ) 
	{
		require_once('cc_data.php');
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
		echo '<p> <a href="'.$mosConfig_live_site.'&tab=mypicks">'.$myPicksConfig.'</a> | <a href="'.$mosConfig_live_site.'&tab=pick">'.$makePicksConfig.'</a> | <a href="'.$mosConfig_live_site.'&tab=leaderboard">LEADERBOARD</a></p>';
		echo displayWeeklySpreadPicks($_GET['week']);
		echo '
			</div>';
	}else
	{
		$contest_name = '';
		if( isset($_GET['contest_id']) && $_GET['contest_id'] > 0)
		{
			$contest_name = getContestName($_GET['contest_id']);
			if(trim($contest_name) != '')$contest_name = ' - '.$contest_name;
		}
		
		echo '<div id="contests">';
		//echo '<h1>BR Contests'.$contest_name.'</h1>';
		
			require_once('cc_content.php');
		
		echo '</div>';
	}
	?>
	</div><!-- end main -->

	<?php get_sidebar(); ?>

</div><!-- end container -->

<?php get_footer(); ?>