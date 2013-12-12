<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once('teams.php');
if (isset($_REQUEST['populate']))
{
	$populate = $_REQUEST['populate'];
	$s = new InitTeams;
	$s->getMatchUps() ;
	echo '<table cellpadding="0" cellspacing="0" width="100%"><tr class="round">
	<td>Round One</td>
	<td>Round Two</td>
	<td>Sweet Sixteen</td>
	<td>Elite Eight</td>
	<td>Final Four</td>
	<td>Championship</td>
	<td>Final Four</td>
	<td>Elite Eight</td>
	<td>Sweet Sixteen</td>
	<td>Round Two</td>
	<td>Round One</td>
	</tr>
	<tr  class="rounddate">
	<td>March 15-16, 2012</td>
	<td>March 17, 18, 2012</td>
	<td>March 22, 23, 2012</td>
	<td>March 24, 25, 2012</td>
	<td>March 31, 2012</td>
	<td>April 2, 2012</td>
	<td>March 31, 2012</td>
	<td>March 24, 25, 2012</td>
	<td>March 22, 23, 2012</td>
	<td>March 17, 18, 2012</td>
	<td>March 15-16, 2012</td>
	</tr>
	<tr><td colspan="11">&nbsp;</tr>';
	$s->createRoundTeams($populate);
	echo '</table>';
	exit;
} else if (isset($_POST['cid']) && isset($_POST['answers']))
{
	echo 'Sorry, the contest is closed.';
	/*
	$s = new InitTeams;
	$r = $s->setAllInfoForCID($_POST['cid'], urldecode($_POST['answers']));
	if ($r == InitTeams::ROUND_ALREADY_SET)
	{
		echo 'You have already entered the contest';
	}
	else if ( $r== InitTeams::ROUND_ALREADY_SET)
	{
		echo 'There was an error processing your request. Please try again later';
				
	} else echo 'Thanks for entering the contest. Your answers have been recorded';
	*/
}
exit;