<?php
/**
 * @package Parament
 */
?>

<?php get_header(); ?>

<div id="container" class="contain">

	</br></br>
	<div id="main_page_ad"><?php $_banner = 'main'; include('banners_inc.php'); ?></div>

	<div id="main" role="main">

		<?php get_template_part( 'content', '404' ); ?>

	</div><!-- end main -->

	<?php get_sidebar(); ?>

</div><!-- end container -->

<?php get_footer(); ?>