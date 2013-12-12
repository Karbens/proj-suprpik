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

$member = $_SESSION['sp_user'];
$user_id = get_current_user_id();
$user_info = get_userdata($user_id);
$user_phone = get_user_meta($user_id, 'phone', true);

?>
<div style="width:60%; float:left;">
<h3><?php _e('Profile'); ?></h3>

<form name="memberaccount" action="" method="post">
	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field(); ?>

	<input type="hidden" name="member[account]" value="1"/>

	<label class="sp_label"><?php _e('Username:') ?>
		<span class="spValue"><?php echo $user_info->user_login; ?></span>
	</label>
	<br/>
	<label class="sp_label"><?php _e('Email:') ?>
		<span class="spValue placeholder"><?php echo $user_info->user_email; ?></span>
		<span class="editable">
			<input type="email" value="<?php echo $user_info->user_email; ?>" name="member[email]" class="sp_input" disabled="disabled"/>
		</span>
	</label>
	
	<br/>
	<label class="sp_label"><?php _e('Mobile/Cell:') ?>
		<span class="spValue placeholder"><?php echo $user_phone; ?></span>
		<span class="editable">
			<input type="text" value="<?php echo $user_phone; ?>" name="member[phone]" class="sp_input" disabled="disabled"/>
		</span>
	</label>
	<br/>
	<label class="sp_label"><?php _e('Password:') ?>
		<span class="spValue placeholder">******</span>
		<span class="editable">
			<input id="Pwd" type="password" value="" name="member[password]" class="sp_input" disabled="disabled"/>
		</span>
	</label>

	<br/>


	<label style="display:none;" id="confirm" class="sp_label"><?php _e('Confirm Password:') ?>
		<span class="spValue placeholder">******</span>
		<span class="editable">
			<input id="confirmPwd" type="password" value="" name="member[confirm_password]" class="sp_input"/>
		</span>
	</label>
	<br/>
	<span class="message"></span>
	<br/>
	<span id="saveIt" class="sp_label">
		<br/>
		<button type="button" class="allowEdit">Change</button>
		<input style="display:none; margin-right:81px;" type="submit" value="Save Changes" />
	</span>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.allowEdit').click(function(){
			if($(this).html()=='Change'){
				$(this).html('Cancel');
				$('.editable input').show().removeAttr('disabled');
				$('.placeholder').hide();
				$('#saveIt input, #confirm').show();
			}else{
				$(this).html('Change');
				$('.editable input').hide().attr('disabled','disabled');
				$('.placeholder').show();
				$('#saveIt input, #confirm').hide();
			}
			return false;
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

	});
</script>
</div>

<div style="width:40%; float:right;">

<h3><?php _e('Contests'); ?></h3>

<?php

	$contests_que = @mysql_query("SELECT * FROM `br3_contests` c WHERE (SELECT count(*) FROM`br3_contests_picks` cp WHERE cp.`user_id` = '".$user_id."') AND c.`end_date` > now()");
	$contests = array();
	if(@mysql_num_rows($contests_que) > 0)
	{ 


		while( $contests_res = mysql_fetch_assoc($contests_que) )
		{
			$contests[] = $contests_res;
		}
	
	foreach ($contests as $key => $contest) {
		if($key>0)	echo '<br/>';
		echo "<div>".$contest['contest_name']."</div>";
	//	echo "<p>".$contest['contest_desc']."</p>";
	//	echo "<div>".$contest['contest_terms']."</div>";
	}

	echo '<br/>';

		echo 'You are active in these contests.';

	} else{
		echo 'You have no active contests.';
	}



?>
</div>