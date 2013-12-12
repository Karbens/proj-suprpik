<?php
	if(isset($_POST['sp_registration_email'],$_POST['sp_account_email'])){
		if(is_email( $_POST['sp_registration_email'] )) update_option( 'sp_registration_email', $_POST['sp_registration_email'] );
		if(is_email( $_POST['sp_account_email'] )) update_option( 'sp_account_email', $_POST['sp_account_email'] );
	}


?>
<div class="wrap">
	<h2><?php _e('Backoffice Options'); ?></h2>


<form name="form" method="post">
<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field(); ?>
<h3 class="title">Common Settings</h3>
<table class="form-table">
	<tr>
		<th><label for="registrations">Registrations will be emailed to</label></th>
		<td> <input name="sp_registration_email" id="registrations" type="text" value="<?php echo get_option( 'sp_registration_email' ); ?>" class="regular-text code" /></td>
	</tr>
	<tr>
		<th><label for="account">Email Address changes will be emailed to</label></th>
		<td> <input name="sp_account_email" id="account" type="text" value="<?php echo get_option( 'sp_account_email' ); ?>" class="regular-text code" /></td>
	</tr>
	</table>


<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>
</form>


</div>