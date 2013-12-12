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

	}

	return $users;

}

	$args = array();

	if(isset($_GET['orderby']))	$args['orderby'] = $_GET['orderby'];

	if(isset($_GET['order'])) $args['order'] = $_GET['order'];

	$users = get_users_sortable( $args );

		$headings = array('ID' => array('title' => 'ID', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:3%'),
						'user_login' => array('title' => 'Username', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:7%'),
						'first_name' => array('title' => 'First Name', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'last_name' => array('title' => 'Last Name', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'dob' => array('title' => 'DOB', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:6%'),
						'user_email' => array('title' => 'Email', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:15%'),
						'city' => array('title' => 'City', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:7%'),
						'phone' => array('title' => 'Phone', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:7%'),
						'ip4' => array('title' => 'IP', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:7%'),
						//'activation' => array('title' => 'Activated', 'class' => 'manage-column column-date sortable asc'),
						'browser' => array('title' => 'Browser', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:7%'),
						'device' => array('title' => 'Device', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:7%'),
						'newsletter_status' => array('title' => 'Newsletter', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:7%'),
						//'login_counter' => array('title' => 'Logins', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:5%'),
						'user_registered' => array('title' => 'Date', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:9%'),
						'user_registered1' => array('title' => 'Time', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:5%'),
						//'updated_at' => array('title' => 'Last Modified', 'class' => 'manage-column column-date sortable asc', 'style' => 'width:8%')	
						);


   	if(isset($_POST['csv'])){

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
			//$csv_values[] = $user['login_counter'];
			$str = strtotime($user['user_registered']);
			$csv_values[] = '"'.date('F d, Y', $str).'"';
			$csv_values[] = '"'.date('h:m A', $str).'"';
			//$csv_values[] = isset($user['updated_at'])? '"'. date('F d, Y h:mA', strtotime($user['updated_at'])).'"':'';
	   		$csv .= implode(',', $csv_values);
	   		$csv .= "\n";
			}

	header('Content-type: text/csv');
	header('Content-disposition: attachment;filename=membersreport_'.time().'.csv');
	echo $csv;
	die();
   	}
