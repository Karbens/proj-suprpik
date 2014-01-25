<?php include_once('admin/db_func.php'); 
tep_db_connect(); 
$today = date('Y-m-d');
$today = date("Y-m-d H:i:s", strtotime($today));
$query = "SELECT * FROM contests WHERE status='Online' AND contest_publish_date <= '$today' AND current_contest_date >= '$today'";
    $results = mysql_query($query);
    if(@mysql_num_rows($results)<1){

	}
	else{
	$resultset = array();
while ($row = mysql_fetch_arraY($results)) {
  $resultset[] = $row;
}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Free Contests - Sportsbetting.com</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="i/sbtfav2.png" />
	<link rel="stylesheet" type="text/css" href="css/phoenix.css" />
	<link rel="stylesheet" type="text/css" href="css/fonts.css" />
	
	<script type="text/javascript" src="admin/js/jquery-latest.js"></script>
	<script type="text/javascript" src="http://www.sportsbetting.com/javascripts/core/head.js"></script>
	<script type="text/javascript">
		  (function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/plusone.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
	</script>
	<style type="text/css">
		.text { height:184px; margin:0 -2px; background:#fff; color:#000; padding:5px; text-align:left; }
		.text p { margin:8px 0; font-size:.9em; }
		.text p b { color:#bd580a; }
		.text a { color:#bd580a; text-decoration:none; }
		.text a:hover { text-decoration:underline; }
	</style>
</head>

<body class="en home">

<?php include('inc/header.php'); ?>

<section id="mainContainer">
	<div class="innerWidth">


		<section id="hpMain" class="left">
			<section id="featuredBets" class="tabs">
				<header>
					<h2>Free Contests</h2>
				</header>
				<nav>
					<ul>
						<?php if(!empty($resultset)){
						foreach($resultset as $res){ 					?>
							<li style="width:239px;"><a href="streaker/"><?php echo $res['contest_name']; ?></a></li>
						<?php } } ?>
					</ul>
				</nav>
				<?php if(!empty($resultset)){
						$cnt_contests = count($resultset);
						$counter = 1;
						$firstTime = true;
						foreach($resultset as $res){ 					?>	
							<?php  if ($firstTime) {
								 echo '<section class="category" id="nfl-game-lines" style="padding:0;">';
								 $firstTime = false;
							}
							if ($counter > 3) {
								echo '<div class="clear"></div></section>';
								echo '<section class="category" id="nfl-game-lines" style="padding:0;">';
								$counter = 0;
							} ?>
					<article style="height:367px; width:222px; margin:3px 0 3px 3px;">
						<a href="streaker/"><img src="i/no_contest_image.png" alt="The Streaker Contest"  style="margin:-3px 0 0 -3px;" /></a>
						
						<div class="text">
							<p><b><?php echo $res['contest_name']; ?></b></p>
							<?php echo $res['contest_desc']; ?> 
							<p><a href="streaker/">Check it out</a></p>
						</div>
					</article>
				<?php $counter++;
				} } ?>
					<div class="clear"></div>
				</section>
			</section>
		</section>

		<aside class="left" id="hpFeeds">

			<?php include('inc/twitter.php'); ?>
			<?php include('inc/facebook.php'); ?>
		</aside>

		<div class="clear"></div>

    </div>
</section>

<?php include('inc/footer.php'); ?>

</body>
</html>