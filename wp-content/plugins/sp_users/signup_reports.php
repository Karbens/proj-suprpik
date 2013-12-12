<?php

	$results = get_users( $args );

	$year = isset($_GET['year'])? (string)$_GET['year'] : date('Y');
	$month = isset($_GET['month'])? (string)$_GET['month'] : date('n');
	$date = isset($_GET['date'])? (string)$_GET['date'] : date('j');


		$signups = array('year' => 0, 'month' => 0, 'date' => 0);
			foreach ( $results as $result ){
				$timestr = strtotime($result->user_registered);
				
				if($year == date('Y', $timestr)){
					$signups['year']++;

					$reg_month = date('n', $timestr);
					$reg_month_name = date('F', $timestr);
					if(isset($signups[m][$reg_month_name])) $signups[m][$reg_month_name]++;
					else $signups[m][$reg_month_name] = 1;					

					if($month == $reg_month){
						$signups['month']++;

							$reg_date = date('j', $timestr);
							if(isset($signups[d][$reg_date])) $signups[d][$reg_date]++;
							else $signups[d][$reg_date] = 1;
	
						if($date == date('j', $timestr)){
							$signups['date']++;
						}
					}
				}		
			}


			if(in_array($_GET['orderby'], array('date_signups', 'month_signups', 'year_signups'))){

				if($_GET['order']=='desc'){
					arsort($signups['m']);
					arsort($signups['d']);
				}else{
					asort($signups['m']);
					asort($signups['d']);
				}

			}else{
				if($_GET['order']=='desc'){
					krsort($signups['m']);
					krsort($signups['d']);
				}else{
					ksort($signups['m']);
					ksort($signups['d']);
				}
			}





$headings = array('month' => array('title' => 'Month', 'class' => 'manage-column column-date sortable asc'),
					'month_signups' => array('title' => 'Signups', 'class' => 'manage-column column-date sortable asc')
						);


	$table_head = '';
	foreach($headings as $id => $heading){
		$table_head .= '<th class="'.$heading['class'].'" id="'.$id.'"><a href="?page=sp_users_signups&orderby='.$id.'&order='.(( isset($_GET['order'], $_GET['orderby']) && $_GET['orderby']==$id && $_GET['order'] =='asc'  )?'desc':'asc').'">'.$heading['title'].'</a></th>';
	} 

?>

<div class="wrap">
	<h2><?php _e('SignUp Report'); ?></h2>

	<form method="get">
	<input type="hidden" name="page" value="sp_users_signups"/>
	<select name="year">
		<?php
		for($i=0, $init=$year-25;$i<50;$i++){
		 echo '<option value="'.$init.'"'.($year==$init?'selected="selected"':'').'>'.$init++.'</option>'; 

	} ?>
	</select>
	<select name="month">
		<?php
		for($j=1;$j<=12;$j++){
			$month_name = date('F', mktime(0, 0, 0, $j, 1, 2000));
		 echo '<option value="'.$j.'"'.($month==$j?'selected="selected"':'').'>'.$month_name.'</option>'; 

	} ?>
	</select>
	<select name="date">
		<?php
		for($k=1;$k<=31;$k++){
		 echo '<option value="'.$k.'"'.($date==$k?'selected="selected"':'').'>'.$k.'</option>'; 

	} ?>
	</select>
	<input type="submit" value="Find"/>
</form>

	<h3>Total Sign Ups for <?php echo $year; ?> : <?php echo $signups['year']; ?></h3>

	<table class="wp-list-table widefat fixed media" cellspacing="0">
	<thead>
	<tr><?php echo $table_head; ?></tr>
	</thead>

	<tfoot>
	<tr><?php echo $table_head; ?></tr>
	</tfoot>

	<tbody>
		<?php
		foreach ($signups[m] as $key => $value) {
			echo '<tr>';
			echo '<th>' .$key . '</th>' .'<td>' .$value . '</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>

<?php
$headings = array('date' => array('title' => 'Date', 'class' => 'manage-column column-date sortable asc'),
					'date_signups' => array('title' => 'Signups', 'class' => 'manage-column column-date sortable asc'),
						);

	$table_head = '';
	foreach($headings as $id => $heading){
		$table_head .= '<th class="'.$heading['class'].'" id="'.$id.'"><a href="?page=sp_users_signups&orderby='.$id.'&order='.(( isset($_GET['order'], $_GET['orderby']) && $_GET['orderby']==$id && $_GET['order'] =='asc'  )?'desc':'asc').'">'.$heading['title'].'</a></th>';
	} 

?>

	<h3>Total Sign Ups for <?php echo date('F', mktime(0, 0, 0, $month, 1, 2000)); ?> : <?php echo $signups['month']; ?></h3>

	<table class="wp-list-table widefat fixed media" cellspacing="0">
	<thead>
	<tr><?php echo $table_head; ?></tr>
	</thead>

	<tfoot>
	<tr><?php echo $table_head; ?></tr>
	</tfoot>

	<tbody>
		<?php
		for($i=1,$j=cal_days_in_month(CAL_GREGORIAN,$month, $year); $i<=$j; $i++){
			echo '<tr>';
			echo '<th>' .$i .'-'. $month .'-'. $year . '</th>' .'<td>' .(int)$signups[d][$i] . '</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>

</div>