<?
session_start();
if(empty($_SESSION['UserID']))
{
	header("Location: ../login.php");
	exit();
}
else
{
	$UserID=$_SESSION['UserID'];
	$UserName=$_SESSION['UserName'];
}
  include('../db_func.php');
  tep_db_connect();
?>