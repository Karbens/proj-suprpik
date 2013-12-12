<?php
require_once('session.php');
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NCAAB Contest Admin</title>
<style>
body { margin-left: 50px; width:1200px; }
td * { margin:0; padding:0; }

input {font-size:11px;color:#223399;border:1px solid #8899bb;text-align:left;padding:2px;width:78px; height:13px;}
table {font-size:13px;border:1px solid;padding:5px }

td {padding:0px;border:none; height:17px;}

.checkit {margin:1px 2px 0 0;width:12px; border:none; float:right;}
.checkit2 {margin:1px 2px 0 0;width:12px;border:none; float:left;}

input.past {color:#666;}
textarea {border:1px solid #223399;color:#223399;font-size:11px;text-align:center;margin:0;padding:0;height:27px;}
#loadingInfo {position:absolute;float:left;left:500px;top:30px;}
h4 {text-align:center; margin:0;}
#info {font-family:Arial, Verdana, sans-serif;position:absolute;float:left;left:425px;top:1113px;display:none; text-align:center; width:350px;}
#info ul { font-size:.6em; }

#loading {position:absolute;float:left;left:520px;}
#submit {text-align:center;margin:10px;width:100px; height:21px;}
#cid {text-align:center;margin:10px;width:100px; }
#loadingInfo {display:none}

tr.round td, tr.rounddate td { background:#344698; color:#fff; text-align:center; border-right:1px solid #fff; font-size:.9em; }
#accinfo { background:#fff; width:220px; margin:0 auto;}
td.champ { text-align:center; }
</style>
</head>
<body>

<div align="center">
	<h1>NCAAB Bracket</h1>
	<h2>Admin Interface</h2>
	<?php echo bracketResults(); ?>
	<div id="contestinfo" class="box" style="margin: 10px auto 10px auto;"></div>
</div>

<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	
	//$('#contestinfo').hide();
	$("#info").hide();
	//$('#contestinfo').html('<img id="loading" src="img/ajax-loader.gif" />');
	
	start();
	
});
function start()
{
	$.ajax({
 	 url: 'adminpop.php',type: "POST",data: 'populate=1',
  	success: function(msg) {
		$('#contestinfo').html(msg);
		$("#info").show();
		$('#clear').click(function() {
			
			if (confirm('All matchups from 2nd Round on will be deleted. Proceed?'))
			{
				$.ajax({
				 url: 'brac.php',type: "POST",data: 'clear=1',
				success: function(msg) {
					alert(msg);
					window.location.reload();
				} });
			}
		});
		$('#update').click(function() {
		var undefined = false;
		var regex = /^\d+$/;
		var collect = 'collection=1';
		$('#contestinfo input:text').each(function(){
// CONTINUE HERE			
			if ($(this).val() != '')
			{
				//var t = $(this).siblings('input:select option:selected');
				var id = $(this).attr('id');
				var ar = id.split('-');
	
				var t= 0;	
				if (id== 'champion')
				{
					collect += '&roundchampion=1,'+$(this).val();
				} else
				{		
					if (ar[1]==1 || ar[1] == 11) {
						var t = $(this).siblings('#rankings').val();
						collect += '&round'+ar[0]+'[]='+t+','+$(this).val()+','+ar[ar.length-1];
					} else
						collect += '&round'+ar[1]+'[]='+ar[2]+','+$(this).val();
				}
			}
			//undefined = true;
			
		});
		//console.log(collect);
			$.ajax({
			 url: 'updateteams.php',type: "POST",data: collect,
			success: function(msg) {
				alert(msg);
			} });

	});	
			
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
				
				$(this).siblings('input:text').css({'background':'white','color':'#223399','font-weight':'normal'});   } );
				$(this).siblings('input:text').attr('class', 'disabled');
				var teamid = $(this).siblings('input:text').attr('id');
				var id = this.id;
				var ar = id.split('-');

				if (ar[1] == 6)
				{
					$('#champion').val($('#'+teamid).val());	
					$(this).siblings('input:text').css({'background':'#223399','color':'white','font-weight':'normal'});	
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
					
				
					//var oldvalue = $('#'+id).siblings('input:text').val();
				
					$('#'+id).siblings('input:text').val($(this).siblings('input:text').val());
					//$('#'+id).siblings('input:text').attr('id', ar[1] + '-'+teamid);
					
					$(this).siblings('input:text').css({'background':'#223399','color':'white','font-weight':'normal'});	
					$(this).siblings('input:text').attr('class', 'enabled');					
					
					/*var va = $(this).siblings('input:text').val();
					$('input:text').each( function() {
						
						if ($(this).val() == oldvalue) $(this).val(va); 
						
					});*/
					
					
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
</body>
</html>
