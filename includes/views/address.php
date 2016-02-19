<?php
//Address metabox
?>
<table class="address_table">
	<tr>
	<div class="tooltiptext">
		Enter address details or choose from map. You can drag map pin also to choose address.
	</div>
</tr>
<tr>
<div class="error_address"></div>
</tr>
<?php
$address_map = '';
//display address values into it's fields
foreach ( $addr as $key => $value ) {
	if ( $add != NULL && !empty( $add ) ) {
		$value = $add[ $key ];
	} else {
		$value = '';
	}
	?>
	<tr>
		<td>
			<label> <?php echo $addr[ $key ]; ?></label>
		</td>
		<td>
			<input size="15" type="text" name="<?php echo "restaurant_add[" . $key . "]"; ?>" value="<?php echo empty( $value ) ? ' ' : $value; ?>" />
		</td> 
	</tr>
	<?php
	$address_map .= $value . ',';
}
?>
<!-- Map on admin side -->
<tr>
	<input type="hidden" name="address_val" id="map_address" value="<?php echo $address_map ?>" />
</tr>
</table>
<!-- google map for address -->
<div id="map"></div>
<?php
