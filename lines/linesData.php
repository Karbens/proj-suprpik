<?php
//book=3&game=1&period=0&date=2011-03-28
date_default_timezone_set('America/Vancouver');
define('_VALID_MOS', '1');//for accessing db_func.php
$nowtime = strtotime("now");//set current time stamp
//if there is a get parameter for league
if($_GET['game']>0 && $_GET['book']>0 && $period>=0)
{
  $gid = $_GET['game'];
  $bid = $_GET['book'];
  $period = $_GET['period'];
  $date = date('Y-m-d');
  if($_GET['date'])$date = $_GET['date'];
  include_once('db_func.php');
  tep_db_connect();
  
  //get league_id
  $game1 = getGameByID($gid , $date);
  $league_id = $game1['league_id'];
  $league_sport = getLeagueSport($league_id);
  //set short_name
	$short_name = "Game";
	if($period != 0)
	{
		if($league_id == 1 && $period == 1)
		{
			$short_name = "5inn.";
		}
		if($league_id == 2 && $period != 3)
		{
			switch($period)
			{
				case '1':
					$short_name = "1H";
					break;
				case '2':
					$short_name = "2H";
					break;
				default:
					$short_name = $period;
			}
		}
	}
	//end of set short_name
  $game = getGameByID($gid , $date, $short_name);
  $gdate = date('m/d/y h:ia',$game[game_seconds]);
  $p_array = array(
  				   "league_id"    	=> 		$league_id,
				   "league_sport" 	=>		$league_sport,
				   "bid" 	    	=> 		$bid,
				   "gid" 	    	=>		$game['game_id'],
				   "date" 			=>		$date,
				   "period"			=>		$period,
				   "vid"			=>		$game['rot_visitor'],
				   "hid"			=>		$game['rot_home'],
				   "away"			=>		$game['team_visitor'],
				   "home"			=>		$game['team_home']
  				  );
  $booklogs = getBookLogs($p_array);
  $booktotals = getBookTotals($p_array);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Lines Data</title>
	<style type="text/css">
	.title  {  
		background-color: #242424;
	    color: #FFFFFF;
	    font-family: "Helvetica",Arial,sans-serif;
	    font-size: 10pt;
	    font-style: normal;
	    font-weight: bold;
	    padding: 3px;
	    text-align: center;
	    text-decoration: none;
	}
	.header  {  
	   -moz-border-bottom-colors: none;
	   -moz-border-image: none;
	   -moz-border-left-colors: none;
	   -moz-border-right-colors: none;
	   -moz-border-top-colors: none;
	   background: none repeat scroll 0 0 #DCDCDC;
	   color: #000000;
	   font-family: "Helvetica",Arial,sans-serif;
	   font-size: 12px;
	   font-weight: bold;
	   height: 16px;
	   line-height: 15px;
	   padding: 5px;
	   text-align: center; 
	}
	.data  {  
		font-family: "Helvetica",Arial,sans-serif; 
		font-size:        9pt;
		color:            #000000;  
		background-color: #FFFFFF;  
	}
	</style>
</head>

<body>

<table width="400" cellspacing="1" cellpadding="3" border="0" bgcolor="#000000"> 
<tbody>
<tr><th class="title" colspan="3"><?php echo $game['rot_visitor']; ?> <?php echo $game['team_visitor']; ?> @ <?php echo $game['rot_home']; ?> <?php echo $game['team_home']; ?> </th></tr>  
<tr><th class="title" colspan="3"><?php echo $gdate;?> Pacific</th></tr>
<tr>
  <th width="166" class="header">Pacific Time</th>
  <th width="103" class="header"><?php echo $game['team_visitor']; ?></th>
  <th width="109" class="header"><?php echo $game['team_home']; ?></th>
</tr>
<?php 
if(count($booklogs)>0 || count($booktotals)>0)
{
?>
	<?php 
	if(count($booklogs)>0)
	{
		foreach($booklogs as $bl)
		{
		?>
		<tr>
		  <td class="data"><?php echo $bl['datetime']; ?></td>
		  <td class="data"><?php echo htmlspecialchars_decode($bl['vvalue'], ENT_NOQUOTES); ?></td>
		  <td class="data"><?php echo htmlspecialchars_decode($bl['hvalue'], ENT_NOQUOTES); ?></td>
		</tr>
		<?php 
		}
	}
	?>
	<tr>
	  <th class="header">&nbsp;</th>
	  <th class="header" colspan="2">Totals</th>
	</tr>
	<?php 
	if(count($booktotals)>0)
	{
		foreach($booktotals as $bt)
		{
		?>
		<tr>
		  <td class="data"><?php echo $bt['datetime'];?></td>
		  <td class="data" colspan="2">
		  <?php echo htmlspecialchars_decode($bt['vvalue'], ENT_NOQUOTES); ?> 
		  <?php echo htmlspecialchars_decode($bt['hvalue'], ENT_NOQUOTES); ?>
		  </td>
		</tr>
		<?php 
		}
	}
	?>
<?php 
}//if(count($booklogs)>0 || count($booktotals)>0)
else
{
?>
	<tr>
	  <td class="data" colspan="3" align="center">
	  No Data Found.
	  </td>
	</tr>
<?php 
}
?>
</tbody>
</table>


</body>
</html>
<?php 
  tep_db_close();
}
?>
