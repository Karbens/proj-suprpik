<?php
/*
function setAnswers($post) 
{ 
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, 'http://crman03.gamingsys.net/bracket/teams.php'); 
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch); 
	if (curl_errno($ch) > 0) $result = -1;
	curl_close($ch); 
	return $result; 
} */

function setAnswers($cid, $cemail, $answers)
{
	require_once('teams.php');
	$s = new InitTeams;
	$r = $s->setAllInfoForCID($cid, $cemail, urldecode($answers));
	if ($r == InitTeams::ROUND_ALREADY_SET)
	{
		echo 'You have already entered the contest.';
	}
	else if ( $r == InitTeams::ROUND_ALREADY_SET)
	{
		echo 'There was an error processing your request. Please try again later.';
				
	}
	else if ( $r == 'Expired')
	{
		echo 'This contest has expired. Thank You.';
				
	} else echo 'Thanks for entering the contest. Your answers have been recorded.';
}