<?php

if (isset($_REQUEST['contest_id']) && $_REQUEST['contest_id'] > 0) {
	$contest_id = $_REQUEST['contest_id'];
}

if(isset($contest_id) && $contest_id > 0)
{
	if ( 0 == $user->ID )	{
	  //user not logged in
	  echo '<p>SuperPicks Soccer Championship is a prediction contest of 17 GameWeeks (rounds) of matches from the English Premier League.<br/><br/>
The first GameWeek will take place January 11, 2014 running until May 3, 2014. The winner gets the exclusive SuperPicks Championship Belt plus the choice of a free iPad mini, or one bitcoin valued over $800 USD or EPL club jersey of his/her choice.<br/><br/>
<a href="'.site_url('/sign-up').'">Sign Up</a> for your chance to win and test your tipping/picking skills!<br/>
<h3 style="text-align:center"><a href="'.site_url('/sign-up').'">SIGN UP NOW</a></h3>';
	}else
	{
	  //user logged in, and conditions accepted
	  if(isset($_COOKIE['contest'][$contest_id][$user->ID]) && $_COOKIE['contest'][$contest_id][$user->ID]=='true') {
		  echo contestDisplaySpread($contest_id);

		  if ( has_post_thumbnail() ) {
		  		echo '<div style="text-align:center"><br/><br/><br/>';
				the_post_thumbnail();
				echo '</div>';
			}

			if(!isset($_GET['tab'])){
				echo '<p>Welcome to the SuperPicks Soccer Championship. Click Make Picks to view the GameWeeks and make your 5 selections. My SuperPicks is where you can view your picks for every GameWeek and the Leaderboard displays all the members\' results.<br/><br/>
			Each winning selection receives 1 point and a tie is 1/2 point. Click to view the <a href="soccer-rules">Rules and Regulations</a>.<br/><br/>
			The road to winning the belt will be hard and long over the next 4 months. Good Luck!</p>';
			}

	  }else{
	 	 $contests = getContests($contest_id);
	 	 if(sizeof($contests)){
		 	echo $contests[$contest_id]['contest_desc'];
		  	echo '<a id="acceptTC" href="?conditions=accepted"><button>Lets Play</button></a>';
	 	 }

	  	
	  }
	  
	}
	
}	
