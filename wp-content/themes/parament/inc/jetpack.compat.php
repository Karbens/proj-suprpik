<?php
/**
 * Compatibility settings and functions for Jetpack.
 * See http://jetpack.me/support/infinite-scroll/
 *
 * @package Parament
 */

/**
 * Add support for Infinite Scroll.
 */
function parament_infinite_scroll_init() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'footer'    => 'container',
	) );
}
add_action( 'after_setup_theme', 'parament_infinite_scroll_init' );