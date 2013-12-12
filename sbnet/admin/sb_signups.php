<?php include_once('session.php'); ?>
<?php
	
	//get contest entries
	$sb_sign = get_sb_signups('all');
	
	$textCol = '#000000';
	if( isset($_GET['EXCEL']) )
	{
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=sb_signups.xls");
	}
if( !isset($_GET['EXCEL']) )
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Sportsbetting.net Signups</title>
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
		   <legend style="font-size:12px; font-weight:bold;">Sportsbetting.net Signups</legend>
		   <?php
}//end of if( !isset($_GET['EXCEL']) )
		   $now_date = date('l, F d, Y H:i:s'). ' EST';
		   if( count($sb_sign) == 0)
		   {
		   	  echo '<p> NO DATA FOUND! </p>';
		   }else
		   {
			  if( !isset($_GET['EXCEL']) )
			  {
			  	$down_page = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&EXCEL=1';
			    $table_header = '<table width="100%">
								   <tr>
								     <td colspan="6">
									   <p>&nbsp;<strong>Last Updated:</strong> '.$now_date.'&nbsp;</p>
									 </td>
									 <td valign="top">
									   <p style="float:right"> <a href="'.$down_page.'"><img src="img/download_icon.jpg" border=0></a></p>
									 </td>
								   </tr>
								 </table>';
			  }else
			  {
			  	$table_header = '<table width="100%">
								   <tr>
								     <td colspan="7">
									   <p>&nbsp;Sportsbetting.net Signups &nbsp;</p>
								   	   <p>&nbsp;Last Updated: '.$now_date.'&nbsp;</p>
									  </td>
								   </tr>
								 </table>';
			  }
			  echo $table_header;
		   ?>
		   <table width="100%" cellpadding="2" cellspacing="2" style="font-size: 12px; border: 1px solid #000000;">
		   
		   <tr bgcolor="#808080" style="color:#ffffff;">
		   	 <th width="50" align="center"> ID </th>
		     <th width="200" align="left">  First Name </th>
			 <th width="200" align="left"> Last Name </th>
			 <th width="300" align="left"> Email </th>
			 <th width="150" align="left"> Phone </th>
			 <th width="50" align="left"> SMS </th>
			 <th width="150" align="left"> Username </th>
			 <th width="130" align="left"> Signup Date </th>
		   </tr>
		   
		   <?php
		   $skc = 1;//event counter
		   foreach($sb_sign as $sk => $sv)
		   {
				$bcol = '';
				$tdstyle = '';
				if( ($skc%2) == 0 )
				{
					$bcol = ' bgcolor="#dcdcdc"';
				}
		   ?>
		   
		   <tr<?php echo $bcol; ?>>
		   	 <td align="center"<?php echo $tdstyle; ?>><?php echo $sv['signup_id']; ?></td>
		     <td align="left" <?php echo $tdstyle; ?>><?php echo $sv['first_name']; ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $sv['last_name']; ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $sv['email']; ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $sv['phone']; ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $sv['sms_contact']; ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $sv['username']; ?></td>
			 <td align="left" <?php echo $tdstyle; ?>><?php echo $sv['date_created']; ?></td>
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
?>
