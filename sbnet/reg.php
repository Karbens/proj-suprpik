<?php include_once('sbnetadmin/db_func.php'); 
tep_db_connect();
 
 $errors = array();

 if(isset($_POST['register'])){

//TODO: database insertions to be escaped

//TODO: check for duplicate entries.


  $firstname = mysql_real_escape_string(trim($_POST['fname']));
  $lastname = mysql_real_escape_string(trim($_POST['lname']));
  $email = mysql_real_escape_string(trim($_POST['email']));
  $username = mysql_real_escape_string(trim($_POST['uname']));
  $password = trim($_POST['passw']);
  $confirm = trim($_POST['confPassw']);

  if(empty($firstname)){
  	$errors[] = 'Please enter your first name.';
  }

  if(empty($lastname)){
  	$errors[] = 'Please enter your last name.';
  }

  if(empty($email)){
  	$errors[] = 'Please enter your email.';
  }

  if(empty($username)){
  	$errors[] = 'Please enter a username.';
  }

  if(empty($password)){
  	$errors[] = 'Please enter a password.';
  }

  if($password!=$confirm){
  	$errors[] = 'Passwords do not match.';
  }

  //check duplicates

  $query = "SELECT * FROM `sb_signups` WHERE `email` = '$email'";
  $result = mysql_query($query);

  if(mysql_num_rows($result) > 0){
  	$errors[] = 'Email already exists.';
  }

  $query = "SELECT * FROM `sb_signups` WHERE `username` = '$username'";
  $result = mysql_query($query);

  if(mysql_num_rows($result) > 0){
  	$errors[] = 'Username already exists. Please try a different name';
  }

  if(count($errors) == 0 ){
		$password = md5($password);
					  
		$query = "insert into sb_signups(first_name,last_name,email,username,password) 
				  VALUES('$firstname','$lastname','$email','$username','$password')";
		$result = mysql_query($query);
		if($result)
		{
		$id = mysql_insert_id();
		header('Location: index.php');
		exit;
		} 
  }
	
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Join Now - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="i/sbtfav2.png" />
	<link rel="stylesheet" type="text/css" href="css/phoenix.css" />
	<link rel="stylesheet" type="text/css" href="css/fonts.css" />
	<link href="css/frontend/style.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript" src="admin/js/jquery-latest.js"></script>
	<script type="text/javascript" src="admin/js/jquery.validate.js"></script>
	<!--<script type="text/javascript" src="http://www.sportsbetting.com/javascripts/core/head.js"></script>-->
	<script type="text/javascript">
		  (function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
	</script>
	<script type="text/javascript">
		  function submit_form()
		  {
			var fname = $('#fname').val();
			var lname = $('#lname').val();
			var username = $('#uname').val();
			var passw = $('#passw').val();
			var confmPass = $('#confPassw').val();
			var email = $('#email').val();
			var val = true;
			if(fname=="" || lname=="" || username=="" || passw=="" || confmPass=="" || email==""){
				val = false;
			}
			if(passw != confmPass){
			val = false;
			}
			if(val==false){
			   $('#reg_err_div').addClass('reg_err_class');   
			   $('#reg_err_div').html("Fields marked with * are compulsory");   
			   return false;
			}
			var em = IsEmail(email);
			if(em==false){  
				$('#reg_err_div').addClass('reg_err_class');   
			    $('#reg_err_div').html("Invalid Email Address");   
			    return false;
			}
		  }
		  
		  function IsEmail(email) {
			  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			  return regex.test(email);
		}
	</script>
	
	<style type="text/css">
		.text { height:184px; margin:0 -2px; background:#fff; color:#000; padding:5px; text-align:left; }
		.text p { margin:8px 0; font-size:.9em; }
		.text p b { color:#bd580a; }
		.text a { color:#bd580a; text-decoration:none; }
		.text a:hover { text-decoration:underline; }
		#error_div{color: #f00}
	</style>
</head>
<?php include('inc/header.php'); ?>
<body>


<div class="outdiv">
<div class="main" id="mainWrapperDiv">

<div class="reg_wrapper" id="contentContainer">
     <div class="pad">
        <div class="reg_wrapper">
			<div id="registerMemberDiv">
				<img alt="Join Now!" src="i/register.png">
				<!--<div style=" margin-left: 180px; font-family:Verdana, Geneva, sans-serif; font-size:16px; font-weight:bold;">REGISTER MEMEBER</div>-->
				<div id="error_div">


				<?php if(count($errors)){

					foreach($errors as $error) echo $error . '<br/>';

				}
				?>
				</div>
				<div id="reg_err_div" ></div>
				<div style="clear:both;"></div>
				<form id="frm_register" name="frm_register" method="post">
					<input type="hidden" name="post_form" value="post" />
					<div style="margin-top: 15px;" class="fieldset">
						<div class="elements">
							<label for="name">First Name <span style="color:red">*</span> : </label>
							<input type="text" name="fname" id="fname"  size="40" />
						</div>
						<div class="elements">
							<label for="name">Last Name <span style="color:red">*</span>: </label>
							<input type="text" name="lname" id="lname"  size="40" />
						</div>
						<div class="elements">
							<label for="name">Email <span style="color:red">*</span>: </label>
							<input type="email" name="email" id="email" size="40" />
							<input type="hidden" name="eamilAvble" id="eamilAvble" value="0" />
						</div>
						<div class="elements">
							<label for="name">Username <span style="color:red">*</span> :</label>
							<input type="text" name="uname" id="uname" size="40" />
							<input type="hidden" name="uNameAvble" id="uNameAvble" value="0" />
						</div>
						<div class="elements">
							<label for="name">Password <span style="color:red">*</span> :</label>
							<input type="password" name="passw" id="passw" size="40" />
						</div>
						<div class="elements">
							<label for="name">Confirm Password <span style="color:red">*</span> :</label>
							<input type="password" name="confPassw" id="confPassw" size="40" />
						</div>
						<div style="float: left; width: 83px; margin: 10px 47px 6px 222px;">
							<input style="width: 86px;" id="register" class="button_class" type="submit" name="register" value="Submit" onclick=" return submit_form();">
						</div>
						<div style="float: left; width: 86px; margin: 10px 0px 6px;">
							<input style="width: 86px;" id="cancel" class="button_class" type="submit" style="float: right;" name="cancel" value="Cancel">
						</div>
					</div> 
					</div>
				</form>
			</div>
		</div>
    </div>
	  <br/>
   
</div>
</div>
</div>


</body>
<div>
<?php include('inc/footer.php'); ?>
</div>
</html>