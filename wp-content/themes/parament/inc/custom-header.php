<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * You can add an optional custom header image to header.php like so ...

	<?php $header_image = get_header_image();
	if ( ! empty( $header_image ) ) { ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
		</a>
	<?php } // if ( ! empty( $header_image ) ) ?>

 *
 * @package Parament
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Rework this function to remove WordPress 3.4 support when WordPress 3.6 is released.
 *
 * @uses parament_header_style()
 * @uses parament_admin_header_style()
 * @uses parament_admin_header_image()
 *
 * @package Parament
 */
function parament_custom_header_setup() {
	$args = array(
		'default-image'          => '',
		'default-text-color'     => 'cccfd7',
		'width'                  => apply_filters( 'parament_header_image_width', 950 ),
		'height'                 => apply_filters( 'parament_header_image_height', 200 ),
		'wp-head-callback'       => 'parament_header_style',
		'admin-head-callback'    => 'parament_admin_header_style',
		'admin-preview-callback' => 'parament_admin_header_image',
	);

	$args = apply_filters( 'parament_custom_header_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-header', $args );
	} else {
		// Compat: Versions of WordPress prior to 3.4.
		define( 'HEADER_TEXTCOLOR',    $args['default-text-color'] );
		define( 'HEADER_IMAGE',        $args['default-image'] );
		define( 'HEADER_IMAGE_WIDTH',  $args['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $args['height'] );
		add_custom_image_header( $args['wp-head-callback'], $args['admin-head-callback'], $args['admin-preview-callback'] );
	}
}
add_action( 'after_setup_theme', 'parament_custom_header_setup' );

/**
 * Shiv for get_custom_header().
 *
 * get_custom_header() was introduced to WordPress
 * in version 3.4. To provide backward compatibility
 * with previous versions, we will define our own version
 * of this function.
 *
 * @todo Remove this function when WordPress 3.6 is released.
 * @return stdClass All properties represent attributes of the curent header image.
 *
 * @package Parament
 */

if ( ! function_exists( 'get_custom_header' ) ) {
	function get_custom_header() {
		return (object) array(
			'url'           => get_header_image(),
			'thumbnail_url' => get_header_image(),
			'width'         => HEADER_IMAGE_WIDTH,
			'height'        => HEADER_IMAGE_HEIGHT,
		);
	}
}

if ( ! function_exists( 'parament_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see parament_custom_header_setup().
 */
function parament_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == $header_text_color )
		return;

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php if ( '' != get_header_image() ) : ?>
		#branding {
			overflow: hidden;
			position: relative;
			width: <?php echo get_custom_header()->width; ?>px;
			height: <?php echo get_custom_header()->height; ?>px;
		}
		#site-title,
		#site-description {
			position: relative;
			margin-left: 50px;
			z-index: 2;
		}
		#site-title {
			margin-top: 60px;
		}
		#site-description {
			display: block;
		}
		#header-image {
			display: block;
			position: absolute;
			top: 0;
			left: 0;
			width: <?php echo get_custom_header()->width; ?>px;
			height: <?php echo get_custom_header()->height; ?>px;
			z-index: 1;
		}
	<?php endif;

		// Has the text been hidden?
		if ( 'blank' == $header_text_color ) :
	?>
		#site-title,
		#site-description {
			position: absolute;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-title a,
		#site-description,
		#site-description a {
			color: #<?php echo $header_text_color; ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // parament_header_style

if ( ! function_exists( 'parament_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see parament_custom_header_setup().
 */
function parament_admin_header_style() {
	$background_image = get_background_image();

	if ( empty( $background_image ) )
		$background_image = get_template_directory_uri() . '/images/diagonal-stripes-010.png';
	?>
		<style type="text/css">
		#headimg {
			border: 1px solid #eee;
			overflow: hidden;
			padding: 0 20px 40px;
			position: relative;
			max-width: 950px;
			background-color: <?php echo parament_sanitize_color( get_background_color(), '20232d' ); ?>;
			background-image: url( <?php echo esc_url( $background_image ); ?> );
		}
		#name,
		#desc {
			color: #<?php echo get_header_textcolor(); ?>;
			font-family: Trebuchet, arial, sans-serif !important;
			position: relative;
			text-shadow: #000 1px 1px 2px;
			z-index: 2 !important;
		}
		#name {
			font-weight: normal;
			font-size: 40px;
			line-height: 47px;
			margin: 55px 0 0 50px;
		}
		#desc {
			font-size: 16px;
			line-height: 1.5;
			margin: 0 0 20px 50px;
		}
	</style>
<?php
}
endif; // parament_admin_header_style

if ( ! function_exists( 'parament_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see parament_custom_header_setup().
 */
function parament_admin_header_image() {
	$tagline      = get_bloginfo( 'description' );
	$header_image = get_header_image();
?>
	<div id="headimg">
		<?php
		printf( '<h1 id="name">%s</h1>', get_option( 'blogname' ) );

		if ( ! empty( $tagline ) )
			printf( '<h2 id="desc">%s</h2>', $tagline );

		if ( ! empty( $header_image ) )
			printf( '<img id="parament-header-image" src="%s" alt="" />', esc_url( $header_image ) );
		?>
	</div>
<?php
}
endif; // parament_admin_header_image
