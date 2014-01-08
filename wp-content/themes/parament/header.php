<?php
/**
 * @package Parament
 */

$header_image = get_header_image();
$tagline      = get_bloginfo( 'description' );
$tag_markup   = empty( $header_image ) ? '<h2 id="site-description">%2$s</h2>' : '<h2 id="site-description"><a href="%1$s">%2$s</a></h2>';

?><!DOCTYPE html>
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link href="https://plus.google.com/108352162256411932297" rel="publisher"/>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="page-wrap" class="contain">
<?php do_action( 'before' ); ?>
	<header id="branding" role="banner">
		<!--<h1 id="site-title"><a href="<?php echo esc_url( home_url() ); ?>"><?php echo get_option( 'blogname' ); ?></a></h1>-->
		<?php //if ( ! empty( $tagline ) ) :  ?>
			<?php ///printf( $tag_markup, esc_url( home_url() ), $tagline );  ?>
		<?php //endif; ?>

		<?php if ( ! empty( $header_image ) ) : ?>
			<a id="header-image" href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_url( $header_image ); ?>" alt="" /></a>
		<?php endif; ?>
			<div class="headerLinks">
			<?php if(is_user_logged_in()){ ?>
			<?php $user_info = get_userdata(get_current_user_id()); ?>
			Hi <?php echo $user_info->first_name; ?>, | 
			<a href="account" title="<?php _e('My Account'); ?>"><?php _e('My Account'); ?></a> |
			<a href="?member=logout" title="<?php _e('Logout'); ?>"><?php _e('Logout'); ?></a>
			<?php } else { ?>
			<a href="<?php echo site_url('/sign-in'); ?>">Login</a> | <a href="<?php echo site_url('/sign-up'); ?>">Sign Up</a>
			<?php } ?>
			</div>


		<?php /*if ( !is_user_logged_in() ) { ?>
		<span style="font-size:15px; float:right; margin-top:5%; color:#fff;"> <a href="/wp-login.php">Login</a> | <a href="/wp-login.php?action=register">Register</a></span>
		<?php } */?>
	</header><!-- #branding -->

	<nav id="menu" role="navigation">
	<div align="center">
	<?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'primary-menu', 'theme_location' => 'primary-menu', 'link_after' => '<span class="arrow-down"></span>' ) ); ?>
	</div>
	</nav>