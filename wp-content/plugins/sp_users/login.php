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

<form name="memberlogin" action="" method="post">
	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field(); ?>

	<input type="hidden" name="member[login]" value="1"/>

	<label class="sp_label"><?php _e('Username:') ?>
	<input type="text" value="" name="member[username]" class="sp_input"/></label>
	<br/>
	<label class="sp_label"><?php _e('Password:') ?>
	<input type="password" value="" name="member[password]" class="sp_input"/></label><br/>
	<div class="sp_label">
		<input type="submit" value="Login" style="float:left;" />
		<a href="<?php echo site_url('/forgot-password'); ?>">Forgot Password?</a>
	</div>
</form>