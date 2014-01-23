<?php 
	include_once('session.php');

	$contest_id = isset($_REQUEST['contest_id'])? (int)$_REQUEST['contest_id'] : 0;

	$contests = get_contests($contest_id);

	if($contest_id>0){
		$contest = $contests[0];

		$templates = get_templates();

		if(class_exists($contest['template'])){

			$contestObject = new $contest['template']($contest);

		} else {
			exit();

		}

		if(isset($_POST['submit_button'])){
			if(isset($_POST['event'],$_POST['choice'])){
				$events = $_POST['event'];
				$choices = $_POST['choice'];
				$eventtimes = isset($_POST['eventtime'])? $_POST['eventtime']: array();

				$contestObject->updateEvents($events,$choices,$eventtimes);
				header('Location: home.php?contest_id='.$contest_id);
				exit;
			}
		}
	} 



 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Home</title>
		<link rel="stylesheet" media="all" type="text/css" href="css/ui-lightness/jquery-ui-1.8.16.custom.css" />
		<style type="text/css"> 
			body,img,p,h1,h2,h3,h4,h5,h6,form,table,td,ul,li,dl,dt,dd,pre,blockquote,fieldset,label{
				margin:0;
				padding:0;
				border:0;
			}
			h1,h2{ margin: 10px 0; }
			p{ margin: 10px 0; }
			
			pre{ padding: 20px; background-color: #ffffcc; border: solid 1px #fff; }
			.wrapper{ background-color: #ffffff; width: 800px; border: solid 1px #eeeeee; padding: 20px; margin: 0 auto; }
			.example-container{ background-color: #f4f4f4; border-bottom: solid 2px #777777; margin: 0 0 40px 0; padding: 20px; }
			.example-container p{ font-weight: bold; }
			.example-container > dl dt{ font-weight: bold; height: 20px; }
			.example-container > dl dd{ margin: -20px 0 10px 100px; border-bottom: solid 1px #fff; }
			.example-container input{ width: 150px; }
			.clear{ clear: both; }
			#ui-datepicker-div, .ui-datepicker{ font-size: 80%; }
			
		</style> 
		
		<link rel="stylesheet" type="text/css" href="css/jform.css" media="all">
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="js/jquery.validate.js"></script>
		<script type="text/javascript" src="js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
		
<style type="text/css">
* { font-family: Verdana; font-size: 100%; }
label { width: 10em; float: left; }
label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
p { clear: both; }
.submit { margin-left: 12em; }
em { font-weight: bold; padding-right: 1em; vertical-align: top; }
.eventTable td {
    font-size: 11px;
	font-weight: bold;
}
.eventInput {
	width: 700px;
	height: 24px;
	border: 1px solid #000000;
	background-color: #EAF3FB;
	padding: 2px 0 2px 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
	line-height: 23px;
}

.eventtime {
	width: 50px;
	height: 24px;
	border: 1px solid #000000;
	background-color: #EAF3FB;
	padding: 2px 0 2px 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
	line-height: 23px;
}

.delaytime {
	width: 300px;
	height: 24px;
	border: 1px solid #000000;
	background-color: #EAF3FB;
	padding: 2px 0 2px 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
	line-height: 23px;
}

.choiceInput {
	width: 338px;
	height: 24px;
	border: 1px solid #000000;
	background-color: #FFFFDD;
	padding: 2px 0 2px 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
	line-height: 23px;
}

.tdDel {
	vertical-align:top;
	padding-top:10px;
}

textarea {
	width: 300px;
	height: 150px;
	font-family: Arial;
	border: 1px solid #000000;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	padding: 2px 0 2px 5px;
	color: <?php echo $textCol; ?>;
	font-size: 12px;
}

.button_plus {
	background: url("img/button_plus.png") no-repeat scroll 0 0 #FFFFFF;
    display: inline;
    padding: 5px 10px 5px 10px;
    text-decoration: none;
	width: 25px;
	height: 25px;
    background-image: 25px 25px;
}

.button_minus {
	background: url("img/button_minus.png") no-repeat scroll 0 0 #FFFFFF;
    display: inline;
    padding: 5px 10px 5px 10px;
    text-decoration: none;
	width: 25px;
	height: 25px;
    background-image: 25px 25px;
}

.button_remove {
	background: url("img/button_close.png") no-repeat scroll 0 0 #FFFFFF;
    display: inline;
    padding: 5px 10px 5px 10px;
    text-decoration: none;
	width: 25px;
	height: 25px;
    background-image: 25px 25px;
}

.hiddenEdit{
	display: none;
}

.rowOdd{ background:#efefef;}
.hiddenEdit{ width:90%;}

td{padding:5px 0;}

table a:link, table a:visited{ color: #882B08}
table a:active, table a:hover{ color: #BD2B00}
</style>


</head>

<body>

	<div align="center">
		<br>
		<div class="box" style="margin: 10px auto 10px auto;">

		   <?php if($contest_id > 0){ ?>

		 <a href="home.php">&larr;Back</a>

		<br>
		<br>
		 <fieldset>
		   <legend style="font-size:20px; font-weight:bold;"><?php echo $contestObject->contest_name; ?></legend>
		   <p>
		   	<?php echo $contestObject->contest_desc; ?>
		   </p>
		  
		 </fieldset>

		 <div><?php $contestObject->listEvents(); ?>

		 </div>
		 <br/>
		 <a href="home.php">&larr;Back</a>


		   <?php } else{ ?>
		 <fieldset>
		   <legend style="font-size:12px; font-weight:bold;">HOME</legend>
		   <p>
		   Please click on a contest name to select.
		   </p>
		  
			 </fieldset>
			 <table width="100%" cellspacing="0">
			 	<tr>
			 		<th width="30px">ID</th>
			 		<th width="150px">Template</th>
			 		<th width="200px">Contest Name</th>
			 		<th width="550px">Description</th>
			 		<th width="220px">Publish Date</th>
			 		<th width="220px">Cut Off Date</th>
			 		<th width="90px">Status</th>
			 		<th width="80px">Action</th>
			 	</tr>
			 	<?php foreach ($contests as $key => $contest) { ?>
			 	<tr class="<?php echo $key%2?'rowEven':'rowOdd'; ?>" id="editContest_<?php echo $contest['contest_id']; ?>" valign="top">
			 		<td>
			 			<?php echo $contest['contest_id']; ?>
			 		</td>
			 		<td><?php echo $contest['template']; ?></td>
			 		<td>
			 			<span class="shownEdit">
			 				<a href="?contest_id=<?php echo $contest['contest_id']; ?>"><?php echo $contest['contest_name']; ?></a>
			 			</span>			 			
			 			<input class="hiddenEdit" type="text" value="<?php echo $contest['contest_name']; ?>" name="contest[name]" />
			 		</td>
			 		<td>
			 			<span class="shownEdit">
			 				<?php echo $contest['contest_desc']; ?>
			 			</span>
			 			<textarea class="hiddenEdit" name="contest[desc]"><?php echo $contest['contest_desc']; ?></textarea>
			 		</td>
			 		<td>
			 			<span class="shownEdit">
			 				<?php echo date( 'Y-m-d H:i', strtotime($contest['contest_publish_date']) ); ?>
			 			</span>
			 			<input class="hiddenEdit datepicker" type="text" value="<?php echo date( 'Y-m-d', strtotime($contest['contest_publish_date']) ); ?>" name="contest[contest_publish_date]" />

			 		</td>
			 		<td>
			 			<span class="shownEdit">
			 				<?php echo date( 'Y-m-d H:i', strtotime($contest['current_contest_date']) ); ?>
			 			</span>
			 			<input class="hiddenEdit datetimepicker" type="text" value="<?php echo date( 'Y-m-d H:i:s', strtotime($contest['current_contest_date']) ); ?>" name="contest[current_contest_date]" />

			 		</td>
			 		<td>
			 			<span class="shownEdit">
				 			<?php echo $contest['status']; ?>			 			
			 			</span>
			 			<select class="hiddenEdit" name="contest[status]">
			 				<option value="0"<?php if($contest['status']=='Offline') echo 'selected="selected"'; ?>>Offline</option>
			 				<option value="1"<?php if($contest['status']=='Online') echo 'selected="selected"'; ?>>Online</option>
			 		</td>
			 		<td><a href="" class="editContest" data-target="<?php echo $contest['contest_id']; ?>" style=>Edit</a></td>
			 	</tr>
			 	<?php } ?>
			 </table>
			 <br><br>

	  	 <?php } ?>
		</div>
	</div>

	<script type="text/javascript">
		$(function(){
			
			$('.datetimepicker').datetimepicker({dateFormat:"yy-mm-dd",minDate: 0});
			$('.datepicker').datepicker({dateFormat:"yy-mm-dd",minDate: 0});

			$('.editContest').click(function(e){
				if($(this).html()=='Save'){

					data = $('#editContest_'+$(this).data('target')+' .hiddenEdit').serialize();
					data += '&contest_id='+$(this).data('target')+'&action=update_contest';

					$.post( "ajax.php",data, function( data ) {
							if(data.success=='true') {
								window.location.href = window.location.href;
							}
					});

				}

				$('#editContest_'+$(this).data('target')+' .hiddenEdit').show();
				$('#editContest_'+$(this).data('target')+' .shownEdit').hide();
				$(this).html("Save");
				e.preventDefault();

			});
		});
	</script>
</body>

</html>