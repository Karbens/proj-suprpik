<?php
/*if ($_SERVER['PHP_AUTH_USER'] != "super" || $_SERVER['PHP_AUTH_PW'] != "su12er!!")
{
 	header('WWW-Authenticate: Basic realm="Protected Page: Enter Username and Password"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}*/
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require('./wp-blog-header.php');
