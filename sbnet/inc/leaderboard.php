<style type="text/css">
#blogFeed { line-height:20px; color:#000; }
</style>

<?php
define('_VALID_MOS', '1');//for accessing contests_func.php
include_once('contests_func.php');//db connection file for contests database
tep_db_connect();
$streakers = get_streakers();
tep_db_close();
?>
<section id="blogFeed">
	<header>
		<h2>STREAKERS LEADERBOARD</h2>
	</header>
	<?php
		
		$content = '<div align="center">';
		if( count($streakers > 0) )
		{
				$content .= '<table>
							   
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
				$content .= '</table>';
		}//end of if( count($streakers > 0) )
		$content .= '</div>';
		echo $content;
	?>
	</section>
