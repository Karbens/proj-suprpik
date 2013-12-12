<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bracket Test</title>
<style>input {font-size:11px;color:#223399;border:1px solid #8899bb;text-align:left;padding:0 2px 0 0;width:87px;}
table {font-size:13px;border:1px solid;padding:5px}
td {padding:0px;width:95px}
.checkit {margin:0 0 0 5px;width:5px;}
.checkit2 {margin:-15px 5px 0 0;width:5px;}
input.past {color:#666;}
textarea {border:1px solid #223399;color:#223399;font-size:11px;text-align:center;margin:0;padding:0;height:27px;}
#info,#loadingInfo {position:absolute;float:left;left:520px;width:200px;margin:30px 0 0 0} h4 {text-align:center}
#loading {position:absolute;float:left;left:520px;}
#submit {text-align:center;margin:10px;}
#loadingInfo {display:none}
</style>
</head>
<body>
<div id="info"><h4>Your Information</h4>
        <table border="0" cellpadding="0" cellspacing="0" id="accinfo">
        <tr>
          <td>Your Account number (CID):<br />
          <small><a href="#" onclick="window.open('https://sportsbook.gamingsys.net/www.sportsbetting.com/misc/customer.cgi','whatsmycid','width=300,height=300,scrollbars=0,toolbar=0,resizable=0');" class="small">What is my Customer ID?</a></small>
          </td>

          <td><input type="text" id="cid" name="cid" /></td></tr><tr>
          <td colspan="2"> <br /><input type="submit" value="Enter the Contest" name="submit" id="submit" /></td>
        </tr>
      </table>
</div>
<div id="contestinfo">
</div>

<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	
	//$('#contestinfo').hide();
	$("#info").hide();
	$('#contestinfo').html('<img id="loading" src="img/ajax-loader.gif" />');
	$('#submit').click(function() {
		;
		var undefined = false;
		var regex = /^\d+$/;
		var cid = $('#cid').val();
		var collect = 'collection=1&cid='+cid;
		if (cid != '' && regex.test(cid))
		{
			$('#contestinfo input:radio').each(function(){
	// CONTINUE HERE			
				if ($(this).attr('checked') != false)
				{
					var id = $(this).siblings('input:text').attr('id');
					var ar = id.split('-');
					if (ar[0] != 'team')
					{	
						collect += '&id[]='+ar[0]+','+ar[ar.length-1];
					}
				}
				undefined = true;
			});
			console.log(collect);
			if (0 && undefined) {
				alert('Please make your selections for all rounds');
			} else
			{
				$.ajax({
				 url: 'postit.php',type: "POST",data: collect,
				success: function(msg) {
					alert(msg);
				} });
			}
		} else {
			alert('Please enter a valid Account Number');	
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
					if ($('#'+id3).siblings('input:text').val() != '')
					{
						$('#'+id).css('visibility','visible');
						$('#'+id3).css('visibility','visible');
					}
				
					$('#'+id).siblings('input:text').val($(this).siblings('input:text').val());
					$('#'+id).siblings('input:text').attr('id', ar[1] + '-'+teamid);
					
					$(this).siblings('input:text').css({'background':'#223399','color':'white','font-weight':'normal'});	
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
</body>
</html>

<?php


?>