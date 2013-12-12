<?php
$userid = ( isset($_GET['userid']) ) ? $_GET['userid'] : '';
$userem = ( isset($_GET['userem']) ) ? $_GET['userem'] : '';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Free Contests - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="../i/sbtfav2.png" />
	<link rel="stylesheet" type="text/css" href="../css/phoenix.css" />
	<link rel="stylesheet" type="text/css" href="../css/fonts.css" />
	<link rel="stylesheet" type="text/css" href="http://fast.fonts.com/t/1.css?apiType=css&projectid=ad1c2391-5578-473c-95e8-486971f0b8dd" />
	<script type="text/javascript" src="http://www.sportsbetting.com/javascripts/core/head.js"></script>
	<script type="text/javascript">
		  (function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();

		  function popUp(URL) {
			window.open(URL, 'Terms', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=960,height=600,left = 300,top = 100');
		}
	</script>
	<style type="text/css"> 
			pre{ padding: 20px; background-color: #ffffcc; border: solid 1px #fff; }
			.wrapper { color:#000; padding:10px 20px!important; font-size:13px; text-align:left!important; }
			.wrapper h1 { padding:0 0 10px; }
			.wrapper p { margin:5px 0 10px; }
			.event-container { background:#e5e5e5; border:1px solid #b2b2b2; margin:0 0 10px; }
			.event-container .qNum {float:left; background:url('/i/question_bg.jpg') no-repeat #434343; width:47px; text-align:center; height:39px; padding:4px 0 0 3px;}
			.event-container .qNum p { color:#000; font-size:1.2em; }
			.event-container .question { width:628px; height:43px; float:left; background:url('/i/question_bg.jpg') right no-repeat; }
			.event-container p { font-weight: bold; color:#fff;padding:10px; margin:0; }
			.event-container select { border:1px solid #b2b2b2; }
			.event-container option { padding:0 5px; }
			.event-container table { margin:15px; }
			.event-container table input#submit { cursor:pointer; font-weight:bold; border:none; color:#fff; background:url('/i/submit_bg.jpg') no-repeat; display:block; width:165px; height:30px;}
			.event-container table input#mypicks, .event-container table input#terms { cursor:pointer; font-weight:bold; border:none; color:#fff; background:url('/i/button_bg.jpg') no-repeat; display:block; width:165px; height:30px; }		
			.event-container > dl dt{ font-weight: bold; height: 20px; }
			.event-container > dl dd{ margin: -20px 0 10px 100px; border-bottom: solid 1px #fff; }
			.event-container .select select { background: transparent; width: 180px; height: 18px; }
			.event-container .select { margin:55px 0 15px 15px; width: 150px; height: 18px; overflow: hidden; background: url('/i/select.jpg') no-repeat right #fff; border: 1px solid #ccc; }
			.event-container .noselect { text-align: center; margin:5px 0px 5px 0px; font-size:13px; font-weight:bold; color: #000000;}
			.clear{ clear: both; }

			body #bracket { border:1px solid #000; padding:5px; background:#fff; width:1200px; margin:0 auto; position:relative; z-index:-1;}
			#bracket td * { margin:0; padding:0; }
			#bracket input {font-size:11px;color:#000;border:1px solid #c00c1a;text-align:center;padding:2px;width:83px; height:13px;}
			#bracket table {background:url('img/bg.jpg') top center no-repeat;font-size:13px;border:1px solid;padding:5px;}

			#bracket td {padding:0px;width:95px;border:none; height:17px;}

			#bracket .checkit {margin:1px 2px 0 0;width:12px; border:none; float:right;}
			#bracket .checkit2 {margin:1px 2px 0 0;width:12px;border:none; float:left;}

			#bracket input.past {color:#666;}
			#bracket textarea {border:1px solid #c00c1a;color:#000;font-size:11px;text-align:center;margin:0;padding:0;height:27px;}
			#bracket #loadingInfo {position:absolute;float:left;left:500px;top:30px;}
			#bracket h4 {text-align:center; margin:0; font-size:1.2em; font-weight:bold;}
			#bracket #info { color:#000; font-size:.8em; position:absolute; padding:10px; left:448px; top:1183px; display:none; text-align:center; width:280px; border:1px solid #000;}
			#bracket #info ul { color:#000; text-align:left; padding:10px 0 0 0; }

			#bracket #loading {position:absolute;float:left;left:520px;}
			#bracket #submit {text-align:center;margin:10px;width:100px; height:21px;}
			#bracket #cid {text-align:left;margin:10px;width:100px; }
			#bracket #cemail {text-align:left;margin:10px;width:100px; }
			#bracket #loadingInfo {display:none}

			#bracket tr.round td, tr.rounddate td { font-family:Arial, sans-serif; background:#c00c1a; color:#fff; text-align:center; border-right:1px solid #fff !important; font-size:.9em; padding:3px 0; }
			#bracket #accinfo { background-image:none; background-color:#fff; width:300px; margin:0 auto;}
			#bracket td.champ { text-align:center; }
			#bracket #submit {text-align:center;margin:10px;padding:0 0 3px; background:#c00c1a; color:#fff; border:1px solid #900;}
	</style>
</head>

<body class="en home">

<?php //include('../inc/header.php'); ?>

<section id="mainContainer" style="min-height:900px !important;">
	<br />
	<div id="bracket">
		<img src="img/header.jpg" alt="2011 NCAA Basketball Bracket" />
		<div id="contestinfo">
		</div>
	</div><!-- end bracket -->
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){

		//$('#contestinfo').hide();
		$("#info").hide();
		$('#contestinfo').html('<img id="loading" src="img/ajax-loader.gif" />');
		start();
		
	});
	function start()
	{
		$.ajax({
		 url: 'bracpop.php',type: "POST",data: 'populate=1&userid=<?php echo urlencode($userid);?>&userem=<?php echo urlencode($userem);?>',
		success: function(msg) {
			$('#contestinfo').html(msg);
			$("#info").show();
			$('input:radio').each( function()
		{
			//$(this).siblings('input:text').val('');
			$(this).attr('checked', false);
			$(this).bind('click', function() {
				var thisCheck = $(this);
				if (thisCheck.is(':checked'))
				{
					var name = $(this).attr('name');
					$('input:radio[name='+name+']').each(function() {  
					
					$(this).siblings('input:text').css({'background':'white','color':'#999999','font-weight':'normal'});   } );
					$(this).siblings('input:text').attr('class', 'disabled');
					var teamid = $(this).siblings('input:text').attr('id');
					var id = this.id;
					var ar = id.split('-');

					if (ar[1] == 6)
					{
						$('#champion').val($('#'+teamid).val());	
						$(this).siblings('input:text').css({'background':'#c00c1a','color':'white','font-weight':'normal'});	
						$(this).siblings('input:text').attr('class', 'enabled');	
					}
					else {
						var leftOrRight = ar[1];
					
						if (ar[1] < 7)
							ar[1]++;
						else ar[1]--;
							
						ar[3] = (parseInt(ar[2]) % 2 == 0) ? 2 : 1;
						ar[2] = Math.ceil(((parseInt(ar[2]) )/2));
						id = ar.join('-');
						
						var ar2 = ar;
						var id3;
						if (ar[3] == 1)
						{
							ar2[3] = 2;
							id3 = ar2.join('-');	
						}
						else
						{
							ar2[3] = 1;
							id3 = ar2.join('-');	
						}
						if ($('#'+id3).siblings('input:text').val() != '')
						{
							$('#'+id).css('visibility','visible');
							$('#'+id3).css('visibility','visible');
						}
					
						$('#'+id).siblings('input:text').val($(this).siblings('input:text').val());
						$('#'+id).siblings('input:text').attr('id', ar[1] + '-'+teamid);
						
						$(this).siblings('input:text').css({'background':'#c00c1a','color':'white','font-weight':'normal'});	
						$(this).siblings('input:text').attr('class', 'enabled');					
					}
				} 
				
				});
			
		} );
			
	  }});
	}

	/*
	sending all text inputs have 

	*/

	</script>
	<br />
</section>

<?php //include('../inc/footer.php'); ?>

</body>
</html>