<?php

	$table_head = '';
	foreach($headings as $id => $heading){
		$orderby = $id;
		if($id=='user_registered1') $orderby = 'user_registered';
		$table_head .= '<th class="'.$heading['class'].'" id="'.$id.'"'.(isset($heading['style'])?' style="'.$heading['style'].'"':'').'><a href="?page=sp_users&orderby='.$orderby.'&order='.(( isset($_GET['order'], $_GET['orderby']) && $_GET['orderby']==$id && $_GET['order'] =='asc'  )?'desc':'asc').'">'.$heading['title'].'</a></th>';
	} 

?>

<div class="wrap">
	<h2><?php _e('Backoffice Reports'); ?></h2>

	<table class="wp-list-table widefat fixed media" cellspacing="0">
	<thead>
	<tr><?php echo $table_head; ?></tr>
	</thead>

	<tfoot>
	<tr><?php echo $table_head; ?></tr>
	</tfoot>

	<tbody>
		<?php
			foreach ( $users as $user ){
				echo '<tr>';
				echo '<td class="column-id">'.$user['ID'] . '</td>';
				echo '<td class="user_login column-user_login">'. $user['user_login'] . '</td>';
				echo '<td class="first_name column-first-name">'. $user['first_name'] . '</td>';
				echo '<td class="last_name column-last-name">'. $user['last_name'] . '</td>';
				echo '<td class="dob column-dob">'.$user['dob'] . '</td>';
				echo '<td class="email column-email">'.$user['user_email'] . '</td>';
				echo '<td class="city column-city">'.$user['city'] . '</td>';
				echo '<td class="phone column-phone">'.$user['phone'] . '</td>';
				echo '<td class="ip4 column-ip4">'.$user['ip4'] . '</td>';
				//echo '<td class="activated column-activated">'.$user['activation'] . '</td>';
				echo '<td class="browser column-browser">'.$user['browser'] . '</td>'; //add user_agent as details
				echo '<td class="device column-device">'.$user['device'] . '</td>';
				echo '<td class="newsletter column-newsletter">'.($user['newsletter_status']==1?'Yes' : 'No') . '</td>';
				//echo '<td class="login_counter column-device">'.$user['login_counter'] . '</td>';
				$str =  strtotime($user['user_registered']);
				echo '<td class="created column-created">'.date('F d, Y',$str) . '</td>';
				echo '<td class="created column-created">'.date('h:m A', $str) . '</td>';
				//echo '<td class="updated column-updated">'. (isset($user['updated_at'])?date('F d, Y h:mA', strtotime($user['updated_at'])):'') . '</td>';
				echo '</tr>';
			}
		?>
	</tbody>
</table>
<br/>
<span>*</span><?php if (date_default_timezone_get()) {
    echo "All times are in " . date_default_timezone_get() ;
} ?>
</div>
<br/>

<div class="wrap alignright">

<form method="post">
	<input type="hidden" name="csv" value="true"/>
	<input type="submit" value="Download as CSV"/>
</form>
</div>