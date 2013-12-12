<?php
/**
  * Template Name: SP User
 */
	if(is_user_logged_in()){
		$sp_title = "My Account";
	}
	else if(strtolower($post->post_name)=='sign-up'){
		$sp_title = "Sign Up";
	} else if(strtolower($post->post_name)=='activate-account'){
		$sp_title = "Activate Account";
	} else if(strtolower($post->post_name)=='forgot-password'){
		if(isset($_GET['resetpass']) && $_GET['resetpass']=='true'){
			$sp_title = "Reset Password";
		}else{
			$sp_title = "Forgot Password";
		}
	} else {
		$sp_title = "Sign In";
	}

get_header(); ?>

<div id="container" class="contain">

	</br></br>
	<div id="main_page_ad"><?php $_banner = 'main'; include('banners_inc.php'); ?></div>

	<div id="main" role="main">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="title">
				<h2 class="entry-title"><?php echo isset($sp_title)? $sp_title : the_title('','',false); ?></h2>
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
			<?php endwhile; ?>
		<?php else : ?>
			<?php get_template_part( 'content', '404' ); ?>
		<?php endif; ?>
		<?php //comments_template(); ?>
	</div><!-- end main -->

	<?php get_sidebar(); ?>

</div><!-- end container -->

<?php get_footer(); ?>