<?php
//timing column in backend

foreach ($current_post_timing as $key => $day) {
?>
<p> 
	<?php echo $days[$key] ?> 
</p>
<?php if ($day[0] == NULL && $day[1] == NULL) { ?>
	<p>Close</p>
<?php } else {
	?>
	<span id="<?php echo $key ?>-am"><?php echo $current_post_timing[$key][0] ?></span> To 
	<span id="<?php echo $key ?>-pm"><?php echo $current_post_timing[$key][1] ?></span>
<?php
}
}
