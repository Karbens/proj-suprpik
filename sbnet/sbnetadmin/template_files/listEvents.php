<?php
require_once('session.php');

$contestDate = $this->current_contest_date;
$contestDateTime = strtotime($this->current_contest_date);
$currentDate = date('Y-m-d');
$currentDateTime = strtotime("now");

?>




<?php
$table_header = '<td colspan="2">&nbsp;';
$view_entries_text = '';

		   if($this->get_entry_count() > 0)
		   {
		   	$view_entries_text = '<p> <a href="contest_entries.php?contest_id='.$this->contest_id.'" style="color:green;font-weight:bold;">VIEW ENTRIES</a> </p>';
		   }


		 if( $contestDateTime < $currentDateTime && count($events) > 0)  {
		   	 $table_header .= 
			 	  '<p style="color:red;"> This contest has expired, so you can\'t modify it.</p>'.
				  '</td>
				   <td valign="top">'.
				     '<p> <a href="contest_grade.php?contest_id='.$this->contest_id.'" style="color:green;font-weight:bold;">GRADE THIS CONTEST</a> </p>'.
					 $view_entries_text;
		   }else
		   {
		   	 $table_header .= 
			 	  '</td>
				   <td valign="top">'.$view_entries_text;
		   }
		   ?>

<form name="eventForm" id="eventForm" method="post" action="home.php">
<input type="hidden" name="contest_id" value="<?php echo $this->contest_id; ?>" />
<table class="eventTable">

<?php

	echo $table_header;

		   $events_counter = 1;//event counter
		   foreach($events as $event)
		   {
		   		$event_id = $event['event_id'];
				$choices = $this->getChoices($event_id);


		   ?>
		   
		   <tr id="eveRow_<?php echo $event_id; ?>">
		     <td width="800" colspan="2">
			   <table width="100%">
			   <tr>
			   	 <td width="100">
		   		    <a name="eventname_<?php echo $event_id; ?>"></a>Event <?php echo $events_counter; ?>:
			     </td>
			     <td width="700">
				 	<input type="text" name="event[<?php echo $event_id; ?>]" value="<?php echo $event['event_desc']; ?>" class="required eventInput" required="required" />
				 </td>
				</tr>
				<?php if($this->contest_daily){ ?>
				 <tr>
			   		<td width="100">Time:</td>
					<td width="700">
					<input type="text" name="eventtime[<?php echo $event_id; ?>]" value="<?php echo $event['event_time']; ?>" class="eventtime" required="required"/>
					</td>
			   	</tr>
				 <?php } ?>
			   </table>
			 </td>
			 <td class="tdDel" nowrap>
			 <?php 
			 if($events_counter > 1 && $contestDateTime >= $currentDateTime)
			 {
			   echo  '<a href="#" class="button_remove" data-event_id="'.$event_id.'" title="Remove This Event">&nbsp;</a>';
			 }else echo '&nbsp;';
			 ?>
			 </td>
		   </tr>
		   
			   <?php
			   if(count($choices)  > 0 )
			   {
			   ?>
			   <tr id="eceRow_<?php echo $event_id; ?>">
				 <td colspan="2">
					<table width="100%" style="font-size: 12px;" id="eventTable_<?php echo $event_id; ?>">
					<?php
					$choice_counter = 1;//choices counter
					foreach($choices as $choice)
					{
						$choice_id = $choice['ec_id']
					?>
					  <tr id="chRow_<?php echo $choice_id; ?>">
					    <td width="80">Choice <?php echo $choice_counter; ?>:</td>
					    <td width="350">
						<input type="text" name="choice[<?php echo $choice_id; ?>]" value="<?php echo $choice['choice']; ?>" class="required choiceInput" required="required" />
						</td>
						<td nowrap><?php
						if($choice_counter == 2 && $contestDateTime >= $currentDateTime)
						{
							echo '<a href="#eventname_'.$event_id.'" class="button_plus" data-event_id="'.$event_id.'" title="Add New Choice">&nbsp;</a>';
						}
						if($choice_counter > 2 && $contestDateTime >= $currentDateTime )
						{
							echo '<a href="#eventname_'.$event_id.'" class="button_minus" data-event_id="'.$event_id.'" data-choice_id="'.$choice_id.'" title="Remove This Choice">&nbsp;</a>';
						}
						?>
						</td>
					  </tr>
					<?php
						$choice_counter++;//increment choices counter
					}//end of foreach($choices as $ch)
					?>
					</table>
				</td>
			     <td>&nbsp;</td>
			   </tr>
			   <?php
			   }//end of if( count($choices) > 0 )
			   ?>
		   
		   <tr id="bleRow_<?php echo $event_id; ?>">
		   	<td colspan="3">
			  <table width="100%">
			    <tr><td>&nbsp;</td></tr>
				<tr><td><hr></td></tr>
				<tr><td>&nbsp;</td></tr>
			  </table>
			</td>
		   </tr>
		   
		   <?php
		   	$events_counter++;//increment event counter
		   }//end of foreach($events as $ev)

if($contestDateTime >= $currentDateTime)
		   {
		   ?>
		   <tr id="addNewEvent">
		   	<td>&nbsp;</td>
			<td align="center"><a name="newevent">&nbsp;</a>&nbsp;<a href="#newevent" style="font-size:19px; font-weight:bold; color:green;">Add Another Event</a></td>
			<td>&nbsp;</td>
		   </tr>
		   <?php
		   }
		   ?>


	<tr>
		<td><input type="submit" name="submit_button" id="submit_button" value="Save" style="font-weight:bold;"></td>
		<td>&nbsp;</td>
	</tr>
</table>
</form>

<script type="text/javascript">
	$(function(){
		$('.button_remove').click(function(e){

			data = {action:'delete_event', contest_id:<?php echo $this->contest_id; ?>, event_id:$(this).data('event_id')};

			$.post( "ajax.php",data, function( data ) {
				if(data.success=='true'){
					$("#eveRow_"+data.pass_id+", #eceRow_"+data.pass_id+", #bleRow_"+data.pass_id).remove();

				}
			});
			e.preventDefault();

		});

		var minus_function = function(e){

			data = {action:'delete_choice', contest_id:<?php echo $this->contest_id; ?>, event_id:$(this).data('event_id'), choice_id: $(this).data('choice_id')};

			$.post( "ajax.php",data, function( data ) {
				if(data.success=='true') $("#chRow_"+data.pass_id).remove();
			});
			e.preventDefault();			
		}


		$('.button_minus').click(minus_function);

		$('.button_plus').click(function(e){

			data = {action:'add_choice', contest_id:<?php echo $this->contest_id; ?>, event_id:$(this).data('event_id')};

			$.post( "ajax.php",data, function( data ) {
				if(data.success=='true'){
					$('#eventTable_'+data.pass_id).append(data.content);
					$('.button_minus').click(minus_function)

				}
			});
			e.preventDefault();

		});


		$('#addNewEvent').click(function(e){

			data = {action:'add_event', contest_id:<?php echo $this->contest_id; ?>};

			$.post( "ajax.php",data, function( data ) {
				if(data.success=='true'){
					window.location.href = window.location.href;

				}
			});

			e.preventDefault();
		});

	<?php if($this->contest_daily){ ?>
		$('.eventtime').timepicker();
	<?php } ?>


	});
</script>