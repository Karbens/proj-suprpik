<?php
require_once('connect.php');
if (isset($_POST['collection']) && isset($_POST['cid']))
{
	$cid 	= trim($_POST['cid']);
	$cemail = trim($_POST['cemail']);
	if ($cid != '' && $cemail != '')
	{
		$values = $_POST['id'];
		$tAr = array();
		foreach ($values as $v)
		{
			$s = explode(',', $v);
			$tAr[$s[0]][] = $s[1];
		}
		ksort($tAr);
		
		//convert answers array to string
		asort($tAr[team]);
		$ans_array[] = 'Round 1:'.implode(',',$tAr[team]);
		for($i=2;$i<7;$i++)
		{
			asort($tAr[$i]);
			$rdl = 'Round '.$i.':'.implode(',',$tAr[$i]);
			if($i < 6)
			{
				$r = 12 - $i;
				$rdr = implode(',',$tAr[$r]);
				$rdl = $rdl.','.$rdr;
			}
			$ans_array[] = $rdl;
		}
		// end of convert answers array to string
		
		//set string and urlencode
		$ans = implode(';',$ans_array);
		$result = urlencode($ans);
		
		// call the appropriate function to save data into the database
		setAnswers($cid, $cemail, $result);
		
	} else echo 'Invalid Account Number';
}

if( isset($_POST['seeMyBracket']) &&  isset($_POST['cid'])  && isset($_POST['cemail']) )
{
	include_once('../admin/db_func.php');
	tep_db_connect();
	$cid = $_POST['cid'];
	$cemail = $_POST['cemail'];
	$nque = mysql_query("SELECT `ANSWERS`
						 FROM `bracket_user`
						 WHERE `USERID` = '".$cid."'
						 AND `EMAIL` = '".$cemail."'");
	if( @mysql_num_rows($nque) > 0 )
	{
		echo '1';
	}else
	{
		echo '0';
	}
	tep_db_close();
}
exit;
?>