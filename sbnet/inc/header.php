<section class="overlay" id="overlay">
	<a href="#" class="close" title="Close"></a>
	<div class="contentWrap"></div>
</section>

<header id="header">
	
	<section id="accountPane">
		<div class="innerWidth">
		
			<ul class="left">
				<li class="block"><a class="button highlight" href="https://www.sportsbetting.com/en/signup">Join now</a></li>
				<li class="block">
					<div id="loginBox">
						<form action="https://www.sportsbetting.com/en/auth/login" method="post" autocomplete="off">
							<input type="hidden" name="targetUrl" value="/" />
								<span class="labeledField">
									<label for="username">Username</label>
									<input type="text" size="15" name="username" id="username" onkeyup="removeLabels();" />
								</span>

								<span class="labeledField">
									<label for="password">Password</label>
									<input type="password" size="15" name="password" id="password" onkeyup="removeLabels();" />
								</span>
							<button type="submit">Login</button>
						</form>
						<script language="javascript">
						
						function removeLabels()
						{
							var un = $('#username').val();
							var ps = $('#password').val();
							$("label").attr("for","username").css("display","block");
							$("label").attr("for","password").css("display","block");
							if( un != '')$("label").attr("for","username").css("display","none");
							if( ps != '')$("label").attr("for","password").css("display","none");
						}

						$(document).ready(function() {
						
						       removeLabels();
						});
						
						</script>
 
					</div>
				</li>
			</ul>
		
			<ul class="right">
				<li>
					<div class="socialIcons">
						<div class="g-plusone" data-size="small" data-annotation="none" data-href="http://freecontests.sportsbetting.com/"></div>
						<a class="facebook" target="_blank" href="http://www.facebook.com/sportsbettingcom"></a>
						<a class="twitter" target="_blank" href="http://www.twitter.com/#!/spbetting"></a>
					</div>
				</li>
				<li class="block"><a class="button" href="mailto:helpteam@sportsbetting.com"><span class="icon"></span>Support</a></li>
				<li><a rel="popup" href="http://www.sportsbetting.com/en/help/general/about-us">Help</a></li>
				<li><a href="mailto:feedback@sportsbetting.com">Feedback?</a></li>
				<li>
					<?php

					if( ! ini_get('date.timezone') )
						{
							date_default_timezone_set('America/Los_Angeles');;
						}


						$now = time();
						$nowEST = $now - (5*60*60);
					?>
					<div id="serverTime">
						Server time: 
						<div class="clock"><?php echo date('g:ia', $nowEST); ?></div>
						Eastern Time
					</div>
				</li>

			</ul>
			
			<div class="clear"></div>
		
		</div>
		
	</section>
	
	<section id="logoBar">
		
		<div class="innerWidth">
	
			<a id="logo" href="http://www.sportsbetting.com/en/home"></a>

			<nav>
				<ul>
					<li ><a href="http://www.sportsbetting.com/en/sports">Sportsbook</a></li>
					<li ><a href="http://www.sportsbetting.com/en/live-betting">Live Betting</a></li>
					<li ><a href="http://www.sportsbetting.com/en/casino">Casino</a></li>
					<li ><a href="http://www.sportsbetting.com/en/live-dealer">Live Dealer</a></li>
					<li ><a href="http://www.sportsbetting.com/en/poker">Poker</a></li>
					<li ><a href="http://www.sportsbetting.com/en/promotions">Promotions</a></li>
					<li class="active"><a href="http://freecontests.sportsbetting.com">Contests</a></li>
					<li><a href="http://blog.sportsbetting.com/">Blog</a></li>
				</ul>
			</nav>
		
		</div>
		
	</section>
</header>