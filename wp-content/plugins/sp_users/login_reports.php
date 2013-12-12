<?php

function get_users_sortable($args = array('orderby' => 'ID', 'order' => 'desc')){
	global $wpdb;

	$sortable = array('ID', 'user_login', 'user_email', 'user_registered');
	$meta_sortable = array('first_name', 'last_name', 'dob', 'city', 'phone', 'ip4', 'browser', 'device', 'newsletter_status', 'updated_at');

	$order_query = '';

	if(in_array($_GET['orderby'], $sortable)){
		$order_query = ' ORDER BY '.$_GET['orderby'] .' '.($_GET['order']=='desc'?'DESC':'ASC');

	}else if(($meta_key = array_search($_GET['orderby'], $meta_sortable))!==false){
		$order_query = " ORDER BY meta_key = '".$meta_sortable[$meta_key]."' DESC, meta_value ".($_GET['order']=='desc'?'DESC':'ASC');
	}

	$query = "SELECT u.ID, u.user_login, u.user_email, u.user_registered, um.* FROM `wp_users` `u` LEFT JOIN `wp_usermeta` `um` on `u`.`ID` = `um`.`user_id` WHERE `um`.`meta_key` IN ('first_name', 'last_name', 'dob', 'city', 'phone', 'ip4', 'user_agent','browser','device','newsletter_status', 'login_counter', 'updated_at')" .$order_query;



	$results = $wpdb->get_results($query);

	$users = array();
	foreach ( $results as $result ){
		if(!isset($users[$result->ID])){
			$users[$result->ID] = array('ID' => $result->ID, 'user_login' => $result->user_login,
										'user_email' => $result->user_email, 'user_registered' => $result->user_registered   );
		}

		$users[$result->ID][$result->meta_key] = $result->meta_value;

		$users[$result->ID]['last_login_info'] = get_user_meta($result->ID, 'last_login_info', true);
		$users[$result->ID]['session_info'] = get_user_meta($result->ID, 'session_info', true);

	}

	return $users;

}



