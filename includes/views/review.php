<?php
//review meta 
?>
<p>
	<label for="rating"><?php echo _e('Rating: '); ?></label>
	<span class="commentratingbox">
		<?php
		for ($i = 1; $i <= 5; $i++) {
			?>
			<span class="commentrating">
				<input type="radio" name="rating" id="rating" value="<?php echo $i ?>"
				       <?php
				       if ($rating == $i)
					       echo ' checked="checked"';
				       ?>
				       /><?php echo $i ?> </span>
				<?php
			}
			?>
	</span>
</p>
<?php
