<?php
$contest_id = $_GET['contest_id'];
$username = $_GET['username'];
define('_VALID_MOS', '1');//for accessing db_func.php
include_once('../contests/contests_func.php');
tep_db_connect();
$userdata = get_pick_pending($contest_id, $username);
tep_db_close();
?>
<!DOCTYPE html>
<html lang="en">
<head>			
  <title>Pending Status</title>
  <meta charset="UTF-8" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <link rel="shortcut icon" href="http://n1.sbtcdn.com/media/generic/sbtfav2.png" />
  <link type="text/css" rel="stylesheet" href="/css/fonts.css" />
  <style type="text/css">
	body { font-family:"Helvetica", Arial, sans-serif; font-size:12px; text-align:center; }
	table { margin:0 auto; text-align:left; font-size:1.2em; border:1px solid #000000;}
	input { margin:5px 5px 0; }
	h2 { margin:10px 0; }
  </style>
</head>
<body>
<?php
if($username != '' && count($userdata) > 0)
{
	$fullTime = strtotime($userdata['contest_date'] . ' ' . $userdata['contest_time']);
	$contDate = date('l, F d, Y', $fullTime);
	$contTime = date('g:h A', $fullTime) . ' EST';
?>
		<h2>Pending Pick for <?php echo $username; ?></h2>
		<table cellpadding="2" cellspacing="2" width="550">
			<tr>
				<td><strong>Event:</strong></td><td><?php echo $userdata['event_desc']; ?></td>
			</tr>
			<tr>
				<td><strong>Pick:</strong></td><td><?php echo $userdata['choice']; ?></td>
			</tr>
			<tr>
				<td><strong>Date:</strong></td><td><?php echo $contDate; ?></td>
			</tr>
			<tr>
				<td><strong>Time:</strong></td><td><?php echo $contTime; ?></td>
			</tr>
			<tr>
				<td><strong>Status:</strong></td><td>Pending</td>
			</tr>
		</table>
<?php
}
else
{
	echo '<h2>No data found.</h2>';
}
?>

</body>
</html>