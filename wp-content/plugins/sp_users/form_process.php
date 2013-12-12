<?php
	//validations
	
	$nonce = $_REQUEST['_wpnonce'];

		if(wp_verify_nonce($nonce )){
			$member = $_POST['member'];

			if(isset($member['register']) && (int)$member['register'] == 1){
				$_SESSION['errors'] = new WP_Error();

					$user_id = sp_validate_and_create($member);

				if(!$_SESSION['errors']->get_error_code()){
					

					wp_update_user( array ( 'ID' => $user_id,
											'first_name' => sanitize_text_field( $member['firstname'] ),
									 		'last_name' => sanitize_text_field( $member['lastname'] ) ) );






					$newsletter_status = (isset($member['newsletters']) && $member['newsletters']=='on')? 1: 0;

					$ua=getBrowser();

					$meta_data = array('dob' => $member['dob'],
										'city' => sanitize_text_field( $member['city'] ),
										'phone' => $member['phone'],
										'ip4' =>  $_SERVER['REMOTE_ADDR'],
										'user_agent' =>  $ua['userAgent'],
										'browser' =>  $ua['name'] . " " . $ua['version'],
										'device' =>  $ua['platform'],
										'newsletter_status' =>  $newsletter_status,
										'updated_at' =>  current_time('mysql', 1),
										'login_counter' => 0);

					foreach($meta_data as $meta_key => $meta_value){
						add_user_meta( $user_id, $meta_key, $meta_value /*, $unique */ );
					}

					$creds = array('user_login' => sanitize_user( $member['username']),
									'user_password' => trim($member['password']));

					sp_send_activation_email( sanitize_user( $member['username']), $member['email'] );

					$title = __('New user sign up');
					$message = __($member['firstname'].' '.$member['lastname']. ' created an account.') . "\r\n\r\n";
					$message .= __('Email:' . $member['email']) . "\r\n\r\n";
					$message .= __('IP:'.$_SERVER['REMOTE_ADDR']) . "\r\n\r\n";

					if($reg_email = get_option( 'sp_registration_email' )){
						wp_mail($reg_email, $title, $message);
					}

				//	if( wp_signon($creds)){

					$_SESSION['errors']->add('activate', __('Check your e-mail for your activation link.'), 'message');
						wp_redirect( site_url('/activate-account') );
						exit;
				//	}
				}


			
			} elseif(isset($member['login']) && (int)$member['login'] == 1){

				$creds = array('user_login' => sanitize_user( $member['username']),
								'user_password' => trim($member['password']));

				$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $creds['user_login']));

				if(!empty($key)){
					$id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_login = %s", $creds['user_login']));
					$login_counter = get_user_meta($id, 'login_counter', true);

					if((int)$login_counter<1){
					$_SESSION['errors'] = new WP_Error('not_activated', __('Please activate your account first'));	

					wp_redirect('/sign-in');
					exit;	
					}

				}	


				$user = wp_signon( $creds, false );

				$login_counter = get_user_meta($user->ID, 'login_counter', true);

				update_user_meta($user->ID, 'login_counter', (int)$login_counter+1);

				sp_update_login_meta($user);

				if( !is_wp_error($user)){

					wp_redirect( home_url('/') );
					exit;

				}else{
					$_SESSION['errors'] = new WP_Error();
					$_SESSION['errors']->add( 'invalid_login', __( 'Invalid username or password.' ) );
				}
			} elseif(isset($member['account']) && (int)$member['account'] == 1){
				sp_validate_change($member);
				wp_redirect( site_url('/account') );
				exit;
			} elseif(isset($member['lostpassword']) && (int)$member['lostpassword'] == 1){
				sp_validate_and_reset($member);
				$_SESSION['errors'] = new WP_Error();
					$_SESSION['errors']->add( 'resetpass_sent', __( 'Please check your email for a password reset link' ), 'message' );
				wp_redirect( site_url('/sign-in') );
				exit;
			} elseif(isset($member['resetpass']) && (int)$member['resetpass'] == 1){
				$user = sp_check_activation_key($_GET['key'], $_GET['login']);
				if ( is_wp_error($user) ) {
   					wp_redirect( site_url('/') );
   					exit;
   				}

   				$errors = new WP_Error();
   				if ( isset($member['password']) && $member['password'] != $member['confirm_password'] )
		$errors->add( 'password_reset_mismatch', __( 'The passwords do not match.' ) );
		do_action( 'validate_password_reset', $errors, $user );

	if ( ( ! $errors->get_error_code() ) && isset( $member['password'] ) && !empty( $member['password'] ) ) {
			do_action('password_reset', $user, $member['password']);
			wp_set_password($member['password'], $user->ID);
		$errors->add( 'password_reset', __( 'Your password has been reset.' ), 'message' );
		$_SESSION['errors'] = $errors;
		wp_redirect( site_url('/sign-in') );
		exit;
	}
	$_SESSION['errors'] = $errors;



			}





		}else{
			wp_redirect( home_url() );
			exit;
		}


