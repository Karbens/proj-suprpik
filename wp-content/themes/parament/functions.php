<?php
/**
 * @package Parament
 */

if ( ! isset( $content_width ) )
	$content_width = 627;

if ( ! function_exists( 'parament_setup' ) ) :
/**
 * Setup for Parament Theme.
 */
function parament_setup() {

	load_theme_textdomain( 'parament', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );

	register_nav_menu( 'primary-menu', __( 'Primary', 'parament' ) );
}
endif;
add_action( 'after_setup_theme', 'parament_setup' );

/**
 * Setup the WordPress core custom background feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * Hooks into the after_setup_theme action.
 *
 * @since Parament 1.3
 */
function parament_register_custom_background() {
	$args = array(
		'default-color' => '',
		'default-image' => '',
	);

	$args = apply_filters( 'parament_custom_background_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-background', $args );
	} else {
		define( 'BACKGROUND_COLOR', $args['default-color'] );
		define( 'BACKGROUND_IMAGE', $args['default-image'] );
		add_custom_background();
	}
}
add_action( 'after_setup_theme', 'parament_register_custom_background' );

/**
 * Register Sidebars.
 */
function parament_register_sidebars() {
	register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'parament' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'parament_register_sidebars' );

if ( ! function_exists( 'parament_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own parament_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function parament_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><span class="pingback-title"><?php _e( 'Pingback:', 'parament' ); ?></span> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'parament' ), ' <span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="contain">
			<footer class="comment-meta contain vcard">

				<?php echo get_avatar( $comment, 40 ); ?>

				<div class="comment-author">
				<?php
						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s said:', 'parament' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'parament' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'parament' ), ' <span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'parament' ); ?></em>
				<?php endif; ?>

			</footer><!-- .vcard -->

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply contain">
				<?php comment_reply_link( array_merge( $args, array(
					'reply_text' => __( 'Reply', 'parament' ),
					'depth'      => $depth,
					'max_depth'  => $args['max_depth']
				) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif;

/**
 * Sanitizes a hex color string.
 *
 * @param string $color The hex color string to test.
 * @param string $default Optional. Default hex color.
 *
 * @return string Sanitizes hex color string with prepended '#'.
 */
function parament_sanitize_color( $color, $default = '20232d' ) {
	$color = ltrim( $color, '#' );

	return ( ctype_xdigit( $color ) && preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) ? '#' . $color : '#' . $default;
}

/**
 * Enqueue scripts and styles
 */
function parament_scripts() {
	wp_enqueue_style( 'parament', get_stylesheet_uri() );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'parament_scripts' );

/**
 * Custom class attributes for the "Branding" header.
 *
 * If present, Parament will add a drop shadow to the
 * user-defined custom header image. This shadow should
 * not be present when no header image is used.
 *
 * @since Parament 1.3
 * @param array $classes Body classes.
 *
 * @return array
 */
function parament_body_class( $classes ) {

	if ( get_header_image() )
		$classes[] = 'has-image';

	return $classes;
}
add_filter( 'body_class', 'parament_body_class' );

/**
 * Adds a custom class when entry meta is displayed.
 *
 * @since Parament 1.3
 * @param array $classes Post classes.
 *
 * @return array
 */
function parament_post_class( $classes ) {

	if ( '' != get_edit_post_link() || is_singular() || is_attachment() )
		$classes[] = 'has-byline';

	return $classes;
}
add_filter( 'post_class', 'parament_post_class' );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since Parament 1.2
 */
function parament_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'parament' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'parament_wp_title', 10, 2 );


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.compat.php';



/**
 * Load WP.com compatibility file.
 */
if ( file_exists( get_template_directory() . '/inc/wpcom.php' ) )
	require get_template_directory() . '/inc/wpcom.php';

/**
 * This function was once used in header.php to output a class name.
 * @deprecated
 */
function parament_header_classes() {
	_deprecated_function( __FUNCTION__, '1.3' );

	$image = get_header_image();
	if ( ! empty( $image ) )
		echo ' class="has-image"';
}

//hide admin bar in front
show_admin_bar( false );

//featured image
add_theme_support( 'post-thumbnails' );

if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'recent-thumb', 70, 70 ); //(cropped)
}