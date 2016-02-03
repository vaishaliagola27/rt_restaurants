<?php
get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php global $post; ?>
		<?php if (have_posts()) : ?>

			<header class="archive-header">
				<h1 class="archive-title"> 
					<?php echo ucfirst($post->post_type); ?>
				</h1>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while (have_posts()) : the_post();
				?><div class="archive-content">
				<?php
					the_title('<h2 class="archive-restaurant-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
					edit_post_link(
						sprintf(
							/* translators: %s: Name of current post */
							esc_html__('Edit %s', '_s'), the_title('<span class="screen-reader-text">"', '"</span>', false)
						), '<span class="archive-edit-link">', '</span>'
					);
					?>
				</div>
				<?php
			endwhile;

			the_posts_navigation();

		else :

			get_template_part('templates-parts/content', 'none');

		endif;
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
