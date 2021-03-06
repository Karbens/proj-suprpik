<?php
/**
 * @package Parament
 */

get_header(); ?>

<div id="container" class="contain">

	</br></br>
	<div id="main_page_ad"><?php $_banner = 'main'; include('banners_inc.php'); ?></div>

	<div id="main" role="main">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<?php get_template_part( 'content', '404' ); ?>
		<?php endif; ?>
		<?php //comments_template(); ?>
	</div><!-- end main -->

	<?php get_sidebar(); ?>

</div><!-- end container -->

<?php get_footer(); ?>