<?php
//Address metabox
?>
<table class="address_table">
	<?php
	
	//display address values into it's fields
	foreach ($addr as $key => $value) {
		if ($add != NULL && !empty($add)) {
			$value = $add[$key];
		} else {
			$value = '';
		}
		?>
		<tr>
			<td>
				<label> <?php echo $addr[$key]; ?></label>
			</td>
			<td>
				<input size="15" type="text" name="<?php echo "restaurant_add[" . $key . "]"; ?>" value="<?php echo empty($value) ? ' ' : $value; ?>" />
			</td> 
		</tr>
		<?php
	}
	?>
</table>
<?php
