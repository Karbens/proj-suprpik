<?php
/*
Plugin Name: Useful Banner Manager
Plugin URI: http://rubensargsyan.com/wordpress-plugin-useful-banner-manager/
Description: This banner manager plugin helps to manage the banners easily over the WordPress blog. It works with BuddyPress too. <a href="admin.php?page=useful-banner-manager/useful-banner-manager-banners.php">Banner Manager</a>
Version: 1.3
Author: Ruben Sargsyan
Author URI: http://rubensargsyan.com/
*/

/*  Copyright 2013 Ruben Sargsyan (email: info@rubensargsyan.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

$useful_banner_manager_plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) );
$useful_banner_manager_plugin_title = 'Useful Banner Manager';
$useful_banner_manager_plugin_prefix = 'useful_banner_manager_';
$useful_banner_manager_table_name = $wpdb->prefix . 'useful_banner_manager_banners';

add_action( 'plugins_loaded', 'useful_banner_manager_load' );

function useful_banner_manager_load() {
	global $wpdb;

    $useful_banner_manager_table_name = $wpdb->prefix . 'useful_banner_manager_banners';
    $useful_banner_manager_plugin_prefix = 'useful_banner_manager_';
    $useful_banner_manager_version = '1.3';

	$charset_collate = '';

	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET " . $wpdb->charset;
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE " . $wpdb->collate;
		}
	}

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $useful_banner_manager_table_name . "'") != $useful_banner_manager_table_name ) {
	    $create_useful_banner_manager_table = "CREATE TABLE " . $useful_banner_manager_table_name . "(" .
			"id INT(11) NOT NULL auto_increment," .
			"banner_name VARCHAR(255) NOT NULL," .
            "banner_type VARCHAR(4) NOT NULL," .
            "banner_title VARCHAR(255) NOT NULL," .
            "banner_alt TEXT NOT NULL," .
            "banner_link VARCHAR(255) NOT NULL," .
            "link_target VARCHAR(7) NOT NULL," .
            "link_rel VARCHAR(8) NOT NULL," .
            "banner_width INT(11) NOT NULL," .
            "banner_height INT(11) NOT NULL," .
            "added_date VARCHAR(10) NOT NULL," .
            "active_until VARCHAR(10) NOT NULL," .
            "banner_order INT(11) NOT NULL DEFAULT 0," .
            "is_visible VARCHAR(3) NOT NULL," .
            "banner_added_by VARCHAR(50) NOT NULL," .
            "banner_edited_by TEXT NOT NULL," .
            "last_edited_date VARCHAR(10) NOT NULL," .
            "PRIMARY KEY (id)) $charset_collate;";

        dbDelta( $create_useful_banner_manager_table );
    }

    $current_version = get_option( 'useful_banner_manager_version');

    if ( $current_version < '1.3' ) {
        $create_useful_banner_manager_not_exists_fields = "ALTER TABLE " . $useful_banner_manager_table_name . " ADD wrapper_id VARCHAR(255) NOT NULL AFTER banner_order, ADD wrapper_class VARCHAR(255) NOT NULL AFTER wrapper_id";

        $wpdb->query( $create_useful_banner_manager_not_exists_fields );
    }

    if ( $current_version == '1.0'){
        $create_useful_banner_manager_not_exists_fields = "ALTER TABLE " . $useful_banner_manager_table_name . " ADD banner_alt TEXT NOT NULL AFTER banner_title, ADD link_rel VARCHAR(8) NOT NULL AFTER link_target";

        $wpdb->query( $create_useful_banner_manager_not_exists_fields );

        update_option( 'useful_banner_manager_version', $useful_banner_manager_version );
    } elseif ( $current_version < $useful_banner_manager_version ) {
        update_option( 'useful_banner_manager_version', $useful_banner_manager_version );
    } elseif ( $current_version === false ) {
        add_option( 'useful_banner_manager_version', $useful_banner_manager_version );
    }

    if( ! file_exists( ABSPATH . 'wp-content/uploads ' ) ) {
        @mkdir( ABSPATH . 'wp-content/uploads' );
    }

    if ( ! file_exists( ABSPATH . 'wp-content/uploads/useful_banner_manager_banners' ) ) {
        @mkdir( ABSPATH . 'wp-content/uploads/useful_banner_manager_banners' );
    }
}

add_action( 'admin_menu', 'useful_banner_manager_menu' );

function useful_banner_manager_menu() {
    if ( function_exists ( 'add_menu_page' ) ) {
		add_menu_page( __( 'Banners', 'useful-banner-manager' ), __( 'Banner Manager', 'useful-banner-manager' ), 'manage_options', 'useful-banner-manager/useful-banner-manager-banners.php' );
	}
}

function useful_banner_manager_add_banner( $banner_data ) {
    global $wpdb, $useful_banner_manager_table_name;

    $data = array(
        'banner_name'       => $banner_data['banner_name'],
        'banner_type'       => $banner_data['banner_type'],
        'banner_title'      => $banner_data['banner_title'],
        'banner_alt'        => $banner_data['banner_alt'],
        'banner_link'       => $banner_data['banner_link'],
        'link_target'       => $banner_data['link_target'],
        'link_rel'          => $banner_data['link_rel'],
        'banner_width'      => $banner_data['banner_width'],
        'banner_height'     => $banner_data['banner_height'],
        'added_date'        => $banner_data['added_date'],
        'active_until'      => $banner_data['active_until'],
        'banner_order'      => $banner_data['banner_order'],
        'wrapper_id'        => $banner_data['wrapper_id'],
        'wrapper_class'     => $banner_data['wrapper_class'],
        'is_visible'        => $banner_data['is_visible'],
        'banner_added_by'   => $banner_data['banner_added_by']
    );

    $wpdb->insert( $useful_banner_manager_table_name, $data );

    $banner_id = $wpdb->insert_id;

    return $banner_id;
}

function useful_banner_manager_update_banner( $banner_id, $banner_data ) {
    global $wpdb, $useful_banner_manager_table_name;

    $data = array(
        'banner_name'       => $banner_data['banner_name'],
        'banner_type'       => $banner_data['banner_type'],
        'banner_title'      => $banner_data['banner_title'],
        'banner_alt'        => $banner_data['banner_alt'],
        'banner_link'       => $banner_data['banner_link'],
        'link_target'       => $banner_data['link_target'],
        'link_rel'          => $banner_data['link_rel'],
        'banner_width'      => $banner_data['banner_width'],
        'banner_height'     => $banner_data['banner_height'],
        'active_until'      => $banner_data['active_until'],
        'banner_order'      => $banner_data['banner_order'],
        'wrapper_id'        => $banner_data['wrapper_id'],
        'wrapper_class'     => $banner_data['wrapper_class'],
        'is_visible'        => $banner_data['is_visible'],
        'banner_edited_by'  => $banner_data['banner_edited_by'],
        'last_edited_date'  => $banner_data['last_edited_date']
    );

    $where = array(
        'id'  => $banner_id
    );

    $wpdb->update( $useful_banner_manager_table_name, $data, $where );
}

function useful_banner_manager_delete_banner( $banner_id ){
    global $wpdb, $useful_banner_manager_table_name;

    $banner = $wpdb->get_row( "SELECT banner_name,banner_type FROM " . $useful_banner_manager_table_name . " WHERE id=" . $banner_id . ";" );

    $wpdb->query( "DELETE FROM " . $useful_banner_manager_table_name . " WHERE id=" . $banner_id . ";" );

    if ( file_exists( ABSPATH . 'wp-content/uploads/useful_banner_manager_banners/' . $banner_id . '-' . $banner->banner_name . '.' . $banner->banner_type ) ) {
        unlink( ABSPATH . 'wp-content/uploads/useful_banner_manager_banners/' . $banner_id . '-' . $banner->banner_name . '.' . $banner->banner_type );
    }
}

function useful_banner_manager_get_banners(){
    global $wpdb, $useful_banner_manager_table_name;

    $banners = $wpdb->get_results( "SELECT * FROM " . $useful_banner_manager_table_name . " ORDER BY id;" );

    return $banners;
}

function useful_banner_manager_get_banner( $banner_id ) {
    global $wpdb, $useful_banner_manager_table_name;

    $banner = $wpdb->get_row( "SELECT * FROM " . $useful_banner_manager_table_name . " WHERE id='" . $banner_id . "';" );

    return $banner;
}

function useful_banner_manager_get_available_years(){
    global $wpdb, $useful_banner_manager_table_name;

    $available_years = array();

    $earliest_date = $wpdb->get_var( "SELECT MIN(added_date) as earliest_date FROM " . $useful_banner_manager_table_name . ";" );

    if ( ! is_null( $earliest_date ) ){
        $earliest_date = substr( $earliest_date, 0, 4 );

        for ( $i = date( 'Y' ); $i >= $earliest_date; $i-- ) {
            $available_years[] = $i;
        }
    }


    return $available_years;
}

function useful_banner_manager_display_banner( $banner ) {
    global $useful_banner_manager_plugin_url;
    ?>
    <div<?php if ( ! empty( $banner->wrapper_id ) ) { echo( ' id="' . $banner->wrapper_id . '"' ); } ?> class="useful_banner_manager_banner<?php if ( ! empty( $banner->wrapper_class ) ) { echo( ' ' . $banner->wrapper_class ); } ?>">
    <?php
    if ( $banner->banner_type == 'swf' ) {
        ?>
        <object width="<?php echo( $banner->banner_width ); ?>" height="<?php echo( $banner->banner_height ); ?>">
            <param name="movie" value="<?php echo( get_bloginfo( 'wpurl' ) . '/wp-content/uploads/useful_banner_manager_banners/' . $banner->id . '-' . $banner->banner_name . '.' . $banner->banner_type ); ?>" />
            <param name="wmode" value="transparent">
            <embed src="<?php echo( get_bloginfo( 'wpurl' ) . '/wp-content/uploads/useful_banner_manager_banners/' . $banner->id . '-' . $banner->banner_name . '.' . $banner->banner_type ); ?>" width="<?php echo( $banner->banner_width ); ?>" height="<?php echo( $banner->banner_height ); ?>" wmode="transparent"></embed>
        </object>
        <?php
    } else {
        if ( $banner->banner_link != '' ) {
        ?>
            <a href="<?php echo( $banner->banner_link ); ?>" target="<?php echo( $banner->link_target ); ?>" rel="<?php echo( $banner->link_rel ); ?>">
        <?php
        }
    ?>
    	<img src="<?php bloginfo( 'wpurl' ); ?>/wp-content/uploads/useful_banner_manager_banners/<?php echo( $banner->id . '-' . $banner->banner_name ); ?>.<?php echo( $banner->banner_type ); ?>" width="<?php echo( $banner->banner_width ); ?>" height="<?php echo( $banner->banner_height ); ?>" alt="<?php echo( $banner->banner_alt ); ?>" />
        <?php
        if ( $banner->banner_link != '' ) {
        ?>
            </a>
        <?php
        }
    }
    ?>
    </div>
    <?php
}

class Useful_Banner_Manager_Widget extends WP_Widget {
     function Useful_Banner_Manager_Widget() {
        $widget_opions = array(
            'classname'     => 'useful_banner_manager_widget',
            'description'   => __( 'UBM banners' )
        );

		$this->WP_Widget( 'useful-banner-manager-banners', 'UBM banners', $widget_opions );
     }

     function widget( $args, $instance ) {
        global $wpdb, $useful_banner_manager_table_name;

        extract( $args );

        $title = $instance['title'];
        $banners_ids = $instance['banners_ids'];
        $count = $instance['count'];

        echo( $before_widget );

        if ( ! empty( $title ) ) {
            echo( $before_title . $title . $after_title );
        }

        if ( ! empty( $banners_ids ) ) {
            $banners = $wpdb->get_results( "SELECT * FROM (SELECT * FROM " . $useful_banner_manager_table_name . " WHERE id IN (" .implode( ',', $banners_ids ) . ") AND (active_until=-1 OR active_until>='" . date( 'Y-m-d' ) . "') AND is_visible='yes' ORDER BY RAND() LIMIT " . $count . ") as banners ORDER BY banner_order DESC;" );

            foreach ( $banners as $banner ) {
                useful_banner_manager_display_banner( $banner );
            }
        }

        echo( $after_widget );
     }

     function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
		$instance['title'] = esc_attr( $new_instance['title'] );
		$instance['banners_ids'] = isset( $new_instance['banners_ids'] ) ? $new_instance['banners_ids'] : '';

        if ( is_numeric( $new_instance['count'] ) && $new_instance['count'] > 0 ) {
            $instance['count'] = $new_instance['count'];
        } elseif( is_numeric( $old_instance['count'] ) && $old_instance['count'] > 0 ) {
            $instance['count'] = $old_instance['count'];
        }else{
            $instance['count'] = 1;
        }

		return $instance;
     }

     function form( $instance ) {
        global $wpdb, $useful_banner_manager_table_name;

        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'banners_ids' => '', 'count' => 1 ) );
		$title = esc_attr( $instance['title'] );
		$banners_ids = $instance['banners_ids'];

        if ( $instance['count'] ) {
            $count = intval( $instance['count'] );
        }else{
            $count = 1;
        }

        $banners = $wpdb->get_results( "SELECT id, banner_name, banner_type, banner_title FROM " . $useful_banner_manager_table_name . " WHERE is_visible='yes' ORDER BY id;" );

        if ( empty( $banners ) ) {
            ?>
            <p><?php _e( 'There is no visible banner.', 'useful_banner_manager' ); ?> <a href="admin.php?page=useful-banner-manager/useful-banner-manager-banners.php"><?php _e( 'Add Banners', 'useful_banner_manager' ); ?></a></p>
            <?php
        } else {
            ?>
            <p><label><?php _e( 'Title:', 'useful_banner_manager' ); ?> <input class="widefat" name="<?php echo( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo( esc_attr( $title ) ); ?>" /></label></p>
            <table width="100%" style="border-collapse: collapse">
                <caption><?php _e( 'Banners', 'useful_banner_manager' ); ?></caption>
                <?php
                foreach ( $banners as $banner ) {
                ?>
                    <tr><td width="90%" style="border: 1px solid #f1f1f1; text-align: left; padding: 2px 5px"><label for="<?php echo( $this->get_field_id( 'banners_ids' ) ); ?>_<?php echo( $banner->id ); ?>"><?php echo( $banner->banner_title ); ?></label></td><td width="10%" style="border: 1px solid #f1f1f1; text-align: center; padding: 2px 0"><input class="checkbox" id="<?php echo( $this->get_field_id( 'banners_ids' ) ); ?>_<?php echo( $banner->id ); ?>" name="<?php echo( $this->get_field_name( 'banners_ids' ) ); ?>[]" type="checkbox" value="<?php echo( $banner->id ); ?>" <?php if ( is_array( $banners_ids ) ){ if( in_array( $banner->id, $banners_ids ) ) { echo( 'checked="checked"' ); } } ?> /></td></tr>
                <?php
                }
                ?>
            </table><br />
            <p><label><?php _e( 'Number of banners to show:', 'useful_banner_manager' ); ?> <input name="<?php echo( $this->get_field_name( 'count' ) ); ?>" type="text" value="<?php echo( esc_attr( $count ) ); ?>" size="2" /></label></p>
        <?php
        }
    }
}

class Useful_Banner_Manager_Rotation_Widget extends WP_Widget {
     function Useful_Banner_Manager_Rotation_Widget() {
        $widget_opions = array(
            'classname'     => 'useful_banner_manager_rotation_widget',
            'description'   => __('UBM banners rotation')
        );

		$this->WP_Widget( 'useful-banner-manager-banners-rotation', 'UBM banners rotation', $widget_opions );
     }

     function widget( $args, $instance ) {
        global $wpdb, $useful_banner_manager_table_name, $useful_banner_manager_plugin_url;

        extract( $args );

        $title = $instance['title'];
        $banners_ids = $instance['banners_ids'];
        $interval = $instance['interval'];
        $width = $instance['width'];
        $height = $instance['height'];

        if ( $instance['orderby'] == 'rand' ) {
            $orderby = 'RAND()';
        } else {
            $orderby = 'banner_order, id DESC';
        }

        echo( $before_widget );

        if ( ! empty( $title ) ) {
            echo( $before_title . $title . $after_title );
        }

        if ( ! empty( $banners_ids ) ) {
            $banners = $wpdb->get_results( "SELECT * FROM " . $useful_banner_manager_table_name . " WHERE id IN (" . implode( ',', $banners_ids ) . ") AND (active_until=-1 OR active_until>='" . date( 'Y-m-d' ) . "') AND is_visible='yes' ORDER BY " . $orderby . ";" );
            ?>
            <div id="<?php echo( $args['widget_id'] ); ?>" class="useful_banner_manager_banners_rotation" style="overflow: hidden; width: <?php echo( $width ); ?>px; height: <?php echo( $height ); ?>px;">
            <?php
            $first_banner = true;

            foreach ( $banners as $banner ) {
                ?>
                <div id="<?php echo( $banner->id ); ?>_useful_banner_manager_banner" class="useful_banner_manager_rotating_banner"<?php if ( $first_banner ) { $first_banner = false; } else { echo( ' style="display: none"' ); } ?>>
                    <?php
                    if ( $banner->banner_link != '' ) {
                    ?>
                        <a href="<?php echo( $banner->banner_link ); ?>" target="<?php echo( $banner->link_target ); ?>" rel="<?php echo( $banner->link_rel ); ?>">
                    <?php
                    }
                    ?>
                	<img src="<?php bloginfo( 'wpurl' ); ?>/wp-content/uploads/useful_banner_manager_banners/<?php echo( $banner->id . '-' . $banner->banner_name ); ?>.<?php echo( $banner->banner_type ); ?>" width="<?php echo( $width ); ?>" height="<?php echo( $height ); ?>" alt="<?php echo( $banner->banner_alt ); ?>" />
                    <?php
                    if ( $banner->banner_link != '' ) {
                    ?>
                        </a>
                    <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>
            </div>
            <script type="text/javascript">
            jQuery(function($){
                $(document).ready(function(){
                    var useful_banner_manager_banners_rotation_block = "<?php echo( $args['widget_id'] ); ?>";
                    var interval_between_rotations = <?php echo( ( $interval*1000 ) ); ?>;

                    if($("#"+useful_banner_manager_banners_rotation_block+" .useful_banner_manager_rotating_banner").length>1){
                        setTimeout("useful_banner_manager_rotate_banners('"+useful_banner_manager_banners_rotation_block+"',"+interval_between_rotations+")",interval_between_rotations);
                    }
                });
            });
            </script>
            <?php
        }

        echo($after_widget);
     }

     function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
		$instance['title'] = esc_attr( $new_instance['title'] );
		$instance['banners_ids'] = isset( $new_instance['banners_ids'] ) ? $new_instance['banners_ids'] : '';

        if ( is_numeric( $new_instance['interval'] ) && $new_instance['interval'] > 0 ) {
            $instance['interval'] = $new_instance['interval'];
        } elseif ( is_numeric( $old_instance['interval'] ) && $old_instance['interval'] > 0 ) {
            $instance['interval'] = $old_instance['interval'];
        }else{
            $instance['interval'] = 10;
        }

        if ( is_numeric( $new_instance['width'] ) && $new_instance['width'] > 0 ) {
            $instance['width'] = $new_instance['width'];
        } elseif ( is_numeric( $old_instance['width'] ) && $old_instance['width'] > 0 ) {
            $instance['width'] = $old_instance['width'];
        }else{
            $instance['width'] = 180;
        }

        if ( is_numeric( $new_instance['height'] ) && $new_instance['height'] > 0 ) {
            $instance['height'] = $new_instance['height'];
        } elseif ( is_numeric( $old_instance['height'] ) && $old_instance['height'] > 0 ) {
            $instance['height'] = $old_instance['height'];
        }else{
            $instance['height'] = 180;
        }

        if ( isset( $new_instance['orderby'] ) && $new_instance['orderby'] == 'rand' ) {
            $instance['orderby'] = 'rand';
        }else{
            $instance['orderby'] = 'banner_order, id';
        }

		return $instance;
     }

     function form( $instance ) {
        global $wpdb, $useful_banner_manager_table_name;

        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'banners_ids' => '', 'orderby' => 'banner_order, id' ) );
		$title = esc_attr( $instance['title'] );
		$banners_ids = $instance['banners_ids'];

        if ( empty( $instance['interval'] ) ) {
            $interval =  10;
        }else{
            $interval =  intval( $instance['interval'] );
        }

        if ( empty( $instance['width'] ) ) {
            $width =  180;
        }else{
            $width =  intval( $instance['width'] );
        }

        if ( empty( $instance['height'] ) ) {
            $height =  180;
        }else{
            $height =  intval( $instance['height'] );
        }

        $banners = $wpdb->get_results( "SELECT id, banner_title FROM " . $useful_banner_manager_table_name . " WHERE is_visible='yes' AND banner_type!='swf' ORDER BY id;" );

        if ( empty( $banners ) ) {
        ?>
            <p><?php _e( 'There is no visible banner.', 'useful_banner_manager' ); ?> <a href="admin.php?page=useful-banner-manager/useful-banner-manager-banners.php"><?php _e( 'Add Banners', 'useful_banner_manager' ); ?></a></p>
        <?php
        } else {
            ?>
            <p><label><?php _e( 'Title:', 'useful_banner_manager' ); ?>	<input class="widefat" id="<?php echo( $this->get_field_id( 'title' ) ); ?>" name="<?php echo( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo( esc_attr( $title ) ); ?>" /></label></p>
            <table width="100%" style="border-collapse: collapse">
                <caption><?php _e( 'Banners', 'useful_banner_manager' ); ?></caption>
                <?php
                foreach ( $banners as $banner ) {
                    ?>
                    <tr><td width="90%" style="border: 1px solid #f1f1f1; text-align: left; padding: 2px 5px"><label for="<?php echo( $this->get_field_id( 'banners_ids' ) ); ?>_<?php echo( $banner->id ); ?>"><?php echo( $banner->banner_title ); ?></label></td><td width="10%" style="border: 1px solid #f1f1f1; text-align: center; padding: 2px 0"><input class="checkbox" id="<?php echo( $this->get_field_id( 'banners_ids' ) ); ?>_<?php echo( $banner->id ); ?>" name="<?php echo( $this->get_field_name( 'banners_ids' ) ); ?>[]" type="checkbox" value="<?php echo( $banner->id ); ?>" <?php if ( is_array( $banners_ids ) ) { if ( in_array( $banner->id, $banners_ids ) ) { echo( 'checked="checked"' ); } } ?> /></td></tr>
                <?php
                }
                ?>
            </table>
            <br />
            <p><label><?php _e( 'Interval:', 'useful_banner_manager' ); ?> <input name="<?php echo( $this->get_field_name( 'interval' ) ); ?>" type="text" value="<?php echo( esc_attr( $interval ) ); ?>" size="2" /></label> <?php _e( 'seconds', 'useful_banner_manager' ); ?></p>
            <p><label><?php _e( 'Width of rotating banners:', 'useful_banner_manager' ); ?> <input name="<?php echo( $this->get_field_name( 'width' ) ); ?>" type="text" value="<?php echo( esc_attr( $width ) ); ?>" size="2" /></label><?php _e( 'px', 'useful_banner_manager' ); ?></p>
            <p><label><?php _e( 'Height of rotating banners:', 'useful_banner_manager' ); ?> <input name="<?php echo( $this->get_field_name( 'height' ) ); ?>" type="text" value="<?php echo( esc_attr( $height ) ); ?>" size="2" /></label><?php _e( 'px', 'useful_banner_manager' ); ?></p>
            <p><label><?php _e( 'Order by rand:', 'useful_banner_manager' ); ?> <input class="checkbox" name="<?php echo( $this->get_field_name( 'orderby' ) ); ?>" type="checkbox" value="rand" <?php if ( $instance['orderby'] == 'rand' ) { echo( 'checked="checked"' ); } ?> /></label></p>
            <?php
        }
     }
}

add_action( 'widgets_init', 'useful_banner_manager_widget_init' );

function useful_banner_manager_widget_init() {
	if ( ! is_blog_installed() ) {
	    return;
	}

    register_widget( 'Useful_Banner_Manager_Widget' );
    register_widget( 'Useful_Banner_Manager_Rotation_Widget' );
}

$banners_rotation_id = 1;

add_shortcode( 'useful_banner_manager', 'add_useful_banner_manager_banners' );

function add_useful_banner_manager_banners( $atts ) {
    global $wpdb, $useful_banner_manager_table_name, $useful_banner_manager_plugin_url;

    if ( empty( $atts['banners'] ) ) {
        $banners_ids_where = '';
    } else {
        $banners_ids = explode( ',', $atts['banners'] );

        $banners_ids_where = "id IN (" . implode( ',', $banners_ids ) . ") AND ";
    }

    if ( empty( $atts['count'] ) ) {
        $count = 1;
    } else {
        $count = $atts['count'];
    }

    $banners = $wpdb->get_results( "SELECT * FROM (SELECT * FROM ".$useful_banner_manager_table_name." WHERE " . $banners_ids_where . "(active_until=-1 OR active_until>='" . date( 'Y-m-d' ) . "') AND is_visible='yes' ORDER BY RAND() LIMIT " . $count . ") as banners ORDER BY banner_order DESC;" );

    $banners_html = '';

    if( ! empty( $banners ) ){
        foreach ( $banners as $banner ) {
            $banners_html .= '<div' . ( ( empty( $banner->wrapper_id ) ) ? '' : ' id="' . $banner->wrapper_id . '"' ) . ' class="useful_banner_manager_banner' . ( ( empty( $banner->wrapper_class ) ) ? '' : ' ' . $banner->wrapper_class ) . '">';

            if ( $banner->banner_type == 'swf' ) {
                $banners_html .= '<object width="' . $banner->banner_width . '" height="' . $banner->banner_height . '">
                    <param name="movie" value="' . get_bloginfo( 'wpurl' ) . '/wp-content/uploads/useful_banner_manager_banners/' . $banner->id . '-' . $banner->banner_name . '.' . $banner->banner_type . '" />
                    <param name="wmode" value="transparent">
                    <embed src="' . get_bloginfo( 'wpurl' ) . '/wp-content/uploads/useful_banner_manager_banners/' . $banner->id . '-' . $banner->banner_name . '.' . $banner->banner_type . '" width="' . $banner->banner_width . '" height="' . $banner->banner_height . '" wmode="transparent"></embed>
                </object>';
            } else {
                if ( $banner->banner_link != '' ) {
                    $banners_html .= '<a href="' . $banner->banner_link . '" target="' . $banner->link_target . '" rel="' . $banner->link_rel . '">';
                }

                $banners_html .= '<img src="' . get_bloginfo( 'wpurl' ) . '/wp-content/uploads/useful_banner_manager_banners/' . $banner->id . '-' . $banner->banner_name . '.' . $banner->banner_type . '" width="' . $banner->banner_width . '" height="' . $banner->banner_height . '" alt="' . $banner->banner_alt . '" />';

                if ( $banner->banner_link != '' ) {
                    $banners_html .= '</a>';
                }
            }

            $banners_html .= '</div>';
        }
    }

    return $banners_html;
}

add_shortcode( 'useful_banner_manager_banner_rotation', 'add_useful_banner_manager_banners_rotation' );

function add_useful_banner_manager_banners_rotation( $atts ) {
    global $wpdb, $useful_banner_manager_table_name, $useful_banner_manager_plugin_url, $banners_rotation_id;

    if ( empty( $atts['banners'] ) ) {
        $banners_ids_where = '';
    } else {
        $banners_ids = explode( ',', $atts['banners'] );

        $banners_ids_where = "id IN (" . implode( ',', $banners_ids ) . ") AND ";
    }

    if ( empty( $atts['interval'] ) ) {
        $interval = 10;
    } else {
        $interval = $atts['interval'];
    }

    if ( empty( $atts['width'] ) ) {
        $width = 180;
    } else {
        $width = $atts['width'];
    }

    if ( empty( $atts['height'] ) ) {
        $height = 180;
    } else {
        $height = $atts['height'];
    }

    if ( $atts['orderby'] == 'rand' ) {
        $orderby = 'RAND()';
    }else{
        $orderby = 'banner_order, id DESC';
    }

    $banners = $wpdb->get_results( "SELECT * FROM (SELECT * FROM ".$useful_banner_manager_table_name." WHERE " . $banners_ids_where . "(active_until=-1 OR active_until>='" . date( 'Y-m-d' ) . "') AND banner_type!='swf' AND is_visible='yes' ORDER BY " . $orderby . ") as banners ORDER BY banner_order DESC;" );

    $banners_rotation_html = '';

    if( ! empty( $banners ) ){
        $banners_rotation_html = '<div id="useful-banner-manager-banners-rotation-n' . $banners_rotation_id . '" class="useful_banner_manager_banners_rotation" style="overflow: hidden; width: ' . $width . 'px; height: ' . $height . 'px;">';

        $first_banner = true;

        foreach ( $banners as $banner ) {
            $banners_rotation_html .= '<div id="' . $banner->id . '_useful_banner_manager_banner" class="useful_banner_manager_rotating_banner"';

            if ( $first_banner ) {
                $first_banner = false;
            } else {
                $banners_rotation_html .= ' style="display: none"';
            }

            $banners_rotation_html .= '>';

            if ( $banner->banner_link != '' ) {
                $banners_rotation_html .= '<a href="' . $banner->banner_link . '" target="' . $banner->link_target . '" rel="' . $banner->link_rel . '">';
            }

            $banners_rotation_html .= '<img src="' . get_bloginfo( 'wpurl' ) . '/wp-content/uploads/useful_banner_manager_banners/' . $banner->id . '-' . $banner->banner_name . '.' . $banner->banner_type . '" width="' . $width . '" height="' . $height . '" alt="' . $banner->banner_alt . '" />';

            if ( $banner->banner_link != '' ) {
                $banners_rotation_html .= '</a>';
            }

            $banners_rotation_html .= '</div>';
        }

        $banners_rotation_html .= '</div>';

        $banners_rotation_html .= '<script type="text/javascript">
        jQuery(function($){
            $(document).ready(function(){
                var useful_banner_manager_banners_rotation_block = "useful-banner-manager-banners-rotation-n' . $banners_rotation_id . '";
                var interval_between_rotations = ' . ( $interval * 1000 ) . ';
                if($("#"+useful_banner_manager_banners_rotation_block+" .useful_banner_manager_rotating_banner").length>1){
                    setTimeout("useful_banner_manager_rotate_banners(\'"+useful_banner_manager_banners_rotation_block+"\',"+interval_between_rotations+")",interval_between_rotations);
                }
            });
        });
        </script>';

        $banners_rotation_id++;
    }

    return $banners_rotation_html;
}

function useful_banner_manager_banners( $banners = '', $count = '' ) {
    echo( do_shortcode( '[useful_banner_manager' . ( ( empty( $banners ) ) ? '' : ' banners=' . $banners ) . ( ( empty( $count ) ) ? '' : ' count=' . $count ) . ']' ) );
}

function useful_banner_manager_banners_rotation( $banners = '', $interval = '', $width = '', $height = '', $orderby = '' ){
    echo( do_shortcode( '[useful_banner_manager_banner_rotation' . ( ( empty( $banners ) ) ? '' : ' banners=' . $banners ) . ( ( empty( $interval ) ) ? '' : ' interval=' . $interval ) . ( ( empty( $width ) ) ? '' : ' width=' . $width ) . ( ( empty( $height ) ) ? '' : ' height=' . $height ) . ( ( empty( $orderby ) ) ? '' : ' orderby=' . $orderby ) . ']' ) );
}

function register_useful_banner_manager_button( $buttons ) {
   array_push( $buttons, 'usefulbannermanager' );

   return $buttons;
}

function add_useful_banner_manager_plugin( $plugin_array ) {
    global $useful_banner_manager_plugin_url;

   $plugin_array['usefulbannermanager'] = $useful_banner_manager_plugin_url . 'tinymce/useful-banner-manager.js';

   return $plugin_array;
}

add_action( 'init', 'useful_banner_manager_button' );

function useful_banner_manager_button() {
   if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
      return;
   }

   if ( get_user_option( 'rich_editing' ) == 'true' ) {
      add_filter( 'mce_external_plugins', 'add_useful_banner_manager_plugin' );
      add_filter( 'mce_buttons', 'register_useful_banner_manager_button' );
   }
}

add_action( 'wp_ajax_useful_banner_manager', 'useful_banner_manager_ajax_tinymce' );

function useful_banner_manager_ajax_tinymce() {
    if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'edit_posts' ) ){
        return;
    }

   	include_once( dirname(__FILE__) . '/tinymce/window.php' );

    die();
}

add_action( 'wp_enqueue_scripts', 'useful_banner_manager_load_scripts' );

function useful_banner_manager_load_scripts() {
    global $useful_banner_manager_plugin_url;

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'useful_banner_manager_scripts', $useful_banner_manager_plugin_url . 'scripts.js', array( 'jquery' ) );
}

add_action( 'admin_enqueue_scripts', 'useful_banner_manager_load_admin_scripts' );

function useful_banner_manager_load_admin_scripts( $hook ) {
    if ( $hook != 'useful-banner-manager/useful-banner-manager-banners.php' ) {
        return;
    }

    global $useful_banner_manager_plugin_url;

    wp_enqueue_style( 'jquery-ui', $useful_banner_manager_plugin_url . '/css/jquery-ui.custom.min.css' );

    wp_enqueue_script( 'jquery-ui', $useful_banner_manager_plugin_url . '/javascript/jquery-ui.custom.min.js', array('jquery' ) );
}
?>