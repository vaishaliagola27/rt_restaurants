<?php
//html for related restaurant meta box

//fetch data
$rel_res = get_post_meta( $post->ID, '_related_restaurant', true );
$rel_val = NULL;
if ( !empty( $rel_res ) ) {
	$rel_val = implode( ",", $rel_res );
	$rel_val .= ",";
}
?>
<label>Related Restaurants</label>
<input id="related_restaurants_tag" size="20" name="related_restaurants" value="<?php echo $rel_val ?>">

<?php
