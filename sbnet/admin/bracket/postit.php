<?php
require_once('connect.php');
if (isset($_POST['collection']) && isset($_POST['cid']))
{
	$cid = trim($_POST['cid']);
	if (is_numeric($cid))
	{
		$values = $_POST['id'];
		$tAr = array();
		foreach ($values as $v)
		{
			$s = explode(',', $v);
			$tAr[$s[0]][] = $s[1];	
		}
		ksort($tAr);
		$result = urlencode(serialize($tAr));
		// call the appropriate function to save data into the database
		$r = setAnswers('cid='.$cid .'&answers='.$result);
		echo $r;
		
	} else echo 'Invalid Account Number';
}
exit;
?>