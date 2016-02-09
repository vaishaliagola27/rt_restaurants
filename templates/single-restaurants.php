<?php
get_header();
?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
		<?php
		// Start the Loop.
		while (have_posts()) : the_post();

			//load content of restaurant
			include_once \rtCamp\WP\rtRestaurants\PATH . "/templates/template-parts/content-restaurants.php";

			do_action('rt_restaurants_add_fields');
			
			// Previous/next post navigation.
			the_post_navigation();

			// If comments are open or we have at least one comment, load up the comment template.
			if (comments_open() || get_comments_number()) {
				comments_template();
			}
		endwhile;
		?>
	</div><!-- #content -->
</div><!-- #primary -->

<?php
get_sidebar('content');
get_sidebar();
get_footer();
