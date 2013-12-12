<?php
/*
Plugin Name: Odds Settings
Plugin URI: 
Description: Odds Settings Plugin
Version: 1.0
Author: Joe Bassi
Author URI: 
License: GPLv2 or later
*/

//hook for admin menu
add_action('admin_menu', 'odds_settings_add');

function odds_settings_add()
{
	//add top level menu
	add_menu_page( __('Odds Settings'), __('Odds Settings'), 'manage_options', 'odds-settings', 'odds_settings_page'  ); 
}


function odds_settings_page()
{

if( preg_match("/local/",$_SERVER['SERVER_NAME']) )
{
	$db_link = @mysql_connect('localhost', 'root', 'password');//local host connection
}
else
{
     	$db_link = @mysql_connect('localhost', 'root', 'password');//main site connection
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
	//header('Location: index.php?updated=1');
	//exit();
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
    <link rel="stylesheet" href="http://superpicks.com/settings/style.css" type="text/css" media="screen" charset="utf-8" />
    <link rel="stylesheet" href="http://superpicks.com/settings/stylesheets/site.css" type="text/css" media="screen" charset="utf-8" />
    
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js" type="text/javascript"></script>
    <script src="http://superpicks.com/settings/jquery/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
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
  <?php if( isset($_POST['is_posted']) ) { ?>
    <header>
      <h1 id="h1_div">
        <a href="admin.php?page=odds-settings">Updated, click to Reload</a>
        <!--<span class="sub" onclick='javascript:$("#h1_div").hide("slow");' style="cursor:pointer;">Close</span>-->
      </h1>
	</header>
  <?php } ?>
	<div id="frame">
      
        <article>

          <h2>Odds Settings</h2>
		  <p>
		  Turn on leagues only during their season, this will save bandwith and speed up the odds.
		  </p>
		  
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
<?php
}