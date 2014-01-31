<?php 
session_start();
include_once('sbnetadmin/db_func.php');
include_once('functions.php');
tep_db_connect(); 

$active_class1 = "active";
if(isset($_SESSION['user'])){
	$resultset = get_active_contests();
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
		.event-container { background:#e5e5e5; border:1px solid #b2b2b2; margin:0 0 10px; }
		.wrapper { color:#000; padding:10px 20px!important; font-size:13px; text-align:left!important; }
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
				<?php if(isset($_SESSION['user'])){
					$count = 1;
					if(!empty($resultset)){ ?>
						<section class="category" id="nfl-game-lines" style="padding:0;">
							<div style="float:left;margin:0px;padding:0px;width:740px;height:auto;background:#ffffff;">		
								<?php foreach($resultset as $res){  ?>
										<div style="float:left;margin:0px;padding:0px;width:246px;height:407px;">
											<nav>
												<ul style="width:246px;">
													<li style="width:246px;"><a href="participation.php?contest_id=<?php echo $res['contest_id']; ?>"><?php echo $res['contest_name']; ?></a></li>
													 
												</ul>
											</nav>						
											<article style="height:367px; width:231px; margin:3px 0 3px 3px;">
												<a href="participation.php?contest_id=<?php echo $res['contest_id']; ?>"><img src="i/no_contest_image.png" alt="<?php echo $res['template']; ?>"  style="margin:0px 0px 0px 0px;" /></a>
													<div class="text">
														<p><b><?php echo $res['contest_name']; ?></b></p>
														<?php echo $res['contest_desc']; ?>
														<p><a href="participation.php?contest_id=<?php echo $res['contest_id']; ?>">Check it out</a></p>
													</div>
											</article>
										</div>
								<?php } ?>
							</div>	
						</section>
					<?php	}
					else{ ?>
						<section id="nfl-game-lines" class="wrapper" style="">
							<div class="event-container">
								<div >
									<p style="color:black;">The Contest is now expired. Please check back for future contests. Good Luck!</p>
								</div>
							</div>
						</section>
					<?php }
				}
				else{ ?>
						<!--echo "<div style='margin: 10px 0; color: #000000; font-weight: bold; text-align: center;'>Login to play free contest</div>"; ?>-->
					<section id="nfl-game-lines" class="wrapper" style="">	
						<div style="height: 32px;" class="event-container">
							<div >
								<p style="color:black; font-weight: bold; margin: 10px 10px 0;">Please login to play free contest.</p>
							</div>
						</div>
					</section>
						
				<?php } ?>
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