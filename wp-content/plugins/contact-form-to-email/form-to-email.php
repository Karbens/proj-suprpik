<?php
/*
Plugin Name: Contact Form to Email
Plugin URI: http://wordpress.dwbooster.com/forms/contact-form-to-email
Description: Contact form that sends the data to email and also to a database list and CSV file.
Version: 1.01
Author: CodePeople.net
Author URI: http://codepeople.net
License: GPL
*/

define('CP_CFEMAIL_DEFER_SCRIPTS_LOADING', (get_option('CP_CFTE_LOAD_SCRIPTS',"1") == "1"?true:false));

define('CP_CFEMAIL_DEFAULT_form_structure', '[[{"name":"email","index":0,"title":"Email","ftype":"femail","userhelp":"","csslayout":"","required":true,"predefined":"","size":"medium"},{"name":"subject","index":1,"title":"Subject","required":true,"ftype":"ftext","userhelp":"","csslayout":"","predefined":"","size":"medium"},{"name":"message","index":2,"size":"large","required":true,"title":"Message","ftype":"ftextarea","userhelp":"","csslayout":"","predefined":""}],[{"title":"Contact Form","description":"You can use the following form to contact us.","formlayout":"top_aligned"}]]');

define('CP_CFEMAIL_DEFAULT_fp_subject', 'Contact from the blog...');
define('CP_CFEMAIL_DEFAULT_fp_inc_additional_info', 'true');
define('CP_CFEMAIL_DEFAULT_fp_return_page', get_site_url());
define('CP_CFEMAIL_DEFAULT_fp_message', "The following contact message has been sent:\n\n<%INFO%>\n\n");

define('CP_CFEMAIL_DEFAULT_cu_enable_copy_to_user', 'true');
define('CP_CFEMAIL_DEFAULT_cu_user_email_field', '');
define('CP_CFEMAIL_DEFAULT_cu_subject', 'Confirmation: Message received...');
define('CP_CFEMAIL_DEFAULT_cu_message', "Thank you for your message. We will reply you as soon as possible.\n\nThis is a copy of the data sent:\n\n<%INFO%>\n\nBest Regards.");
define('CP_CFEMAIL_DEFAULT_email_format','text');

define('CP_CFEMAIL_DEFAULT_vs_use_validation', 'true');

define('CP_CFEMAIL_DEFAULT_vs_text_is_required', 'This field is required.');
define('CP_CFEMAIL_DEFAULT_vs_text_is_email', 'Please enter a valid email address.');

define('CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy', 'Please enter a valid date with this format(mm/dd/yyyy)');
define('CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy', 'Please enter a valid date with this format(dd/mm/yyyy)');
define('CP_CFEMAIL_DEFAULT_vs_text_number', 'Please enter a valid number.');
define('CP_CFEMAIL_DEFAULT_vs_text_digits', 'Please enter only digits.');
define('CP_CFEMAIL_DEFAULT_vs_text_max', 'Please enter a value less than or equal to {0}.');
define('CP_CFEMAIL_DEFAULT_vs_text_min', 'Please enter a value greater than or equal to {0}.');

define('CP_CFEMAIL_DEFAULT_cv_enable_captcha', 'true');
define('CP_CFEMAIL_DEFAULT_cv_width', '180');
define('CP_CFEMAIL_DEFAULT_cv_height', '60');
define('CP_CFEMAIL_DEFAULT_cv_chars', '5');
define('CP_CFEMAIL_DEFAULT_cv_font', 'font-1.ttf');
define('CP_CFEMAIL_DEFAULT_cv_min_font_size', '25');
define('CP_CFEMAIL_DEFAULT_cv_max_font_size', '35');
define('CP_CFEMAIL_DEFAULT_cv_noise', '200');
define('CP_CFEMAIL_DEFAULT_cv_noise_length', '4');
define('CP_CFEMAIL_DEFAULT_cv_background', 'ffffff');
define('CP_CFEMAIL_DEFAULT_cv_border', '000000');
define('CP_CFEMAIL_DEFAULT_cv_text_enter_valid_captcha', 'Please enter a valid captcha code.');


/* initialization / install */

include_once dirname( __FILE__ ) . '/classes/cp-base-class.inc.php';
include_once dirname( __FILE__ ) . '/cp-main-class.inc.php';

$cp_plugin = new CP_ContactFormToEmail;

register_activation_hook(__FILE__, array($cp_plugin,'install') ); 
add_action( 'media_buttons', array($cp_plugin, 'insert_button'), 11);
add_action( 'init', array($cp_plugin, 'data_management'));

if ( is_admin() ) {    
    add_action('admin_enqueue_scripts', array($cp_plugin,'insert_adminScripts'), 1);    
    add_filter("plugin_action_links_".plugin_basename(__FILE__), array($cp_plugin,'plugin_page_links'));   
    add_action('admin_menu', array($cp_plugin,'admin_menu') );
} else {    
    add_shortcode( $cp_plugin->shorttag, array($cp_plugin, 'filter_content') );    
}  

?>