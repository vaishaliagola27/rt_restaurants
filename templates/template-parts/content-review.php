<?php
//Reviews display at front end
// Output buffer starts
ob_start();

$GLOBALS['comment'] = $review;
extract($args, EXTR_SKIP);
if ('div' == $args['style']) {
	$tag = 'div';
	$add_below = 'comment';
} else {
	$tag = 'li';
	$add_below = 'div-comment';
}
?>
<fieldset id="div-comment-<?php comment_ID() ?>" class="comment-body" itemprop="review" itemscope itemtype="http://schema.org/Review">
	<legend class="comment-author" itemprop="author">
		<!-- display avatar of reviewer -->
<?php if ($args['avatar_size'] != 0) echo get_avatar($review, $args['avatar_size']); ?>
		<?php echo get_comment_author_link(); ?>
	</legend>

<?php if ($review->comment_approved == '0') : ?>
		<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.'); ?></em>
		<br />
<?php endif; ?>

	<div class="comment-meta commentmetadata" itemprop="datePublished">
<?php
/* translators: 1: date, 2: time */
printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time());
?></a><?php edit_comment_link(__('(Edit)'), '  ', '');
?>
	</div>

	<div itemprop="description">
<?php echo $review->comment_content; ?>
	</div>

<?php

// fetching rating value for review
$commentrating = get_comment_meta(get_comment_ID(), 'rating', true);
$star_url = \rtCamp\WP\rtRestaurants\URL . 'assets/images/';

//Rating display
?>

	<p class="comment-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
		<img src="<?php echo $star_url . $commentrating . 'star.png'; ?>" />
		<br/>
		Rating: 
		<strong itemprop="ratingValue">
<?php echo $commentrating; ?>
			/ <span itemprop="bestRating">5</span>
		</strong>
	</p>

	<div class="reply">
<?php comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
	</div>
</fieldset>
<?php
// Store output buffer into variable and clean it
$ob_review_all = ob_get_clean();

/**
 * Allow to change review display
 *
 *  change display of reviews by using this filter. Add output string into $ob_review_all variable.
 *
 * @since 0.1
 *
 * @param string  $var 
 * @param string $ob_review_all  
 */
$ob_review_all = apply_filters('rt_restaurant_review_display', $ob_review_all);


/**
 * Action to display extra fields
 * 
 * @param string $ob_review_all
 */
do_action('rt_restaurants_review_display', $ob_review_all);

echo $ob_review_all;
