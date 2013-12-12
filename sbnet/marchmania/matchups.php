<?php
require_once('teams.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bracket Test</title>
<style>input {font-size:11px;color:#223399;border:1px solid #8899bb;text-align:left;padding:0 2px 0 0;width:87px;}
table {font-size:13px;border:1px solid;padding:10px}
td {padding:1px;width:95px}
.checkit {margin:0 0 0 5px;width:5px;}
.checkit2 {margin:-15px 5px 0 0;width:5px;}
input.past {color:#666;}
textarea {border:1px solid #223399;color:#223399;font-size:11px;text-align:center;margin:0;padding:0;height:27px}
#info,#loadingInfo {position:absolute;float:left;left:520px;width:200px;margin:30px 0 0 0} h4 {text-align:center}
#submit {text-align:center;margin:10px;}
#loadingInfo {display:none}
</style>
</head>
<body>
<div id="loadingInfo"><img src="img/ajax-loader.gif" /></div>
<div id="info"><form name="customerInfoForm" method="post" action=""><h4>Enter Your Information</h4>
        <table border="0" cellpadding="0" cellspacing="0" id="accinfo">
        <tr>
          <td>Your Account number (CID):<br />
          <small><a href="#" onclick="window.open('https://sportsbook.gamingsys.net/www.sportsbetting.com/misc/customer.cgi','whatsmycid','width=300,height=300,scrollbars=0,toolbar=0,resizable=0');" class="small">What is my Customer ID?</a></small>
          </td>

          <td><input type="text" id="cid" name="cid" /></td></tr><tr>
          <td><br /><input type="button" value="submit" name="submit" id="submit" /></td>
        </tr>
      </table>
      </form>
</div>
<div id="contestinfo">
<table cellpadding="0" cellspacing="0" width="100%">

<tr class="round">
<td>Round One</td>
<td>Round Two</td>
<td>Sweet Sixteen</td>
<td>Elite Eight</td>
<td>Final Four</td>
<td>Championship</td>
<td>Final Four</td>
<td>Elite Eight</td>
<td>Sweet Sixteen</td>
<td>Round Two</td>
<td>Round One</td>
</tr>
<tr  class="rounddate">
<td>March 15-16, 2011</td>
<td>March 17, 19, 2011</td>
<td>March 24, 26, 2011</td>
<td>March 25, 27, 2011</td>
<td>April 2, 2011</td>
<td>April 4, 2011</td>
<td>April 2, 2011</td>
<td>March 25, 27, 2011</td>
<td>March 24, 26, 2011</td>
<td>March 17, 19, 2011</td>
<td>March 15-16, 2011</td>
</tr>

<?php 
$s = new Matchups;
$s->getMatchUps() ;
$s->createRoundTeams();

?>
</table>
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

$('#submit').click( function() {
	var data = $('#cid').val();
$.ajax({
  url: 'postit.php',dataType: 'json',type: "POST",data: 'cid='+data,
  success: function(msg) {
	 console.log(msg[0]);
  }});
   });


$(document).ready(function(){
	$('#contestinfo').hide();
	$('input:radio').each( function()
	{
		$(this).attr('checked', false);
		$(this).bind( 'click', function() {
			var thisCheck = $(this);
			if (thisCheck.is(':checked'))
			{
				var name = $(this).attr('name');
				$('input:radio[name='+name+']').each(function() {  $(this).siblings('input:text').css({'background':'white','color':'#223399','font-weight':'normal'});   } );
				
				if (name.indexOf('final') !== -1)
				{
					$(this).siblings('input:text').css({'background':'white','color':'#223399','font-weight':'normal'}); 
					$(this).prev().css({'background':'#223399','color':'white','font-weight':'normal'});
					
				}else {
					$(this).siblings('input:text').css({'background':'#223399','color':'white','font-weight':'normal'});	
				}
			} 
			
			});
		
	} );
	
});
</script>
</div>
</body>
</html>