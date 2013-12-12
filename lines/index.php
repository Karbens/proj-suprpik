<?php
//set the time zone
date_default_timezone_set('America/Vancouver');
define('_VALID_MOS', '1');//for accessing db_func.php
include_once('db_func.php');
tep_db_connect();

$leagues_array   =  getAllLeagues();
$sports_settings =  get_sports_settings();
$def_league      =  $sports_settings['default'];

//set the date value
$cur_date = date('Y-m-d');
if(isset($_GET['date']))$cur_date = $_GET['date'];

//set the sport value
$league = ( isset($leagues_array[$def_league]) ) ? $leagues_array[$def_league] : "nfl";
if(isset($_GET['league']))$league = $_GET['league'];

//set the period value
$period = 0;
if(isset($_GET['period']))$period = $_GET['period'];

if($league != '')
{
  $league_params = getLeagueParams($league);
  $league_sport = $league_params['sport'];
  $league_id = $league_params['league_id'];
  if($league_sport == 'Football')
  {
  	$minDate = '"-15D"';
	$maxDate = '"+2W"';
  }else
  {
  	$minDate = '"-15D"';
	$maxDate = '"+1D"';
  }
  $leg_val = strtoupper($league) . ' - ';
}


?>
<html>
   <head>
      <title>Live Odds</title>
      <meta name="keywords" content="offshore sports betting, online sportsbook, online gambling, internet articles, free picks, betting odds, sports forum, sports betting, online sports betting, sports betting, tips, sports betting lines, sportsbook, online gambling casino, offshore sportsbook, offshore gambling"/>
      <meta name="description" content="Online offshore sports betting, internet sportsbook and online gambling. Daily internet articles on gaming industry, free picks, odds, list of reputable and disreputable sites, sports forum. "/>
      <meta name="language" content="english"/>
      <meta name="robots" content="index, follow"/>
      <meta name="revisit-after" content="1 days"/>
      <meta name="document-rights" content="Copyrighted Work"/>
      <meta name="document-type" content="Web Page"/>
      <meta name="document-rating" content="General"/>
      <meta name="document-distribution" content="Global"/>
      <meta name="cache-control" content="Public"/>
      <meta http-equiv="Content-Language" content="EN"/>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	  
<!-- Start of Calendar files -->
<style>
#datepicker {float:right;}
body, table {
	font-size:		12px;
	color:			#0e3056;
}
</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.7.custom.min.js"></script>
<link type="text/css" href="css/redmond/jquery-ui-1.8.7.custom.css" rel="stylesheet" />	
<!-- end of files for calendar -->

  <link rel="stylesheet" type="text/css" href="lines_files/style2.css" />
  <link rel="stylesheet" type="text/css" href="lines_files/rx_menu.css" />
  <link rel="stylesheet" type="text/css" media="all" href="lines_files/odds.css?version=20120412">
  <link rel="stylesheet" type="text/css" href="lines_files/stats.css" />
  <script language="JavaScript" src="lines_files/combined_Obfs.js?version=20120413" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="lines_files/statmenu.css" />
  <link rel="stylesheet" type="text/css" href="lines_files/so_style.css" />
  <script src="lines_files/AC_RunActiveContent.js" type="text/javascript"></script>

      <script src="lines_files/flash_import.js" type="text/javascript">
      </script>
      <SCRIPT TYPE="text/javascript">
         function popup(mylink, windowname)
         {
            if (! window.focus)return true;
            var href;
            if (typeof(mylink) == 'string')
            href=mylink;
            else
            href=mylink.href;
            window.open(href, windowname, 'width=567,height=427,scrollbars=no');
            return false;
         }
         function startMenuForIE(sElementId) {
         if (document.all && document.getElementById) {
         //var navRoot = document.getElementById("mainNav");
         var navRoot = document.getElementById(sElementId);
         if (navRoot) {
         var liNodes = navRoot.getElementsByTagName("LI");
         for (var i=0; i < liNodes.length; i++) {
         node = liNodes[i];
         node.onmouseover=function() {
         this.className+=" over";
         positionTopBlocker(this);
      }
      node.onmouseout=function() {
      this.className=this.className.replace(" over", "");
      positionTopBlocker();
   }
   }//end for
}
}// end if
}
function positionTopBlocker(oNode)
{
// Size and position iframe to block out windowed objects in IE
var oframe=document.getElementById("topBlockframe");
if (oNode) {
var ulNodes = oNode.getElementsByTagName("UL");
if (ulNodes.length > 0) {
oframe.style.display = "block";
oframe.style.top = oNode.offsetTop + ulNodes[0].offsetTop;
oframe.style.left = oNode.offsetLeft + ulNodes[0].offsetLeft;
oframe.width = ulNodes[0].offsetWidth;
oframe.height = ulNodes[0].offsetHeight;
}
}
else
{
oframe.style.display = "none";
}
}
// Prepares dynamic menu for IE
startList = function() {
startMenuForIE("mainnav");
startMenuForIE("tickernav");
startMenuForIE("nhltickernav");
}
window.onload=startList;// Load script to handle drop-down menu in IE
</script>
<script language="javascript">
var timeout	= 500;
var closetimer	= 0;
var ddmenuitem	= 0;

