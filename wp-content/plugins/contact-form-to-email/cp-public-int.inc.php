<?php if ( !defined('CP_AUTH_INCLUDE') ) { echo 'Direct access not allowed.'; exit; } ?>
</p>
<link href="<?php echo plugins_url('css/stylepublic.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
<link href="<?php echo plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
<form class="cpp_form" name="<?php echo $this->prefix; ?>_pform" id="<?php echo $this->prefix; ?>_pform" action="<?php $this->get_site_url(); ?>" method="post" enctype="multipart/form-data" onsubmit="return <?php echo $this->prefix; ?>_pform_doValidate(this);"><input type="hidden" name="<?php echo $this->prefix; ?>_pform_process" value="1" /><input type="hidden" name="<?php echo $this->prefix; ?>_id" value="<?php echo $this->item; ?>" /><input type="hidden" name="cp_ref_page" value="<?php esc_attr($this->get_site_url()); ?>" /><input type="hidden" name="form_structure" id="form_structure" size="180" value="<?php echo str_replace("\r","",str_replace("\n","",esc_attr($this->cleanJSON($this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure))))); ?>" />
    <div id="fbuilder">
        <div id="formheader"></div>
        <div id="fieldlist"></div>
    </div>
<div id="cpcaptchalayer">
<?php if ($this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha) != 'false') { ?>
  <?php _e("Please enter the security code"); ?>:<br />
<?php //$captcha_srcurl = $this->get_site_url();
		$captcha_srcurl = '/index.php?'.$this->prefix.'_captcha=captcha&width='.$this->get_option('cv_width', CP_CFEMAIL_DEFAULT_cv_width).'&height='.$this->get_option('cv_height', CP_CFEMAIL_DEFAULT_cv_height).'&letter_count='.$this->get_option('cv_chars', CP_CFEMAIL_DEFAULT_cv_chars).'&min_size='.$this->get_option('cv_min_font_size', CP_CFEMAIL_DEFAULT_cv_min_font_size).'&max_size='.$this->get_option('cv_max_font_size', CP_CFEMAIL_DEFAULT_cv_max_font_size).'&noise='.$this->get_option('cv_noise', CP_CFEMAIL_DEFAULT_cv_noise).'&noiselength='.$this->get_option('cv_noise_length', CP_CFEMAIL_DEFAULT_cv_noise_length).'&bcolor='.$this->get_option('cv_background', CP_CFEMAIL_DEFAULT_cv_background).'&border='.$this->get_option('cv_border', CP_CFEMAIL_DEFAULT_cv_border).'&font='.$this->get_option('cv_font', CP_CFEMAIL_DEFAULT_cv_font); ?>
  <img src="<?php echo $captcha_srcurl; ?>"  id="captchaimg" alt="security code" border="0"  />
  <br /><a href="javascript:void(0);" onclick="javascript:document.getElementById('captchaimg').src='<?php echo $captcha_srcurl; ?>&timestamp='+Math.random(); ">Reload</a><br />
  <div class="dfield" style="clear:left;">
  <?php _e("Security Code (lowercase letters)"); ?>:<br><input type="text" size="20" name="hdcaptcha_<?php echo $this->prefix; ?>_post" id="hdcaptcha_<?php echo $this->prefix; ?>_post" value="" />
  <div class="cpefb_error message" id="hdcaptcha_error" generated="true" style="display:none;position: absolute; left: 0px; top: 25px;"><?php echo esc_attr($this->get_option('cv_text_enter_valid_captcha', CP_CFEMAIL_DEFAULT_cv_text_enter_valid_captcha)); ?></div>
  </div><br />  
<?php } ?>
</div>
<div id="cp_subbtn"><?php _e("Submit"); ?></div>
</form>