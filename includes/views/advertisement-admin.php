<?php
//html of advertisement of image
global $wpdb;
?>
<h2>Advertisement image</h2>
<div id="advertisement_image_div">
	<?php
// Get WordPress' media upload URL
	$upload_link = esc_url( get_upload_iframe_src( 'image', $user->ID ) );

// See if there's a media id already saved as post meta
	$adv_img_id = $wpdb->get_results( "SELECT image_id FROM wp_advertisement_images WHERE user_id=".$user->ID."", OBJECT );
	if($adv_img_id){
		$adv_img_id = $adv_img_id[0]->image_id;
	}

// Get the image src
	$adv_img_src = wp_get_attachment_image_src( $adv_img_id, 'full' );

// For convenience, see if the array is valid
	$adv_img = is_array( $adv_img_src );
	?>

	<!-- Your image container, which can be manipulated with js -->
	<div class="custom-img-container">
		<?php if ( $adv_img ) : ?>
			<img src="<?php echo $adv_img_src[ 0 ] ?>" alt="" style="max-width:100%;" />
		<?php endif; ?>
	</div>

	<!-- Your add & remove image links -->
	<p class="hide-if-no-js">
		<a class="upload-custom-img <?php
		if ( $adv_img ) {
			echo 'hidden';
		}
		?>" 
		   href="<?php echo $upload_link ?>">
			   <?php _e( 'Set custom image' ) ?>
		</a>
		<a class="delete-custom-img <?php
		if ( !$adv_img ) {
			echo 'hidden';
		}
		?>" 
		   href="#">
			   <?php _e( 'Remove this image' ) ?>
		</a>
	</p>

	<!-- A hidden input to set and post the chosen image id -->
	<input class="custom-img-id" name="custom-img-id" type="hidden" value="<?php echo esc_attr( $adv_img_id ); ?>" />
</div>