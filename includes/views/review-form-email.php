<?php
//review form email html 

//output buffer
ob_start();
?>
<p class="comment-form-email">
	<label for="email"><?php echo __('Email') ?></label>
	<?php ( $req ? '<span class="required">*</span>' : '' ) ?>
	<input id="email" name="email" type="text" value="<?php echo esc_attr($commenter['comment_author_email']) ?>" size="30" <?php $aria_req ?> />
</p>

<?php
//clean output buffer and return its value
return ob_get_clean();
