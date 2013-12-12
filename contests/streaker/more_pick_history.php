<?php
define('_VALID_MOS', '1');//for accessing contests_func.php
include_once('../contests/contests_func.php');
tep_db_connect();
	
	if( isset($_REQUEST['more_history']) )
	{
		$contest_id = $_REQUEST['contest_id'];
		$username = $_REQUEST['username'];
		$pagination = $_REQUEST['more_history'];
		
		$datacount = get_pick_history_count($contest_id, $username);
		$userdata  = get_pick_history($contest_id, $username, $pagination);
		$next_count = $pagination+30;
		
		$today = date('Y-m-d');
		$content = '';
		if( count($userdata) > 0 )
		{
				$skc = 1;
				foreach($userdata as $sk => $sv)
				{
					$bcol = '';
					if( ($skc%2) == 0 )
					{
						$bcol = ' background:#dcdcdc !important;';
					}
					//figure out the result
					$result = '';
					if($sv['points'] > 0 || $sv['event_result'] > 0)
					{
						$result = ( $sv['points'] > 0 || ($sv['event_result'] == $sv['entry_value']) ) ? 'Won' : 'Loss';
					}
					else
					{
						$result = '<a rel="gb_page_center[600, 200]" href="pick_pending.php?contest_id=1&username='.$username.'">Pending</a>';
					}
					
					
					$bstyle = ' style="font-weight: normal;'.$bcol.'"';
					if($result == 'Won')$bstyle = ' style="font-weight: bold;'.$bcol.'"';
					
					$content .= '
							 <tr'.$bstyle.'>
						   	   <td'.$bstyle.'>'.$sv['contest_date'].'</td>
							   <td'.$bstyle.'>'.$sv['event_desc'].'</td>
							   <td'.$bstyle.'>'.$sv['choice'].'</td>
							   <td'.$bstyle.'>'.$result.'</td>
						     </tr>';
					$skc++;
				}
				
				if( $next_count < $datacount )
				{
					$content .= '<tr class="moreHistory">
								  <td colspan="4" style="text-align:center;">
								  	<a href="javascript:void(\'0\');" onclick="loadMoreHistory(\''.$next_count.'\')">View More...</a>
								  </td>
								</tr>';
				}
		}//end of if( count($userdata) > 0 )
		echo $content;
	}

tep_db_close();
?>