<?php
/**
 * @package Parament
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="title">
		<h2 class="entry-title"><?php the_title(); ?></h2>
		<?php //edit_post_link( __( 'Edit This', 'parament' ), '<div class="entry-byline">', '</div>' ); ?>
	</div><!-- end title -->

	<div class="entry-content">
		<?php
			the_content( __( 'Continue Reading', 'parament' ) );
			wp_link_pages( array(
				'after'       => '</div>',
				'before'      => '<div class="entry-navigation">',
				'link_after'  => '</span>',
				'link_before' => '<span>',
			) );
		?>
	</div>

</article>