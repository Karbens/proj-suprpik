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

//hook for admin menu
add_action('admin_menu', 'sp_contests_add');

function sp_contests_add()
{
	//add top level menu
	add_menu_page( __('SP Contests'), __('SP Contests'), 'manage_options', 'sp-contests', 'sp_contests_page'  ); 
}


function sp_contests_page()
{

	//required for the contests functions
	require_once( get_template_directory() . '/cc_functions.php');
	
	if( $_POST['is_posted'] )
	{
		//echo '<pre>'; print_r($_POST); echo'</pre>'; exit();
		extract($_POST);
		if(count($game) > 0 )
		{
			foreach($game as $gid => $gval)
			{
				
				$upd = "UPDATE `br3_contests_nfl`
						SET `ps_home` = '".$home[$gid]."',
							`ps_away` = '".$away[$gid]."'
						WHERE `week_num` = ".$week."
						AND `game_id` = ".$gid;
				@mysql_query($upd);
			}
			if( isset($publish) )
			{
				$pbd = "UPDATE `br3_contests_nfl`
						SET `published` = 'yes'
						WHERE `week_num` = ".$week;
				@mysql_query($pbd);
			}else
			{
				$wk_que = "SELECT *
						   FROM `br3_contests_nfl`
						   WHERE week_num = ".$week."
						   ORDER BY game_date
						   LIMIT 1";
				$wk_query = mysql_query($wk_que);
				$wk_res = mysql_fetch_assoc($wk_query);
				$wk_date = $wk_res['game_date'];
				$cu_date = date('Y-m-d H:i:s');
				if($cu_date < $wk_date)
				{
					$pbd = "UPDATE `br3_contests_nfl`
						    SET `published` = 'no'
							WHERE `week_num` = ".$week;
					@mysql_query($pbd);
				}
			}
		}
	}
	
	$contests_que = @mysql_query("SELECT * FROM `br3_contests` ");
	$contests = array();
	if(@mysql_num_rows($contests_que) > 0)
	{
		while( $contests_res = mysql_fetch_assoc($contests_que) )
		{
			$contests[] = $contests_res;
		}
	}
	
	?>
	    <link rel="stylesheet" href="http://superpicks.com/settings/style.css" type="text/css" media="screen" charset="utf-8" />
	    <link rel="stylesheet" href="http://superpicks.com/settings/stylesheets/site.css" type="text/css" media="screen" charset="utf-8" />
	    
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js" type="text/javascript"></script>
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
	        <a href="admin.php?page=sp-contests&contest_id=2&week=<?php echo $week; ?>">Updated, click to Reload</a>
	      </h1>
		</header>
	  <?php } ?>
	  
	  <?php if( isset($_GET['contest_id']) && $_GET['contest_id'] == 2 ) { 
	  	
		$week = getCurrentWeek();
		
		if($_GET['week']>0 && $_GET['week']<18)$week = $_GET['week'];
		
		$week_que = 'SELECT * FROM `br3_contests_nfl` WHERE `week_num` = '.$week;
		$week_query = mysql_query($week_que);
		$week_arr = array();
		if(@mysql_num_rows($week_query) > 0)
		{
			while($week_res = mysql_fetch_assoc($week_query))
			{
				$week_arr[] = $week_res;
			}
		}
		
		//disable publish checkbox if first game of the game has started
		$wk_que1 = "SELECT *
				   FROM `br3_contests_nfl`
				   WHERE week_num = ".$week."
				   ORDER BY `game_date`
				   LIMIT 1";
		$wk_query1 = mysql_query($wk_que1);
		$wk_res1 = mysql_fetch_assoc($wk_query1);
		$wk_date1 = $wk_res1['game_date'];
		$cu_date1 = date('Y-m-d H:i:s');
		$pub_disable = 0;
		if($wk_date1 < $cu_date1)
		{
			$pub_disable = 1;
		}
	  ?>
		<div id="frame">
	      
	        <article>
	
	          <h2>SP Contests - Super Picks Football</h2>
			  <p>
			  Pick Week:
			  <?php
			  for($i=1; $i<=17; $i++)
			  {
			  	if($i > 1) echo ' | ';
				if($i != $week)echo '<a href="admin.php?page=sp-contests&contest_id=2&week='.$i.'">';
				echo $i;
				if($i != $week)echo '</a>';
			  }
			  ?>
			  </p>
			  
			  <?php if(count($week_arr) > 0) { ?>
		      <div class='table'>
			  <form name="f1" id="f1" method="post">
			  	<input type="hidden" name="is_posted" value="1">
				<input type="hidden" name="week" value="<?php echo $week; ?>">
	            <table>
	              <tr>
	                <th style='vertical-align: middle !important;'>
	                  &nbsp;&nbsp;Visitor
	                </th>
					<th>&nbsp;&nbsp;</th>
	                <th>
					  &nbsp;&nbsp;Home
	                </th>
					<th>&nbsp;&nbsp;</th>
	              </tr>
				<?php
				$published = 0;
				foreach($week_arr as $wk)
				{
					if($wk['published'] == 'yes' && $published == 0)
					{
						$published = 1;
					}
				?>
	              <tr>
	                <td style='vertical-align: middle !important;'>
	                  <?php echo $wk['away_team']; ?>&nbsp;&nbsp;
					  <input type="hidden" name="game[<?php echo $wk['game_id']; ?>] value="<?php echo $wk['game_id']; ?>">
					</td>
					<td>
					  <input type='textbox' name='away[<?php echo $wk['game_id']; ?>]' value='<?php echo $wk['ps_away']; ?>' size="5"/>
	                </td>
	                <td>
	                  <?php echo $wk['home_team']; ?>&nbsp;&nbsp;
					</td>
					<td>
					  <input type='textbox' name='home[<?php echo $wk['game_id']; ?>]' value='<?php echo $wk['ps_home']; ?>' size="5"/>
	                </td>
	              </tr>
				<?php
				}
				?>
				<tr>
				  <td>&nbsp;</td>
				  <td colspan="2"><span style="font-weight:bold;margin-left:20px;">Publish</span> <input type="checkbox" name="publish" value="1"<?php if($published == 1){ echo ' checked="checked" '; } if($pub_disable == 1){ echo ' disabled="disabled" '; }?>></td>
				  <td>&nbsp;</td>
				</tr>
				<tr>
				  <td>&nbsp;</td>
				  <td colspan="2"><input type="Submit" name="sbt_button" id="sbt_button" value="Save Changes"></td>
				  <td>&nbsp;</td>
				</tr>
	            </table>
			  </form>
	          </div>
			  <?php } else { ?>
			  <div class='table'>
	            <table>
	              <tr>
	                <th style='vertical-align: middle !important;'>
	                  &nbsp;&nbsp;No Data Found for Week <?php echo $week; ?>.
	                </th>
				  </tr>
				</table>
			  </div>
			  <?php } ?>
	
	          
	
	        </article>
	    </div>
	  
	  <?php } else { ?>
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
		<?php } ?>
<?php
}