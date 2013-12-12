<link rel="stylesheet" type="text/css" href="../css/fonts.css" />
<style type="text/css">
* { margin:0; padding:0; font-family:"Helvetica", Arial, sans-serif; }
table { text-align:center; font-size:.9em; }
td { padding:5px 12px; }
th { background:#666; color:#fff; padding:5px 14px; }
a { color:#bd580a; }
</style>

<?php
define('_VALID_MOS', '1');//for accessing contests_func.php
include_once('contests_func.php');//db connection file for contests database
tep_db_connect();
$streakers = get_streakers('',10);
tep_db_close();

		
		if( count($streakers > 0) )
		{
				$content .= '<table cellpadding="0" cellspacing="1">
							   
							   <tr>
							   	 <th> # </th>
							     <th> Streaker </th>
								 <th> Streak </th>
							   </tr>
							 ';
				$skc = 1;
				foreach($streakers as $sk => $sv)
				{
					$bcol = '';
					if( ($skc%2) == 0 )
					{
						$bcol = ' bgcolor="#dcdcdc"';
					}
					$content .= '
							 <tr'.$bcol.'>
						   	   <td>'.$skc.'</td>
						       <td>'.$sv['customer_id'].'</td>
							   <td>'.$sv['streak'].'</td>
						     </tr>';
					$skc++;
				}
				$content .= '<tr><td colspan="3"><a href="/streaker/full_leaderboard.php" target="_top">View Full Leaderboard</a></td></tr>';
				$content .= '</table>';
		}//end of if( count($streakers > 0) )
		echo $content;
	?>
	</section>
