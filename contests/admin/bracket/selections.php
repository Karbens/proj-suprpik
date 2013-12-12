<?php
$p = '<table cellpadding="1" cellspacing="1">';

	$con = sqlrcon_alloc('localhost',9000,'','scorecard','G3tUPNg0',0,1);
	$cur = sqlrcur_alloc($con);
	
	sqlrcur_prepareQuery($cur,"SELECT * FROM BRACKET_TEAM");	
	sqlrcur_executeQuery($cur);
	for ($row=0; $row<sqlrcur_rowCount($cur); $row++) {
		$s = sqlrcur_getRowAssoc($cur,$row); 
		$teams[$s['ID']] = $s['NAME'];
	}
	sqlrcon_endSession($con);
	sqlrcur_prepareQuery($cur,"SELECT * FROM BRACKET_USER");	
	sqlrcur_executeQuery($cur);
	for ($row=0; $row<sqlrcur_rowCount($cur); $row++) {
		$s = sqlrcur_getRowAssoc($cur,$row); 
		$p .='<tr><td>'.$s['USERID'].'</td><td>'.date('h:i Ymd', $s['CREATED']).'</td><td>';
		$k = unserialize($s['ANSWERS']);
		$p.='<table cellpadding="1" cellspacing="1">';
		foreach ($k as $round=>$answers)
		{
			foreach ($answers as $id)
			{
				switch ($round)
				{
					case 2:case 10:
					$r2 .= '<td>'.$teams[$id].'</td>';
					break;	
					case 3:case 9:
					$r3 .= '<td>'.$teams[$id].'</td>';
					break;	
					case 4:case 8:
					$r4 .= '<td>'.$teams[$id].'</td>';
					break;	
					case 5:case 7:
					$r5 .= '<td>'.$teams[$id].'</td>';
					break;
					case 6:
					$r6 .= '<td>'.$teams[$id].'</td>';
					break;	
				}
			}
			
		}
		$p .= "<tr>$r2</<tr><tr>$r3</<tr><tr>$r4</<tr><tr>$r5</<tr><tr>$r6</<tr>";
		$p.='</table>';
		$p.='</td>';
	}
	sqlrcon_endSession($con);
	sqlrcur_free($cur);
	sqlrcon_free($con);
	
$p .= '</table>';
echo $p;	
?>