<?php
/*
Plugin Name: SP Contests
Plugin URI: 
Description: Super Picks Contests Plugin
Version: 1.0
Author: Joe Bassi
Author URI: 
License: GPLv2 or later
*/
date_default_timezone_set('America/Los_Angeles');

//hook for admin menu
add_action('admin_menu', 'sp_contests_add');


function sp_contests_add()
{
	//add top level menu
	add_menu_page( __('SP Contests'), __('SP Contests'), 'manage_options', 'sp-contests', 'sp_contests_page'  ); 
}


function sp_contests_page()
{
	$contest_id = isset($_GET['contest_id'])? (int)$_GET['contest_id'] : false;
	$where_qry = $contest_id? ' where `contest_id`='.$contest_id : '';

	$contests_que = @mysql_query("SELECT * FROM `br3_contests` ".$where_qry);
	$contests = array();
	if(@mysql_num_rows($contests_que) > 0)
	{
		while( $contests_res = mysql_fetch_assoc($contests_que) )
		{
			$contests[] = $contests_res;
		}
	}

	//dirty hack for the right function file
	if($contest_id==3){
		require_once( get_template_directory() . '/soccer/cc_functions.php');
	}else{
		//required for the contests functions
		require_once( get_template_directory() . '/cc_functions.php');
	}
	
	if( $_POST['is_posted'] ){
		if(file_exists( plugin_dir_path( __FILE__ ).'/'.$contest_id.'_post.php' )){
			require_once($contest_id.'_post.php');
		}
	}
	
	
	?>
	    <link rel="stylesheet" href="http://superpicks.com/settings/style.css" type="text/css" media="screen" charset="utf-8" />
	    <link rel="stylesheet" href="http://superpicks.com/settings/stylesheets/site.css" type="text/css" media="screen" charset="utf-8" />
	    <script src="http://superpicks.com/settings/jquery/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
	    <style type="text/css">
	    body 
		{
	      padding: 10px; 
		}
	    th 
		{
	      text-align: left;
	      padding: 4px;
	      padding-right: 15px;
	      vertical-align: top;
		  font-size: 16px;
		  font-weight: bold;
		}
	    .css_sized_container .iPhoneCheckContainer 
		{
	      width: 250px; 
		}
	    </style>
	  <?php if( isset($_POST['is_posted']) ) { ?>
	    <header>
	      <h1 id="h1_div">
	        <a href="admin.php?page=sp-contests&contest_id=<?php echo $contest_id; ?><?php echo isset($week)?'&week='.$week:''; ?>">Updated, click to Reload</a>
	      </h1>
		</header>
	  <?php } ?>
	  
	  <?php

		if($contest_id && file_exists( plugin_dir_path( __FILE__ ).'/'.$contest_id.'.php')){
			require_once($contest_id.'.php');
		} else { ?>
		<div id="frame">
	      
	        <article>
	
	          <h2>SP Contests</h2>
			  <p>
			  Select Contest Below
			  </p>
			  
	          <div class='table'>
	            <table>
	              <tr>
	                <th style='vertical-align: middle !important;'>
	                  &nbsp;&nbsp;Name
	                </th>
	                <th>
					  &nbsp;&nbsp;Type
	                </th>
					<th>&nbsp;&nbsp;Start Date</th>
					<th>&nbsp;&nbsp;End Date</th>
	              </tr>
				<?php
				foreach($contests as $contest)
				{
				?>
	              <tr>
	                <td style='vertical-align: middle !important;'>
	                  <?php echo '<a href="admin.php?page=sp-contests&contest_id='.$contest['contest_id'].'">'.$contest['contest_name'].'</a>'; ?>
	                </td>
	                <td>
					  <?php echo $contest['contest_type']; ?>
	                </td>
					<td>
					  <?php echo $contest['start_date']; ?>
					</td>
					<td>
					  <?php echo $contest['end_date']; ?>
					</td>
	              </tr>
				<?php
				}
				?>
	            </table>
	          </div>
	
	          
	
	        </article>
	    </div>
<?php }

}

