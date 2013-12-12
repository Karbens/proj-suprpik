<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( 'Access Denied' );
}
?>
<div class="wrap">
    <div style="margin: 20px 0; text-align: center; display: inline-block">
        <div style="float: left; text-align: justify; width: 400px; border: 1px solid #DFDFDF; padding: 10px; padding-bottom: 6px;">
            <div style="float: left; margin-right: 10px;">
                <a href="http://rubensargsyan.com/wordpress-plugin-ubm-premium/" target="_blank"><img src="http://rubensargsyan.com/images/ubm-premium.png" alt="UBM Premium" style="border: none" /></a>
            </div>
            <div style="font-size: 11px">"UBM Premium" plugin is the advenced version of the "Useful Banner Manager" plugin which supports more features like impressions, clicks and CTR of the banners. It also allows you to add the links of flash banners outside.
                <div style="margin-top: 14px;"><a href="http://rubensargsyan.com/wordpress-plugin-ubm-premium/" target="_blank">"UBM Premium" homepage</a></div>
            </div>
        </div>
        <div style="float: right; margin-left: 50px; text-align: justify; width: 400px; border: 1px solid #DFDFDF; padding: 10px; padding-bottom: 6px;">
            <div style="float: left; margin-right: 10px;">
                <a href="http://rubensargsyan.com/wordpress-plugin-useful-video-player/" target="_blank"><img src="http://rubensargsyan.com/images/useful-video-player.png" alt="UBM Premium" style="border: none" /></a>
            </div>
            <div style="font-size: 11px">"Useful Video Player" plugin allows you to embed videos to your WordPress powered website without hassle.
                <div style="margin-top: 7px;">Among the basic features other plugins might offer you, this plugin will enable you to use videos as ads, add overlay text and images.</div>
                <div style="margin-top: 7px;"><a href="http://rubensargsyan.com/wordpress-plugin-useful-video-player/" target="_blank">"Useful Video Player" homepage</a></div>
            </div>
        </div>
    </div>
    <h1><?php echo( $useful_banner_manager_plugin_title ); ?></h1>
    <h2><?php _e( 'Banners' ); ?></h2>
    <?php
    if ( isset( $_GET[ $useful_banner_manager_plugin_prefix . 'banner_id' ] ) && is_numeric( $_GET[ $useful_banner_manager_plugin_prefix . 'banner_id' ] ) && $_GET[ $useful_banner_manager_plugin_prefix . 'banner_id' ] > 0 ) {
        if ( $_GET['page'] == 'useful-banner-manager/useful-banner-manager-banners.php' ) {
            if ( isset( $_POST[ $useful_banner_manager_plugin_prefix . 'save_banner' ] ) ) {
                $banner_id = $_GET[ $useful_banner_manager_plugin_prefix . 'banner_id' ];

                $banner_old_name = $_POST[ $useful_banner_manager_plugin_prefix . 'banner_name' ];
                $banner_old_type = $_POST[ $useful_banner_manager_plugin_prefix . 'banner_type' ];
                $banner_old_file = $banner_id . '-' . $banner_old_name . '.' . $banner_old_type;

                $errors = array();

                if ( $_FILES[ $useful_banner_manager_plugin_prefix . 'banner_file' ]['error'] == 0 ) {
                    $banner_name_parts = explode( '.', $_FILES[ $useful_banner_manager_plugin_prefix . 'banner_file' ]['name'] );

                    array_pop( $banner_name_parts );

                    $banner_name = implode( '.', $banner_name_parts );
                    $banner_type = array_pop( explode( '.', $_FILES[ $useful_banner_manager_plugin_prefix . 'banner_file' ]['name'] ) );

                    $available_formats = array( 'jpg', 'jpeg', 'gif', 'png', 'swf' );

                    if ( ! in_array( strtolower( $banner_type ), $available_formats ) ) {
                        $errors[] = 'banner_type';
                    }

                    $banner_tmp_file = $_FILES[ $useful_banner_manager_plugin_prefix . 'banner_file' ]['tmp_name'];
                } else {
                    $banner_name = $banner_old_name;
                    $banner_type = $banner_old_type;
                }

                if ( trim( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_title' ] ) == '' ) {
                    $banner_title = '';

                    $errors[] = 'banner_title';
                } else {
                    $banner_title = esc_attr( stripslashes( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_title' ] ) );
                }

                $banner_alt = esc_attr( stripslashes( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_alt' ] ) );

                $banner_link = esc_url_raw( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_link' ] );

                if ( $banner_link == '' ) {
                    $link_target = '';
                } else {
                    if ( in_array( $_POST[ $useful_banner_manager_plugin_prefix . 'link_target' ], array( '_self', '_top', '_blank', '_parent' ) ) ) {
                        $link_target = $_POST[ $useful_banner_manager_plugin_prefix . 'link_target' ];
                    } else {
                        $link_target = '_self';
                    }
                }

                if ( $banner_link == '' ) {
                    $link_rel = '';
                }else{
                    if ( in_array( $_POST[ $useful_banner_manager_plugin_prefix . 'link_rel' ], array( 'nofollow', 'dofollow' ) ) ) {
                        $link_rel = $_POST[ $useful_banner_manager_plugin_prefix . 'link_rel' ];
                    } else {
                        $link_rel = 'dofollow';
                    }
                }

                if ( isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) && ! in_array( 'banner_type', $errors ) ) {
                    if ( $banner_type == 'swf' ) {
                        $errors[] = 'swf_auto_sizes';
                    } else {
                        list( $banner_width, $banner_height ) = @getimagesize( $banner_tmp_file );
                    }
                } elseif ( ! isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) ) {
                    if ( is_numeric( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_width' ] ) && $_POST[ $useful_banner_manager_plugin_prefix . 'banner_width'] > 0 ) {
                        $banner_width = $_POST[ $useful_banner_manager_plugin_prefix . 'banner_width' ];
                    } else {
                        $banner_width = '';

                        $errors[] = 'banner_width';
                    }

                    if ( is_numeric( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_height' ] ) && $_POST[ $useful_banner_manager_plugin_prefix . 'banner_height' ] > 0 ) {
                        $banner_height = $_POST[ $useful_banner_manager_plugin_prefix . 'banner_height' ];
                    } else {
                        $banner_height = '';

                        $errors[] = 'banner_height';
                    }
                }

                if ( $_POST[ $useful_banner_manager_plugin_prefix . 'active_until' ] == '' ) {
                    $active_until = -1;
                }else{
                    $active_until = trim( $_POST[ $useful_banner_manager_plugin_prefix . 'active_until' ] );

                    if ( ! preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $active_until ) || $active_until < date( 'Y-m-d' ) ) {
                        unset( $active_until );

                        $errors[] = 'active_until';
                    }
                }

                if ( is_numeric( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_order' ] ) && $_POST[ $useful_banner_manager_plugin_prefix . 'banner_order' ] >= 0 ){
                    $banner_order = $_POST[$useful_banner_manager_plugin_prefix . 'banner_order'];
                }else{
                    $errors[] = 'banner_order';
                }

                $wrapper_id = esc_attr( stripslashes( $_POST[ $useful_banner_manager_plugin_prefix . 'wrapper_id' ] ) );
                $wrapper_class = esc_attr( stripslashes( $_POST[ $useful_banner_manager_plugin_prefix . 'wrapper_class' ] ) );

                switch( $_POST[ $useful_banner_manager_plugin_prefix . 'is_visible' ] ) {
                    case 'no':
                    $is_visible = 'no';
                    break;
                    default:
                    $is_visible = 'yes';
                }

                $current_user = wp_get_current_user();
                $banner_edited_by = $current_user->user_login;
                $last_edited_date = date( 'Y-m-d' );

                if ( empty( $errors ) ) {
                    $banner_data = array(
                        'banner_name'       => $banner_name,
                        'banner_type'       => $banner_type,
                        'banner_title'      => $banner_title,
                        'banner_alt'        => $banner_alt,
                        'banner_link'       => $banner_link,
                        'link_target'       => $link_target,
                        'link_rel'          => $link_rel,
                        'banner_width'      => $banner_width,
                        'banner_height'     => $banner_height,
                        'active_until'      => $active_until,
                        'banner_order'      => $banner_order,
                        'wrapper_id'        => $wrapper_id,
                        'wrapper_class'     => $wrapper_class,
                        'is_visible'        => $is_visible,
                        'banner_edited_by'  => $banner_edited_by,
                        'last_edited_date'  => $last_edited_date
                    );

                    useful_banner_manager_update_banner( $banner_id, $banner_data );

                    if ( $_FILES[ $useful_banner_manager_plugin_prefix . 'banner_file' ]['error'] == 0 ) {
                        if ( file_exists( ABSPATH . 'wp-content/uploads/useful_banner_manager_banners/' . $banner_old_file ) ) {
                          unlink( ABSPATH . 'wp-content/uploads/useful_banner_manager_banners/' . $banner_old_file );
                        }

                        move_uploaded_file( $banner_tmp_file, ABSPATH . 'wp-content/uploads/useful_banner_manager_banners/' . $banner_id . '-' . $banner_name . '.' . $banner_type );
                    }

                    echo( '<div id="message" class="updated fade"><p><strong>' . __( 'The banner is edited.', 'useful_banner_manager' ) . '</strong></p></div>' );

                } else {
                    echo( '<div id="message" class="updated fade"><p><strong>' . ( ( count( $errors ) > 1 ) ? __( 'The following fields are wrong:', 'useful_banner_manager' ) : __( 'The following field is wrong:', 'useful_banner_manager' ) ) );

                    foreach ( $errors as $error ) {
                      echo( '<p>' . ucwords( str_replace( '_', ' ', $error ) ) . '</p>' );
                    }

                    echo( '</strong></p></div>' );
                }
            }
        }

        $banner_id = $_GET[ $useful_banner_manager_plugin_prefix . 'banner_id' ];
        $banner = useful_banner_manager_get_banner( $banner_id );

        if ( empty( $banner ) ) {
            echo( '<p>' . __('The banner ID is wrong.', 'useful_banner_manager' ) . '</p>' );
        } else {
            ?>
            <form method="post" enctype="multipart/form-data">
              <table id="useful_banner_manager_edit_banner">
                  <tr>
                    <td colspan="2"><h3><?php echo( sprintf( __( 'Edit the banner "%s"', 'useful_banner_manager' ), $banner->banner_title ) ); ?></h3></td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Banner File', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <p>
                          <?php
                          if ( $banner->banner_type == 'swf' ) {
                              ?>
                              <object width="<?php echo( $banner->banner_width ); ?>" height="<?php echo( $banner->banner_height ); ?>">
                                    <param name="movie" value="<?php bloginfo( 'wpurl' ); ?>/wp-content/uploads/useful_banner_manager_banners/<?php echo( $banner->id . '-' . $banner->banner_name ); ?>.<?php echo( $banner->banner_type ); ?>">
                                    <param name="wmode" value="transparent">
                                    <embed src="<?php bloginfo( 'wpurl' ); ?>/wp-content/uploads/useful_banner_manager_banners/<?php echo( $banner->id . '-' . $banner->banner_name ); ?>.<?php echo( $banner->banner_type ); ?>" width="<?php echo( $banner->banner_width ); ?>" height="<?php echo( $banner->banner_height ); ?>" wmode="transparent">
                                  </embed>
                              </object>
                          <?php
                          } else {
                              ?>
                              <img src="<?php bloginfo( 'wpurl' ); ?>/wp-content/uploads/useful_banner_manager_banners/<?php echo( $banner->id . '-' . $banner->banner_name ); ?>.<?php echo( $banner->banner_type ); ?>" width="<?php echo( $banner->banner_width ); ?>" height="<?php echo( $banner->banner_height ); ?>" alt="<?php echo( $banner->banner_alt ); ?>" />
                          <?php
                          }
                          ?>
                          </p>
                          <input type="hidden" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_name" value="<?php echo( $banner->banner_name ); ?>" />
                          <input type="hidden" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_type" value="<?php echo( $banner->banner_type ); ?>" />
                          <input type="file" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_file" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_file" /> <small><?php _e( 'The banner type can be jpg, jpeg, gif, png or swf.', 'useful_banner_manager' ); ?></small>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Banner Title', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_title" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_title" style="width: 300px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . $banner_title . '"' ); } else { echo( 'value="' . $banner->banner_title . '"' ); } ?> /> <?php _e( '(required)', 'useful_banner_manager' ); ?>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Image Alt', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_alt" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_alt" style="width: 300px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . $banner_alt . '"' ); } else { echo( 'value="' . $banner->banner_alt . '"' ); } ?> /> <small><?php _e( 'Not for swf files.', 'useful_banner_manager' ); ?></small>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Banner Link', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_link" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_link" style="width: 300px" <?php if ( ! empty ( $errors ) ) { echo( 'value="' . $banner_link . '"' ); } else { echo( 'value="' . $banner->banner_link . '"' ); } ?> />
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Link Target', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <select id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>link_target" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>link_target" style="width: 80px">
                              <option value="_self" <?php if ( ! empty( $errors ) && $link_target == '_self' ) { echo( 'selected="selected"' ); } elseif ( empty( $errors ) && $banner->link_target == '_self' ) { echo( 'selected="selected"' ); } ?>>_self</option>
                              <option value="_top" <?php if ( ! empty( $errors ) && $link_target == '_top' ) { echo( 'selected="selected"' ); } elseif ( ( empty( $errors ) ) && $banner->link_target == '_top' ) { echo( 'selected="selected"' ); } ?>>_top</option>
                              <option value="_blank" <?php if ( ! empty( $errors ) && $link_target == '_blank' ) { echo( 'selected="selected"' ); } elseif ( ( empty( $errors ) ) && $banner->link_target == '_blank' ) { echo( 'selected="selected"' ); } ?>>_blank</option>
                              <option value="_parent" <?php if ( ! empty( $errors ) && $link_target == '_parent' ) { echo( 'selected="selected"' ); } elseif ( ( empty( $errors ) ) && $banner->link_target == '_parent' ) { echo( 'selected="selected"' ); } ?>>_parent</option>
                          </select>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Link Rel', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <select id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>link_rel" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>link_rel" style="width: 80px">
                              <option value="dofollow" <?php if ( ! empty( $errors ) && $link_rel == 'dofollow' ) { echo( 'selected="selected"' ); } elseif ( ( empty( $errors ) ) && $banner->link_rel == 'dofollow' ) { echo( 'selected="selected"' ); } ?>>dofollow</option>
                              <option value="nofollow" <?php if ( ! empty( $errors ) && $link_rel == 'nofollow' ) { echo( 'selected="selected"' ); } elseif ( ( empty( $errors ) ) && $banner->link_rel == 'nofollow' ) { echo( 'selected="selected"' ); } ?>>nofollow</option>
                          </select> <small><?php _e( 'Not for swf files.', 'useful_banner_manager' ); ?></small>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Banner Sizes', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <label><?php _e( 'Auto:', 'useful_banner_manager' ); ?> <input type="checkbox" name="<?php echo($useful_banner_manager_plugin_prefix); ?>auto_sizes" onclick="if(jQuery(this).is(':checked')){ jQuery('#'<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width').attr('disabled',true); jQuery('#'<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height').attr('disabled',true); }else{ jQuery('#'<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width').removeAttr('disabled'); jQuery('#'<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height').removeAttr('disabled'); }" <?php if ( ! empty( $errors ) && isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) ) { echo( 'checked="checked"' ); } ?> /></label> <small><?php _e( 'Check this to set the original sizes of the banner, not for swf files.', 'useful_banner_manager' ); ?></small>
                          <table>
                              <tr>
                                <td><label for="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width"><?php _e( 'Width:', 'useful_banner_manager' ); ?></label></td>
                                <td><input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width" style="width: 50px" <?php if ( ! empty( $errors ) && ! isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) ) { echo( 'value="' . $banner_width . '"' ); } else { echo( 'value="' . $banner->banner_width . '"' ); } ?> /><?php _e( 'px', 'useful_banner_manager' ); ?> <?php _e( '(required if the banner is swf file)', 'useful_banner_manager' ); ?></td>
                              </tr>
                              <tr>
                                <td><label for="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height"><?php _e( 'Height:', 'useful_banner_manager' ); ?></label></td>
                                <td><input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height" style="width: 50px" <?php if ( ! empty( $errors ) &&  ! isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) ) { echo( 'value="' . $banner_height . '"' ); } else { echo( 'value="' . $banner->banner_height . '"' ); } ?> /><?php _e( 'px', 'useful_banner_manager' ); ?> <?php _e( '(required if the banner is swf file)', 'useful_banner_manager' ); ?></td>
                              </tr>
                          </table>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Active Until', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>active_until" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>active_until" class="datepicker" style="width: 100px" <?php if ( ! empty( $errors ) ) { if( in_array( 'active_until', $errors ) ) { echo( 'value="' . esc_attr( $_POST[ $useful_banner_manager_plugin_prefix . 'active_until' ] ) . '"'); } elseif ( $active_until != -1 ) { echo( 'value="' . $active_until . '"' ); } } elseif ( $banner->active_until != -1 ) { echo( 'value="' . $banner->active_until . '"' ); } ?> /> <small><?php _e( 'Leave empty if there is no date.', 'useful_banner_manager' ); ?></small>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Banner Order', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_order" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_order" style="width: 50px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . esc_attr( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_order' ] ) . '"' ); } else { echo( 'value="' . $banner->banner_order . '"' ); } ?> /> <small><?php _e( 'Set the number depends on which the banner will be shown on more top places.', 'useful_banner_manager' ); ?></small>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Wrapper ID', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>wrapper_id" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>wrapper_id" style="width: 100px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . $wrapper_id . '"' ); } else { echo( 'value="' . $banner->wrapper_id . '"' ); } ?> /> <small><?php _e( 'ID of the tag "div" wrapping the banner.', 'useful_banner_manager' ); ?></small>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Wrapper Class', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>wrapper_class" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>wrapper_class" style="width: 100px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . $wrapper_class . '"' ); } else { echo( 'value="' . $banner->wrapper_class . '"' ); } ?> /> <small><?php _e( 'Class or classes of the tag "div" wrapping the banner.', 'useful_banner_manager' ); ?></small>
                      </td>
                  </tr>
                  <tr>
                      <td width="25%" valign="middle"><strong><?php _e( 'Is Visible', 'useful_banner_manager' ); ?></strong></td>
                      <td width="75%">
                          <label><?php _e( 'Yes:', 'useful_banner_manager' ); ?><input type="radio" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>is_visible" value="yes" <?php if ( ! empty( $errors ) && $is_visible != 'no' ) { echo( 'checked="checked"' ); }elseif ( ( empty( $errors ) ) && $banner->is_visible == 'yes' ) { echo( 'checked="checked"' ); } ?> /></label> <label><?php _e( 'No:', 'useful_banner_manager' ); ?><input type="radio" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>is_visible" value="no" <?php if ( ! empty( $errors ) && $is_visible == 'no' ) { echo( 'checked="checked"' ); }elseif ( ( empty( $errors ) ) && $banner->is_visible == 'no' ) { echo( 'checked="checked"' ); } ?> /></label>
                      </td>
                  </tr>
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
              </table>
              <p class="submit">
                  <input name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>save_banner" type="submit" value="<?php _e( 'Save', 'useful_banner_manager' ); ?>" /> <a href="admin.php?page=useful-banner-manager/useful-banner-manager-banners.php"><?php _e( 'Cancel', 'useful_banner_manager' ); ?></a>
              </p>
          </form>
          <script type="text/javascript">
          if(jQuery.isFunction(jQuery.fn.datepicker)){
              jQuery('.datepicker').datepicker({ yearRange: '-0:+20', changeYear: true, dateFormat: 'yy-mm-dd' });
          }
          </script>
        <?php
        }
    }else{
        if ( $_GET['page'] == 'useful-banner-manager/useful-banner-manager-banners.php' ) {
            if ( isset( $_POST[ $useful_banner_manager_plugin_prefix . 'add_banner' ] ) ) {
                $errors = array();

                $banner_name_parts = explode( '.', $_FILES[ $useful_banner_manager_plugin_prefix . 'banner_file' ]['name'] );

                array_pop( $banner_name_parts );

                $banner_name = implode( '.', $banner_name_parts );
                $banner_type = array_pop( explode( '.', $_FILES[ $useful_banner_manager_plugin_prefix . 'banner_file' ]['name'] ) );

                $available_formats = array( 'jpg', 'jpeg', 'gif', 'png', 'swf' );

                if ( ! in_array( strtolower( $banner_type ), $available_formats ) ) {
                    $errors[] = 'banner_type';
                }

                $banner_tmp_file = $_FILES[ $useful_banner_manager_plugin_prefix . 'banner_file' ]['tmp_name'];

                if ( trim( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_title' ] ) == '' ) {
                    $banner_title = '';

                    $errors[] = 'banner_title';
                } else {
                    $banner_title = esc_attr( stripslashes( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_title' ] ) );
                }

                $banner_alt = esc_attr( stripslashes( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_alt' ] ) );

                $banner_link = esc_url_raw( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_link' ] );

                if ( $banner_link == '' ) {
                    $link_target = '';
                } else {
                    if ( in_array( $_POST[ $useful_banner_manager_plugin_prefix . 'link_target' ], array( '_self', '_top', '_blank', '_parent' ) ) ) {
                        $link_target = $_POST[ $useful_banner_manager_plugin_prefix . 'link_target' ];
                    } else {
                        $link_target = '_self';
                    }
                }

                if ( $banner_link == '' ) {
                    $link_rel = '';
                }else{
                    if ( in_array( $_POST[ $useful_banner_manager_plugin_prefix . 'link_rel' ], array( 'nofollow', 'dofollow' ) ) ) {
                        $link_rel = $_POST[ $useful_banner_manager_plugin_prefix . 'link_rel' ];
                    } else {
                        $link_rel = 'dofollow';
                    }
                }

                if ( isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) && ! in_array( 'banner_type', $errors ) ) {
                    if ( $banner_type == 'swf' ) {
                        $errors[] = 'swf_auto_sizes';
                    } else {
                        list( $banner_width, $banner_height ) = @getimagesize( $banner_tmp_file );
                    }
                } elseif ( ! isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) ) {
                    if ( is_numeric( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_width' ] ) && $_POST[ $useful_banner_manager_plugin_prefix . 'banner_width'] > 0 ) {
                        $banner_width = $_POST[ $useful_banner_manager_plugin_prefix . 'banner_width' ];
                    } else {
                        $banner_width = '';

                        $errors[] = 'banner_width';
                    }

                    if ( is_numeric( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_height' ] ) && $_POST[ $useful_banner_manager_plugin_prefix . 'banner_height' ] > 0 ) {
                        $banner_height = $_POST[ $useful_banner_manager_plugin_prefix . 'banner_height' ];
                    } else {
                        $banner_height = '';

                        $errors[] = 'banner_height';
                    }
                }

                $added_date = date( 'Y-m-d' );

                if ( $_POST[ $useful_banner_manager_plugin_prefix . 'active_until' ] == '' ) {
                    $active_until = -1;
                }else{
                    $active_until = trim( $_POST[ $useful_banner_manager_plugin_prefix . 'active_until' ] );

                    if ( ! preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $active_until ) || $active_until < date( 'Y-m-d' ) ) {
                        unset( $active_until );

                        $errors[] = 'active_until';
                    }
                }

                if ( is_numeric( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_order' ] ) && $_POST[ $useful_banner_manager_plugin_prefix . 'banner_order' ] >= 0 ){
                    $banner_order = $_POST[$useful_banner_manager_plugin_prefix . 'banner_order'];
                }else{
                    $errors[] = 'banner_order';
                }

                $wrapper_id = esc_attr( stripslashes( $_POST[ $useful_banner_manager_plugin_prefix . 'wrapper_id' ] ) );
                $wrapper_class = esc_attr( stripslashes( $_POST[ $useful_banner_manager_plugin_prefix . 'wrapper_class' ] ) );

                switch( $_POST[ $useful_banner_manager_plugin_prefix . 'is_visible' ] ) {
                    case 'no':
                    $is_visible = 'no';
                    break;
                    default:
                    $is_visible = 'yes';
                }

                $current_user = wp_get_current_user();
                $banner_added_by = $current_user->user_login;

                if ( empty( $errors ) ) {
                    $banner_data = array(
                        'banner_name'       => $banner_name,
                        'banner_type'       => $banner_type,
                        'banner_title'      => $banner_title,
                        'banner_alt'        => $banner_alt,
                        'banner_link'       => $banner_link,
                        'link_target'       => $link_target,
                        'link_rel'          => $link_rel,
                        'banner_width'      => $banner_width,
                        'banner_height'     => $banner_height,
                        'added_date'        => $added_date,
                        'active_until'      => $active_until,
                        'banner_order'      => $banner_order,
                        'wrapper_id'        => $wrapper_id,
                        'wrapper_class'     => $wrapper_class,
                        'is_visible'        => $is_visible,
                        'banner_added_by'   => $banner_added_by
                    );

                    $added_banner_id = useful_banner_manager_add_banner( $banner_data );

                    move_uploaded_file( $banner_tmp_file, ABSPATH . 'wp-content/uploads/useful_banner_manager_banners/' . $added_banner_id . '-' . $banner_name . '.' . $banner_type );

                    echo('<div id="message" class="updated fade"><p><strong>' . __( 'New banner is added.', 'useful_banner_manager' ) . '</strong></p></div>');

                }else{
                    echo( '<div id="message" class="updated fade"><p><strong>' . ( ( count( $errors ) > 1 ) ? __( 'The following fields are wrong:', 'useful_banner_manager' ) : __( 'The following field is wrong:', 'useful_banner_manager' ) ) );

                    foreach ( $errors as $error ) {
                      echo( '<p>' . ucwords( str_replace( '_', ' ', $error ) ) . '</p>' );
                    }

                    echo( '</strong></p></div>' );
                }
            }

            if ( isset( $_POST[ $useful_banner_manager_plugin_prefix . 'delete' ] ) ) {
                if ( isset( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_id' ] ) && is_numeric( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_id' ] ) && $_POST[ $useful_banner_manager_plugin_prefix . 'banner_id' ] > 0 ) {
                    $banner_id = $_POST[ $useful_banner_manager_plugin_prefix . 'banner_id' ];

                    useful_banner_manager_delete_banner( $banner_id );

                    echo('<div id="message" class="updated fade"><p><strong>' . __( 'The banner is deleted.', 'useful_banner_manager' ) . '</strong></p></div>');
                }
            }
        }
        ?>
        <form method="post" enctype="multipart/form-data">
          <table id="useful_banner_manager_add_banner">
              <tr>
                <td colspan="2"><h3><?php _e( 'Add banner', 'useful_banner_manager' ); ?></h3></td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Banner File', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <input type="file" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_file" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_file" /> <?php _e( '(required)', 'useful_banner_manager' ); ?> <small><?php _e( 'The banner type can be jpg, jpeg, gif, png or swf.', 'useful_banner_manager' ); ?></small>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Banner Title', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_title" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_title" style="width: 300px" <?php if ( ! empty( $errors ) ){ echo( 'value="' . $banner_title . '"' ); } ?> /> <?php _e( '(required)', 'useful_banner_manager' ); ?>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Image Alt', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_alt" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_alt" style="width: 300px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . $banner_alt . '"' ); } ?> /> <small><?php _e( 'Not for swf files.', 'useful_banner_manager' ); ?></small>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Banner Link', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_link" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_link" style="width: 300px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . $banner_link . '"'); } ?> />
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Link Target', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <select id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>link_target" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>link_target" style="width: 80px">
                          <option value="_self" <?php if ( ! empty( $errors ) && $link_target == '_self' ) { echo( 'selected="selected"' ); } elseif ( empty( $errors ) ) { echo( 'selected="selected"' ); } ?>>_self</option>
                          <option value="_top" <?php if ( ! empty( $errors ) && $link_target == '_top' ) { echo( 'selected="selected"' ); } ?>>_top</option>
                          <option value="_blank" <?php if ( ! empty( $errors ) && $link_target == '_blank' ) { echo( 'selected="selected"' ); } ?>>_blank</option>
                          <option value="_parent" <?php if ( ! empty( $errors ) && $link_target == '_parent' ) { echo( 'selected="selected"' ); } ?>>_parent</option>
                      </select>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Link Rel', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <select id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>link_rel" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>link_rel" style="width: 80px">
                          <option value="dofollow" <?php if ( ! empty( $errors ) && $link_rel == 'dofollow' ) { echo( 'selected="selected"' ); } elseif ( empty( $errors ) ) { echo( 'selected="selected"' ); } ?>>dofollow</option>
                          <option value="nofollow" <?php if ( ! empty( $errors ) && $link_rel == 'nofollow' ) { echo( 'selected="selected"' ); } ?>>nofollow</option>
                      </select> <small><?php _e( 'Not for swf files.', 'useful_banner_manager' ); ?></small>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Banner Sizes', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <label><?php _e( 'Auto:', 'useful_banner_manager' ); ?> <input type="checkbox" name="<?php echo($useful_banner_manager_plugin_prefix); ?>auto_sizes" onclick="if(jQuery(this).is(':checked')){ jQuery('#<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width').attr('disabled',true); jQuery('#<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height').attr('disabled',true); }else{ jQuery('#<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width').removeAttr('disabled'); jQuery('#<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height').removeAttr('disabled'); }" <?php if ( ! empty( $errors ) && isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) ) { echo( 'checked="checked"' ); } ?> /></label> <small><?php _e( 'Check this to set the original sizes of the banner, not for swf files.', 'useful_banner_manager' ); ?></small>
                      <table>
                          <tr>
                            <td><label for="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width"><?php _e( 'Width:', 'useful_banner_manager' ); ?></label></td>
                            <td><input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_width" style="width: 50px" <?php if ( ! empty( $errors ) && ! isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) ) { echo( 'value="' . $banner_width . '"' ); } ?> /><?php _e( 'px', 'useful_banner_manager' ); ?> <?php _e( '(required if the banner is swf file)', 'useful_banner_manager' ); ?></td>
                          </tr>
                          <tr>
                            <td><label for="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height"><?php _e( 'Height:', 'useful_banner_manager' ); ?></label></td>
                            <td><input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_height" style="width: 50px" <?php if ( ! empty( $errors ) && ! isset( $_POST[ $useful_banner_manager_plugin_prefix . 'auto_sizes' ] ) ) { echo( 'value="' . $banner_height . '"' ); } ?> /><?php _e( 'px', 'useful_banner_manager' ); ?> <?php _e( '(required if the banner is swf file)', 'useful_banner_manager' ); ?></td>
                          </tr>
                      </table>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Active Until', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>active_until" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>active_until" class="datepicker" style="width: 100px" <?php if ( ! empty( $errors ) ) { if( in_array( 'active_until', $errors ) ) { echo( 'value="' . esc_attr( $_POST[ $useful_banner_manager_plugin_prefix . 'active_until' ] ) . '"'); } elseif ( $active_until != -1 ) { echo( 'value="' . $active_until . '"' ); } } ?> /> <small><?php _e( 'Leave empty if there is no date.', 'useful_banner_manager' ); ?></small>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Banner Order', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_order" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_order" style="width: 50px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . esc_attr( $_POST[ $useful_banner_manager_plugin_prefix . 'banner_order' ] ) . '"' ); } else { echo( 'value="0"' ); } ?> /> <small><?php _e( 'Set the number depends on which the banner will be shown on more top places.', 'useful_banner_manager' ); ?></small>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Wrapper ID', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>wrapper_id" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>wrapper_id" style="width: 100px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . esc_attr( $_POST[ $useful_banner_manager_plugin_prefix . 'wrapper_id' ] ) . '"' ); } else { echo( 'value=""' ); } ?> /> <small><?php _e( 'ID of the tag "div" wrapping the banner.', 'useful_banner_manager' ); ?></small>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Wrapper Class', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <input type="text" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>wrapper_class" id="<?php echo( $useful_banner_manager_plugin_prefix ); ?>wrapper_class" style="width: 100px" <?php if ( ! empty( $errors ) ) { echo( 'value="' . esc_attr( $_POST[ $useful_banner_manager_plugin_prefix . 'wrapper_class' ] ) . '"' ); } else { echo( 'value=""' ); } ?> /> <small><?php _e( 'Class or classes of the tag "div" wrapping the banner.', 'useful_banner_manager' ); ?></small>
                  </td>
              </tr>
              <tr>
                  <td width="25%" valign="middle"><strong><?php _e( 'Is Visible', 'useful_banner_manager' ); ?></strong></td>
                  <td width="75%">
                      <label><?php _e( 'Yes:', 'useful_banner_manager' ); ?><input type="radio" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>is_visible" value="yes" <?php if ( ! empty( $errors ) && $is_visible != 'no' ) { echo( 'checked="checked"' ); } elseif ( empty( $errors ) ) { echo( 'checked="checked"' ); } ?> /></label> <label><?php _e( 'No:', 'useful_banner_manager' ); ?><input type="radio" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>is_visible" value="no" <?php if ( ! empty( $errors ) && $is_visible == 'no' ) { echo( 'checked="checked"' ); } ?> /></label>
                  </td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
          </table>
          <p class="submit">
              <input name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>add_banner" type="submit" value="<?php _e( 'Add banner', 'useful_banner_manager' ); ?>" />
          </p>
        </form>
        <script type="text/javascript">
        if(jQuery.isFunction(jQuery.fn.datepicker)){
            jQuery('.datepicker').datepicker({ yearRange: '-0:+20', changeYear: true, dateFormat: 'yy-mm-dd' });
        }
        </script>
        <br />
        <?php $banners = useful_banner_manager_get_banners(); ?>
        <style>
        .widefat td{
        	padding: 3px 7px;
        	vertical-align: middle;
        }

        .widefat tbody th.check-column{
        	padding: 7px 0;
            vertical-align: middle;
        }
        </style>
        <h3><?php _e( 'Manage banners', 'useful_banner_manager' ); ?></h3>
        <table class="widefat fixed" cellspacing="0" id="useful_banner_manager_manage_banners" width="100%">
              <thead>
              	<tr>
                    <th scope="col" width="3%"><?php _e( 'ID', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="5%"><?php _e( 'Type', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="12%"><?php _e( 'Title', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="23%"><?php _e( 'Link', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="6%"><?php _e( 'Rel', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="9%"><?php _e( 'Added Date', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="9%"><?php _e( 'Active Until', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="5%"><?php _e( 'Order', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="7%"><?php _e( 'Is Visible', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="8%"><?php _e( 'Added By', 'useful_banner_manager' ); ?></th>
                    <th scope="col" width="5%"></th>
                    <th scope="col" width="8%"></th>
              	</tr>
          	</thead>
          	<tfoot>
              	<tr>
                    <th scope="col"><?php _e( 'ID', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Type', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Title', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Link', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Rel', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Added Date', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Active Until', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Order', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Is Visible', 'useful_banner_manager' ); ?></th>
                    <th scope="col"><?php _e( 'Added By', 'useful_banner_manager' ); ?></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
              	</tr>
          	</tfoot>
            <tbody>
            <?php
            foreach ( $banners as $banner ) {
            ?>
                <tr class="alternate">
                    <td><?php echo( $banner->id ); ?></td>
                    <td><?php echo( $banner->banner_type ); ?></td>
                    <td><?php echo( $banner->banner_title ); ?></td>
                    <td><?php echo( $banner->banner_link ); ?></td>
                    <td><?php echo( $banner->link_rel ); ?></td>
                    <td><?php echo( $banner->added_date ); ?></td>
                    <td><?php echo( ( $banner->active_until == -1 ) ? __( 'No date', 'useful_banner_manager' ) : $banner->active_until ); ?></td>
                    <td><?php echo( $banner->banner_order ); ?></td>
                    <td><?php echo( $banner->is_visible ); ?></td>
                    <td><?php echo( $banner->banner_added_by ); ?></td>
                    <td>
                      <form method="get">
                          <p class="submit">
                            <input type="submit" value="<?php _e( 'Edit', 'useful_banner_manager' ); ?>" />
                            <input type="hidden" name="page" value="useful-banner-manager/useful-banner-manager-banners.php" />
                            <input type="hidden" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_id" value="<?php echo( $banner->id ); ?>" />
                          </p>
                      </form>
                    </td>
                    <td>
                      <form method="post">
                          <p class="submit">
                            <input name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>delete" type="submit" value="<?php _e( 'Delete', 'useful_banner_manager' ); ?>" onclick="javascript:if(!confirm('<?php echo( sprintf( __( 'Are you sure you want to delete the banner &quot;%s&quot;?' ), $banner->banner_title ) ); ?>')){ return false; }" />
                            <input type="hidden" name="<?php echo( $useful_banner_manager_plugin_prefix ); ?>banner_id" value="<?php echo( $banner->id ); ?>" />
                          </p>
                      </form>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    <?php
    }
    ?>
</div>