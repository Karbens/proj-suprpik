<?php

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

global $wpdb;
$message = "";
if (isset($_GET['a']) && $_GET['a'] == '1')
{
    define('CP_CFEMAIL_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
    define('CP_CFEMAIL_DEFAULT_fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_from_email);
    
    $wpdb->insert( $wpdb->prefix.$this->table_items, array( 
                                      'form_name' => stripcslashes($_GET["name"]),

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
    
    $message = "Item added";
} 
else if (isset($_GET['u']) && $_GET['u'] != '')
{
    $wpdb->query('UPDATE `'.$wpdb->prefix.$this->table_items.'` SET form_name="'.$wpdb->escape($_GET["name"]).'" WHERE id='.$_GET['u']);           
    $message = "Item updated";        
}
else if (isset($_GET['d']) && $_GET['d'] != '')
{
    $wpdb->query('DELETE FROM `'.$wpdb->prefix.$this->table_items.'` WHERE id='.$_GET['d']);       
    $message = "Item deleted";
} else if (isset($_GET['c']) && $_GET['c'] != '')
{
    $myrows = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix.$this->table_items." WHERE id=".$_GET['c'], ARRAY_A);    
    unset($myrows["id"]);
    $myrows["form_name"] = 'Cloned: '.$myrows["form_name"];
    $wpdb->insert( $wpdb->prefix.$this->table_items, $myrows);
    $message = "Item duplicated/cloned";
}
else if (isset($_GET['ac']) && $_GET['ac'] == 'st')
{   
    update_option( 'CP_CFTE_LOAD_SCRIPTS', ($_GET["scr"]=="1"?"0":"1") );   
    if ($_GET["chs"] != '')
    {
        $target_charset = $_GET["chs"];
        $tables = array( $wpdb->prefix.$this->table_messages, $wpdb->prefix.$this->table_items );                
        foreach ($tables as $tab)
        {  
            $myrows = $wpdb->get_results( "DESCRIBE {$tab}" );                                                                                 
            foreach ($myrows as $item)
	        {
	            $name = $item->Field;
		        $type = $item->Type;
		        if (preg_match("/^varchar\((\d+)\)$/i", $type, $mat) || !strcasecmp($type, "CHAR") || !strcasecmp($type, "TEXT") || !strcasecmp($type, "MEDIUMTEXT"))
		        {
	                $wpdb->query("ALTER TABLE {$tab} CHANGE {$name} {$name} {$type} COLLATE {$target_charset}");	            
	            }
	        }
        }
    }
    $message = "Throubleshoot settings updated";
}

if ($message) echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>".$message."</strong></p></div>";

?>
<div class="wrap">
<h2><?php echo $this->plugin_name; ?></h2>

<script type="text/javascript">
 function cp_addItem()
 {
    var calname = document.getElementById("cp_itemname").value;
    document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&a=1&r='+Math.random()+'&name='+encodeURIComponent(calname);       
 }
 
 function cp_updateItem(id)
 {
    var calname = document.getElementById("calname_"+id).value;    
    document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&u='+id+'&r='+Math.random()+'&name='+encodeURIComponent(calname);    
 }
 
 function cp_cloneItem(id)
 {
    document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&c='+id+'&r='+Math.random();  
 } 
 
 function cp_manageSettings(id)
 {
    document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&cal='+id+'&r='+Math.random();
 }
 
 function cp_viewMessages(id)
 {
    document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&cal='+id+'&list=1&r='+Math.random();
 } 
 
 function cp_viewReport(id)
 {
    document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&cal='+id+'&report=1&r='+Math.random();
 } 
 
 function cp_deleteItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {        
        document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&d='+id+'&r='+Math.random();
    }
 }
 
 function cp_updateConfig()
 {
    if (confirm('Are you sure that you want to update these settings?'))
    {        
        var scr = document.getElementById("ccscriptload").value;    
        var chs = document.getElementById("cccharsets").value;    
        document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&ac=st&scr='+scr+'&chs='+chs+'&r='+Math.random();
    }    
 } 
 
</script>


<div id="normal-sortables" class="meta-box-sortables">


 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Form List / Items List</span></h3>
  <div class="inside">
  
  
  <table cellspacing="10"> 
   <tr>
    <th align="left">ID</th><th align="left">Form Name</th><th align="left">&nbsp; &nbsp; Options</th><th align="left">Shorttag for Pages and Posts</th>
   </tr> 
<?php  

  $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );                                                                     
  foreach ($myrows as $item)         
  {
?>
   <tr> 
    <td nowrap><?php echo $item->id; ?></td>
    <td nowrap><input type="text" name="calname_<?php echo $item->id; ?>" id="calname_<?php echo $item->id; ?>" value="<?php echo esc_attr($item->form_name); ?>" /></td>          
    
    <td nowrap>&nbsp; &nbsp; 
                             <input type="button" name="calupdate_<?php echo $item->id; ?>" value="Update" onclick="cp_updateItem(<?php echo $item->id; ?>);" /> &nbsp; 
                             <input type="button" name="calmanage_<?php echo $item->id; ?>" value="Settings" onclick="cp_manageSettings(<?php echo $item->id; ?>);" /> &nbsp;                              
                             <input type="button" name="calmessages_<?php echo $item->id; ?>" value="Messages" onclick="cp_viewMessages(<?php echo $item->id; ?>);" /> &nbsp;                              
                             <input type="button" name="calreport_<?php echo $item->id; ?>" value="Reports" onclick="cp_viewReport(<?php echo $item->id; ?>);" /> &nbsp;                              
                             <input type="button" name="calclone_<?php echo $item->id; ?>" value="Clone" onclick="cp_cloneItem(<?php echo $item->id; ?>);" /> &nbsp;                              
                             <input type="button" name="caldelete_<?php echo $item->id; ?>" value="Delete" onclick="cp_deleteItem(<?php echo $item->id; ?>);" />                             
    </td>
    <td nowrap>[<?php echo $this->shorttag; ?> id="<?php echo $item->id; ?>"]</td>          
   </tr>
<?php  
   } 
?>   
     
  </table> 
    
    
   
  </div>    
 </div> 
 

 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>New Form</span></h3>
  <div class="inside"> 
   
    <form name="additem">
      Item Name:<br />
      <input type="text" name="cp_itemname" id="cp_itemname"  value="" /> <input type="button" onclick="cp_addItem();" name="gobtn" value="Add" />
      <br /><br />      
    </form>

  </div>    
 </div>


 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Throubleshoot Area</span></h3>
  <div class="inside"> 
    <p><strong>Important!</strong>: Use this area <strong>only</strong> if you are experiencing conflicts with third party plugins, with the theme scripts or with the character encoding.</p>
    <form name="updatesettings">
      Script load method:<br />
       <select id="ccscriptload" name="ccscriptload">
        <option value="0" <?php if (get_option('CP_CFTE_LOAD_SCRIPTS',"1") == "1") echo 'selected'; ?>>Classic (Recommended)</option>
        <option value="1" <?php if (get_option('CP_CFTE_LOAD_SCRIPTS',"1") != "1") echo 'selected'; ?>>Direct</option>
       </select><br />
       <em>* Change the script load method if the form doesn't appear in the public website.</em>
      
      <br /><br />
      Character encoding:<br />
       <select id="cccharsets" name="cccharsets">
        <option value="">Keep current charset (Recommended)</option>
        <option value="utf8_general_ci">UTF-8 (try this first)</option>
        <option value="latin1_swedish_ci">latin1_swedish_ci</option>
       </select><br />
       <em>* Update the charset if you are getting problems displaying special/non-latin characters. After updated you need to edit the special characters again.</em>
       <br />
       <input type="button" onclick="cp_updateConfig();" name="gobtn" value="UPDATE" />
      <br /><br />      
    </form>

  </div>    
 </div> 

  
</div> 


[<a href="http://wordpress.dwbooster.com/contact-us" target="_blank">Request Custom Modifications</a>] | [<a href="<a href="<?php echo $this->plugin_URL; ?>" target="_blank">Help</a>]
</form>
</div>