if(is_admin()){
	wp_enqueue_style( 'jquery-ui.css.css', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' );
	wp_enqueue_style( 'timepicker.css', plugins_url( 'timepicker.css', __FILE__ ) );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-datepicker');
	wp_enqueue_script( 'jquery-ui-timepicker-addon', plugins_url( 'jquery-ui-timepicker-addon.js', __FILE__ ) );



	//ajax delete for superpicks soccer
	add_action( 'wp_ajax_soccer_delete', 'superpicks_soccer_delete' );

	function superpicks_soccer_delete() {


		if(isset($_POST['contest_id']) && (int)$_POST['contest_id']==3){
			if(isset($_POST['game_id'])){
				$where_qry = ' where `game_id`='.(int)$_POST['game_id'];
				$query = "DELETE FROM `br3_contests_soccer` ".$where_qry;
				if(mysql_query($query)){
					echo json_encode('success');
					exit;
				}

			}
		}
		echo json_encode('failure');
		exit;
	}

	//ajax grading for superpicks soccer
	add_action( 'wp_ajax_soccer_grade', 'superpicks_soccer_grade' );

	function superpicks_soccer_grade() {

		if(isset($_POST['contest_id']) && (int)$_POST['contest_id']==3){

			
			if(isset($_POST['week'], $_POST['scores'])){
				$week = (int)$_POST['week'];
				$scores = $_POST['scores'];
				$output = array();
				foreach ($scores as $key => $value) {

					$query = 'SELECT * FROM `br3_contests_soccer` WHERE week_num = '.$week.' AND `game_id` = '.$key.' LIMIT 1';

							$winner = mysql_query($query);
							$winner_res = mysql_fetch_assoc($winner);

							$home_handicap = (float)$winner_res['ps_home'];
							$away_handicap = 0; // using one handicap only

							$home_total = (int)$value['home'] + $home_handicap;
							$away_total = (int)$value['away'] + $away_handicap;



					if($home_total > $away_total){
						$result =  $winner_res['home_team'];
					}
					if($away_total > $home_total){
						$result =  $winner_res['away_team'];
					}

					if($home_total ==$away_total){
						$result =  'Tie';
					}
					$update = "UPDATE `br3_contests_soccer` SET `away_score` = '".(int)$value['away']."',
									    	`home_score` = '". (int)$value['home']. "',
									    `winning_team` = '".$result."',
									     `winning_spread_team` = '".$result."'
										WHERE `week_num` = ".$week.' AND `game_id` = '.$key;

							@mysql_query($update);
							$output[$key] = $result;



				}


				//fetch members
				$query = "SELECT * FROM `br3_contests_soccer_picks` WHERE week_num = '".$week."'";
			
				$members = mysql_query($query);

				while($res = mysql_fetch_assoc($members)){



					$query = 'SELECT *  FROM `br3_contests_soccer` WHERE `week_num` = ' . $week;

					$user_picks_array = explode(',',$res['user_picks']);

					$team_arr = $user_picks_array;

					if(count($team_arr) > 1){
						foreach($team_arr as $key => $team){
							$team_arr[$key] = "'".$team."'";
						}
						$teams = implode(',',$team_arr);
					
						$query .= ' AND (`away_team` IN ('.$teams.') OR `home_team` IN ('.$teams.') )';

					}

					$query .= ' ORDER BY game_id';
					//echo '<br>que: '.$query;

					$result = mysql_query($query);
					$rows_count = (integer)@mysql_num_rows($result);
					$team_arr = array();
					if($rows_count > 0){
						while($res1 = mysql_fetch_assoc($result)){
							$team_arr[] = $res1;
						}
					}

					$w = 0;//wins
					$l = 0;//losses
					$t = 0;//ties

					foreach($team_arr as $team){
						$game_id = $team['game_id'];
						if( in_array($team['winning_spread_team'], $user_picks_array))$w++;
						if($team['winning_spread_team'] == 'Tie')$t++;
					}
					
					$l = 5-($w+$t);

					$points = $w  + ($t*0.5);

					//fetch members
					$query = "UPDATE `br3_contests_soccer_picks` SET `user_points` = '".$points."' WHERE pick_id = '".$res['pick_id']."'";
				//	echo $query; die();

					 mysql_query($query);
				}


				echo json_encode($output);
				exit;
			}

		}
		echo json_encode('failure');
		exit;
	}
	
}