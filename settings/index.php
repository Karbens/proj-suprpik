<?php
if ($_SERVER['PHP_AUTH_USER'] != "settings" || $_SERVER['PHP_AUTH_PW'] != "Epmltd2013")
{
 	header('WWW-Authenticate: Basic realm="Protected Page: Enter Username and Password"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}

if( preg_match("/local/",$_SERVER['SERVER_NAME']) )
{
	$db_link = @mysql_connect('localhost', 'root', 'mysql');//local host connection
}
else
{
     	$db_link = @mysql_connect('localhost', 'super100_dbmain', 'FgDvr436oy');//main site connection
}
@mysql_select_db('super100_lines');
if (!$db_link) 
{
  die('Could not connect: ' . mysql_error());
}

if( $_POST['is_posted'] )
{
	extract($_POST);
	$active_leagues = implode(';',$leagues);
	$upd_que = "UPDATE `ol_feeds_settings` 
				SET `default_league` = '".$default_league."',
					`active_leagues` = '".$active_leagues."' 
				LIMIT 1";
	@mysql_query($upd_que);
	header('Location: index.php?updated=1');
	exit();
}

$leagues_que = @mysql_query("SELECT * FROM ol_leagues WHERE `status` = 'online'");
while( $leagues_res = mysql_fetch_assoc($leagues_que) )
{
	$id = $leagues_res['league_id'];
	$leagues_arr[$id] = $leagues_res['description'];
}

$set_que = mysql_query("SELECT * FROM `ol_feeds_settings`");
$set_res = mysql_fetch_assoc($set_que);
$def_league = $set_res['default_league'];
$active_arr = explode(";", $set_res['active_leagues']);
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv='X-UA-Compatible' content='IE=edge;chrome=1'>
    <meta name="author" content="Thomas Reynolds">
    
    <!-- Mobile viewport optimization http://goo.gl/b9SaQ -->
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Mobile IE allows us to activate ClearType technology for smoothing fonts for easy reading -->
    <meta http-equiv="cleartype" content="on">
    
    <title>Sports Settings</title>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" charset="utf-8" />
    <link rel="stylesheet" href="stylesheets/site.css" type="text/css" media="screen" charset="utf-8" />
    
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js" type="text/javascript"></script>
    <script src="jquery/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
    <style type="text/css">
    body 
	{
      padding: 10px; 
	}
    th 
	{
      text-align: left;
      padding: 4px;
      padding-right: 15px;
      vertical-align: top;
	  font-size: 16px;
	  font-weight: bold;
	}
    .css_sized_container .iPhoneCheckContainer 
	{
      width: 250px; 
	}
    </style>
	<script type="text/javascript">
    $(document).ready(function() 
	{
      $(':checkbox').iphoneStyle();
    });
	</script>
  </head>
  <body>
  <?php if( isset($_GET['updated']) ) { ?>
    <header>
      <h1 id="h1_div">
        <a href="index.php">Updated, click to Reload</a>
        <!--<span class="sub" onclick='javascript:$("#h1_div").hide("slow");' style="cursor:pointer;">Close</span>-->
      </h1>
	</header>
  <?php } ?>
	<div id="frame">
      
        <article>

          <h2>Sports Settings</h2>
		  
          <div class='table'>
		  <form name="f1" id="f1" method="post">
		  	<input type="hidden" name="is_posted" value="1">
            <table>
              <tr>
                <th style='vertical-align: middle !important;'>
                  &nbsp;&nbsp;League Name
                </th>
                <th>
				  &nbsp;&nbsp;Status
                </th>
				<th>&nbsp;&nbsp;Default</th>
              </tr>
			<?php
			foreach($leagues_arr as $id => $league)
			{
			?>
              <tr>
                <td style='vertical-align: middle !important;'>
                  <?php echo $league; ?>
                </td>
                <td>
                  <input type='checkbox' name='leagues[]' value='<?php echo $id; ?>' class='normal'<?php if( in_array($id,$active_arr) )echo " checked='checked'";?>/>
                </td>
				<td>&nbsp;&nbsp;&nbsp;
				  <input type="radio" name="default_league" value="<?php echo $id; ?>" class='normal'<?php if( $def_league == $id )echo " checked='checked'";?>/>
				</td>
              </tr>
			<?php
			}
			?>
			<tr>
			  <td>&nbsp;</td>
			  <td colspan="2"><input type="Submit" name="sbt_button" id="sbt_button" value="Save Changes"></td>
			</tr>
            </table>
		  </form>
          </div>

          

        </article>
    </div>
  </body>
</html>