// open hidden layer
function mopen(id)
{	
	// cancel close timer
	mcancelclosetime();

	// close old layer
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';

	// get new layer and show it
	ddmenuitem = document.getElementById(id);
	ddmenuitem.style.visibility = 'visible';

}
// close showed layer
function mclose()
{
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';
}

// go close timer
function mclosetime()
{
	closetimer = window.setTimeout(mclose, timeout);
}

// cancel close timer
function mcancelclosetime()
{
	if(closetimer)
	{
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

// close layer when click-out
document.onclick = mclose; 
</script>
<!--
Lightbox Functionality
-->
<script type="text/javascript" language="JavaScript" src="lines_files/popupimg.js">
</script>
<link rel="stylesheet" href="lines_files/lightbox.css" type="text/css" media="screen" />
<script src="lines_files/prototype.js" type="text/javascript">
</script>
<script src="lines_files/scriptaculous.js?load=effects" type="text/javascript">
</script>
<script src="lines_files/lightbox.js" type="text/javascript">

</script>
</head>
<body OnUnLoad="checkCount()" onLoad="javascript:get_books();">
<div align="center">
<div id="bdcontainer" align="center">
<table id="settingsTable" width="970" cellspacing="0" style="margin-top:14px; display:none;">
<tr bgcolor="#A2A2A2">
<td>

	<table cellpadding="5" cellspacing="0" border="0" height="78" style="background: -moz-linear-gradient(center top , #FFFFFF 0%, #E6E6E6 100%) repeat scroll 0 0 transparent;">
	<tr>
		<td colspan="4">
			<table cellpadding="0" cellspacing="0" border="0" height="68" width="220" background="lines_files/keybg4.png">
			<tr>
			<td colspan="5" height="8"><img src="lines_files/b.png" height="8"></td></tr>
			<tr>
			<td width="34" height="16"><br /></td>
			<td width="16" height="16" id="show_2_minute_color" class="redbg-right"><img src="lines_files/b.png" height="16"></td>
			
			<td width="84" height="16"><font face="arial" size="1" color="#ffffff">&nbsp;&nbsp; < 2 min</font></td>
			<td width="16" height="16" id="show_5_minute_color" class="greenbg-right"><img src="lines_files/b.png" height="16"></td>
			<td width="70" height="16"><font face="arial" size="1" color="#ffffff">&nbsp;&nbsp; 2 - 5 min</font></td>
			</tr>
			<tr><td colspan="5" height="2"><img src="lines_files/b.png" height="2"></td></tr>
			<tr>
			<td width="34" height="16"><br /></td>
			<td width="16" height="16" id="show_10_minute_color" class="yellowbg-right"><img src="lines_files/b.png" height="16"></td>
			<td width="84" height="16"><font face="arial" size="1" color="#ffffff">&nbsp;&nbsp; 5 - 10 min</font></td>
			<td width="16" height="16" class="whitebg-right"><img src="lines_files/b.png" height="16"></td>
			<td width="70" height="16"><font face="arial" size="1" color="#ffffff">&nbsp;&nbsp; > 10 min</font></td>
			
			</tr>
			<tr><td colspan="5" height="2"><img src="lines_files/b.png" height="2"></td></tr>
			<tr>
			<td width="34" height="16"><br /></td>
			<td width="16" height="16" id="show_game_gone_color" class="pink-game-gone-left"><img src="lines_files/b.png" height="16"></td>
			<td colspan="3"><font face="arial" size="1" color="#ffffff">&nbsp;&nbsp; Game has already started</font></td>
			</tr>
			<tr><td colspan="5" height="8"><img src="lines_files/b.png" height="8"></td></tr>
			</table>
		</td>
		
		<td width="134"> <!--Get changes every --><input type="hidden" size="4" id="changes_interval" value="60"><!-- seconds-->
		<?php
		echo '<input type="hidden" name="league_sport" id="league_sport" value="'.$league_sport.'|'.$league_id.'">'."\n";
		?>
			<table cellpadding="0" cellspacing="0" border="0" height="78" width="130">
			<tr><td width="134" valign="center" height="5"><img src="lines_files/b.png" height="5"></td></tr>
			<tr><td width="134" valign="center" height="17"><input type="image" value="Clear Changes" src="lines_files/lineclear.png" onClick="clear_changes()"></td></tr>
			<tr><td width="134" valign="center" height="8"><img src="lines_files/b.png" height="8"></td></tr>
			<tr><td width="134" valign="center" height="17"><input id="toggle_game_gone_button" type="image" src="lines_files/linehide.png" value="Hide started games" onClick="toggle_game_gone()"></td></tr>
			<tr><td width="134" valign="center" height="8"><img src="lines_files/b.png" height="8"></td></tr>
			<tr><td width="134" valign="center" height="17"><input type="image" src="lines_files/lineoptions.png" value="Options" onClick="display_options_popup()"></td></tr>
			<tr><td width="134" valign="center" height="5"><img src="lines_files/b.png" height="5"></td></tr>
			</table>
		</td>
		<td width="18"><br /></td>
	</tr>
	</table>
</td>
<td>&nbsp;</td>
</tr>
</table>
<div style="position:absolute; visibility:hidden; z-index:1000; width:300px; height:425px; background-color:#FFFFFF;" name="options_popup" id="options_popup">

<form name="options_form">
<table width="100%" height="250px" border="1" bgcolor="#FFFFFF" style="text-align:center;">
	<tr>
		<td valign="center" colspan="4">
			<font size="3"><b>Color Options</b></font>
			<br>
			(Click a color to change your setting)
		</td>
	</tr>
	<tr>
		<td>
			<b>Started-game color settings:</b>
		</td>
		<td colspan="3">
			<b>Line-change color settings:</b>
		</td>
	</tr>
	<tr>
	  <td>&nbsp;&nbsp;Started-game background:
		<table>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td class="game-gone" id="game_gone_color">&nbsp;Current&nbsp;</td>
				<td>&nbsp;</td>
				<td class="game-gone"      style="cursor:pointer;" onClick="select_color(0, 'game-gone');"     >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="pink-game-gone" style="cursor:pointer;" onClick="select_color(0, 'pink-game-gone');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="whitebg"        style="cursor:pointer;" onClick="select_color(0, 'whitebg');"       >(None)</td>
			</tr>
		</table>
	  </td>
	  <td>&nbsp;&nbsp;Less than 2 minutes:
		<table>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td class="redbg" id="changes_2_minutes">&nbsp;Current&nbsp;</td>
				<td>&nbsp;</td>
				<td class="redbg"    style="cursor:pointer;" onClick="select_color(2, 'redbg');"   >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="yellowbg" style="cursor:pointer;" onClick="select_color(2, 'yellowbg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="greenbg"  style="cursor:pointer;" onClick="select_color(2, 'greenbg');" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="bluebg"   style="cursor:pointer;" onClick="select_color(2, 'bluebg');"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="purplebg" style="cursor:pointer;" onClick="select_color(2, 'purplebg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="orangebg" style="cursor:pointer;" onClick="select_color(2, 'orangebg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
		</table>
	  </td>
	  <td>&nbsp;&nbsp;2 - 5 minutes:
		<table>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td class="yellowbg" id="changes_5_minutes">&nbsp;Current&nbsp;</td>
				<td>&nbsp;</td>
				<td class="redbg"    style="cursor:pointer;" onClick="select_color(5, 'redbg');"   >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="yellowbg" style="cursor:pointer;" onClick="select_color(5, 'yellowbg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="greenbg"  style="cursor:pointer;" onClick="select_color(5, 'greenbg');" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="bluebg"   style="cursor:pointer;" onClick="select_color(5, 'bluebg');"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="purplebg" style="cursor:pointer;" onClick="select_color(5, 'purplebg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="orangebg" style="cursor:pointer;" onClick="select_color(5, 'orangebg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
		</table>
	  </td>
	  <td>&nbsp;&nbsp;5 - 10 minutes:
		<table>
			<tr>
				<td>&nbsp;&nbsp;</td>
				<td class="greenbg" id="changes_10_minutes">&nbsp;Current&nbsp;</td>
				<td>&nbsp;</td>
				<td class="redbg"    style="cursor:pointer;" onClick="select_color(10, 'redbg');"   >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="yellowbg" style="cursor:pointer;" onClick="select_color(10, 'yellowbg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="greenbg"  style="cursor:pointer;" onClick="select_color(10, 'greenbg');" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="bluebg"   style="cursor:pointer;" onClick="select_color(10, 'bluebg');"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="purplebg" style="cursor:pointer;" onClick="select_color(10, 'purplebg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="orangebg" style="cursor:pointer;" onClick="select_color(10, 'orangebg');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
		</table>
	  </td>
	</tr>
	<tr>
		<td colspan="4">
			<input type="button" onClick="save_and_hide_popup();" value="Save">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" onClick="cancel_and_hide_popup();" value="Cancel">
		</td>
</table>
</form>
</div>

<div id="odds_table"><h2>Loading page...</h2><hr></div>
<font color="#FFFFFF"><div id="last_check"></div></font>
<div id="debug"></div>

<script language="JavaScript">
table_width=970;
sport_name = '<?php echo $league; ?>';
page_period = '<?php echo $period; ?>';
books_url = 'books.xml?version=20120404';
lines_url = 'linesFeeds.php?date=<?php echo $cur_date; ?>&league='+sport_name+'&period='+page_period;
changes_url = 'linesFeeds.php?date=<?php echo $cur_date; ?>&league='+sport_name+'&period='+page_period;
line_history_url = 'linesData.php?host=SuperPicks';
clear_time = GetCookie ("clear-time-" + sport_name + page_period);
if (!clear_time)
  clear_time = 0;
options_popup = document.getElementById ("options_popup");
set_options_popup_values ();
/*function F(book,number,game_date)
{
	url = line_history_url + '&book=' + book + 	
							 '&game=' + number + 
							 '&period=' + page_period +
							 '&date=' + game_date.substring(0,4) + '-' + game_date.substring(4,6) + '-' + game_date.substring(6);
	winref = window.open(url,'LH'+(lh_count++),'width=600,height=400,resizable=1,scrollbars=1;');
}*/
</script>




</div>
</div>
</body>

</html>