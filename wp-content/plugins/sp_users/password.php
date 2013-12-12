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
			echo '<div id="login_error">' . apply_filters('password_errors', $errors) . "</div>\n";
		if ( !empty($messages) )
			echo '<p class="message">' . apply_filters('password_messages', $messages) . "</p>\n";
	}


}

?>

<?php if(isset($_GET['key'],$_GET['login'], $_GET['resetpass']) && $_GET['resetpass']=='true'){ ?>

<p>Please enter a new password and a confirmation.</p>

<form name="resetpassform" action="<?php echo esc_url( site_url( 'forgot-password?action=activate&resetpass=true&key=' . urlencode( $_GET['key'] ) . '&login=' . urlencode( $_GET['login'] ), 'login_post' ) ); ?>" method="post">
	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field(); ?>

	<input type="hidden" name="member[resetpass]" value="1"/>



	<input type="hidden" id="member[login]" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />

	<label class="sp_label" style="display:inline-block"><?php _e('Password:') ?>
	<input type="password" value="<?php echo $_POST['member']['password']; ?>" id="Pwd" name="member[password]" class="sp_input" required="required"/><span class="required">*</span></label><span class="message" style="display:inline-block; margin-left:20px;"></span>
	<br/>
	<br/>
	<label class="sp_label"><?php _e('Confirm Password:') ?>
	<input type="password" value="<?php echo $_POST['member']['confirm_password']; ?>" id="confirmPwd" name="member[confirm_password]" class="sp_input" required="required"/><span class="required">*</span></label>
	<br/>
	<div class="sp_label">
		<a href="<?php echo site_url('/sign-in'); ?>">Back to Login</a>
		<input type="submit" value="Submit" />
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($) {

      	$('#confirmPwd, #Pwd').change(function() {
      		$('.message').html('');
      		if($(this).val()!=''){
			 	if($('#Pwd').val()!=$('#confirmPwd').val()){
			 		$('.message').html('Passwords not matching.').css({'color': 'red', 'opacity':1}).stop().fadeIn();
			 	}else{
			 		$('.message').html('Passwords are matching').css({'color': 'green'}).fadeOut(5000);
			 	}      			
      		}
		});


	});
</script>
<?php } else { ?>

<p>If you have forgotten your password or display name, please submit the email address associated with your account. You will be sent a link that enables you to reset your password.</p>

<form name="passwordreset" action="" method="post">
	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field(); ?>

	<input type="hidden" name="member[lostpassword]" value="1"/>

	<label class="sp_label"><?php _e('Email:') ?>
	<input type="email" value="" name="member[email]" class="sp_input" required="required"/><span class="required">*</span></label>
	<br/>
	<div class="sp_label">
		<a href="<?php echo site_url('/sign-in'); ?>">Back to Login</a>
		<input type="submit" value="Submit" style="float:left;" />
	</div>
</form>
<?php }