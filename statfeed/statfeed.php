<?php
	  // Settings for implementing a stored file cache
	  $cachedir = 'cache/'; // Directory to cache files in
	  $cachetime = 7200; // Seconds to cache files for
	  $cachepage = true;
		$querystring=$_SERVER['QUERY_STRING'];
		if ($querystring != ""){
			$querystring = "?".$querystring."&sn=101";
		}else{
			$querystring = "?sn=101";
	  }
	  
		if(!empty($_GET['page'])){
			$pp = seo_filter($_GET['page']);
		}else{
			$pp = '';
		}
		if(!empty($_GET['teamid'])){
			$tt = seo_filter($_GET['teamid']);
		}else{
			$tt='';
		}
		
		$title = strtoupper(str_replace("/"," ",$pp)) ." Team Statistics ". $tt;
		$desc = $title;
		
		//error_reporting(0);
		$PageDisplay=$pp;
		if(!$PageDisplay){
			$PageDisplay="nfl/daily";
		}
		
		/* OLD STATFOX FEED
		// Ex. http://statfeed.statfox.com/feed/statfeedv2/nhl/daily.php
		//$remote_url="http://statfeed.statfox.com/feed/statfeedv2/";
		//$remote_url.=$PageDisplay;
		//$remote_url.=".php";
		*/
		
		/* NEW STATFOX FEED */
		// EX. http://statfeed.statfox.com/statfeed/statfeed.php?page=nhl/daily
		$remote_url = "http://statfeed.statfox.com/statfeed/statfeed.php";
		$remote_url .= $querystring;
		
  // dont cache the scoreboard
  if ($PageDisplay == 'scoreboard'){
    $cachepage =false;
  }

  if ($cachepage){
      $cachefile = $cachedir . md5($remote_url) . '.html'; // Cache file to either load or create
      //this line clears any statisitics that PHP may have cached for files
      @clearstatcache();

      if(@file_exists($cachefile)){
        $cachefile_created = @filemtime($cachefile);
      }else{
        $cachefile_created=0;
      }
      // Show file from cache if still valid
      if (time() - $cachetime < $cachefile_created) {

        ob_start('ob_gzhandler');
        @readfile($cachefile);
		echo("<!-- page created from local cache on " .$_ENV["COMPUTERNAME"] . " file datetime=" . date("F d Y H:i:s.", $cachefile_created) ."-->");        
        ob_end_flush();
        exit();
      }
  }
    //the requested file is not yet cached or caching is not on
    //create the page normally by calling the statfeed service

	  $remote_content="";
	  $ErrorCount=0;
    while ($remote_content =="")
    {
    	$remote_content = getRemoteContent($remote_url);
    	++$ErrorCount;
    	if ($ErrorCount == 8)
    		break;
    }

    if($remote_content==""){
	    //include("header.php");
    	echo"<div id=\"statheader\"><h1>Can't Connect to StatFeed --- Please REFRESH</h1></div>";
  	    //include("footer.php");
    	exit();
    }else{
	     ob_start();
  	    //include("header.php");
			//Use H1 TAG for SEO
			//echo "<div id=\"statheader\"><h1>$title</h1><hr style=\"color:white\"/></div>";
    	echo "<!-- START OF STATFEED -->";
			
			if( eregi('Matchup Report was not found for this game', $remote_content) )
			{
				echo '<p>
					  Matchup Report was not found for this game.<br>
					  Expanded matchup reports are generated after lines are available for the game.<br>
					  Please check back later.</p>';
			}
			elseif( isset($_GET['page']) && isset($_GET['teamid']) && ( eregi("nbateam",$_GET['page']) || eregi("cbbteam",$_GET['page'])) ){
				echo "<!-- START OF DHTML TABS -->";
				echo add_bb_dhtml_tabs($remote_content);//add dhtml tabs for basketball team page
				echo "<!-- END OF DHTML TABS -->";
			}
			elseif( isset($_GET['page']) && isset($_GET['gameid']) && eregi("expanded",$_GET['page']) ){
				echo "<!-- START OF DHTML TABS -->";
				echo add_dhtml_tabs($remote_content);//add dhtml tabs for expanded game page
				echo "<!-- END OF DHTML TABS -->";
			}else{
				echo($remote_content);
		    }
		echo "<!-- END OF STATFEED -->";
  	    //include("footer.php");
	    // Now the script has run, generate a new cache file
	    //but only if it is larger than 30K
			if ( $cachepage && strlen(ob_get_contents()) > 30000) {
		        $fp = @fopen($cachefile, 'w');
		    	    fwrite($fp, ob_get_contents());
		        	fclose($fp);
			}
			echo("<!-- page created from scratch on " .$_SERVER["COMPUTERNAME"] . "-->");
		  ob_end_flush();
    }
    




	function getRemoteContent($url)
	{
		$numberOfSeconds=3;
		$url = str_replace("http://","",$url);
		$urlComponents = explode("/",$url);
		$domain = $urlComponents[0];
		$resourcePath = str_replace($domain,"",$url);
		
		$socketConnection = fsockopen($domain, 80, $errno, $errstr, $numberOfSeconds);
		
			
		if (!$socketConnection)
		{
			return "";
		}
		else 
		{
			$data = '';
			fputs($socketConnection, "GET /$resourcePath HTTP/1.0\r\nHost: $domain\r\n\r\n");
			while (!feof($socketConnection))
				{$data .= fgets($socketConnection );}
			fclose ($socketConnection);
		}
		
		$data=substr(strstr($data,"\r\n\r\n"),4);
		
	  return $data;
	
	} 

	// SEO 
	function seo_filter($s){
	    $antwort="";
		$ls = strlen($s);
			   for($i=0;$i<$ls;$i++){
				   if((ord(substr($s,$i,1))>=65 && ord(substr($s,$i,1))<=90) || 
					  (ord(substr($s,$i,1))>=97 && ord(substr($s,$i,1))<=122) || 
					  (ord(substr($s,$i,1))>=48 && ord(substr($s,$i,1))<=57) || 
					   substr($s,$i,1)==" " || substr($s,$i,1)=="-" || substr($s,$i,1)=="/" || substr($s,$i,1)=="+")
				   {$antwort.=substr($s,$i,1);} 
				} 
		return $antwort;
	}
	
	//puts the statfeed content (team page for basketball nba,cbb or nfl) into dhtml tabs
	function add_bb_dhtml_tabs($content)
	{
		if( eregi("cbb", $_GET['page']) )
		{
			return _add_cbb_tabs($content);
		}else
		{
			return _add_nba_tabs($content);
		}
	}//end of --> add_bb_dhtml_tabs($content)
	
	
	//default function for adding tabs to stats page
	function add_dhtml_tabs($content)
	{
		if( eregi("nfl", $_GET['page']) )
		{
			return _add_nfl_tabs($content);
		}else
		{
			return _add_dhtml_tabs($content);
		}
	}
	
	
	//private function returns statfeed content (team page for mlb) into dhtml tabs
	function _add_nba_tabs($content)
	{
		$init_tabs = "'Upcoming Games','Standings','Injuries','Game Log','View All'";
		
		$tabs = array();
		$tabs_content = '';
		$ta = '<table align="center" width="100%" style="border:1px solid #999999; background-color:#ffffff" cellspacing="0" cellpadding="0" class="mainTableOutline"><tr><td align="center" valign="top">';
	    $te = '</td></tr></table>';
		$a = explode('<table class="datatable" border="0" style="width:650px;" cellpadding="0" cellspacing="0">', $content);
		//return count($a);
		if(count($a) < 2)return $content;//if proper content not found, return original content
		$a1 = '<table class="datatable" border="0" width="100%" style="width:100%;" cellpadding="0" cellspacing="0">'.$a[1];
		//$tabs[] = $a1;
		
		$ba = '<table align="center" width="100%" style="border:1px solid #999999; background-color:#ffffff" cellspacing="0" cellpadding="0" class="mainTableOutline"><tr><td align="center" valign="top"><table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0"><tbody><tr><td>';
	    $be = '&nbsp;</td></tr></tbody></table></td></tr></table>';
		
		$b = explode('<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">', $a1);
		
		if(count($b) < 2)return $content;//if proper content not found, return original content
		
		for($i=1;$i<count($b);$i++)
		{
			$c = $ba.'<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">'.$b[$i].$be;
			$tabs[] = $c;
		}
		$tabs_content = '<div id="dhtmlgoodies_tabView1">';
		foreach($tabs as $tab){
			$tabs_content .= '<div class="dhtmlgoodies_aTab">'.$tab.'</div>';
		}
		$view_all_tab = implode('<br>', $tabs);
		$tabs_content .= '<div class="dhtmlgoodies_aTab">'.$view_all_tab.'</div>';//view all tab
		$tabs_content .= "
						</div>
						<script type=\"text/javascript\">
						initTabs('dhtmlgoodies_tabView1',Array(".$init_tabs."),0,620,'');
						</script>";
		return $tabs_content;
	}//end of --> function _add_mlb_tabs($content)
	
	
	//private function returns statfeed content (team page for basketball cbb) into dhtml tabs
	function _add_cbb_tabs($content)
	{
		$init_tabs = "'Standings','Upcoming Games','Injuries','Game Log','View All'";
		$content = str_replace('<th class="header1" align="center">', '<th class="header1" align="left">', $content);//align headings
		$tabs = array();
		$tabs_content = '';
		$ta = '<table align="center" width="100%" style="border:1px solid #999999; background-color:#ffffff" cellspacing="0" cellpadding="0" class="mainTableOutline"><tr><td align="center" valign="top">';
	    $te = '</td></tr></table>';
		$a = explode('<table class="datatable" border="0" cellpadding="0" cellspacing="0">', $content);
		if(count($a) < 2)return $content;//if proper content not found, return original content
		$a1 = '<table class="datatable" border="0" cellpadding="0" cellspacing="0">'.$a[1].'<table class="datatable" border="0" cellpadding="0" cellspacing="0">'.$a[2];
		//$tabs[] = $a1;
		
		$ba = '<table align="center" width="100%" style="border:1px solid #999999; background-color:#ffffff" cellspacing="0" cellpadding="0" class="mainTableOutline"><tr><td align="center" valign="top"><table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0"><tbody><tr><td>';
	    $be = '&nbsp;</td></tr></tbody></table></td></tr></table>';
		
		$b = explode('<table class="datatable" border="0" cellpadding="3" cellspacing="1">', $a1);
		
		if(count($b) < 2)return $content;//if proper content not found, return original content
		
		for($i=1;$i<count($b);$i++)
		{
			$c = $ba.'<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">'.$b[$i].$be;
			$tabs[] = $c;
		}
		$tabs_content = '<div id="dhtmlgoodies_tabView1">';
		foreach($tabs as $tab){
			$tabs_content .= '<div class="dhtmlgoodies_aTab">'.$tab.'</div>';
		}
		$view_all_tab = implode('<br>', $tabs);
		$tabs_content .= '<div class="dhtmlgoodies_aTab">'.$view_all_tab.'</div>';//view all tab
		$tabs_content .= "
						</div>
						<script type=\"text/javascript\">
						initTabs('dhtmlgoodies_tabView1',Array(".$init_tabs."),0,620,'');
						</script>";
		return $tabs_content;
	}//end of --> function _add_cbb_tabs($content)
	
	//private function returns statfeed content (team page for basketball cbb) into dhtml tabs
	function _add_nfl_tabs($content)
	{
		$init_tabs = "'Stats','Rating','Trends','Team Stats','Results','History','Team Line','Injuries','View All'";
		$tabs = array();
		$tabs_content = '';
		$ta = '<table align="center" width="100%" style="border:1px solid #999999; background-color:#ffffff" cellspacing="0" cellpadding="0" class="mainTableOutline"><tr><td align="center" valign="top">';
	    $te = '</td></tr></table>';
		$a = explode('<table class="datatable" border="0" width="100%" cellpadding="1" cellspacing="1">', $content);
		if(count($a) < 2)return $content;//if proper content not found, return original content
		$a[0] = str_replace('<table class="datatable" border="0" cellpadding="0" cellspacing="0"><tr><td bgcolor="#999999"><table class="datatable" style="width:650"','<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td bgcolor="#999999"><table class="datatable" style="width:650"',$a[0]);
		$a0 = explode('<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">', $a[0]);
		//echo 'counta: '.count($a0); exit();
		if(count($a0) < 3)return $content;//if proper content not found, return original content
		$a1 = $ta.'<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">'.$a0[1].$te;
		$tabs[] = $a1;
		
		$ba = '<table align="center" width="100%" style="border:1px solid #999999; background-color:#ffffff" cellspacing="0" cellpadding="0" class="mainTableOutline"><tr><td align="center" valign="top"><table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0"><tbody><tr><td>';
	    $be = '&nbsp;</td></tr></tbody></table></td></tr></table>';
		
		$b = explode('<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">', $a[1]);
		
		if(count($b) < 2)return $content;//if proper content not found, return original content
		
		for($i=1;$i<count($b);$i++)
		{
			$c = $ba.'<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">'.$b[$i].$be;
			$tabs[] = $c;
		}
		$tabs_content = '<div id="dhtmlgoodies_tabView1">';
		foreach($tabs as $tab){
			$tabs_content .= '<div class="dhtmlgoodies_aTab">'.$tab.'</div>';
		}
		$view_all_tab = implode('<br>', $tabs);
		$tabs_content .= '<div class="dhtmlgoodies_aTab">'.$view_all_tab.'</div>';//view all tab
		$tabs_content .= "
						</div>
						<script type=\"text/javascript\">
						initTabs('dhtmlgoodies_tabView1',Array(".$init_tabs."),0,620,'');
						</script>";
		return $tabs_content;
	}//end of --> function _add_cbb_tabs($content)
	
	
	//puts the statfeed content (expanded page) into dhtml tabs
	function _add_dhtml_tabs($content)
	{
		$init_tabs = "'Stats','Rating','Trends','Team Stats','Results','History','Team Line','Injuries','View All'";
		if( eregi("cbb", $_GET['page']) )
		{
			$init_tabs = "'Stats','Rating','Trends','Team Stats','History','Team Line','Injuries','View All'";
		}
		if( eregi("mlb", $_GET['page']) )
		{
			$init_tabs = "'Lines','Rating','Trends','Current','Stats','Performance','History','Versus','Action', 'Injuries', 'View All'";
		}
		$tabs = array();
		$tabs_content = '';
		$ta = '<table align="center" width="100%" style="border:1px solid #999999; background-color:#ffffff" cellspacing="0" cellpadding="0" class="mainTableOutline"><tr><td align="center" valign="top">';
	    $te = '</td></tr></table>';
		$a = explode('<table class="datatable" border="0" width="100%" cellpadding="1" cellspacing="1">', $content);
		if(count($a) < 2)return $content;//if proper content not found, return original content
		$a0 = explode('<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">', $a[0]);
		//echo 'counta: '.count($a0); exit();
		if(count($a0) < 3)return $content;//if proper content not found, return original content
		$a1 = $ta.'<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">'.$a0[1].$te;
		$tabs[] = $a1;
		
		$ba = '<table align="center" width="100%" style="border:1px solid #999999; background-color:#ffffff" cellspacing="0" cellpadding="0" class="mainTableOutline"><tr><td align="center" valign="top"><table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0"><tbody><tr><td>';
	    $be = '&nbsp;</td></tr></tbody></table></td></tr></table>';
		
		$b = explode('<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">', $a[1]);
		
		if(count($b) < 2)return $content;//if proper content not found, return original content
		
		for($i=1;$i<count($b);$i++)
		{
			$c = $ba.'<table class="datatable" border="0" width="100%" cellpadding="0" cellspacing="0">'.$b[$i].$be;
			$tabs[] = $c;
		}
		$tabs_content = '<div id="dhtmlgoodies_tabView1">';
		foreach($tabs as $tab){
			$tabs_content .= '<div class="dhtmlgoodies_aTab">'.$tab.'</div>';
		}
		$view_all_tab = implode('<br>', $tabs);
		$tabs_content .= '<div class="dhtmlgoodies_aTab">'.$view_all_tab.'</div>';//view all tab
		$tabs_content .= "
						</div>
						<script type=\"text/javascript\">
						initTabs('dhtmlgoodies_tabView1',Array(".$init_tabs."),0,620,'');
						</script>";
		return $tabs_content;
	}//end of --> function add_dhtml_tabs($content)

?>
