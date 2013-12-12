<!DOCTYPE html>
<html>
<head>
	<title>Free Contests - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="../i/sbtfav2.png" />
	<link rel="stylesheet" type="text/css" href="../css/phoenix.css" />

	<style type="text/css"> 
			body #bracket { min-height:660px;border:1px solid #000; padding:5px; background:#fff; width:1200px; margin:0 auto; position:relative; z-index:-1;}
			#bracket td * { margin:0; padding:0; }

			#bracket input {font-size:11px;color:#000;border:1px solid #c00c1a;text-align:center;padding:2px;width:83px; height:13px;}
			#bracket table {background:url('img/bg.jpg') top center no-repeat;font-size:13px;border:1px solid;padding:5px}

			#bracket td {padding:0px;width:95px;border:none; height:17px;}

			#bracket .checkit {margin:5px 3px 0 0;width:12px; border:none; float:right;}
			#bracket .checkit2 {margin:2px 3px 0 0;width:12px;border:none; float:left;}

			#bracket input.past {color:#666;}
			#bracket textarea {border:1px solid #c00c1a;color:#000;font-size:11px;text-align:center;margin:0;padding:0;height:27px;}
			#bracket #loadingInfo {position:absolute;float:left;left:500px;top:30px;}
			#bracket h4 {text-align:center; margin:0; font-size:1.2em; font-weight:bold;}
			#bracket #info { color:#000; font-size:.8em; position:absolute; padding:10px; left:448px; top:1133px; display:none; text-align:center; width:280px; border:1px solid #000;}
			#bracket #info ul { color:#000; text-align:left; padding:10px 0 0 0; }

			#bracket #loading {position:absolute;float:left;left:520px;}
			#bracket #submit {text-align:center;margin:10px;width:100px; height:21px;}
			#bracket #bracketBtn {text-align:center;margin:10px;width:100px; height:21px;}
			#bracket #cid {text-align:left;margin:10px;width:100px; }
			#bracket #cemail {text-align:left;margin:10px;width:100px; }
			#bracket #loadingInfo {display:none}

			#bracket tr.round td, tr.rounddate td { font-family:Arial, sans-serif; background:#c00c1a; color:#fff; text-align:center; border-right:1px solid #fff !important; font-size:.9em; padding:3px 0; }
			#bracket #accinfo { background-image:none; background-color:#fff; width:300px; margin:0 auto;}
			#bracket td.champ { text-align:center; }
			#bracket #submit {text-align:center;margin:10px;padding:0 0 3px; background:#c00c1a; color:#fff; border:1px solid #900;}
			#bracket #bracketBtn {text-align:center;margin:10px;padding:0 0 3px; background:#c00c1a; color:#fff; border:1px solid #900;}
	</style>
</head>

<body class="en home">

<section id="mainContainer">
	<br />
	<div id="bracket">
		<img src="img/header.jpg" alt="2011 NCAA Basketball Bracket" />
		<div id="info">
			<h4>Your Information</h4>
			
			<p><br>This contest has expired.</p>	
			
			<p>Username: <input id="cid" name="cid" type="text"></p>
			
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email: <input id="cemail" name="cemail" type="text"></p>
			
			<p><input value="See My Bracket" name="bracketBtn" id="bracketBtn" type="button" onclick="seeMyBracket();"></p>

			<ul>
				<li>Contest closes at Noon ET March 15th, 2012.</li>
				<li>Please check <a href="#">March Mania 2012</a> for all the details</li>
			</ul>
			<b>****One Entry per player.****</b>

		</div>
		<div id="contestinfo">
		</div>
	</div><!-- end bracket -->
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript">
	function popUp(URL) {
		window.open(URL, 'My Bracket', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=1240,height=700,left = 100,top = 50');
	}
	function seeMyBracket()
	{
		var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9][a-zA-Z0-9.-]*[\.]{1}[a-zA-Z]{2,4}$/;
		var cid = $.trim($('#cid').val());
		var cemail = $.trim($('#cemail').val());
		if(cid =='')
		{
			alert('Please enter the Username.');
		}else if(cemail == '')
		{
			alert('Please enter the Email.');
		}else if(cemail != '' && !regex.test(cemail) )
		{
			alert('Please enter a valid Email.');
		}else
		{
			var collect = 'seeMyBracket=1&cid='+cid+'&cemail='+cemail;
			$.ajax({
				 url: 'postit.php',type: "POST",data: collect,
				 success: function(msg) {
					
					if(msg == 0)
					{
						alert('No data found!');
					}else
					{
						popUp('mybracket.php?userid='+cid+'&userem='+cemail);
					}
					
				 } 
			});
			
		}
		return false;
	
	}
	
	
	$(document).ready(function(){

		//$('#contestinfo').hide();
		$("#info").hide();
		$('#contestinfo').html('<img id="loading" src="img/ajax-loader.gif" />');
		$('#submit').click(function() {
			;
			var chCount = 0;
			var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9][a-zA-Z0-9.-]*[\.]{1}[a-zA-Z]{2,4}$/;
			var cid = $('#cid').val();
			var cemail = $.trim($('#cemail').val());
			var collect = 'collection=1&cid='+cid+'&cemail='+cemail;
			//if (cid != '' && regex.test(cid))
			alert('This contest has expired.');
			return false;
			if( cid != '' && cemail != '' && regex.test(cemail) )
			{
				$('#contestinfo input:radio').each(function(){
					// CONTINUE HERE			
					if ($(this).attr('checked') != false)
					{
						var id = $(this).siblings('input:text').attr('id');
						var ar = id.split('-');
						//if (ar[0] != 'team')
						//{	
							collect += '&id[]='+ar[0]+','+ar[ar.length-1];
							//alert('id: ' + id);
						//}
						chCount++;
					}
				});

				if (chCount != 63) {
					alert('Please make your selections for all rounds.');
				} else
				{
					$.ajax({
					 url: 'postit.php',type: "POST",data: collect,
					success: function(msg) {
						alert(msg);
					} });
				}
			} else {
				if(cid =='')
				{
					alert('Please enter the Username.');
				}else if(cemail == '')
				{
					alert('Please enter the Email.');
				}else if(cemail != '' && !regex.test(cemail) )
				{
					alert('Please enter a valid Email.');
				}
			}
		});
		start();
		
	});
	function start()
	{
		$.ajax({
		 url: 'populate.php',type: "POST",data: 'populate=1',
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

</body>
</html>