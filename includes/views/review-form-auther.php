<?php
//review form auther html

//output buffer
ob_start();
?>
<p class="comment-form-author">
	<label for="author">
		<?php echo __('Name') ?>
	</label>
	<?php ($req ? '<span class="required">*</span>' : '' ) ?> 
	<input id="author" name="author" type="text" value="<?php echo esc_attr($commenter['comment_author']) ?>" size="30" <?php echo $aria_req ?> />
</p>

<?php
//clean output buffer and return its value
return ob_get_clean();