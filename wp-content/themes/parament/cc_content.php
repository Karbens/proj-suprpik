<?php

if (isset($_REQUEST['contest_id']) && $_REQUEST['contest_id'] > 0) {
	$contest_id = $_REQUEST['contest_id'];
}

if(isset($contest_id) && $contest_id > 0)
{
	if ( 0 == $user->ID )
	{
	  //user not logged in
	  $contests = getContests();
	  echo '<p>You must <a href="/sign-in">login</a> to participate in the contests.</p>';
	}else
	{
	  //user logged in
	  /*(if(!isset($_COOKIE['contest'][$contest_id]))
	  {
	  	setContestCookie($contest_id);
	  }*/
	  
	  echo contestDisplay($contest_id);
	}
}else
{
	$contests = getContests();

	if(count($contests) > 0)
	{
		echo '<script>
				function checkTerms(id)
				{
					if( document.getElementById("contCheck_"+id).checked == true )
					{
						return true;
					}else
					{
						alert(\' You must agree with the Terms & Conditions.\');
						return false;
					}
				}
			  </script>';
		if(count($contests) > 1)echo '<p>Please pick a contest.<br><br></p>';
		$cc = 1;
		$c_cookie = array();
		if(isset($_COOKIE['contest']))$c_cookie = $_COOKIE['contest'];
		foreach($contests as $contest)
		{
			$cval = '';
			$cid = $contest['contest_id'];
			
			//contest image
			$contest_image = '<p><a href="/component/contests/?contest_id='.$cid.'"><img src="/components/com_contests/images/Contest-Banners-'.$cid.'.jpg"></a></p>';
			
			if( isset($c_cookie[$cid]) )$cval = ' checked';
			echo '<p>';
			//if(count($contests) > 1)echo $cc.'. ';
			/*echo '<b>'
				 . $contest['contest_name']
				 . '</b><br></p>'*/
			  echo $contest_image
				 . $contest['contest_desc']
				 . "<p>"
				 . '<span style="vertical-align:bottom;">
					<input type="checkbox" id="contCheck_'.$contest['contest_id'].'" name="cont_'.$contest['contest_id'].'"'.$cval.'>
					</span>
					&nbsp;'
				  . "<a onclick=\"javascript:window.open('".$mosConfig_live_site."?option=com_contests&contest_id=".$contest['contest_id']."&terms_and_conditions=1','".$contest['contest_name']." Contest - Terms &amp; Conditions','width=600,height=500,scrollbars=1,toolbar=0,resizable=1');\" href=\"javascript:void(0);\">"
				  . 'Terms &amp; Conditions'
				 . '</a><br><br><br></p>';
			$cc++;
		}
		//echo '<p>Check back here in early October for details on our NHL contest. </p>';
	}else
	{
		echo '<p>Currently, there are no active contests.</p>';
	}
}

?>