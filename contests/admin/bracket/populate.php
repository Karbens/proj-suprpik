<?php
require_once('connect.php');
if (isset($_REQUEST['populate']))
{
	echo setAnswers('populate=1');
	exit;
}
echo '';
exit;