if(isset($_GET[user_id])){
	$user = get_userdata( (int)$_GET[user_id]);

	if(is_wp_error($user)){

		wp_redirect('/');

	}else{ ?>


<div class="wrap">
	<h2><?php _e('Login Report for ' .$user->user_login ); ?></h2>

<br/>
	<div class="loginInfo">
	<?php  $last_login_info = get_user_meta($user->ID, 'last_login_info', true);



	if(empty($last_login_info)){
		echo 'No Information available';
	}else {
			$str = strtotime($last_login_info['updated_at']);
	 ?>
	<h2><?php _e('Last Logged In details' ); ?></h2>
	<span class="label">Login Count: </span><span class="info"><?php echo get_user_meta($user->ID, 'login_counter', true); ?></span>
<br/><br/>
	<span class="label">Date: </span><span class="info"><?php echo date('F d, Y', $str); ?></span>

<br/><br/>
	<span class="label">Time: </span><span class="info"><?php echo date('h:m A', $str); ?></span>

<br/><br/>
	<span class="label">Username: </span><span class="info"><?php echo $user->user_login; ?></span>

<br/><br/>
	<span class="label">IP: </span><span class="info"><?php echo $last_login_info['ip4']; ?></span>

<br/><br/>
	<span class="label">City: </span><span class="info"><?php echo get_user_meta($user->ID, 'city', true); ?></span>

<br/><br/>
	<span class="label">Browser: </span><span class="info"><?php echo $last_login_info['browser']; ?></span>

<br/><br/>
	<span class="label">Device: </span><span class="info"><?php echo $last_login_info['device']; ?></span>

<br/><br/>

<?php } ?>

<br/><br/>



<h3><?php _e('Active Contests'); ?></h3>

<?php

	$contests_que = @mysql_query("SELECT * FROM `br3_contests` c LEFT JOIN `br3_contests_picks` cp on c.`contest_id`= cp.`contest_id`  where c.`end_date` > now() AND cp.`user_id` = '".$user->ID."'");
	$contests = array();
	if(@mysql_num_rows($contests_que) > 0)
	{ 


		while( $contests_res = mysql_fetch_assoc($contests_que) )
		{
			$contests[] = $contests_res;
		}
	
	foreach ($contests as $key => $contest) {
		if($key>0)	echo '<hr/>';
		echo "<h4>".$contest['contest_name']."</h4>";
		echo "<p>".$contest['user_picks']."</p>";
	//	echo "<div>".$contest['contest_terms']."</div>";
	}

	} else{
		echo 'No active contests.';
	}
?>

<br/><br/><br/>

<a href="?page=sp_users_login">Back</a>



</div>

</div>


<?php

	}
}else {

$args = array();

	if(isset($_GET['orderby']))	$args['orderby'] = $_GET['orderby'];

	if(isset($_GET['order'])) $args['order'] = $_GET['order'];

	$users = get_users_sortable( $args );

		$headings = array('ID' => array('title' => 'ID', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:3%'),
						'user_login' => array('title' => 'Username', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'first_name' => array('title' => 'First Name', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'last_name' => array('title' => 'Last Name', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'dob' => array('title' => 'DOB', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'user_email' => array('title' => 'Email', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:11%'),
						'city' => array('title' => 'City', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'phone' => array('title' => 'Phone', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'ip4' => array('title' => 'IP', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						//'activation' => array('title' => 'Activated', 'class' => 'manage-column column-date sortable asc'),
						'browser' => array('title' => 'Browser', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'device' => array('title' => 'Device', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'newsletter_status' => array('title' => 'Newsletter', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:7%'),
						'login_counter' => array('title' => 'Logins', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:5%'),
						'user_registered' => array('title' => 'Created', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:9%'),
						'last_login_info' => array('title' => 'Last Login', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:8%'),
						'last_login_ip' => array('title' => 'Last Login IP', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:8%')	
						);


   	if(isset($_POST['csv_login'])){

   		$csv_heads = array();

   		foreach ($headings as $key => $value) {
   			$csv_heads[] = $value['title'];
   		}
   		$csv = implode(',', $csv_heads);
   		$csv .= "\n";

		foreach ( $users as $user ){
			$csv_values = array();

			$csv_values[] = $user['ID'];
			$csv_values[] = $user['user_login'];
			$csv_values[] = $user['first_name'];
			$csv_values[] = $user['last_name'];
			$csv_values[] = $user['dob'];
			$csv_values[] = $user['user_email'];
			$csv_values[] = $user['city'];
			$csv_values[] = $user['phone'];
			$csv_values[] = $user['ip4'];
			$csv_values[] = $user['browser'];
			$csv_values[] = $user['device'];
			$csv_values[] = ($user['newsletter_status']==1?'Yes' : 'No');
			$csv_values[] = $user['login_counter'];
			$str = strtotime($user['user_registered']);
			$csv_values[] = '"'.date('F d, Y h:m A', $str).'"';
			$str = strtotime($user['last_login_info']['updated_at']);
			if(empty($str)){
				$csv_values[] = '';
			}else{
				$csv_values[] = '"'.date('F d, Y h:m A', $str).'"';
				
			}
			$csv_values[] = $user['last_login_info']['ip4'];


	   		$csv .= implode(',', $csv_values);
	   		$csv .= "\n";
			}

	header('Content-type: text/csv');
	header('Content-disposition: attachment;filename=membersreport_'.time().'.csv');
	echo $csv;
	die();
   	}




	$table_head = '';
	foreach($headings as $id => $heading){
		$orderby = $id;
		if($id=='user_registered1') $orderby = 'user_registered';
		$table_head .= '<th class="'.$heading['class'].'" id="'.$id.'"'.(isset($heading['style'])?' style="'.$heading['style'].'"':'').'><a href="?page=sp_users_login&orderby='.$orderby.'&order='.(( isset($_GET['order'], $_GET['orderby']) && $_GET['orderby']==$id && $_GET['order'] =='asc'  )?'desc':'asc').'">'.$heading['title'].'</a></th>';
	} 

?>

<div class="wrap">
	<h2><?php _e('Members Reports'); ?></h2>

	<div class="">
	<p>	<?php
$result = count_users();
echo 'There are ', $result['total_users'], ' total users';
foreach($result['avail_roles'] as $role => $count)
    echo ', ', $count, ' are ', $role, 's';
echo '.';
?></p></div>

	<table id="clickable" class="wp-list-table widefat fixed media" cellspacing="0">
	<thead>
	<tr><?php echo $table_head; ?></tr>
	</thead>

	<tfoot>
	<tr><?php echo $table_head; ?></tr>
	</tfoot>

	<tbody>
		<?php
			foreach ( $users as $user ){
				echo '<tr'.(!empty($user['session_info'])?' class="loggedIn"':'').' onclick="window.location.href=(\'?page=sp_users_login&user_id='.$user['ID'].'\')">';
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
				echo '<td class="login_counter column-device">'.$user['login_counter'] . '</td>';
				$str =  strtotime($user['user_registered']);
				echo '<td class="created column-created">'.date('F d, Y',$str).'<br/>'.date('h:m A', $str) . '</td>';
				$str =  strtotime($user['last_login_info']['updated_at']);
				echo '<td class="last-login-info column-last-login-info">'.($str==''?'': date('F d, Y',$str).'<br/>'.date('h:m A', $str) ) . '</td>';
				echo '<td class="ip4 column-ip4">'.$user['last_login_info']['ip4'] . '</td>';
				echo '</tr>';
			}
		?>
	</tbody>
</table>

</div>
<br/>

<div class="wrap alignright">

<form method="post">
	<input type="hidden" name="csv_login" value="true"/>
	<input type="submit" value="Download as CSV"/>
</form>
</div>
<?php

}
