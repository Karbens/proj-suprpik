<?php
define('MOSCONFIG_BRDPREFIX', 'br3_');

$contest_dates = getContestStartEndDate(3);


define('SOCCER_START_DATE', $contest_dates['start']);
define('SOCCER_END_DATE', $contest_dates['end']);

$add = isset($_GET['add']) && $_GET['add']=='true';
$grading = isset($_GET['grading']) && $_GET['grading']=='true';

		$week = getCurrentWeek();
		
		if($_GET['week']>0 && $_GET['week']<18) $week = $_GET['week'];
		
		$week_que = 'SELECT * FROM `br3_contests_soccer` WHERE `week_num` = '.$week .' ORDER BY `game_date` ASC';
		$week_query = mysql_query($week_que);
		$week_arr = array();
		if(@mysql_num_rows($week_query) > 0)
		{
			while($week_res = mysql_fetch_assoc($week_query))
			{
				$week_arr[] = $week_res;
			}
		}
		
		//disable publish checkbox if first game of the week has started
		$wk_que1 = "SELECT *
				   FROM `br3_contests_soccer`
				   WHERE week_num = ".$week."
				   ORDER BY `game_date`
				   LIMIT 1";
		$wk_query1 = mysql_query($wk_que1);
		$wk_res1 = @mysql_fetch_assoc($wk_query1);
		$wk_date1 = $wk_res1['game_date'];
		$cu_date1 = date('Y-m-d H:i:s');
		$pub_disable = 0;
		if($wk_date1 < $cu_date1)
		{
			$pub_disable = 1;
		}
	  ?>
		<div id="frame" style="max-width:<?php echo !$add? '700':'1000'; ?>px">
	      
	        <article>
	
	          <h2><?php echo $grading? 'Grading' :'SP Contests'; ?> - SuperPicks Soccer</h2>
	          <?php if(!$add){ ?>
	          <a class="alignleft" href="?page=sp-contests&contest_id=<?php echo $contest_id; ?>&add=true"><button>Add Data</button></a>
	          <?php if(!$grading){ ?>
			  <a href="?page=sp-contests&contest_id=<?php echo $contest_id; ?>&week=<?php echo $week; ?>&grading=true" class="alignright">Scoring/Grading</a>
			  <?php } else { ?>
			  <a href="?page=sp-contests&contest_id=<?php echo $contest_id; ?>&week=<?php echo $week; ?>" class="alignright">Handicap Setting</a>
			  <?php } ?>
			  <br/><br/><br/>
			  <p>
			  GameWeek:
			  <?php
			  for($i=1; $i<=17; $i++)
			  {
			  	if($i > 1) echo ' | ';
				if($i != $week)echo '<a href="admin.php?page=sp-contests&contest_id='.$contest_id.'&week='.$i.($grading? '&grading=true':'').'">';
				echo $i;
				if($i != $week)echo '</a>';
			  }
			  ?>
			  </p>
		<?php
			 }

	if($add){ ?>
	<div class='table'>
		<form method="post" action="?page=sp-contests&contest_id=<?php echo $contest_id; ?>">
			<input type="hidden" name="is_posted" value="1">
			<input type="hidden" name="add" value="true">
			<table>
				<tr>
	                <th>Home</th>
					<th>Handicap</th>
	                <th>Visitor</th>
					<th>Handicap</th>
					<th>Game Date</th>
					<th>GameWeek</th>
					<th>Delete</th>
	              </tr>
				<tr id="lastHold">
				  <td colspan="7" style="text-align:center">
				  	<a id="addMore"><button>Add Another</button></a>
				  	<input type="Submit" name="sbt_button" id="sbt_button" value="Save Changes"></td>
				</tr>
	            </table>
			  </form>
	          </div>
	          <script type="text/javascript">
	jQuery(function($){

		$('#addMore').click(function(event){
			$('#lastHold').before('<tr><td><input required="required" type="text" name="gameinfo[home][name][]" value="" size="15"/></td><td><input required="required" type="text" name="gameinfo[home][handicap][]" value="" size="5"/></td><td><input required="required" type="text" name="gameinfo[away][name][]" value="" size="15"/></td><td><input required="required" type="text" name="gameinfo[away][handicap][]" value="" size="5"/></td><td><input required="required" class="gameTime" type="text" name="gameinfo[time][]" value="" size="15"/></td><td><input required="required" class="gameWeek" type="text" name="gameinfo[week][]" value="" size="2"/></td><td style="cursor:pointer" onclick="jQuery(this).parent().remove();">X</td></tr>');

			$( ".gameTime" ).datetimepicker({ dateFormat: "yy-mm-dd" });
			event.preventDefault();

		});

		$('#addMore').trigger('click');

	});
      	</script>




<?php }elseif ($grading) {
	 ?>
	<div class='table'>
		<form method="post" id="gradeForm">
			<table>
				<tr>
					<th>Game Date Time (PST)</th>
	                <th>Home</th>
					<th>Home Score</th>
	                <th>Away</th>
					<th>Away Score</th>
					<th>Result</th>
	              </tr>

	              <?php
	              $submit_grade = false;
	               if(empty($week_arr[0]['winning_team'])){
	              	$submit_grade = true;
	              } ?>


	            <?php foreach ($week_arr as $key => $value) { ?>
	            <tr>
					<td><?php echo date('Y-m-d H:i', strtotime($value['game_date'])); ?></td>
					<td><?php echo $value['home_team']; ?> <?php echo $value['ps_home']; ?></td>
					<td><input type='textbox' name='scores[<?php echo $value['game_id']; ?>][home]' value='<?php echo $value['home_score']; ?>' size="3"/>
					<td><?php echo $value['away_team']; ?> <?php echo $value['ps_away']; ?></td>
					<td><input type='textbox' name='scores[<?php echo $value['game_id']; ?>][away]' value='<?php echo $value['away_score']; ?>' size="3"/>
					<td class="ajaxResponse" style="font-weight:bold;" data-id="<?php echo $value['game_id']; ?>"><?php echo $value['winning_team']; ?></td>
				</tr>

	            <?php } ?>
	            <tr><td colspan="4"></td><td><?php echo $submit_grade? '<input type="submit" id="gradesubmit" value="Score" />':''; ?></td><td>&nbsp;</td></tr>
	            </table>
			  </form>
	          </div>

	          <script type="text/javascript">

	          jQuery(function($){

					$('#gradesubmit').click(function(event){
					//	if(confirm('Are you sure?')){
							var scores = $('#gradeForm').serialize();
								scores += '&action=' + 'soccer_grade' + '&contest_id=<?php echo $contest_id; ?>&week=<?php echo $week; ?>';
							
			
							// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
							$.post(ajaxurl, scores, function(response) {
								if(response){
									$('.ajaxResponse').each(function(){
										$(this).html(response[$(this).data('id')]);

									});
									$('#gradesubmit').remove();

								}
							}, 'json');
					//	}
						

						event.preventDefault();
					})

				});
				</script>



<?php 


}else if(count($week_arr) > 0) { ?>


		      <div class='table'>
			  <form name="f1" id="f1" method="post">
			  	<input type="hidden" name="is_posted" value="1">
				<input type="hidden" name="week" value="<?php echo $week; ?>">
	            <table>
	              <tr>
	                <th style='vertical-align: middle !important;'>
	                	Game Time (PST)
	                </th>
	                <th style='vertical-align: middle !important;'>
					  &nbsp;&nbsp;Home
	                </th>
					<th>&nbsp;&nbsp;</th>
	                <th>
	                  &nbsp;&nbsp;Visitor
	                </th>
					<th>&nbsp;&nbsp;</th>
					<th>Delete Event</th>
	              </tr>
				<?php
				$published = 0;
				foreach($week_arr as $wk)
				{
					if($wk['published'] == 'yes' && $published == 0)
					{
						$published = 1;
						$published_time = $wk['published_time'];
					}
				?>
	              <tr id="gameNum<?php echo $wk['game_id']; ?>">
	                <td style='vertical-align: middle !important;'>
	                  <?php echo $wk['game_date']; ?>&nbsp;&nbsp;
					</td>
					<td style='vertical-align: middle !important;'>
	                  <?php echo $wk['home_team']; ?>&nbsp;&nbsp;
					</td>
					<td>
					  <input type='textbox' name='home[<?php echo $wk['game_id']; ?>]' value='<?php echo $wk['ps_home']; ?>' size="5"/>
	                </td>
	                <td>
	                  <?php echo $wk['away_team']; ?>&nbsp;&nbsp;
					  <input type="hidden" name="game[<?php echo $wk['game_id']; ?>]" value="<?php echo $wk['game_id']; ?>">
					</td>
					<td>
					  <input type='textbox' name='away[<?php echo $wk['game_id']; ?>]' value='<?php echo $wk['ps_away']; ?>' size="5"/>
	                </td>
	                <td style="vertical-align:middle;">
					  <a href="#" class="deleteMatch" style="border: 1px solid; color: #FF0000;padding: 4px 7px;" data-id="<?php echo $wk['game_id']; ?>">X</a>
	                </td>
	              </tr>
				<?php
				}
				?>
				<tr>
				  <td colspan="6" style="text-align:center;"><?php if($published == 1){ echo 'Published at ' .date('d/m/Y H:i', strtotime($published_time)); } else{ echo 'Not Published'; } ?></td>
				</tr>
				<tr>
				  <td colspan="6" style="text-align:center;"><?php if($pub_disable == 0){ ?><input type="Submit" name="publish" id="sbt_button" value="Publish"><?php  } ?></td>

				</tr>
	            </table>
			  </form>
	          </div>

	          <script type="text/javascript">

	          jQuery(function($){


					$('.deleteMatch').click(function(event){
						if(confirm('Are you sure?')){
							
							var data = {
								action: 'soccer_delete',
								game_id: $(this).data('id'),
								contest_id: <?php echo $contest_id; ?>
							};

							// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
							$.post(ajaxurl, data, function(response) {
								if(response=='success'){
									$('#gameNum'+data.game_id).remove();

								}else{
									alert('Error, Please retry');
								}
							}, 'json');
						}
						

						event.preventDefault();
					})

				});
				</script>

			  <?php } else { ?>
			  <div class='table'>
	            <table>
	              <tr>
	                <th style='vertical-align: middle !important;'>
	                  &nbsp;&nbsp;No Data Found for GameWeek <?php echo $week; ?>.
	                </th>
				  </tr>
				</table>
			  </div>
			  <?php } ?>



	          <!--change contest dates here-->
	          <br/>
	          Contest Start &amp; End Dates (For testing only)
	          <br/>
	          <br/>
	          <?php $dates = getContestStartEndDate(3,false); ?>

		<form method="post">
			<input type="hidden" name="change_dates" value="true">
			<input type="hidden" name="is_posted" value="true">
			<label>Start Date:<input type='textbox' readonly="readonly" class="startEndDate" name='start' value='<?php echo $dates['start']; ?>' size="15"/></label>
			<label>End Date:<input type='textbox' readonly="readonly" class="startEndDate" name='end' value='<?php echo $dates['end']; ?>' size="15"/></label>
			<input type="submit" value="Change"/>
			</form>
	          <script type="text/javascript">

	          jQuery(function($){

	          	$( ".startEndDate" ).datetimepicker({ dateFormat: "yy-mm-dd" });
	          });
	          </script>
	          <!--change contest dates here-->          
	
	        </article>
	    </div>