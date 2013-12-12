<?php
/**
 * Plugin Name: SP Users
 * Plugin URI: 
 * Description: Custom Plugin for Super Picks Login.
 * Version: 1.0
 * Author: Arlston
 * Author URI: 
 * License: GPL2
 */

function sp_form_process_init() {
	global $wpdb;
	session_start();
   	if(isset($_POST['member'])) require_once('form_process.php');


   	if($_GET['member']=='logout'){
   		sp_update_session_meta(true);
	   	wp_logout();
		wp_safe_redirect( '/' );
		exit();   		
   	}

   	if(is_user_logged_in()){
   		sp_update_session_meta();
   	}

   	if(isset($_GET['action'],$_GET['key'],$_GET['login']) && $_GET['action']=='activate'){
   		if(isset($_GET['resetpass']) && $_GET['resetpass']=='true'){
   			$user = sp_check_activation_key($_GET['key'],$_GET['login']);
   			if ( is_wp_error($user) ) {
   				wp_redirect( site_url('/') );
   				exit;
   			}

   		}else{
	   		sp_activate_member($_GET['key'],$_GET['login']);
   		}
   	}

   	if(is_admin()){
   		if(isset($_POST['csv'])){
   			require_once('backoffice_query.php');
   		}
   		if(isset($_POST['csv_login'])){
   			require_once('login_reports.php');
   		}
   	}

}


function sp_registration_function(){
	if(!is_user_logged_in()) {
		require_once('register.php');
	}else {
		require_once('account.php');
	}
}

function sp_account_function(){
	if(is_user_logged_in()) {
		require_once('account.php');
	}else {
		require_once('login.php');
	}
}

function sp_login_function(){
	if(!is_user_logged_in()) {
		require_once('login.php');
	}else {
		require_once('account.php');
	}
}

function sp_password_function(){
	if(is_user_logged_in()) {
		require_once('account.php');
	}else {
		require_once('password.php');
	}

}

function sp_activate_member($key, $login){
	global $wpdb;
	$user = sp_check_activation_key($key, $login);

	if ( is_wp_error($user) ) {
		wp_redirect( site_url('/') );
		exit;
	}else{
		$login_counter = get_user_meta($user->ID, 'login_counter', true);

		if($login_counter!=0){
			$unsaved_changes = unserialize(get_user_meta($user->ID, 'unsaved_changes', true));

			if(isset($unsaved_changes['email']) || isset($unsaved_changes['phone'])){

				if(isset($unsaved_changes['email'])){
					wp_update_user( array ( 'ID' => $user->ID,
											'user_email' => $unsaved_changes['email']  ) );
				}
				if(isset($unsaved_changes['phone'])){
					update_user_meta($user->ID, 'phone', $unsaved_changes['phone']);
				}


				

				$title = __('User Details changed');
				$message = __($user->user_login. ' changed account details.') . "\r\n\r\n";

				$message .= __('New Details:' ) . "\r\n\r\n";
				foreach ($unsaved_changes as $k => $value) {
					$message .= __(ucfirst($k) . ':' . $value ) . "\r\n\r\n";
				}
				$message .= __('IP:'.$_SERVER['REMOTE_ADDR']) . "\r\n\r\n";

				if($acc_email = get_option( 'sp_account_email' )){
					wp_mail($acc_email, $title, $message);
				}

				update_user_meta($user->ID, 'unsaved_changes', '');
				$_SESSION['errors'] = new WP_Error('activation_success', __('Account details successfully updated'), 'message');		

			}

			wp_redirect( site_url('/account') );
			exit;


			
		}else {

			wp_set_auth_cookie($user->ID);

			$wpdb->update($wpdb->users, array('user_activation_key' => ''), array('ID' => $user->ID) );

			update_user_meta($user->ID, 'login_counter', $login_counter+1);

			sp_update_login_meta($user);

			$_SESSION['errors'] = new WP_Error('activation_success', __('Account successfully activated'), 'message');		
			wp_redirect( site_url('/account') );
			exit;			
		}
	}


}


function sp_update_login_meta($user){	
	$ua=getBrowser();
	$login_info = array('ip4' =>  $_SERVER['REMOTE_ADDR'],
						'user_agent' =>  $ua['userAgent'],
						'browser' =>  $ua['name'] . " " . $ua['version'],
						'device' =>  $ua['platform'],
						'updated_at' =>  current_time('mysql', 1));

	update_user_meta($user->ID, 'last_login_info', $login_info);

}

function sp_update_session_meta($destroy = false){
	$user_id = get_current_user_id();
	if($destroy){
		update_user_meta($user_id, 'session_info', NULL);
		return true;
	}

	update_user_meta($user_id, 'session_info', $_SERVER);

}




function getBrowser(){ 

    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'Mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}



function sp_users_install() { 
}

register_activation_hook( __FILE__, 'sp_users_install' );

add_action('init', 'sp_form_process_init');

add_action('wp_logout',create_function('','wp_redirect(home_url());exit();'));

add_shortcode( 'sp_registration', 'sp_registration_function' );
add_shortcode( 'sp_login', 'sp_login_function' );
add_shortcode( 'sp_account', 'sp_account_function' );
add_shortcode( 'sp_forgotten', 'sp_password_function' );

if(!is_admin()){
	wp_enqueue_style( 'sp_user.css', plugins_url( 'sp_user.css', __FILE__ ) );
	wp_enqueue_style( 'jquery-ui.css.css', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-datepicker');
	
}else{
	wp_enqueue_style( 'sp_user_admin.css', plugins_url( 'sp_user_admin.css', __FILE__ ) );

}


//hook for admin menu
add_action('admin_menu', 'sp_users_add');

function sp_users_add(){
	//add top level menu
	add_menu_page( __('SP Users Backoffice Reports'), __('Backoffice'), 'manage_options', 'sp_users', 'sp_backoffice_page'  );
	add_options_page( __('SP Users Options'),__('SP Users Options'), 'manage_options', 'sp_users_options', 'sp_admin_options');
	add_submenu_page( 'sp_users', __('SP Users Backoffice Reports'), __('Signups Report'), 'manage_options', 'sp_users_signups', 'sp_backoffice_signups' ); 
	add_submenu_page( 'sp_users', __('SP Users Backoffice Reports'), __('Member Report'), 'manage_options', 'sp_users_login', 'sp_backoffice_logins' ); 
}
function sp_backoffice_page(){

	require_once('backoffice_query.php');
	require_once('backoffice.php');
}

function sp_backoffice_signups(){
	require_once('signup_reports.php');
}
function sp_backoffice_logins(){
	require_once('login_reports.php');
}

function sp_admin_options(){
	require_once('sp_options.php');	
}

function sp_check_activation_key($key, $login) {
	global $wpdb;

	$key = preg_replace('/[^a-z0-9]/i', '', $key);

	if ( empty( $key ) || !is_string( $key ) )
		return new WP_Error('invalid_key', __('Invalid key'));

	if ( empty($login) || !is_string($login) )
		return new WP_Error('invalid_key', __('Invalid key'));

	$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login));

	if ( empty( $user ) )
		return new WP_Error('invalid_key', __('Invalid key'));

	return $user;
}