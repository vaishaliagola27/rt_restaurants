<?php 
//timing meta box
?>
<form name="restaurant_timing" method="post">
	<?php
	//nonce field for timing 
	wp_nonce_field('rt_restaurant_timing_nonce', 'restaurant_timing_nonce', false);
	?>

	<table style="font-size: 12px;margin:auto">
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
			$am = $pm = NULL;

			// Check if time is not already set for restaurant
			if (!empty($time) && is_array($time)) {
				if ($time[$key]['am'] != NULL) {
					$am = $time[$key]['am'];
				}
				if ($time[$key]['am'] != NULL) {
					$pm = $time[$key]['pm'];
				}
			}
			?>
			<tr>
				<td name=" <?php echo $day ?> "> <?php echo $day ?> </td>
				<td><input type="text" name="<?php echo "time[" . $key . "][am]"; ?>" size="3" value="<?php echo $am; ?>">AM</td>
				<td><input type="text" name="<?php echo "time[" . $key . "][pm]"; ?>" size="3" value="<?php echo $pm; ?>">PM</td>
			</tr>
			<?php
		}
		?>
	</table>
</form>
<?php
