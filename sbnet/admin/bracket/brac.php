<?php
require_once('session.php');
if (isset($_POST['clear']))
{

	for ($i=2;$i<8;$i++)
	{
		$round = (int)$i;
		@mysql_query("UPDATE `bracket_game` SET TID1=0, TID2=0, COLUMN1=0 WHERE ROUND=".$round);
	}
	@mysql_close();
	
	//$Cache_Lite = new Cache_Lite($options);
	//echo '<script type="text/javascript">console.log('.ob_get_clean().');
	echo 'Rounds cleared ... Reloading Page';
}
exit;
?>