<?php

if(empty($_SESSION['UserID'])){
	header("Location: login.php");
	exit();
}
	class MarchMania extends template{
}