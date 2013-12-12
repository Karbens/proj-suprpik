<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<jdoc:include type="head" />
<link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/template.css" type="text/css" />

<!-- Does not displays login/signup to logged in users -->
<?php
	$user = JFactory::getUser();
	if($user->id != 0) {
?>
<link rel="stylesheet" href="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/css/main-login.css" type="text/css" />
<?php } ?>
<script type="text/javascript" src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/javascript/jq.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/javascript/jquery.validate.js"></script>
<link rel="stylesheet" href="<?php echo $this->baseurl ;?>/modules/mod_otweets/css/tab-view.css" type="text/css" media="screen">
<script type="text/javascript" src="<?php echo $this->baseurl ;?>/modules/mod_otweets/js/ajax.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl ;?>/modules/mod_otweets/js/tab-view.js">
/************************************************************************************************************
(C) www.dhtmlgoodies.com, October 2005

This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	

Terms of use:
You are free to use this script as long as the copyright message is kept intact. However, you may not
redistribute, sell or repost it without our permission.

Updated:
	
	March, 14th, 2006 - Create new tabs dynamically

Thank you!

www.dhtmlgoodies.com
Alf Magne Kalleland

************************************************************************************************************/		
</script>

</head>

<body>
	<div id="header">
		<div id="logout">
			<a href="index.php?option=com_users&task=user.logout&<?php echo JUtility::getToken(); ?>=1&return=<?php echo base64_encode(JURI::base()); ?>">Logout</a>
		</div>
		<div id="main-nav">
			<jdoc:include type="modules" name="mainNav" style="raw" />			
		</div><!-- end main-nav -->
		<div id="sub-nav">
			<jdoc:include type="modules" name="subNav" style="raw" />
		</div><!-- end main-nav -->
		<a href="/"><img src="<?php echo $this->baseurl ;?>/templates/<?php echo $this->template ;?>/images/logo.jpg" alt="Bet Republic" /></a>
	</div>

	<div id="content">
		<div id="main">
			<jdoc:include type="modules" name="optTweet" style="raw" />
			<jdoc:include type="message" />
			<jdoc:include type="component" />			
		</div><!-- end main -->
		<div id="sidebar">
			<?php if($user->id != 0 && $_GET['option'] == 'com_liveblog') { ?>
				<jdoc:include type="modules" name="chatter" style="raw" />
			<?php } ?>

			<jdoc:include type="modules" name="search" style="raw" />
			
			<jdoc:include type="modules" name="sports-trivia" style="raw" />

			<jdoc:include type="modules" name="brSays" style="raw" />

			<jdoc:include type="modules" name="rightAd" style="raw" />
		</div>
	</div><!-- end content -->
	<div id="footer">
		<jdoc:include type="modules" name="footer" style="raw" />
	</div>
</body>
</html>

