<?
session_start();
include_once('sbnetadmin/db_func.php');
tep_db_connect();
if(isset($_POST))
{	
	$event_choices = $_POST['choices'];
	$customer_id = $_SESSION['logged_id'];
	$customer_name = $_SESSION['logged_name'];
	if(!empty($event_choices)){
		foreach($event_choices as $evc){
			$choice_arr = explode("_",$evc);
			$event_choice_id = $choice_arr[0];
			$choice_type = $choice_arr[1];
			
						  
			$query = "insert into customer_choices(ec_id,customer_id,choice_type) 
					  VALUES($event_choice_id,$customer_id,$choice_type)";
			$result = mysql_query($query);
		}
	}
	header('Location: /contest.php');
	exit;
	
} 
?>