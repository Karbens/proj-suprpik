<?php
//error_reporting(E_ALL);
error_reporting(null);
ini_set('display_errors',0);
//set time zone to eastern time zone beacause all lines use eastern time
date_default_timezone_set('America/New_York');
set_time_limit(180);//script can only take max 90 seconds
ini_set("memory_limit", "640M");//max memory allowed for the script

define('_VALID_MOS', '1');//for accessing lines.class.php (included in db_func.php)

//update BR Contests Scripts
include_once('updateBRContests.php');
if( (date('l') == 'Wednesday' && date('G') == 15) || (date('l') == 'Tuesday' && date('G') == 3) )
{
	@mysql_query("OPTIMIZE TABLE `ol_feeds_log` , `ol_games` , `ol_lines_bd` , `ol_lines_ps`");
}

//file for db connection, also includes other required files
require_once("db_func.php");
tep_db_connect();//create connection with database

if( date('G') == 4 && date('i') < 10 )//only run at 4am Eastern within first 10 minutes
{
	//remove_old_data();//delete records that are at least 15 days old, excluding NFL
	
	//update final scores today and yesterday
	$feed_url_a = 'http://www.statfox.com/sports-scores-odds/?nflchk=1&cfbchk=1&nbachk=1&cbbchk=1&mlbchk=1&nhlchk=1&timeframe=yesterday&view=all&submit.x=30&submit.y=11';
	$feed_url_b = 'http://www.statfox.com/sports-scores-odds/?nflchk=1&cfbchk=1&nbachk=1&cbbchk=1&mlbchk=1&nhlchk=1&timeframe=today&view=all&submit.x=30&submit.y=8';
	update_final_scores($feed_url_a);
	update_final_scores($feed_url_b);
}

//get active leagues from settings
$_settings = get_sports_settings();


//initiate the lines class
$lines = New lines();



/************************************************************************/
//update Bodog
$b = 0;
$bd_array = array();

/*
Bodog NFL Feeds
FNF - NFL First Halves
NFH - NFL Halftimes
NFL - NFL Lines
NFX - NFL Exhibition
FQL - NFL Quarter Lines
*/
if( in_array('4',$_settings['active']) )
{
	 $b++;
	 $bd_array[$b]['league_id'] = 4;
	 $bd_array[$b]['book_id'] = 3;
	 $bd_array[$b]['feed_url'] = array('http://sportsfeeds.Bodoglife.com/basic/NFL.xml',
								   'http://sportsfeeds.Bodoglife.com/basic/FNF.xml',
								   'http://sportsfeeds.Bodoglife.com/basic/NFH.xml',
								   'http://sportsfeeds.Bodoglife.com/basic/FQL.xml'
								  );
}
/*
Bodog MLB Feeds
MLB - MLB Game Lines
MLH - MLB First 5 Innings
*/
if( in_array('1',$_settings['active']) )
{
	$b++;
	$bd_array[$b]['league_id'] = 1;
	$bd_array[$b]['book_id'] = 3;
	$bd_array[$b]['feed_url'] = array('http://sportsfeeds.Bodoglife.com/basic/MLB.xml',
									 'http://sportsfeeds.Bodoglife.com/basic/MLH.xml'
									 );
}

/*
Bodog College Football Feeds
FCF - College First Halves
CFH - College Halftimes
NCF - College Lines
FQC - College Quarter Lines
*/
if( in_array('7',$_settings['active']) )
{
	$b++;
	$bd_array[$b]['league_id'] = 7;
	$bd_array[$b]['book_id'] = 3;
	$bd_array[$b]['feed_url'] = array('http://sportsfeeds.Bodoglife.com/basic/NCF.xml',
									 'http://sportsfeeds.Bodoglife.com/basic/FCF.xml',
									 'http://sportsfeeds.Bodoglife.com/basic/CFH.xml',
									 'http://sportsfeeds.Bodoglife.com/basic/FQC.xml'
									);
}

/*Bodog NBA Feeds
BNF - NBA First Halves
HTL - NBA Halftimes
NBA - NBA Lines
NBQ - Quarter Lines
*/
if( in_array('2',$_settings['active']) )
{
	$b++;
	$bd_array[$b]['league_id'] = 2;
	$bd_array[$b]['book_id'] = 3;
	$bd_array[$b]['feed_url'] = array('http://sportsfeeds.Bodoglife.com/basic/NBA.xml',
									 'http://sportsfeeds.Bodoglife.com/basic/BNF.xml',
									 'http://sportsfeeds.Bodoglife.com/basic/NBQ.xml',
									 'http://sportsfeeds.Bodoglife.com/basic/HTL.xml'
									);
}


//Bodog NCB Feed
if( in_array('3', $_settings['active']) )
{
	$b++;
	$bd_array[$b]['league_id'] = 3;
	$bd_array[$b]['book_id'] = 3;
	$bd_array[$b]['feed_url'] = array('http://sportsfeeds.bodoglife.com/basic/NCB.xml');
}


