<?php 
//timing meta box
?>
<form name="restaurant_timing" method="post">
	<?php
	//nonce field for timing 
	wp_nonce_field('rt_restaurant_timing_nonce', 'restaurant_timing_nonce', false);
	?>

	<table style="font-size: 12px;margin:auto">
		<!-- tooltip for close day preview -->
		<tr>
			<td colspan="3">
				<div class="tooltiptext">
					For close day of restaurant, leave time blank.
				</div>
				<span class="info-timing">help?</span>
			</td>
		</tr>
		<tr style="text-align: center;font-size: 12px; font-weight: bold">
			<td>Day</td>
			<td>From</td>
			<td>To</td>
		</tr>
		<?php
		//Get data if available for current post
		$time = get_post_meta($post->ID, '_timing', true);

		$days = array("mon" => "Monday", "tue" => "Tuesday", "wed" => "Wednesday", "thu" => "Thursday", "fri" => "Friday", "sat" => "Saturday", "sun" => "Sunday");
		foreach ($days as $key => $day) {
			$open = $close = NULL;

			// Check if time is not already set for restaurant
			if (!empty($time) && is_array($time)) {
				if ($time[$key][0] != NULL) {
					$open = $time[$key][0];
				}
				if ($time[$key][1] != NULL) {
					$close = $time[$key][1];
				}
			}
			?>
			<tr>
				<td name=" <?php echo $day ?> "> <?php echo $day ?> </td>
				<td>
					<input type="text" name="<?php echo "time[" . $key . "][0]"; ?>" size="5" value="<?php echo $open; ?>" class="timepicker">
				</td>
				<td>
					<input type="text" name="<?php echo "time[" . $key . "][1]"; ?>" size="5" value="<?php echo $close; ?>" class="timepicker">
				</td>
			</tr>
			<?php
		}
		?>
	</table>
</form>
<?php
