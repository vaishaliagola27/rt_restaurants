<?php
//review field html

//output buffer
ob_start();
?>
<p class="comment-form-comment">
	<label for="Review">
		<?php echo _x('Review', 'noun') ?>
	</label>
	<textarea id="review_area" name="comment" cols="20" rows="5" width=50% aria-required="true" required="required"></textarea>
</p>

<?php
//clean output buffer and return its value
return ob_get_clean();
