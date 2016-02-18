<?php
global $wp_query;

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			printf( // WPCS: XSS OK.
				esc_html( _nx( 'Review on &ldquo;%2$s&rdquo;', '%1$s reviews on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', '_s' ) ), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>'
			);
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through?   ?>
			<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
				<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', '_s' ); ?></h2>
				<div class="nav-links">

					<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', '_s' ) ); ?></div>
					<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', '_s' ) ); ?></div>

				</div><!-- .nav-links -->
			</nav><!-- #comment-nav-above -->
		<?php endif; // Check for comment navigation.   ?>

		<ol class="comment-list">
			<?php
			//load comment template
			$args = array(
			    'callback' => array( '\rtCamp\WP\rtRestaurants\Review', 'reviews_html' ),
			    'type' => 'comment'
			);
			wp_list_comments( $args );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through?   ?>
			<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
				<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', '_s' ); ?></h2>
				<div class="nav-links">

					<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', '_s' ) ); ?></div>
					<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', '_s' ) ); ?></div>

				</div><!-- .nav-links -->
			</nav><!-- #comment-nav-below -->
			<?php
		endif; // Check for comment navigation.

	endif; // Check for have_comments().
	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( !comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>

		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', '_s' ); ?></p>
		<?php
	endif;

	comment_form();
	?>

</div><!-- #comments -->
