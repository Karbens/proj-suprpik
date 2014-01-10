<?php
session_start();
include('db_func.php');
tep_db_connect();

if(!empty($_SESSION['UserID']))
{
	header("location: index.php");
}

if(isset($_POST['username'], $_POST['password'])){

	$UserName = trim($_POST['username']);
	$Password = md5(trim($_POST['password']));

	$query = "SELECT * 
			  FROM `_staff` 
			  WHERE `username` = '".$UserName."'
			  AND `password` = '".$Password."'
			  AND `status` = 'Active'";
	//echo $query;exit();
    $result = mysql_query($query);
    if(@mysql_num_rows($result)<1){
	  $ShowError = true;
	}else{
		$user_id = mysql_result($result,0,"id");
		$ip = $_SERVER['REMOTE_ADDR'];
		mysql_query("INSERT INTO `login_log` 
					( `ID` , `user_id` , `log_time` , `log_ip` ) 
					VALUES (
					'', '".$user_id."', '".date('Y-m-d H:i:s')."', 
					'".$ip."');");
		$_SESSION['UserID'] = mysql_result($result,0,"id");
		$_SESSION['UserName'] = mysql_result($result,0,"username");
		header("Location: index.php");
		exit();
	}
}
?>
<html>

<head>

<title>SB CONTESTS LOGIN</title>

		<link rel="stylesheet" media="all" type="text/css" href="css/ui-lightness/jquery-ui-1.8.16.custom.css" />
		<style type="text/css"> 
			.fsLeg
			{
				font-size:13px; 
				font-weight:bold;
			}
			.loginBg
			{
				font-size: 12px;
			}
		</style> 
		<link rel="stylesheet" type="text/css" href="css/jform.css">

<SCRIPT language=JavaScript>
<!-- 

		 function trim(s)
         {
           return s.replace( /^\s*/, "" ).replace( /\s*$/, "" );
         }
		 
		 function checkMail(x)
         {
           var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
           if (filter.test(x))
            {return true;}
           else
            {return false;}
         }
		 
         function validate()
         {
	         alerttext = "";
			 if(trim(document.getElementById("username").value).length<=0)
	         {
	           alerttext += "Username is required\n";
	         }
			 
			 /*if(checkMail(document.getElementById("username").value) == false && trim(document.getElementById("username").value).length!=0)
             {
			   alerttext+="Valid User Email is required\n";
			 }*/
			 
			 if(trim(document.getElementById("password").value).length<=0)
	         {
	           alerttext += "Password is required\n";
	         }
			 
			 if(alerttext!="")
        	 {
			    alert(alerttext);
				return false;
			 }
		 }
//-->
</SCRIPT>

</head>

<body bgcolor="#F5F5F5" marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" ONLOAD="document.login.username.focus()">

 <!-- body //--> 
	<br><br>
	<div align="center">
		<br>
		<div class="box" style="width:500px;margin: 10px auto 10px auto;">
		 <fieldset>
		   <legend class="fsLeg">SB FREE CONTESTS ADMIN</legend>
				
				<form name="login" action="login.php" method="POST" onSubmit="return validate()">
				
				<table width="400" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="loginBg">
						
					<table width="280" cellpadding="0" cellspacing="0" border="0" align="center" cellpadding="2" cellspacing="2">
						
						<tr>
							<td colspan="2">
							&nbsp;
							</td>
						</tr>
						
						<tr>
							<td class="loginBg">
							USERNAME:&nbsp;
							</td>
							<td class="loginBg">
							<input name="username" id="username" type="text" class="textField" value="" size="25" maxlength="50">
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
							&nbsp;
							</td>
						</tr>
						
						<tr>
							<td class="loginBg">
							PASSWORD:&nbsp;
							</td>
							<td class="loginBg">
							<input name="password" id="password" type="password" class="textField" value="" size="25" maxlength="50">
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
							&nbsp;
							</td>
						</tr>
						
						<tr>
							<td>&nbsp;</td>
							<td class="loginBg">
							&nbsp;&nbsp;<input type=submit name=submit value="Login" align="right">
							<br>
							<br>
							</td>
						</tr>
						<?php
						if(isset($GLOBALS['ShowError']))
						{
							echo '<tr><td colspan="2" style="color:red; font-weigh:bold;">
									Invalid Username or Password,<br> 
									please try again.
								  </td></tr>';
						}	
						?>
					</table>
					
					</td>
				</tr>
			</table>
			
			</form>
		 </fieldset>
		</div>		
	</div>
</body>

</html>
<?tep_db_close();?>

