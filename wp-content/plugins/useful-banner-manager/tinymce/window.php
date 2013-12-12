<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You are not allowed to call this page directly.' );
}

global $wpdb, $useful_banner_manager_plugin_url, $useful_banner_manager_table_name;

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

$banners = $wpdb->get_results( "SELECT id, banner_name, banner_type, banner_title FROM " . $useful_banner_manager_table_name . " WHERE is_visible='yes' ORDER BY id;" );
?>
<html>
<head>
	<title>Useful Banner Manager</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo( site_url() ); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo(  $useful_banner_manager_plugin_url ); ?>tinymce/tinymce.js"></script>
</head>
<body class="useful_banner_manager_tinymce_window" id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
    <?php
    if ( empty( $banners ) ) {
        ?>
        <p><?php _e( 'There is no visible banner.', 'useful_banner_manager' ); ?> <a href="admin.php?page=useful-banner-manager/useful-banner-manager-banners.php"><?php _e( 'Add Banners', 'useful_banner_manager' ); ?></a></p>
        <?php
    } else {
        ?>
        <form name="useful_banner_manager" action="#">
        	<div>
                <p><label style="cursor: pointer;"><?php _e( 'Rotate:', 'useful_banner_manager' ); ?> <input id="rotate" name="rotate" type="checkbox" value="true" onchange="if(this.checked){ document.getElementById('not_for_rotation').style.display='none'; document.getElementById('for_rotation').style.display='block'; }else{ document.getElementById('for_rotation').style.display='none'; document.getElementById('not_for_rotation').style.display='block'; }" style="vertical-align: middle;" /></label></p>
                <table width="100%" style="border-collapse: collapse">
                    <caption style="font-size: 12px;"><?php _e( 'Banners', 'useful_banner_manager' ); ?></caption>
                    <?php
                    foreach ( $banners as $banner ) {
                    ?>
                        <tr><td width="90%" style="padding: 2px 5px; font-size: 11px"><label for="ubm_banner_<?php echo( $banner->id ); ?>" style="cursor: pointer;"><?php echo( $banner->banner_title ); ?></label></td><td width="10%" style="border: 1px solid #f1f1f1; text-align: center; padding: 2px 0"><input class="checkbox" id="ubm_banner_<?php echo( $banner->id ); ?>" name="banners_ids[]" type="checkbox" value="<?php echo( $banner->id ); ?>" /></td></tr>
                    <?php
                    }
                    ?>
                </table><br />
                <div id="not_for_rotation">
                    <p><label style="cursor: pointer;"><?php _e( 'Number of banners to show:', 'useful_banner_manager' ); ?> <input id="count" name="count" type="text" value="1" size="5" /></label></p>
                </div>
                <div id="for_rotation" style="display: none;">
                    <p><label style="cursor: pointer;"><?php _e( 'Interval:', 'useful_banner_manager' ); ?> <input id="interval" name="interval" type="text" value="" size="5" /></label> <?php _e( 'seconds', 'useful_banner_manager' ); ?></p>
                    <p><label style="cursor: pointer;"><?php _e( 'Width of rotating banners:', 'useful_banner_manager' ); ?> <input id="width" name="width" type="text" value="" size="5" /></label><?php _e( 'px', 'useful_banner_manager' ); ?></p>
                    <p><label style="cursor: pointer;"><?php _e( 'Height of rotating banners:', 'useful_banner_manager' ); ?> <input id="height" name="height" type="text" value="" size="5" /></label><?php _e( 'px', 'useful_banner_manager' ); ?></p>
                    <p><label style="cursor: pointer;"><?php _e( 'Order by rand:', 'useful_banner_manager' ); ?> <input id="orderby" name="orderby" type="checkbox" value="rand"style="vertical-align: middle;" /></label></p>
                </div>
            </div>

        	<div class="mceActionPanel" style="margin-top: 15px; padding-bottom: 30px;">
        		<div style="float: left">
        			<input type="button" id="cancel" name="cancel" value="<?php _e( 'Cancel', 'useful_banner_manager' ); ?>" onclick="tinyMCEPopup.close();" />
        		</div>

        		<div style="float: right">
        			<input type="submit" id="insert" name="insert" value="<?php _e( 'Insert', 'useful_banner_manager' ); ?>" onclick="insert_useful_banner_manager_shortcode();" />
        		</div>
        	</div>
        </form>
        <?php
    }
    ?>
</body>
</html>