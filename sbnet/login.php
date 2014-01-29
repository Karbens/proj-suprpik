<?
session_start();
include_once('sbnetadmin/db_func.php');
tep_db_connect();
if(isset($_POST))
{	
	$UserName = trim($_POST['username']);
	$Password = md5(trim($_POST['password']));

	$query = "SELECT * 
			  FROM sb_signups 
			  WHERE `username` = '".$UserName."'
			  AND `password` = '".$Password."'";
    $result = mysql_query($query);
    if(@mysql_num_rows($result)<1){
	  echo 1;
	}
	else{
		$_SESSION['logged_id'] = mysql_result($result,0,"signup_id");
		$_SESSION['logged_name'] = mysql_result($result,0,"username");
		echo 0;
		/* redirect to contests page */
	}
}	
?>