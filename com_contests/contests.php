<?php
/*
 * contests.php - Bet Republic Contests.
 */
 
error_reporting (E_ALL ^ E_NOTICE);
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//set the time zone to eastern, cause all gametimes use that
date_default_timezone_set('America/New_York');

$mosConfig_brdbprefix = 'br3_';
$mosConfig_live_site = '';
require_once('cc_functions.php');

$app	= JFactory::getApplication();
$user	= JFactory::getUser();

//if contest picks posted
if( isset($_POST['s_contest']) || isset($_POST['p_contest']) || isset($_REQUEST['terms_and_conditions']) ) 
{
	require_once('cc_data.php');
	exit();
}

if( isset($_GET['user']) && $_GET['tab'] == 'leaderboard' && $_GET['week']>0)
{
	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	  <meta name="robots" content="index, follow" />
	  <title>Bet Republic</title>
		
		<link rel="stylesheet" href="/templates/betrepublictemplatev3.0/css/template.css" type="text/css" />
		<style>
		body {
			font-size: 75%;
		}
		.picks_heading {
			background: url("/templates/betrepublictemplatev3.0/images/main-h1-bg.jpg") repeat-x scroll 0 0 transparent;
		    color: #FFFFFF;
		    font-size: 1.2em;
		    margin: 0 0 10px;
		    padding: 10px 0 7px 10px; 
		}
		</style>
		</head>
		
		<body>
		<div id="contests">
		';
	echo displayWeeklySpreadPicks($_GET['week']);
	echo '
		</div>
		</body>
	</html>';
	exit();
}

$contest_name = '';
if( isset($_GET['contest_id']) && $_GET['contest_id'] > 0)
{
	$contest_name = getContestName($_GET['contest_id']);
	if(trim($contest_name) != '')$contest_name = ' - '.$contest_name;
}
?>

<div id="contests">
<h1>BR Contests<?php echo $contest_name; ?></h1>

<?php

	require_once('cc_content.php');

?>
</div>
