<?php

class CP_ContactFormToEmail extends CP_CFTEMAIL_BaseClass {

    private $menu_parameter = 'cp_contactformtoemail';
    private $prefix = 'cp_contactformtoemail';
    private $plugin_name = 'Contact Form to Email';
    private $plugin_URL = 'http://wordpress.dwbooster.com/forms/contact-form-to-email';
    protected $table_items = "cftemail_forms";
    private $table_messages = "cftemail_messages";

    public $shorttag = 'CONTACT_FORM_TO_EMAIL';

    function _install() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_messages."'");
        if (!count($results))
        {
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_messages." (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                formid INT NOT NULL,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                ipaddr VARCHAR(32) DEFAULT '' NOT NULL,
                notifyto VARCHAR(250) DEFAULT '' NOT NULL,
                data text,
                posted_data text,
                UNIQUE KEY id (id)
            );";
            $wpdb->query($sql);
        }

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_items."'");
        if (!count($results))
        {
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_items." (
                 id mediumint(9) NOT NULL AUTO_INCREMENT,

                 form_name VARCHAR(250) DEFAULT '' NOT NULL,

                 form_structure text,

                 fp_from_email VARCHAR(250) DEFAULT '' NOT NULL,
                 fp_destination_emails text,
                 fp_subject VARCHAR(250) DEFAULT '' NOT NULL,
                 fp_inc_additional_info VARCHAR(10) DEFAULT '' NOT NULL,
                 fp_return_page VARCHAR(250) DEFAULT '' NOT NULL,
                 fp_message text,
                 fp_emailformat VARCHAR(10) DEFAULT '' NOT NULL,

                 cu_enable_copy_to_user VARCHAR(10) DEFAULT '' NOT NULL,
                 cu_user_email_field VARCHAR(250) DEFAULT '' NOT NULL,
                 cu_subject VARCHAR(250) DEFAULT '' NOT NULL,
                 cu_message text,
                 cu_emailformat VARCHAR(10) DEFAULT '' NOT NULL,

                 vs_use_validation VARCHAR(10) DEFAULT '' NOT NULL,
                 vs_text_is_required VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_is_email VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_datemmddyyyy VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_dateddmmyyyy VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_number VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_digits VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_max VARCHAR(250) DEFAULT '' NOT NULL,
                 vs_text_min VARCHAR(250) DEFAULT '' NOT NULL,

                 cv_enable_captcha VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_width VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_height VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_chars VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_font VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_min_font_size VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_max_font_size VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_noise VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_noise_length VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_background VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_border VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_text_enter_valid_captcha VARCHAR(200) DEFAULT '' NOT NULL,

                 UNIQUE KEY id (id)
            );";
            $wpdb->query($sql);
        }

        // insert initial data
        $count = $wpdb->get_var(  "SELECT COUNT(id) FROM ".$wpdb->prefix.$this->table_items  );
        if (!$count)
        {
            define('CP_CFEMAIL_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
            define('CP_CFEMAIL_DEFAULT_fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_from_email);
            $wpdb->insert( $wpdb->prefix.$this->table_items, array( 'id' => 1,
                                      'form_name' => 'Form 1',

                                      'form_structure' => $this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure),

                                      'fp_from_email' => $this->get_option('fp_from_email', CP_CFEMAIL_DEFAULT_fp_from_email),
                                      'fp_destination_emails' => $this->get_option('fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_destination_emails),
                                      'fp_subject' => $this->get_option('fp_subject', CP_CFEMAIL_DEFAULT_fp_subject),
                                      'fp_inc_additional_info' => $this->get_option('fp_inc_additional_info', CP_CFEMAIL_DEFAULT_fp_inc_additional_info),
                                      'fp_return_page' => $this->get_option('fp_return_page', CP_CFEMAIL_DEFAULT_fp_return_page),
                                      'fp_message' => $this->get_option('fp_message', CP_CFEMAIL_DEFAULT_fp_message),
                                      'fp_emailformat' => $this->get_option('fp_emailformat', CP_CFEMAIL_DEFAULT_email_format),

                                      'cu_enable_copy_to_user' => $this->get_option('cu_enable_copy_to_user', CP_CFEMAIL_DEFAULT_cu_enable_copy_to_user),
                                      'cu_user_email_field' => $this->get_option('cu_user_email_field', CP_CFEMAIL_DEFAULT_cu_user_email_field),
                                      'cu_subject' => $this->get_option('cu_subject', CP_CFEMAIL_DEFAULT_cu_subject),
                                      'cu_message' => $this->get_option('cu_message', CP_CFEMAIL_DEFAULT_cu_message),
                                      'cu_emailformat' => $this->get_option('cu_emailformat', CP_CFEMAIL_DEFAULT_email_format),

                                      'vs_use_validation' => $this->get_option('vs_use_validation', CP_CFEMAIL_DEFAULT_vs_use_validation),
                                      'vs_text_is_required' => $this->get_option('vs_text_is_required', CP_CFEMAIL_DEFAULT_vs_text_is_required),
                                      'vs_text_is_email' => $this->get_option('vs_text_is_email', CP_CFEMAIL_DEFAULT_vs_text_is_email),
                                      'vs_text_datemmddyyyy' => $this->get_option('vs_text_datemmddyyyy', CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy),
                                      'vs_text_dateddmmyyyy' => $this->get_option('vs_text_dateddmmyyyy', CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy),
                                      'vs_text_number' => $this->get_option('vs_text_number', CP_CFEMAIL_DEFAULT_vs_text_number),
                                      'vs_text_digits' => $this->get_option('vs_text_digits', CP_CFEMAIL_DEFAULT_vs_text_digits),
                                      'vs_text_max' => $this->get_option('vs_text_max', CP_CFEMAIL_DEFAULT_vs_text_max),
                                      'vs_text_min' => $this->get_option('vs_text_min', CP_CFEMAIL_DEFAULT_vs_text_min),

                                      'cv_enable_captcha' => $this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha),
                                      'cv_width' => $this->get_option('cv_width', CP_CFEMAIL_DEFAULT_cv_width),
                                      'cv_height' => $this->get_option('cv_height', CP_CFEMAIL_DEFAULT_cv_height),
                                      'cv_chars' => $this->get_option('cv_chars', CP_CFEMAIL_DEFAULT_cv_chars),
                                      'cv_font' => $this->get_option('cv_font', CP_CFEMAIL_DEFAULT_cv_font),
                                      'cv_min_font_size' => $this->get_option('cv_min_font_size', CP_CFEMAIL_DEFAULT_cv_min_font_size),
                                      'cv_max_font_size' => $this->get_option('cv_max_font_size', CP_CFEMAIL_DEFAULT_cv_max_font_size),
                                      'cv_noise' => $this->get_option('cv_noise', CP_CFEMAIL_DEFAULT_cv_noise),
                                      'cv_noise_length' => $this->get_option('cv_noise_length', CP_CFEMAIL_DEFAULT_cv_noise_length),
                                      'cv_background' => $this->get_option('cv_background', CP_CFEMAIL_DEFAULT_cv_background),
                                      'cv_border' => $this->get_option('cv_border', CP_CFEMAIL_DEFAULT_cv_border),
                                      'cv_text_enter_valid_captcha' => $this->get_option('cv_text_enter_valid_captcha', CP_CFEMAIL_DEFAULT_cv_text_enter_valid_captcha)
                                     )
                      );
        }
    }


    /* Filter for placing the maps into the contents */
    public function filter_content($atts) {
        global $wpdb;
        extract( shortcode_atts( array(
    		                           'id' => '',
    	                        ), $atts ) );
        if ($id != '')
            $this->item = $id;
        ob_start();
        $this->insert_public_item();
        $buffered_contents = ob_get_contents();
        ob_end_clean();
        return $buffered_contents;
    }


    function insert_public_item() {
        global $wpdb;
        if (CP_CFEMAIL_DEFER_SCRIPTS_LOADING)
        {
            wp_deregister_script('query-stringify');
            wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));

            wp_deregister_script($this->prefix.'_validate_script');
            wp_register_script($this->prefix.'_validate_script', plugins_url('/js/jquery.validate.js', __FILE__));

            wp_enqueue_script( $this->prefix.'_builder_script',
               plugins_url('/js/fbuilderf.jquery.js', __FILE__),array("jquery","jquery-ui-core","jquery-ui-datepicker","query-stringify",$this->prefix."_validate_script"), false, true );

            wp_localize_script($this->prefix.'_builder_script', $this->prefix.'_fbuilder_config', array('obj' =>
            '{"pub":true,"messages": {
            	                	"required": "'.str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_is_required', CP_CFEMAIL_DEFAULT_vs_text_is_required)).'",
            	                	"email": "'.str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_is_email', CP_CFEMAIL_DEFAULT_vs_text_is_email)).'",
            	                	"datemmddyyyy": "'.str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_datemmddyyyy', CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy)).'",
            	                	"dateddmmyyyy": "'.str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_dateddmmyyyy', CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy)).'",
            	                	"number": "'.str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_number', CP_CFEMAIL_DEFAULT_vs_text_number)).'",
            	                	"digits": "'.str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_digits', CP_CFEMAIL_DEFAULT_vs_text_digits)).'",
            	                	"max": "'.str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_max', CP_CFEMAIL_DEFAULT_vs_text_max)).'",
            	                	"min": "'.str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_min', CP_CFEMAIL_DEFAULT_vs_text_min)).'"
            	                }}'
            ));
        }
        else
        {
            wp_enqueue_script( "jquery" );
            wp_enqueue_script( "jquery-ui-core" );
            wp_enqueue_script( "jquery-ui-datepicker" );
        }
        ?>
        <script type="text/javascript">
         function <?php echo $this->prefix; ?>_pform_doValidate(form)
         {
            document.<?php echo $this->prefix; ?>_pform.cp_ref_page.value = document.location;
            <?php if ($this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha) != 'false') { ?>  $dexQuery = jQuery.noConflict();
            if (document.<?php echo $this->prefix; ?>_pform.hdcaptcha_<?php echo $this->prefix; ?>_post.value == '') { setTimeout( "<?php echo $this->prefix; ?>_cerror()", 100); return false; }
            var result = $dexQuery.ajax({ type: "GET", url: "<?php echo $this->get_site_url(); ?>?<?php echo $this->prefix; ?>_pform_process=2&hdcaptcha_<?php echo $this->prefix; ?>_post="+document.<?php echo $this->prefix; ?>_pform.hdcaptcha_<?php echo $this->prefix; ?>_post.value, async: false }).responseText;
            if (result == "captchafailed") {
                $dexQuery("#captchaimg").attr('src', $dexQuery("#captchaimg").attr('src')+'&'+Date());
                setTimeout( "<?php echo $this->prefix; ?>_cerror()", 100);
                return false;
            } else <?php } ?>
                return true;
         }
         function <?php echo $this->prefix; ?>_cerror(){$dexQuery = jQuery.noConflict();$dexQuery("#hdcaptcha_error").css('top',$dexQuery("#hdcaptcha_<?php echo $this->prefix; ?>_post").outerHeight());$dexQuery("#hdcaptcha_error").css("display","inline");}
        </script>
        <?php
        define('CP_AUTH_INCLUDE',true);
        @include_once dirname( __FILE__ ) . '/cp-public-int.inc.php';
        if (!CP_CFEMAIL_DEFER_SCRIPTS_LOADING)
        {
            // This code won't be used in most cases. This code is for preventing problems in wrong WP themes and conflicts with third party plugins.
            ?>
                 <?php $plugin_url = plugins_url('', __FILE__); ?>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/jquery.js'; ?>'></script>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/jquery.ui.core.min.js'; ?>'></script>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/jquery.ui.datepicker.min.js'; ?>'></script>  
                 <script type='text/javascript' src='<?php echo plugins_url('js/jQuery.stringify.js', __FILE__); ?>'></script>
                 <script type='text/javascript' src='<?php echo plugins_url('js/jquery.validate.js', __FILE__); ?>'></script>
                 <script type='text/javascript'>
                 /* <![CDATA[ */
                 var <?php echo $this->prefix; ?>_fbuilder_config = {"obj":"{\"pub\":true,\"messages\": {\n    \t                \t\"required\": \"<?php echo str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_is_required', CP_CFEMAIL_DEFAULT_vs_text_is_required));?>\",\n    \t                \t\"email\": \"<?php echo str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_is_email', CP_CFEMAIL_DEFAULT_vs_text_is_email));?>\",\n    \t                \t\"datemmddyyyy\": \"<?php echo str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_datemmddyyyy', CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy));?>\",\n    \t                \t\"dateddmmyyyy\": \"<?php echo str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_dateddmmyyyy', CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy));?>\",\n    \t                \t\"number\": \"<?php echo str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_number', CP_CFEMAIL_DEFAULT_vs_text_number));?>\",\n    \t                \t\"digits\": \"<?php echo str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_digits', CP_CFEMAIL_DEFAULT_vs_text_digits));?>\",\n    \t                \t\"max\": \"<?php echo str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_max', CP_CFEMAIL_DEFAULT_vs_text_max));?>\",\n    \t                \t\"min\": \"<?php echo str_replace(array('"', "'"),array('\\"', "\\'"),$this->get_option('vs_text_min', CP_CFEMAIL_DEFAULT_vs_text_min));?>\"\n    \t                }}"};
                 /* ]]> */
                 </script>
                 <script type='text/javascript' src='<?php echo plugins_url('js/fbuilderf.jquery.js', __FILE__); ?>'></script>
            <?php
        }
    }


    /* Code for the admin area */

    public function plugin_page_links($links) {
        $customAdjustments_link = '<a href="http://wordpress.dwbooster.com/contact-us">'.__('Request custom changes').'</a>';
    	array_unshift($links, $customAdjustments_link);
        $settings_link = '<a href="options-general.php?page='.$this->menu_parameter.'">'.__('Settings').'</a>';
    	array_unshift($links, $settings_link);
    	$help_link = '<a href="'.$this->plugin_URL.'">'.__('Help').'</a>';
    	array_unshift($links, $help_link);
    	return $links;
    }


    public function admin_menu() {
        add_options_page($this->plugin_name.' Options', $this->plugin_name, 'manage_options', $this->menu_parameter, array($this, 'settings_page') );
        add_menu_page( $this->plugin_name.' Options', $this->plugin_name, 'edit_pages', $this->menu_parameter, array($this, 'settings_page') );
    }


    function insert_button() {
        print '<a href="javascript:send_to_editor(\'[CONTACT_FORM_TO_EMAIL]\');" title="'.__('Insert').' '.$this->plugin_name.'"><img hspace="5" src="'.plugins_url('/images/cp_form.gif', __FILE__).'" alt="'.__('Insert').' '.$this->plugin_name.'" /></a>';
    }


    public function settings_page() {
        global $wpdb;
        if ($this->get_param("cal"))
        {
            $this->item = $this->get_param("cal");
            if ($this->get_param("list") == '1')
                @include_once dirname( __FILE__ ) . '/cp-admin-int-message-list.inc.php';
            else if ($this->get_param("report") == '1')
                @include_once dirname( __FILE__ ) . '/cp-admin-int-report.inc.php';
            else
                @include_once dirname( __FILE__ ) . '/cp-admin-int.inc.php';
        }
        else
            @include_once dirname( __FILE__ ) . '/cp-admin-int-list.inc.php';
    }


    function insert_adminScripts($hook) {
        if ($this->get_param("page") == $this->menu_parameter)
        {
            wp_deregister_script('query-stringify');
            wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', __FILE__));
            wp_enqueue_script( $this->prefix.'_builder_script', plugins_url('/js/fbuilderf.jquery.js', __FILE__),array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-tabs","jquery-ui-droppable","jquery-ui-button","query-stringify","jquery-ui-datepicker") );
            wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        }
        if( 'post.php' != $hook  && 'post-new.php' != $hook )
            return;
        // space to include some script in the post or page areas if needed
    }

    /* hook for checking posted data for the admin area */

    function data_management() {
        global $wpdb;

        if ($this->get_param($this->prefix.'_encodingfix') == '1')
        {
            $wpdb->query('alter table '.$wpdb->prefix.$this->table_items.' convert to character set utf8 collate utf8_unicode_ci;');
            $wpdb->query('alter table '.$wpdb->prefix.$this->table_messages.' convert to character set utf8 collate utf8_unicode_ci;');
            echo 'Ok, encoding fixed.';
            exit;
        }

        if ($this->get_param($this->prefix.'_captcha') == 'captcha' )
        {
            @include_once dirname( __FILE__ ) . '/captcha/captcha.php';
            return;
        }


        if ($this->get_param($this->prefix.'_csv') && is_admin() )
        {
            $this->export_csv();
            return;
        }

        if ( $this->get_param($this->prefix.'_post_options') && is_admin() )
        {
            $this->save_options();
            return;
        }

    	if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST[$this->prefix.'_pform_process'] ) )
    	    if ( 'GET' != $_SERVER['REQUEST_METHOD'] || !isset( $_GET['hdcaptcha_'.$this->prefix.'_post'] ) )
    		    return;

        if ($this->get_param($this->prefix.'_id')) $this->item = $this->get_param($this->prefix.'_id');

        @session_start();
        if (
               ($this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha) != 'false') &&
               ( (strtolower($this->get_param('hdcaptcha_'.$this->prefix.'_post')) != strtolower($_SESSION['rand_code'])) ||
                 ($_SESSION['rand_code'] == '')
               )
           )
        {
            echo 'captchafailed';
            exit;
        }

    	// if this isn't the real post (it was the captcha verification) then echo ok and exit
        if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST[$this->prefix.'_pform_process'] ) )
    	{
    	    echo 'ok';
            exit;
    	}

        // get form info
        //---------------------------
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        $form_data = json_decode($this->cleanJSON($this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure)));
        $fields = array();
        foreach ($form_data[0] as $item)
            $fields[$item->name] = $item->title;

        // grab posted data
        //---------------------------
        $buffer = "";
        foreach ($_POST as $item => $value)
            if (isset($fields[$item]))
            {
                $buffer .= $fields[$item] . ": ". (is_array($value)?(implode(", ",$value)):($value)) . "\n\n";
                $params[$item] = $value;
            }
        $buffer_A = $buffer;

        // insert into database
        //---------------------------
        $to = $this->get_option('cu_user_email_field', CP_CFEMAIL_DEFAULT_cu_user_email_field);
        $rows_affected = $wpdb->insert( $wpdb->prefix.$this->table_messages, array( 'formid' => $this->item,
                                                                                    'time' => current_time('mysql'),
                                                                                    'ipaddr' => $_SERVER['REMOTE_ADDR'],
                                                                                    'notifyto' => $_POST[$to],
                                                                                    'posted_data' => serialize($params),
                                                                                    'data' =>$buffer_A
                                                                                   ) );
        if (!$rows_affected)
        {
            echo 'Error saving data! Please try again.';
            echo '<br /><br />Error debug information: '.mysql_error();
            exit;
        }

        $myrows = $wpdb->get_results( "SELECT MAX(id) as max_id FROM ".$wpdb->prefix.$this->table_messages );
        $item_number = $myrows[0]->max_id;

        $this->ready_to_go_reservation($item_number, "", $params);

        header("Location: ".$this->get_option('fp_return_page', CP_CFEMAIL_DEFAULT_fp_return_page));
        exit();
    }


    function check_upload($uploadfiles) {
        $filename = $uploadfiles['name'];
        $filetype = wp_check_filetype( basename( $filename ), null );

        if ( in_array ($filetype["ext"],array("php","asp","aspx","cgi","pl","perl","exe")) )
            return false;
        else
            return true;
    }


    function ready_to_go_reservation($itemnumber, $payer_email = "", $params = array())
    {

        global $wpdb;

        $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE id=".$itemnumber );

        $mycalendarrows = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.$this->table_items.' WHERE `id`='.$myrows[0]->formid);

        $this->item = $myrows[0]->formid;

        $buffer_A = $myrows[0]->data;
        $buffer = $buffer_A;

        if ('true' == $this->get_option('fp_inc_additional_info', CP_CFEMAIL_DEFAULT_fp_inc_additional_info))
        {
            $buffer .="ADDITIONAL INFORMATION\n"
                  ."*********************************\n"
                  ."IP: ".$myrows[0]->ipaddr."\n"
                  ."Server Time:  ".date("Y-m-d H:i:s")."\n";
        }

        // 1- Send email
        //---------------------------
        $attachments = array();
        if ('html' == $this->get_option('fp_emailformat', CP_CFEMAIL_DEFAULT_email_format))
            $message = str_replace('<%INFO%>',str_replace("\n","<br />",str_replace('<','&lt;',$buffer)),$this->get_option('fp_message', CP_CFEMAIL_DEFAULT_fp_message));
        else
            $message = str_replace('<%INFO%>',$buffer,$this->get_option('fp_message', CP_CFEMAIL_DEFAULT_fp_message));
        foreach ($params as $item => $value)
        {
            $message = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$message);
            if (strpos($item,"_link"))
                $attachments[] = $value;
        }
        $subject = $this->get_option('fp_subject', CP_CFEMAIL_DEFAULT_fp_subject);
        $from = $this->get_option('fp_from_email', CP_CFEMAIL_DEFAULT_fp_from_email);
        $to = explode(",",$this->get_option('fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_destination_emails));
        if ('html' == $this->get_option('fp_emailformat', CP_CFEMAIL_DEFAULT_email_format)) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";

        foreach ($to as $item)
            if (trim($item) != '')
            {
                wp_mail(trim($item), $subject, $message,
                    "From: \"$from\" <".$from.">\r\n".
                    $content_type.
                    "X-Mailer: PHP/" . phpversion(), $attachments);
            }

        // 2- Send copy to user
        //---------------------------
        $to = $this->get_option('cu_user_email_field', CP_CFEMAIL_DEFAULT_cu_user_email_field);
        $_POST[$to] = $myrows[0]->notifyto;
        if ((trim($_POST[$to]) != '' || $payer_email != '') && 'true' == $this->get_option('cu_enable_copy_to_user', CP_CFEMAIL_DEFAULT_cu_enable_copy_to_user))
        {
            if ('html' == $this->get_option('cu_emailformat', CP_CFEMAIL_DEFAULT_email_format))
                $message = str_replace('<%INFO%>',str_replace("\n","<br />",str_replace('<','&lt;',$buffer_A)).'</pre>',$this->get_option('cu_message', CP_CFEMAIL_DEFAULT_cu_message));
            else
                $message = str_replace('<%INFO%>',$buffer_A,$this->get_option('cu_message', CP_CFEMAIL_DEFAULT_cu_message));
            foreach ($params as $item => $value)
                $message = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$message);
            $subject = $this->get_option('cu_subject', CP_CFEMAIL_DEFAULT_cu_subject);
            if ('html' == $this->get_option('cu_emailformat', CP_CFEMAIL_DEFAULT_email_format)) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
            if ($_POST[$to] != '')
                wp_mail(trim($_POST[$to]), $subject, $message,
                        "From: \"$from\" <".$from.">\r\n".
                        $content_type.
                        "X-Mailer: PHP/" . phpversion());
            if ($_POST[$to] != $payer_email && $payer_email != '')
                wp_mail(trim($payer_email), $subject, $message,
                        "From: \"$from\" <".$from.">\r\n".
                        $content_type.
                        "X-Mailer: PHP/" . phpversion());
        }

    }


    function save_options()
    {
        global $wpdb;
        $this->item = $_POST[$this->prefix."_id"];

        foreach ($_POST as $item => $value)
            $_POST[$item] = stripcslashes($value);

        $data = array(
                      'fp_from_email' => $_POST['fp_from_email'],
                      'fp_destination_emails' => $_POST['fp_destination_emails'],
                      'fp_subject' => $_POST['fp_subject'],
                      'fp_inc_additional_info' => $_POST['fp_inc_additional_info'],
                      'fp_return_page' => $_POST['fp_return_page'],
                      'fp_message' => $_POST['fp_message'],
                      'fp_emailformat' => $_POST['fp_emailformat'],

                      'cu_enable_copy_to_user' => $_POST['cu_enable_copy_to_user'],
                      'cu_user_email_field' => $_POST['cu_user_email_field'],
                      'cu_subject' => $_POST['cu_subject'],
                      'cu_message' => $_POST['cu_message'],
                      'cu_emailformat' => $_POST['cu_emailformat'],

                      'vs_use_validation' => $_POST['vs_use_validation'],
                      'vs_text_is_required' => $_POST['vs_text_is_required'],
                      'vs_text_is_email' => $_POST['vs_text_is_email'],
                      'vs_text_datemmddyyyy' => $_POST['vs_text_datemmddyyyy'],
                      'vs_text_dateddmmyyyy' => $_POST['vs_text_dateddmmyyyy'],
                      'vs_text_number' => $_POST['vs_text_number'],
                      'vs_text_digits' => $_POST['vs_text_digits'],
                      'vs_text_max' => $_POST['vs_text_max'],
                      'vs_text_min' => $_POST['vs_text_min'],

                      'cv_enable_captcha' => $_POST['cv_enable_captcha'],
                      'cv_width' => $_POST['cv_width'],
                      'cv_height' => $_POST['cv_height'],
                      'cv_chars' => $_POST['cv_chars'],
                      'cv_font' => $_POST['cv_font'],
                      'cv_min_font_size' => $_POST['cv_min_font_size'],
                      'cv_max_font_size' => $_POST['cv_max_font_size'],
                      'cv_noise' => $_POST['cv_noise'],
                      'cv_noise_length' => $_POST['cv_noise_length'],
                      'cv_background' => $_POST['cv_background'],
                      'cv_border' => $_POST['cv_border'],
                      'cv_text_enter_valid_captcha' => $_POST['cv_text_enter_valid_captcha']
    	);
        $wpdb->update ( $wpdb->prefix.$this->table_items, $data, array( 'id' => $this->item ));
    }


    function export_csv ()
    {
        if (!is_admin())
            return;
        global $wpdb;

        $this->item = intval($this->get_param("cal"));

        $cond = '';
        if ($this->get_param("search")) $cond .= " AND (data like '%".$wpdb->escape($this->get_param("search"))."%' OR posted_data LIKE '%".$wpdb->escape($this->get_param("search"))."%')";
        if ($this->get_param("dfrom")) $cond .= " AND (`time` >= '".$wpdb->escape($this->get_param("dfrom"))."')";
        if ($this->get_param("dto")) $cond .= " AND (`time` <= '".$wpdb->escape($this->get_param("dto"))." 23:59:59')";
        if ($this->item != 0) $cond .= " AND formid=".$this->item;

        $events = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );

        $fields = array("Form ID", "Time", "IP Address", "email");
        $values = array();
        foreach ($events as $item)
        {
            $value = array($item->formid, $item->time, $item->ipaddr, $item->notifyto);
            if ($item->posted_data)
                $data = unserialize($item->posted_data);
            else
                $data = array();

            $end = count($fields);
            for ($i=0; $i<$end; $i++)
                if (isset($data[$fields[$i]]) ){
                    $value[$i] = $data[$fields[$i]];
                    unset($data[$fields[$i]]);
                }

            foreach ($data as $k => $d)
            {
               $fields[] = $k;
               $value[] = $d;
            }
            $values[] = $value;
        }

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=export.csv");

        $end = count($fields);
        for ($i=0; $i<$end; $i++)
            echo '"'.str_replace('"','""', $fields[$i]).'",';
        echo "\n";
        foreach ($values as $item)
        {
            for ($i=0; $i<$end; $i++)
                echo '"'.str_replace('"','""', @$item[$i]).'",';
            echo "\n";
        }

        exit;
    }

} // end class

?>