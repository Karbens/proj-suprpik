<?php
require_once('session.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SB CONTESTS ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link type="text/css" rel="stylesheet" href="datepicker_files/styles.css">
<link type="text/css" href="datepicker_files/jquery-ui-1.css" rel="stylesheet">
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: x-small;
}
.style1 {
	color: #FFFFFF;
	font-weight: bold;
}
a:link {
	color: #000000;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #000000;
}
a:hover {
	text-decoration: underline;
	color: #000000;
}
a:active {
	text-decoration: none;
	color: #000000;
}
-->
</style>
<script type="text/javascript" src="datepicker_files/jquery-1.js"></script>
<script type="text/javascript" src="datepicker_files/jquery-ui-1.js"></script>
<script type="text/javascript">
function load_contest()
{
	if( $('#contest_id').val() != '')
	{
		parent.location.href = 'index.php?contest_id='+$('#contest_id').val();
	}
	return false;
}
</script>
</head>

<body bgcolor="#DCDFCC">
<table width="150" cellpadding="2">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#000000">
<?php
$contest_id = 0;
$contests = get_contests();
?>
	<select name="contest_id" id="contest_id" onchange="load_contest();">
	  <option value="">Please Select</option>
	  <?php
	  foreach( $contests as $ct )
	  {
	  	$sel = '';
		if($_GET['contest_id'] == $ct['contest_id'])
		{
		  $contest_id = $ct['contest_id'];
		  $sel = ' selected';
		}
		echo '<option value="'.$ct['contest_id'].'"'.$sel.'>'.$ct['contest_name'].'</option>';
	  }
	  ?>
	</select>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
<?php
if($contest_id > 0)
{
	$contest_info_array = get_contests($contest_id);
	$contest_info = $contest_info_array[0];
	if($contest_info['contest_daily'] == 'Yes')
	{
	  	$cont_page = 'contest_add.php';
	  	$maxDays = '5';
	  	$startDay = "2012/01/13";
	}else
	{
		$cont_page = 'contest_add_other.php';
		$maxDays = '31';
		$startDay = "2012/01/13";
	}
	
?>
<script type="text/javascript">
var theDate = new Date('<?php echo date('Y/m/d`'); ?>');
$(document).ready(function ()
{
<?php
	if($contest_info['contest_daily'] == 'No')
	{
		$contDates = get_contest_dates($contest_id);
		$contArray = array();
		if( count($contDates) > 0)
		{
		  	foreach($contDates as $cdat)
			{
				$cdat = $cdat.' 12:00:00';
				$cdate = date('n-j-Y',strtotime($cdat));
				$contArray[] = '"'.$cdate.'"';
			}
			
			$lastContDate = date('Ymd', strtotime($cdat));
			for($i=1;$i<11;$i++)
			{
			  $ctime = $i*86400;
			  $dtime = strtotime($lastContDate)+$ctime;
			  $ddate = date('n-j-Y', $dtime);
			  if( date('Ymd',$dtime) >= date('Ymd') && $contest_id != 3)
			  {
			    $contArray[] = '"'.$ddate.'"';
			  }
			}
			$startDay = date('Y/m/d', strtotime($contDates[0]));
		}
		else
		{
			$cdat = date('Y-m-d').' 12:00:00';
			$contArray[] = '"'.date('n-j-Y',strtotime($cdat)).'"';
			for($i=1;$i<=30;$i++)
			{
				$ctime = $i*86400;
				$dtime = strtotime($cdat)+$ctime;
				$ddate = date('n-j-Y', $dtime);
				if( date('Ymd',$dtime) >= date('Ymd') )$contArray[] = '"'.$ddate.'"';
			}
			$startDay = date('Y/m/d', strtotime($cdat));
		}
		if($contest_id == 3)
		{
			$startDay = "2012/02/05";
			$contArray = array();
			$contArray[0] = '"2-5-2012"';
			$maxDays = '10';
			echo "pickDate('2012-02-05');";
		}
?>
	/* create an array of days which need to be disabled */
	var contDays = [<?php echo implode(', ', $contArray); ?>];
	
	/* function to load contest schedule */
	function contSchedule(date) {
	  var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	  //console.log('Checking (raw): ' + m + '-' + d + '-' + y);
	  for (i = 0; i < contDays.length; i++) {
	    if($.inArray((m+1) + '-' + d + '-' + y,contDays) != -1) {
	      //console.log('bad:  ' + (m+1) + '-' + d + '-' + y + ' / ' + contDays[i]);
	      return [true];
	    }
	  }
	  //console.log('good:  ' + (m+1) + '-' + d + '-' + y);
	  return [false];
	}
<?php
	}//end of if($contest_info['contest_daily'] == 'No')
?>
	
	$('#datepicker').datepicker(
    {
        minDate: new Date('<?php echo $startDay; ?>'),
        maxDate: "+<?php echo $maxDays; ?>D",
        inline: true,
        onSelect: function (dateText, inst)
        {
            var s = dateText.split('/');
            theDate.setYear(s[2]);
            var m = s[0] - 1;
            theDate.setMonth(m);
            theDate.setDate(s[1]);
			var frmDate = s[2] + '-' + s[0] + '-' + s[1];
			pickDate(frmDate);
        }
<?php
	if($contest_info['contest_daily'] == 'No')
	{
		echo ',beforeShowDay: contSchedule';
	}
?>
    })
});
function pickDate(dateStr) {
      // Do something with the chosen date...
	  parent.frames['mainFrame'].location='<?php echo $cont_page; ?>?contest_id=<?php echo $contest_id; ?>&contestDate='+dateStr;
}
</script>
<?php
  if($contest_id == 1)
  {
?>
  <tr>
    <td bgcolor="#000000"><a href="contest_streakers.php" target="mainFrame"><div align="center" class="style1"> CURRENT STREAKERS </div></a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
<?php
  }
  if($contest_id != 6)
  {
?>
  <tr>
  	<td>
		<div id="datepicker"></div>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <?php
  }else
  {
  ?>
  
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#000000">
	<a href="bracket/index.php" target="mainFrame">
	<div align="center" class="style1"> CURRENT BRACKET </div>
	</a>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor="#000000">
	<a href="bracket_entries.php?contest_id=6&date=2012-03-15&contest_round=All" target="mainFrame">
	<div align="center" class="style1"> VIEW ENTRIES </div>
	</a>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  
  
  <?php
  }
  }//end of if($contest_id > 0)
  ?>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;<a href="sb_signups.php" target="mainFrame"><strong>SB Signups</strong></a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;<a href="logout.php" target="_top"><strong>LOGOUT</strong></a></td>
  </tr>
</table>
</body>
</html>
<?php @mysql_close();?>