function sp_validate_and_create($member){

	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user( $member['username']);
	$user_email = apply_filters( 'user_registration_email', $member['email'] );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( 'Please enter a username.' ) );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( 'This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( 'This username is already registered. Please choose another one.' ) );
	}

	if ( empty( $member['firstname'] ) ) {
		$errors->add('empty_first_name', __('Please enter your first name.'));
	}

	if ( empty( $member['lastname'] ) ) {
		$errors->add('empty_last_name', __('Please enter your last name.'));
	}
	
	if ( empty( $member['dob'] ) ) {
		$errors->add('empty_dob', __('Please enter your date of birth.'));
	}elseif(strtotime(date('Y-m-d'))<strtotime($member['dob'])){
		$errors->add('empty_dob', __('Please enter a valid date of birth.'));		
	}

	if ( empty( $member['password'] ) ) {
		$errors->add('empty_password', __('Please enter a password.'));
	}else if ( $member['password'] != $member['confirm_password'] ) {
		$errors->add('password_mismatch', __('Your password and the confirmation do not match.'));
	}


	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( 'Please type your e-mail address.' ) );

	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( 'The email address isn&#8217;t correct.' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( 'This email is already registered, please choose another one.' ) );
	}

	if ( empty( $member['city'] ) ) {
		$errors->add('empty_city', __('Please enter your city.'));
	}


	if ( !empty( $member['phone'] ) && !is_numeric( $member['phone'] ) ) {
		$errors->add('empty_phone', __('Please enter a valid mobile/cell number.'));
	}


	if ( empty( $member['captcha'] ) ) {
		$errors->add('empty_captcha', __('Please enter CAPTCHA code.'));
	}else if ( $_SESSION['1k2j48djh'] != md5($member['captcha']) ){
		$errors->add('wrong_captcha', __('The entered CAPTCHA code is invalid.'));
		unset($_SESSION['1k2j48djh']);
	}

	if(!isset($member['agree']) || $member['agree']!='on'){		
		$errors->add('not_agree', __('You need to agree to the terms of service and privacy policy.'));
	}

	if ( $errors->get_error_code() ){
		$_SESSION['errors'] = $errors;
		return false;
	}

	$user_pass = $member['password'];
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( 'Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
		$_SESSION['errors'] = $errors;
		return false;
	}

	return $user_id;
}



function sp_validate_change($member){

	$errors = new WP_Error();


	$user_id = get_current_user_id();

	$unsaved_changes = array();

	if(isset($member['email'])){
		$user_email =  $member['email'];

		// Check the e-mail address
		if ( $user_email == '' ) {
			$errors->add( 'empty_email', __( 'Please type your e-mail address.' ) );

		} elseif ( ! is_email( $user_email ) ) {
			$errors->add( 'invalid_email', __( 'The email address isn&#8217;t correct.' ) );
			$user_email = '';
		} else{
			$existing_id = email_exists( $user_email );
			if ($existing_id && $existing_id != $user_id )
			$errors->add( 'email_exists', __( 'This email is already registered, please choose another one.' ) );
		}
		$unsaved_changes['email'] = $user_email;

	}

	if(isset($member['phone'])){

		if ( empty( $member['phone'] ) ) {
			$errors->add('empty_phone', __('Please enter your mobile/cell number.'));
		}else if ( !is_numeric( $member['phone'] ) ) {
			$errors->add('empty_phone', __('Please enter a valid mobile/cell number.'));
		}		

		$unsaved_changes['phone'] = $member['phone'];
	}

		$new_pass = false;

	if(isset($member['password'])){
		if ( empty( $member['password'] ) ) {
			$errors->add('empty_password', __('Please enter a password.'));
		}else if ( $member['password'] != $member['confirm_password'] ) {
			$errors->add('password_mismatch', __('Your password and the confirmation do not match.'));
		}

		$new_pass = true;
	}


	if ( $errors->get_error_code() ){
		$_SESSION['errors'] = $errors;
		return false;
	}

	if(isset($unsaved_changes['email']) || isset($unsaved_changes['phone'])){

		update_user_meta( $user_id, 'unsaved_changes', serialize($unsaved_changes));
		$user_info = get_userdata( $user_id );
		$user_email = isset($unsaved_changes['email'])? $unsaved_changes['email'] : $user_info->user_email;

		sp_send_activation_email($user_info->user_login, $user_email, $unsaved_changes);

		$errors->add( 'activation_link', __( 'Please check your email for activation link.' ), 'message' );
	}




	if($new_pass){
		wp_set_password( trim($member['password']), $user->ID );
		$errors->add( 'pwd_change_success', __( 'Password successfully changed.' ), 'message' );
	}

	$_SESSION['errors'] = $errors;

	return true;

}


