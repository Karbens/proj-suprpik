<style type="text/css">
	#blogFeed h3 { line-height:18px; margin:0 5px 0 0; }
	#blogFeed span { line-height:16px; font-size:11px; color:#000; }
</style>
<?php
function fetch_fb_feed($url, $maxnumber) {
     /* The following line is absolutely necessary to read Facebook feeds. Facebook will not recognize PHP as a browser and therefore won't fetch anything. So we define a browser here */
     ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');

     $updates = simplexml_load_file($url);  //Load feed with simplexml

     $fb_array = array();  //Initialize empty array to store statuses
     foreach ( $updates->channel->item as $fb_update ) {
          if ($maxnumber == 0) {
               break;
          } else {
               $desc = $fb_update->description;

               //Add www.facebook.com to hyperlinks
               //$desc = str_replace('href="', 'href="http://www.facebook.com', $desc); 

                //Converts UTF-8 into ISO-8859-1 to solve special symbols issues
               $desc = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);

                //Get status update time
               $pubdate = strtotime($fb_update->pubDate);
               $propertime = gmdate('F jS Y, H:i', $pubdate);  //Customize this to your liking

               //Get link to update
               $linkback = $fb_update->link;

               //Store values in array
               $fb_item = array(
                      'desc' => $desc,
                      'date' => $propertime,
                      'link' => $linkback
               );
               array_push($fb_array, $fb_item);

               $maxnumber--;
          }          
     }
     //Return array
     return $fb_array;
}
?>

<?php
//Run the function with the url and a number as arguments
//$myfb_statuses = fetch_fb_feed('http://www.new.facebook.com/feeds/page.php?id=240978869302339&viewer=746015526&key=AWiOG9ejHFXtI5U0&format=rss20', 1);
  
//Print Facebook status updates\
?>
<section id="blogFeed">
	<header>
		<h2>Follow Us On Facebook</h2>
	</header>
<?php
  /* foreach ($myfb_statuses as $k => $v) {
      echo '<article style="padding:8px;">';
      echo '<span class="update">' .$v['desc']. '</span>';
      echo '<span class="date">' .$v['date']. '</span>';
      echo '</article>';
   } */
echo '</section>';
?>