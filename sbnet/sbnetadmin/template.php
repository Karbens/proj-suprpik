<?php

if(!defined('IS_FRONTEND')){
	define('IS_FRONTEND', false);
}


if( (IS_FRONTEND && !isset($_SESSION['user'])) || (!IS_FRONTEND && !isset($_SESSION['UserID'])) ){	
	header("Location: login.php");
	exit();
}

define('TEMPLATE_INCLUDED', true);


	class template{

		private $contest_id = 0;
		public $frontend = IS_FRONTEND;
		public $contest_name = '';
		public $contest_desc = '';
		public $contest_daily = false;
		public $status = '';
		public $current_contest_date = '';
		public $contest_publish_date = '';

		function template($contest = array('contest_id' => 0)){

			if($contest['contest_id'] > 0){
				$this->contest_id = (int)$contest['contest_id'];
				$this->contest_name = $contest['contest_name'];
				$this->contest_desc = $contest['contest_desc'];
				$this->contest_daily = $contest['contest_daily']=='Yes';
				$this->current_contest_date = $contest['current_contest_date'];
				$this->contest_publish_date = $contest['contest_publish_date'];
				$this->status = $contest['status'];
			}

		}

		public function getEvents(){

			if($this->contest_id>0){

				$query = "SELECT * FROM `events` WHERE event_desc!='' AND `contest_id` = $this->contest_id";
				$result = mysql_query($query);
				$events = array();
				if(@mysql_num_rows($result) > 0)
				{
					while($row = mysql_fetch_assoc($result))
					{
						$events[] = $row;
					}
				}
				return $events;
			}	


		}

		public function getChoices($event_id){

			$query = "SELECT * FROM `events_choices` WHERE `event_id` = $event_id";
			$result = mysql_query($query);
			$choices = array();
			
			if(@mysql_num_rows($result) > 0){

				while($row = mysql_fetch_assoc($result)){
					$choices[] = $row;
				}
			}
			return $choices;
		}	


		public function get_entry_count(){
			$query = "SELECT * FROM `contest_entries` WHERE `contest_id` = ".$this->contest_id;
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			return $count;
		}

		public function get_contest_entries(){

			$query = "SELECT * FROM `contest_entries`
					WHERE `contest_id` = ".$this->contest_id;
			$result = mysql_query($query);
			$ret = array();
			if(@mysql_num_rows($result) > 0)
			{
				while($res = mysql_fetch_assoc($result))
				{
					$ret[] = $res;
				}
			}
			return $ret;
		}

		public function contest_graded_result() {

			$que = "SELECT e.`event_result` , c.`choice`
					FROM `events` e
					LEFT JOIN `events_choices` c ON c.`ec_id` = e.`event_result`
					WHERE `contest_id` = ".$this->contest_id."
					AND `event_result` >0";
			$query = @mysql_query($que);
			$ret = array();
			if(@mysql_num_rows($query) > 0)
			{
				while($res = mysql_fetch_row($query))
				{
					$id = $res[0];
					$ret[$id] = $res[1];
				}
			}
			return $ret;

		}

		public function entered_values_count($entry_values){
			if(trim($entry_values) == '')return 0;
			$que = "SELECT *
					FROM `events`
					WHERE `contest_id` =".$this->contest_id."
					AND `event_result`
					IN ( ".$entry_values." )";
			$query = @mysql_query($que);
			$ret = @mysql_num_rows($query)+0;
			return $ret;
		}

		function get_entry_choices($entry_value){
			$que = "SELECT `ec_id`, `choice`
					FROM `events_choices`
					WHERE ec_id
					IN (".$entry_value.")";
			$query = @mysql_query($que);
			$ret = array();
			if(@mysql_num_rows($query) > 0)
			{
				while($res = mysql_fetch_row($query))
				{
					$id = $res[0];
					$ret[$id] = $res[1];
				}
			}
			return $ret;
		}


		public function listEvents(){ 

			$events = $this->getEvents();

			include('template_files/listEvents.php');

		}

		public function updateEvents($events = array(), $choices = array(), $eventtimes = array()){

			foreach($events as $key => $event_desc){
				mysql_query("UPDATE `events` SET `event_desc` = '".mysql_real_escape_string($event_desc)."' WHERE `event_id` = ".(int)$key);
			}
			foreach($choices as $key => $choice_value){
				mysql_query("UPDATE `events_choices` SET `choice` = '".mysql_real_escape_string($choice_value)."' WHERE `ec_id` = ".(int)$key);
			}
			foreach($eventtimes as $key => $event_time){
				mysql_query("UPDATE `events` SET `event_time` = '".mysql_real_escape_string($event_time)."' WHERE `event_id` = ".(int)$key);
			}
		}

		public function delete_event($event_id){

			$delete_choices = "DELETE FROM `events_choices` WHERE `event_id` = '".(int)$event_id ."'";
			if(mysql_query($delete_choices)) {

				$delete_event = "DELETE FROM `events` WHERE `event_id` = '".(int)$event_id ."'";

				if(mysql_query($delete_event)) {
				return true;
				}

			  }
			  return false;

		}

		public function delete_event_choice($event_id, $choice_id){

			$query = "DELETE FROM `events_choices` WHERE `event_id` = '".(int)$event_id ."' AND `ec_id` = '".(int)$choice_id."'";

			if(mysql_query($query)) {
				return true;
			  }
			  return false;

		}

		public function add_event_choice($event_id){



			$query = "INSERT INTO `events_choices` (`ec_id`, `event_id`, `choice`, `ec_order`) VALUES (NULL, '".(int)$event_id."', '', '0')";


			if(mysql_query($query)){
				return mysql_insert_id();

			}
			return false;

		}

		public function add_event(){

			if($this->contest_id>0){

				$insert_event = "INSERT INTO `events` 
								(
								`event_id` ,  `contest_id` , `event_date` , `event_desc` , `event_order`
								)
								VALUES 
								(
								NULL , '".(int)$this->contest_id."', '".$this->current_contest_date."', '', ''
								)";
				if(mysql_query($insert_event)){

					$event_id = mysql_insert_id();
					$insert_event_choices = "INSERT INTO `events_choices` 
					  			(
								`ec_id` , `event_id` , `choice` , `ec_order`
								)
								VALUES 
								(
								NULL , '".(int)$event_id."', '', '0'
								), (
								NULL , '".(int)$event_id."', '', '0'
								)";

					if(mysql_query($insert_event_choices)){
						return $event_id;
					}
				}
			}
			return false;
		}

		public function create_contest($contest = array()){

			$defaults = array('template' => '', 'contest_name' => '', 'contest_desc' => '', 'status' => '', 'contest_daily' => 'No');

			$contest = array_merge($defaults, $contest);

			$error = false;

			$template = get_class($this);

			$contest_name = mysql_real_escape_string($contest['contest_name']);

			$contest_daily = $contest['contest_daily'];

			$contest_desc = mysql_real_escape_string($contest['contest_desc']);

			$status = $contest['status']=='1'?'Online':'Offline';

			if(empty($contest_name)) $error = true;
			if(empty($contest_desc)) $error = true;

			$latest_datetime_today = strtotime(date( 'Y-m-d 00:00:00'));

			$publish_strtotime = strtotime(trim($contest['contest_publish_date']));

			$contest_publish_date = date( 'Y-m-d H:i:s', $publish_strtotime );

			$current_contest_strtotime = strtotime(trim($contest['current_contest_date']));
			$current_contest_date = date( 'Y-m-d H:i:s', $current_contest_strtotime );

			$delay_timestamp = '';

			if($latest_datetime_today > $publish_strtotime) {
				$error = true;
				$message = 'Please enter a publish date later than today.';
			}

			if($latest_datetime_today > $current_contest_strtotime) {
				$error = true;
				$message = 'Please enter a Cut off date later than today.';
			}

			if($publish_strtotime > $current_contest_strtotime) {
				$error = true;
				$message = 'Publish date cannot be greater than Cut off date';
			}

			if(!$error){
				//insert into db
		 		$query = "INSERT INTO  `contests` (`contest_id` ,`template` ,`contest_name` ,`contest_daily` ,`contest_desc` ,
	`status`, `contest_publish_date` ,`current_contest_date` ,`delay_timestamp`)
	VALUES (NULL ,  '$template',  '$contest_name',  '$contest_daily',  '$contest_desc',  '$status', '$contest_publish_date',  '$current_contest_date',  '$delay_timestamp')";

				if(mysql_query($query)){

					$this->contest_id = mysql_insert_id();

					$this->current_contest_date = $current_contest_date;

					$this->add_event();


					$message = 'Contest sucessfully added.';				
				}else{
					$message = 'Error creating new contest, please retry.';
				}
			}

			return compact('error', 'message');

		}


		public function update_contest($contest = array()){

			if($this->contest_id>0){

				$contest_name = mysql_real_escape_string($contest['name']);
				$contest_desc = mysql_real_escape_string($contest['desc']);
				$status = $contest['status']==1?'Online':'Offline';
				$contest_publish_date = date( 'Y-m-d', strtotime($contest['contest_publish_date']) );
				$current_contest_date = date( 'Y-m-d H:i:s', strtotime($contest['current_contest_date']) );

				mysql_query("UPDATE `contests` SET `contest_name` = '$contest_name', `contest_desc` = '$contest_desc', `status` = '$status', `contest_publish_date` = '$contest_publish_date',
					`current_contest_date` = '$current_contest_date' WHERE `contest_id` = ".(int)$this->contest_id);
				return true;

			}

		}
	}



//add all templates
	$dir = dirname(__FILE__).'/templates';
	if(is_dir($dir)){
		$files = scandir($dir);
		
	}

	foreach($files as $file){
		if($file=='.' || $file =='..') continue;

		include_once($dir.'/'.$file);
	}

//get subclasses
	$parentClassName = 'template';
	$templates = array();
    foreach (get_declared_classes() as $className){
    	if (is_subclass_of($className, $parentClassName))
    		$templates[] = $className;
    }