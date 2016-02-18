<?php
//rating form
?>
<p class="comment-form-rating">
	<label for="rating"><?php echo __('Rating') ?>
		<span class="required">*</span>
	</label>
	<span class="commentratingbox">
		<?php
		
		for ($i = 1; $i <= 5; $i++) { ?>
			<span class="commentrating">
				<input type="radio" name="rating" id="rating" value="<?php echo $i ?>"/><?php echo $i ?>
			</span>
			<?php
		}
		?>
	</span>
	<?php
	//Action to add extra fields in review
	do_action( 'rt_restaurants_review_fields');
	?>
</p>
<?php
		