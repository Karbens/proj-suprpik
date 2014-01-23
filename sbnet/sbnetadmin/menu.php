<?php
require_once('session.php');

$hide_menu = true;

$templates = get_templates();


 if(isset($_GET['action']) && $_GET['action']=='contest_create'){
 	$hide_menu = false;
 	 	 	
	if(isset($_POST['template'],$_POST['contest_name'],$_POST['contest_desc'],$_POST['status'])){

		$template = trim($_POST['template']);
		

		if(class_exists($template)){
			$contestObject = new $template;
			$output = $contestObject->create_contest($_POST);

			if(isset($output['message']))	$_SESSION['message'] = $output['message'];
			
			header('Location: menu.php');
			exit();
		}else{
			$error = true;
			$_SESSION['message'] = 'Please select a contest template.';
		}

	}

}




?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SB CONTESTS ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" media="all" type="text/css" href="css/ui-lightness/jquery-ui-1.8.16.custom.css" />
<style type="text/css">
body{background: #ECECEC}
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
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
#contestCreator{
	max-width: 250px;
	text-align: left;
}

#contestCreator label{
	float: left;
}

/* css for timepicker */
.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
.ui-timepicker-div dl { text-align: left; }
.ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
.ui-timepicker-div dl dd { margin: 0 10px 10px 45%; }
.ui-timepicker-div td { font-size: 90%; }
.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

.ui-timepicker-rtl{ direction: rtl; }
.ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
.ui-timepicker-rtl dl dt{ float: right; clear: right; }
.ui-timepicker-rtl dl dd { margin: 0 45% 10px 10px; }
</style>
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">

$(function(){
	$('#template').change(function(){
		if($(this).val()=='') $('#contestCreator').hide('slow');
		else $('#contestCreator').show('slow');
	});
	//  $("#datepicker").datepicker({dateFormat:'yy-mm-dd',minDate: 0});
	  $("#datetimepicker, #datepicker").datetimepicker({dateFormat:'yy-mm-dd',minDate: 0});

	  $('#createContest').submit(function(){
	  	var publishDate = $("#datepicker").datepicker( "getDate" );
	  	var cutoffDate = $("#datetimepicker").datepicker( "getDate" );

	  	if(publishDate > cutoffDate){
	  		alert('Publish date cannot be greater than Cut off date');
	  			return false;
	  	}
	  });

	<?php
	if(isset($_SESSION['message'])){ ?>
		alert("<?php show_session_message(); ?>");
		mainFrame = parent.document.getElementById('mainFrame');
		mainFrame.src = mainFrame.src
	<?php }	?>
});
</script>
</head>

<body bgcolor="#DCDFCC">
<table cellpadding="2" width="100%">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
    	Create New Contest
	<form id="createContest" action="menu.php?action=contest_create" method="post">
		<select name="template" id="template">
		  <option value="">Please select</option>
		  <?php
		  foreach( $templates as $template )
		  {
		  	$sel = '';
		//	if($_GET['template'] == $template){
		//	  $sel = ' selected';
		//	}
			echo '<option value="'.$template.'"'.$sel.'>'.$template.'</option>';
		  }
		  ?>
		</select>
		<div id="contestCreator"<?php echo $hide_menu?'style="display:none"':''; ?>>
			<div style="margin:20px 0; float:left;">
				<label>Contest Name<input type="text" name="contest_name" value="" required="required" /></label>
			</div>
			<div style="margin:0px 0 20px; float:left;">
				<label>Contest Description<textarea name="contest_desc" rows="12" required="required"></textarea></label>
			</div>

			<div style="margin:0px 0 20px; float:left;">
			Status<br/>
			<label><input required="required" type="radio" name="status" value="0" /> Inactive</label><br/><br/>
			<label><input required="required" type="radio" name="status" value="1" checked="checked" /> Active</label><br/>
			</div>

			<div style="margin:0px 0 20px; float:left;">
				<label>Publish Date<input required="required" type="text" name="contest_publish_date" value="" id="datepicker"/>
				</label>
			</div>

			<div style="margin:0px 0 20px; float:left;">
				<label>Cut off Date<input required="required" type="text" name="current_contest_date" value="" id="datetimepicker"/>
				</label>
			</div>

			<div style="margin:0px 0 20px; float:left;">
				<input id="contestCreate" type="submit" value="Create Contest"/>
			</div>
		</div>
	</form>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
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
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Current Time:</td>
  </tr>
  <tr>
    <td><?php echo date('d F Y h:ia'); ?></td>
  </tr>
  <tr>
</table>
</body>
</html>