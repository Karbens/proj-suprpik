<?php

if ( isset($_SESSION['errors']) ){
	$wp_error = $_SESSION['errors'];
	unset($_SESSION['errors']);

	if ( $wp_error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data($code);
			foreach ( $wp_error->get_error_messages($code) as $error ) {
				if ( 'message' == $severity )
					$messages .= '	' . $error . "<br />\n";
				else
					$errors .= '	' . $error . "<br />\n";
			}
		}
		if ( !empty($errors) )
			echo '<div id="login_error">' . apply_filters('login_errors', $errors) . "</div>\n";
		if ( !empty($messages) )
			echo '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
	}


}

?>

<form name="memberRegister" id="memberRegister" action="" method="post">
	<div class="alignright"><span class="required">*</span> Required Fields</div>
	<br/>
	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field(); ?>

	<input type="hidden" name="member[register]" value="1"/>
	<label class="sp_label"><?php _e('Username:') ?>
	<input type="text" value="<?php echo $_POST['member']['username']; ?>" name="member[username]" class="sp_input" required="required"/><span class="required">*</span></label>
	<br/>
	<label class="sp_label"><?php _e('First Name:') ?>
	<input type="text" value="<?php echo $_POST['member']['firstname']; ?>" name="member[firstname]" class="sp_input" required="required"/><span class="required">*</span></label>
	<br/>
	<label class="sp_label"><?php _e('Last Name:') ?>
	<input type="text" value="<?php echo $_POST['member']['lastname']; ?>" name="member[lastname]" class="sp_input" required="required"/><span class="required">*</span></label>
	<br/>
	<label class="sp_label"><?php _e('Date of Birth:') ?>
	<input type="text" value="<?php echo $_POST['member']['dob']; ?>" name="member[dob]" class="sp_input" id="dob" readonly="readonly" required="required"/><span class="required">*</span></label>
	<br/>
	<label class="sp_label" style="display:inline-block"><?php _e('Password:') ?>
	<input type="password" value="<?php echo $_POST['member']['password']; ?>" id="Pwd" name="member[password]" class="sp_input" required="required"/><span class="required">*</span></label><span class="message" style="display:inline-block; margin-left:20px;"></span>
	<br/>
	<br/>
	<label class="sp_label"><?php _e('Confirm Password:') ?>
	<input type="password" value="<?php echo $_POST['member']['confirm_password']; ?>" id="confirmPwd" name="member[confirm_password]" class="sp_input" required="required"/><span class="required">*</span></label>
	<br/>
	<label class="sp_label"><?php _e('Email:') ?>
	<input type="email" value="<?php echo $_POST['member']['email']; ?>" name="member[email]" class="sp_input" required="required"/><span class="required">*</span></label>
	<br/>
	<label class="sp_label"><?php _e('City:') ?>
	<input type="text" value="<?php echo $_POST['member']['city']; ?>" name="member[city]" class="sp_input" required="required"/><span class="required">*</span></label>
	<br/>
	<label class="sp_label"><?php _e('Mobile/Cell:') ?>
	<input type="text" value="<?php echo $_POST['member']['phone']; ?>" name="member[phone]" class="sp_input"/></label>
	<br/>
	<h4><?php _e('Newsletters and Offers') ?></h4>
	<label class="sp_label"><input<?php echo ($_POST['member']['newsletters']=='on')?' checked="checked"':''; ?> type="checkbox" name="member[newsletters]"><?php _e('Yes I would like to receive occasional newsletters, offers and updates from '); bloginfo('name'); ?></label><br/>

	<label class="sp_label"><?php _e('Validation code:') ?>
		<a id="newCode">Refresh</a>
		<img id="captcha" src="<?php echo plugins_url( 'captcha.php', __FILE__ ); ?>" alt="<?php _e("Captcha"); ?>"/>
	</label>
	<br/>
	<label class="sp_label"><?php _e('Enter code:') ?>
	<input type="text" value="" name="member[captcha]" id="captchaInput" class="sp_input" autocomplete="off" required="required" oninvalid="setCustomValidity('Please enter CAPTCHA code.')"/><span class="required">*</span></label><br/><br/>
	
	<label class="sp_label"><input<?php echo ($_POST['member']['agree']=='on')?' checked="checked"':''; ?> type="checkbox" name="member[agree]" required="required"/><?php _e('I agree and consent to the '.get_bloginfo('name').' <a target="_blank" href="'.site_url('/terms-of-service').'">terms of service</a> and <a target="_blank" href="'.site_url('/privacy-policy').'">privacy policy</a>.'); ?></label><br/>
	<div class="sp_label">
		<input type="reset" value="Cancel" />
		<input type="submit" value="Register" style="margin-right:40px;" />
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#newCode').click(function(){
			$('#captcha').attr('src', '<?php echo plugins_url( 'captcha.php', __FILE__ ); ?>?seed='+Math.random() );
			return false;
		});
		<?php 
			$year = date('Y');
			$from_year = $year-100;

		 ?>
		$( "#dob" ).datepicker({
			changeMonth: true,
      		changeYear: true,
      		maxDate: 0,
      		dateFormat: "yy-mm-dd", yearRange : '<?php echo $from_year.':'.$year;?>'
      	});

      	$('#confirmPwd, #Pwd').keyup(function() {
      		$('.message').html('');
      		if($(this).val()!=''){
			 	if($('#Pwd').val()!=$('#confirmPwd').val()){
			 		$('#confirmPwd, #Pwd').attr('title', 'Passwords not matching.').css({'border-color': 'red'});
			 		$('.message').html('Passwords not matching.').css({'color': 'red'});
			 	}else{
			 		$('#confirmPwd, #Pwd').attr('title', 'Passwords are matching.').css({'border-color': 'green'});
			 		$('.message').html('Passwords are matching.').css({'color': 'green'});
			 	}      			
      		}
		});

		$('#memberRegister').submit(function(){
			if($('#captchaInput').val()==''){
				alert('Please enter CAPTCHA code.');
				return false;
			}
		});


	});

</script>