function sp_send_activation_email($user_login, $user_email, $unsaved_changes = false){
	global $wpdb;

	$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));

	if ( empty($key) ) {
		// Generate something random for a key...
		$key = wp_generate_password(20, false);
		// Now insert the new md5 key into the db
		$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
	}

	if ( is_multisite() )
		$blogname = $GLOBALS['current_site']->site_name;
	else
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	if (!$unsaved_changes) {
		$title = sprintf( __('[%s]: Thank you for registering'), $blogname );

		$message = __('Thank you for registering to '.$blogname .', '. $user_login .'.' ). "\r\n\r\n";
		$message .= __('Your membership is almost complete. To activate your account, please click the following address:') . "\r\n\r\n";
		$message .=  network_site_url("sign-in?action=activate&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";
		$message .= __('Cheers,') . "\r\n\r\n";
		$message .= __($blogname . ' <'.network_home_url( '/' ) . '>') . "\r\n\r\n";
	} else {
		$title = sprintf( __('[%s] Activate New Details'), $blogname );

		$message = sprintf(__('Hello %s'), $user_login) . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= __('New Details:' ) . "\r\n\r\n";
		foreach ($unsaved_changes as $k => $value) {
			$message .= __(ucfirst($k) . ':' . $value ) . "\r\n\r\n";
		}
		$message .= __('To activate your new information, please visit the following address:') . "\r\n\r\n";
		$message .= '<' . network_site_url("account?action=activate&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

	}


	if ( $message && !wp_mail($user_email, $title, $message) )
		wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

	return true;

}

function sp_validate_and_reset($member){
	global $wpdb, $current_site;

	$errors = new WP_Error();

	if ( empty( $member['email'] ) ) {
		$errors->add('empty_email', __('Please enter your e-mail address.'));
	} else if ( strpos( $member['email'], '@' ) ) {
		$user_data = get_user_by( 'email', trim( $member['email']) );
		if ( empty( $user_data ) )
			$errors->add('invalid_email', __('There is no user registered with that email address.'));
	}

	do_action('lostpassword_post');

	if ( $errors->get_error_code() )
		return $errors;

	if ( !$user_data ) {
		$errors->add('invalidcombo', __('Invalid e-mail.'));
		return $errors;
	}

	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	do_action('retreive_password', $user_login);  // Misspelled and deprecated
	do_action('retrieve_password', $user_login);

	$allow = apply_filters('allow_password_reset', true, $user_data->ID);

	if ( ! $allow )
		return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
	else if ( is_wp_error($allow) )
		return $allow;

	$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
	if ( empty($key) ) {
		// Generate something random for a key...
		$key = wp_generate_password(20, false);
		do_action('retrieve_password_key', $user_login, $key);
		// Now insert the new md5 key into the db
		$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
	}

	$message = __('Hi '.$user_data->first_name . ' ' . $user_data->last_name) . "\r\n\r\n";
	$message = __('We received a forgot password request on '.network_home_url( '/' ).'. Here are you account details:') . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= __('To reset your password, Click on the the following URL or copy the URL in your browser address.') . "\r\n\r\n";
	$message .= '<' . network_site_url("forgot-password?action=activate&resetpass=true&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n\r\n";
	$message .= __('If you have not initiated forgot password, please ignore this email.') . "\r\n\r\n";
	$message .= __('Thanks,') . "\r\n\r\n";
	if ( is_multisite() )
		$blogname = $GLOBALS['current_site']->site_name;
	else
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$title = sprintf( __('[%s] Password Reset'), $blogname );
	$message .= __($blogname.' Team') . "\r\n\r\n";

	$title = apply_filters('retrieve_password_title', $title);
	$message = apply_filters('retrieve_password_message', $message, $key);

	if ( $message && !wp_mail($user_email, $title, $message) )
		wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );

	return true;
}