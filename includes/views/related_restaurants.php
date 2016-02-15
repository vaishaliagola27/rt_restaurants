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
<input id="related_restaurants_tag" size="20" name="related_restaurants">
<input type="hidden" id="res_ids" value="<?php echo $rel_val ?>" name="related_restaurants_ids"/>
<div class="tagchecklist">
	<?php
	foreach ($rel_res as $id)
	{
		?><span><a> </a><?php echo get_the_title($id) ?></span> <?php
	}
	?>
	
</div>
<?php
