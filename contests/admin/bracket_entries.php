<?php include_once('session.php'); ?>
<?php
	$contest_id = $_GET['contest_id'];
	$contest_date = $_GET['date'];
	$contest_round = $_GET['contest_round'];
	//get contest entries
	$entries = get_bracket_entries();
	$textCol = '#000000';
	if( isset($_GET['EXCEL']) )
	{
		$econtest_info = get_contests($contest_id);
		$econtest_name = str_replace(' ', '_', $econtest_info[0]['contest_name']);
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$econtest_name.".xls");
	}
if( !isset($_GET['EXCEL']) )
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Contest Entries</title>
		<link rel="stylesheet" media="all" type="text/css" href="css/ui-lightness/jquery-ui-1.8.16.custom.css" />
		<style type="text/css"> 
			body,img,p,h1,h2,h3,h4,h5,h6,form,table,td,ul,li,dl,dt,dd,pre,blockquote,fieldset,label{
				margin:0;
				padding:0;
				border:0;
			}
			h1,h2{ margin: 10px 0; }
			p{ margin: 10px 0; }
			.box th {
			    background: none repeat scroll 0 0 #4D4D4D;
			    border: 0px solid #32AC33;
			    color: #FFFFFF;
			    padding: 5px;
				margin: 5px 5px 5px 10px;
			}
			.box td {
			    padding: 5px;
			}
		</style> 
		
		<link rel="stylesheet" type="text/css" href="css/jform.css" media="all">
		<script type="text/javascript" src="js/jquery-latest.js"></script>
	<script type="text/javascript">

		  function popUp(URL) {
			window.open(URL, 'Terms', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=960,height=600,left = 300,top = 100');
		}
	</script>	
<style type="text/css">
* { font-family: Verdana; font-size: 96%; }
label { width: 10em; float: left; }
label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
p { clear: both; }
.submit { margin-left: 12em; }
em { font-weight: bold; padding-right: 1em; vertical-align: top; }
.eventTable td {
    font-size: 11px;
	font-weight: bold;
}
</style>

</head>

<body>

	<div align="center">
		<br>
		<div class="box" style="margin: 10px auto 10px auto;">
		 <fieldset>
		   <legend style="font-size:12px; font-weight:bold;">CONTEST ENTRIES</legend>
		   <?php
}//end of if( !isset($_GET['EXCEL']) )
		   if( count($entries) == 0)
		   {
			  //contest values
			  $contest_info = get_contests($contest_id);
			  $contest_name = $contest_info[0]['contest_name'];
			  //table header
			  $table_header = '<table width="100%"><tr><td colspan=6>'.
			  				  '<p> <b>Contest:</b> <a href="bracket/index.php" style="color:green; font-weight:bold;">'.$contest_name.'</a> </p>'.
							  '<p> <b>End Date:</b> '.$contest_date.' </p>'.
							  '<p> NO DATA FOUND! </p>'.
							  '</td>'.
							  '</tr>'.
							  '</table>';
			  echo $table_header;
		   }else
		   {
			  //contests graded or not
			  $correct_entries = bracket_graded_result($contest_round);
			  $grade_result = (count($correct_entries) > 0) ? 'Lost' : 'Pending';
			  $contest_result = '';
			  foreach($correct_entries as $rn => $ce)
			  {
			  	if($contest_round == 'All')
				{
					$contest_result .= '<br><a href="bracket_entries.php?contest_id=6&date=2012-03-15&contest_round='.$rn.'" style="color:green; font-weight:bold;">Round '.$rn.'</a>: ';
				}
				$contest_result .= (count($correct_entries[$rn]) > 0) ? implode(' | ',$correct_entries[$rn]) : 'Pending';
			  }
			  if($contest_round != 'All' && $contest_round > 0 && $contest_result == '')$contest_result = 'Pending';
			  
			  if($contest_round == 'All')
			  {
				  $np = $rn+1;
				  for($p=$np;$p<7;$p++)
				  {
				  	$contest_result .= '<br><a href="bracket_entries.php?contest_id=6&date=2012-03-15&contest_round='.$p.'" style="color:green; font-weight:bold;">Round '.$p.'</a>: Pending';
				  }
			  }
			  
			  //contest values
			  $contest_info = get_contests($contest_id);
			  $contest_date_time = $contest_info[0]['current_contest_date'] . ' NOON EST';
			  $contest_name = $contest_info[0]['contest_name'];
			  $table_header = '<table width="100%"><tr><td colspan=7>'.
			  				  '<p> <b>Contest:</b>  <a href="bracket/index.php" style="color:green; font-weight:bold;">'.$contest_name.'</a> </p>'.
							  '<p> <b>End Date:</b> '.$contest_date_time.' </p>'.
							  '<p> <b>Round:</b> '.$contest_round.' </p>'.
							  '<p> <b>Result:</b> '.$contest_result.' </p>'.
							  '</td>';
			  if( !isset($_GET['EXCEL']) ){
			  	$down_page = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&EXCEL=1';
			    $table_header .= '<td valign="top"><p style="float:right"> <a href="'.$down_page.'"><img src="img/download_icon.jpg" border=0></a> </p></td>';
			  }
			  $table_header .= '</tr></table>';
			  echo $table_header;
		   ?>
		   <table width="100%" cellpadding="2" cellspacing="2" style="font-size: 12px; border: 1px solid #000000;">
		   
		   <tr bgcolor="#808080" style="color:#ffffff;">
		   	 <th width="50" align="center"> # </th>
		     <th width="150" align="left"> Customer ID </th>
			 <th width="200" align="left"> Email </th>
			 <th width="50" align="left"> Site </th>
			 <th width="100" align="left"> Entry Date Time </th>
			 <th width="<?php echo ($contest_id == 1) ? '200' : '600'; ?>" align="left"> Picks </th>
			 <th width="50" align="center"> Result </th>
			 <?php if( !isset($_GET['EXCEL']) ){ ?>
			 <th width="50" align="center"> Bracket </th>
			 <?php } ?>
		   </tr>
		   
		   <?php
		   $skc = 1;//event counter
		   foreach($entries as $ek => $ev)
		   {
				$bcol = '';
				$tdstyle = '';
				if( ($skc%2) == 0 )
				{
					$bcol = ' bgcolor="#dcdcdc"';
				}
				
				$result = $grade_result;
				if($ev['points'] > 0)
				{
					$result = 'Won';
					$tdstyle = ' style="font-weight:bold;"';
				}
				
				//overall count for correct answers
				$ec_array = bracket_values_count($contest_round, $correct_entries, $ev['ANSWERS']);
				$ec_count = $ec_array['count'];
				$choices = $ec_array['str'];
				$to_count = bracket_total_count($contest_round);
				$half_count = ceil($to_count/2);
				$result = ($ec_count >= $half_count) ? ' <b>'.$ec_count.'/'.$to_count.'</b> ' : ' '.$ec_count.'/'.$to_count.' ';
				/*
				$choice_array = get_entry_choices($ev['entry_value']);
				if( (count($choice_array)>0) )
				{
					$choices_array = array();
					foreach($choice_array as $ck => $cv)
					{
						if( isset($correct_entries[$ck]) )
						{
							$choices_array[] = '<b>'.$cv.'</b>';
						}else
						{
							$choices_array[] = $cv;
						}
					}
					$choices = implode(' | ',$choices_array);
				}else
				{
					$choices = '';
				}
				*/
				//site of customer (sb.net or sb.com)
				$site = 'sb.com';
				$cust_id = $ev['USERID'];
				if( isset($signUps[$cust_id]) && $signUps[$cust_id] == $ev['EMAIL'] )
				{
					$site = 'sb.net';
				}
		   ?>
		   
		   <tr<?php echo $bcol; ?>>
		   	 <td align="center"<?php echo $tdstyle; ?>><?php echo $skc; ?></td>
		     <td align="left" <?php echo $tdstyle; ?>><?php echo $cust_id; ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $ev['EMAIL']; ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $site; ?></td>
			 <td align="left" <?php echo $tdstyle; ?> nowrap><?php echo date('Y-m-d H:i:s',$ev['CREATED']); ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $choices; ?></td>
			 <td align="center" <?php echo $tdstyle; ?>><?php echo $result; ?></td>
			 <?php if( !isset($_GET['EXCEL']) ){ ?>
			 <td align="center"> <a href="javascript:Void('0');" onclick="popUp('../marchmania/mybracket.php?userid=<?php echo $cust_id; ?>&userem=<?php echo $ev['EMAIL']; ?>');" style="color:green;font-weight:bold;"> View </td>
			 <?php } ?>
		   </tr>
		   
		   <?php
		   		$skc++;
		   }//end of foreach($events as $ev)
		   ?>
		   
		   </table>
		   <?php
		   }
if( !isset($_GET['EXCEL']) )
{
		   ?>
		 </fieldset>
		 
		 <br><br>
		</div>
	</div>
</body>

</html>

<?php
}//end of if( !isset($_GET['EXCEL']) )
mysql_close();
?>
