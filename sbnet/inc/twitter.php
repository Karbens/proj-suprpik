<style style="text/css">
	#twitterFeed h3 { line-height:18px; margin:0 5px 0 0; }
	#twitterFeed p { line-height:16px; font-size:11px; color:#000; }
</style>
<?php
/*
$username = "spbetting";
$limit = 2;
$feed = 'http://twitter.com/statuses/user_timeline.rss?screen_name='.$username.'&count='.$limit;
$tweets = file_get_contents($feed);
$tweet = explode("<item>", $tweets);
$tcount = count($tweet) - 1;
 
for ($i = 1; $i <= $tcount; $i++) {
$endtweet = explode("</item>", $tweet[$i]);
$title = explode("<title>", $endtweet[0]);
$content = explode("</title>", $title[1]);
$content[0] = str_replace("–", "—", $content[0]);
$content[0] = str_replace("—", "—", $content[0]);

$content[0] = preg_replace("/(http:\/\/|(www\.))(([^\s<]{4,68})[^\s<]*)/", '<a onclick="javascript:pageTracker._trackPageview(\'/outgoing/\');" href="http://$2$3" target="_blank">$1$2$4</a>', $content[0]);
$content[0] = str_replace("$username: ", "", $content[0]);
$content[0] = preg_replace("/@(\w+)/", "<a onclick=\"javascript:pageTracker._trackPageview('/outgoing/www.twitter.com/\');\" href=\"http://www.twitter.com/%5C1%5C\" target=\"\"_blank\"\">@\\1</a>", $content[0]);
$content[0] = preg_replace("/#(\w+)/", "<a onclick=\"javascript:pageTracker._trackPageview('/outgoing/search.twitter.com/search?q=\');\" href=\"http://search.twitter.com/search?q=%5C1%5C\" target=\"\"_blank\"\">#\\1</a>", $content[0]);
$content[0] = str_replace("SPBETTING: ", "", $content[0]);
 
$mytweets[] = $content[0];
	}
	 
while (list(, $v) = each($mytweets)) {
	    $tweetout .= '<article><h3><a href="http://twitter.com/#!/spbetting/">spbetting</a></h3><p>'.$v.'</p></article>';
	}*/
?>

<section id="twitterFeed">
	<header>
		<h2>Follow Us On Twitter</h2>
	</header>
		<?php //echo $tweetout; ?>
</section>