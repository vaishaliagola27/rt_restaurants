<?php
//timing column in backend

foreach ($current_post_timing as $key => $day) {
?>
<p> 
	<?php echo $days[$key] ?> 
</p>
<?php if ($day['am'] == NULL && $day['pm'] == NULL) { ?>
	<p>Close</p>
<?php } else {
	?>
	<span id="<?php echo $key ?>-am"><?php echo $current_post_timing[$key]['am'] ?></span>AM To 
	<span id="<?php echo $key ?>-pm"><?php echo $current_post_timing[$key]['pm'] ?></span>PM
<?php
}
}
