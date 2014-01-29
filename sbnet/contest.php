<?php include_once('sbnetadmin/db_func.php'); 
tep_db_connect(); 
$today = date('Y-m-d');
$today = date("Y-m-d H:i:s", strtotime($today));
$query = "SELECT * FROM contests WHERE status='Online' AND contest_publish_date <= '$today' AND current_contest_date >= '$today'";
    $results = mysql_query($query);
    if(@mysql_num_rows($results)>0){
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
		.text { height:180px; margin:0px; background:#fff; color:#000; padding:5px; text-align:left; }
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

				<section class="category" id="nfl-game-lines" style="padding:0;">
				
			<div style="float:left;margin:0px;padding:0px;width:740px;height:auto;background:#ffffff;">		
					<?php $count = 1;
						if(!empty($resultset)){
							foreach($resultset as $res){  ?>
									<div style="float:left;margin:0px;padding:0px;width:246px;height:407px;">
										<nav>
											<ul style="width:246px;">
												<li style="width:246px;"><a href="participation.php?contest=<?php echo $res['contest_id']; ?>"><?php echo $res['contest_name']; ?></a></li>
												 
											</ul>
										</nav>						
										<article style="height:367px; width:231px; margin:3px 0 3px 3px;">
											<a href="participation.php?contest=<?php echo $res['contest_id']; ?>"><img src="i/no_contest_image.png" alt="The Streaker Contest"  style="margin:0px 0px 0px 0px;" /></a>
												<div class="text">
													<p><b><?php echo $res['contest_name']; ?></b></p>
													<?php echo $res['contest_desc']; ?>
													<p><a href="participation.php?contest=<?php echo $res['contest_id']; ?>">Check it out</a></p>
												</div>
										</article>
									</div>
							<?php }
						} ?>
							
			</div>	
		
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