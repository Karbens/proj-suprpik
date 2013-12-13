<?php
/**
 * @package Parament
 */

do_action( 'before_sidebar' ); ?>
<ul id="sidebar" role="complementary">
	<?php //if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

	<?php /*the_widget( 'WP_Widget_Recent_Posts', array( 'number' => 10 ), array(
		'before_widget' => '<li id="recent-posts" class="widget widget_recent_entries">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widget-title2">',
		'after_title'   => '</h2>'
	) ); */ ?>

	<?php /*the_widget( 'WP_Widget_Recent_Comments', array( 'number' => 5 ), array(
		'before_widget' => '<li id="recent-comments" class="widget widget_recent_comments">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>'
	) ); */ ?>

	<?php /*the_widget( 'WP_Widget_Meta', array(), array(
		'before_widget' => '<li id="meta" class="widget widget_meta">',
		'after_widget'  => '</li>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>'
	) ); */ ?>

	<?php //endif; ?>

	

	<li>
	<a class="twitter-timeline"  href="https://twitter.com/superpicks_com"  data-widget-id="385132673678577664">Tweets by @superpicks_com</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>	</li>
	
	<li>
	<br>
	<?php $_banner = 'side1'; include('banners_inc.php'); ?>
	</li>

	<li id="recent-posts" class="widget widget_recent_entries">

		<ul>
		<?php $the_query = new WP_Query( 'showposts=10' ); ?>
		<?php while ($the_query -> have_posts()) : $the_query -> the_post(); ?>
		<li><a class="imageLink" href="<?php the_permalink() ?>">
			<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'recent-thumb' ); 
				}
			?>
		</a>
		<a href="<?php the_permalink() ?>">

			<?php the_title(); ?></a>
			<div class="timeAgo">
<?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></div>
		</li>
		<?php endwhile;?>
		</ul>
	</li>
	
</ul><!-- end sidebar -->