//Bodog CFL Feed
if( in_array('6',$_settings['active']) )
{
	$b++;
	$bd_array[$b]['league_id'] = 6;
	$bd_array[$b]['book_id'] = 3;
	$bd_array[$b]['feed_url'] = 'http://sportsfeeds.bodoglife.com/basic/CFL.xml';
}


//Bodog NHL Feed
if( in_array('8',$_settings['active']) )
{
	$b++;
	$bd_array[$b]['league_id'] = 8;
	$bd_array[$b]['book_id'] = 3;
	$bd_array[$b]['feed_url'] = 'http://sportsfeeds.Bodoglife.com/basic/NHL.xml';
}


//Bodog WNBA Feed
/*
$b++;
$bd_array[$b]['league_id'] = 9;
$bd_array[$b]['book_id'] = 3;
$bd_array[$b]['feed_url'] = 'http://sportsfeeds.Bodoglife.com/basic/WBA.xml';
*/

for($i=1;$i<=count($bd_array);$i++)
{
	if(is_array($bd_array[$i]['feed_url']))
	{
		foreach( $bd_array[$i]['feed_url'] as $f_url)
		{
		  $lines->setLines($bd_array[$i]['book_id'], $bd_array[$i]['league_id'], $f_url);
	  	  $lines->updateLines();
		}
	}else
	{
	  $lines->setLines($bd_array[$i]['book_id'], $bd_array[$i]['league_id'], $bd_array[$i]['feed_url']);
	  $lines->updateLines();
	}
}
//end of update Bodog




/************************************************************************/
//update Pinnacle Sports
$ps_array = array();
$p = 0;

//Pinnacle Sports MLB Feed
if( in_array('1',$_settings['active']) )
{
	$p++;
	$ps_array[$p]['league_id'] = 1;
	$ps_array[$p]['book_id'] = 5;
	$ps_array[$p]['feed_url'] = 'http://api.pinnaclesports.com/v1/feed?sportid=3&leagueid=246&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0';
}


//Pinnacle Sports NFL Feed
if( in_array('4',$_settings['active']) )
{
	$p++;
	$ps_array[$p]['league_id'] = 4;
	$ps_array[$p]['book_id'] = 5;
	$ps_array[$p]['feed_url'] = 'http://api.pinnaclesports.com/v1/feed?sportid=15&leagueid=889&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0';
}


//Pinnacle Sports NFLPreSeason Feed
if( in_array('4',$_settings['active']) )
{
	$p++;
	$ps_array[$p]['league_id'] = 4;
	$ps_array[$p]['book_id'] = 5;
	$ps_array[$p]['feed_url'] = 'http://api.pinnaclesports.com/v1/feed?sportid=15&leagueid=4347&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0';
}


//Pinnacle Sports College Football Feed
if( in_array('7',$_settings['active']) )
{
	$p++;
	$ps_array[$p]['league_id'] = 7;
	$ps_array[$p]['book_id'] = 5;
	$ps_array[$p]['feed_url'] = 'http://api.pinnaclesports.com/v1/feed?sportid=15&leagueid=880&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0';
}


//Pinnacle Sports NBA Feed
if( in_array('2',$_settings['active']) )
{
	$p++;
	$ps_array[$p]['league_id'] = 2;
	$ps_array[$p]['book_id'] = 5;
	$ps_array[$p]['feed_url'] = 'http://api.pinnaclesports.com/v1/feed?sportid=4&leagueid=487&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0';
}


//Pinnacle Sports NCB Feed
if( in_array('3',$_settings['active']) )
{
	$p++;
	$ps_array[$p]['league_id'] = 3;
	$ps_array[$p]['book_id'] = 5;
	$ps_array[$p]['feed_url'] = 'http://api.pinnaclesports.com/v1/feed?sportid=4&leagueid=493&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0';
}


//Pinnacle Sports NHL Feed
if( in_array('8',$_settings['active']) )
{
	$p++;
	$ps_array[$p]['league_id'] = 8;
	$ps_array[$p]['book_id'] = 5;
	$ps_array[$p]['feed_url'] = 'http://api.pinnaclesports.com/v1/feed?sportid=19&leagueid=1460&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0';
}


//Pinnacle Sports CFL Feed
if( in_array('6',$_settings['active']) )
{
	$p++;
	$ps_array[$p]['league_id'] = 6;
	$ps_array[$p]['book_id'] = 5;
	$ps_array[$p]['feed_url'] = 'http://api.pinnaclesports.com/v1/feed?sportid=15&leagueid=876&clientid=PB188292&apikey=ad20feed-0f60-4e35-8abe-ccf8942a80c5&oddsformat=0&islive=0';
}

for($i=1;$i<=count($ps_array);$i++)
{
	$lines->setLines($ps_array[$i]['book_id'], $ps_array[$i]['league_id'], $ps_array[$i]['feed_url']);
	$lines->updateLines();
}
//end of update Pinnacle Sports



tep_db_close();//close database connection
?>