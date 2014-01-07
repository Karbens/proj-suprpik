<?php  	
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
					  <input type="hidden" name="game[<?php echo $wk['game_id']; ?>]" value="<?php echo $wk['game_id']; ?>">
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