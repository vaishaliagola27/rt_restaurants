<?php
//html for related restaurant meta box
add_action( 'wp_ajax_related_restaurants', 'related_restaurants' );

function related_restaurants() {
	$post_type = 'restaurants';
	$args = array(
	    'post_type' => $post_type
	);
	print_r($args);
	die;
	$posts = get_posts( $args );
	$id_title = array();
	foreach ( $posts as $key => $value ) {
		$id_title[] = array(
		    'id' => $posts[ 'ID' ],
		    'title' => $posts[ 'post_title' ]
		);
	}
	print_r($id_title);
	die;
	echo json_encode( $id_title );
	wp_die();
}

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
