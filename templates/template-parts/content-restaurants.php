<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//register script of restaurant
wp_register_script('restaurant-js', get_template_directory_uri() . '/js/restaurants.js');

if (is_singular('restaurants')) {
	global $post;
	?>
	<article class="main-content" itemscope itemtype="http://schema.org/Restaurant">
		<header class="entry-header"> 
			<!-- Display Restaurant title -->
			<div id="restaurant-title"><?php echo get_post($post->ID)->post_title ?></div>

			<!-- Display aggregate rating for restaurant  -->
			<div id="ratting" itemprop="ratingValue">
				<?php
				$rating = get_post_meta($post->ID, '_average_rating', true);
				if (!empty($rating) || $rating != NULL) {
					$star_url = \rtCamp\WP\rtRestaurants\URL . 'assets/images/';
					echo "<img src=\"" . $star_url . intval($rating) . "star.png\" />";
				}
				
				//Action to add into rating star display
				do_action( 'rt_restaurants_average_rating_display');
				?>
			</div>
		</header>
		<section class="content">
			<div class="content-left">
				<!--             address, contact number, restaurant type, food type -->
				<div class="left">

					<!-- Display address  -->
					<div class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
						<?php
						// Output buffer starts
						ob_start();
						//get address of the restaurant
						$current_post_address = get_post_meta($post->ID, '_restaurant_address', true);
						
						$addr = array("streetAddress", "addressLocality", "addressRegion", "postalCode", "addressCountry");
						?>
						<p class='labels'>
							Address 
						</p>
						<div itemprop = "address" itemscope itemtype = "http://schema.org/PostalAddress">
							<?php
							// Loop for retrive address fields from address array
							$address = '';
							foreach ($addr as $key) {
								?>
								<span itemprop = "<?php echo $key ?>"> <?php echo $current_post_address[$key];
						$address .= "," . $current_post_address[$key];
								?>
									<br />
								</span>
								<?php
							}
							?>
						</div>
						<!-- store address into hidden field for google map display -->
						<input type="hidden" value="<?php echo $address; ?>" id="address_value"/>
						<?php
						// Empty output buffer and store it into variable
						$ob_restaurant_address = ob_get_clean();

						/**
						 *  Filter for changing address html
						 *
						 *   This filter will allow you to customize the look of address post meta
						 * 
						 * @since 0.1
						 *
						 * @param string  $var   Filter name
						 * @param string  $ob_restaurant_address  output string of address html
						 */
						$ob_restaurant_address = apply_filters('rt_restaurant_address_html', $ob_restaurant_address);

						
						/**
						 * Action to add fields in address display
						 * 
						 * @param string  $ob_restaurant_address  output string of address html
						 */
						do_action('rt_restaurants_address_frontend_display',$ob_restaurant_address);
						
						echo $ob_restaurant_address;
						?>
					</div>

					<!-- Display Contact Number -->
					<div class="contact">
						<?php $phone_no = get_post_meta($post->ID, '_restaurant_contactno', true); ?>
						<label class="labels">Contact Us:</label>
						<span itemprop="telephone">
							<a href="tel://<?php echo $phone_no ?>"><?php echo $phone_no ?></a>
							<?php
							
							/**
							 * Action to add fields in contact number display
							 * 
							 * @param string $phone_no
							 */
							do_action('rt_restaurants_contactno_frontend_display',$phone_no);
							?>
						</span>
					</div>

					<!-- Display Restaurant type -->
					<div class="restaurant-type">
						<p class='labels' >Restaurant Type</p>
						<p>
							<?php
							// Output buffer starts
							ob_start();
							//get restaurant type
							$terms = wp_get_post_terms($post->ID, 'restaurants_type', '');
							if (!is_wp_error($terms) && $terms) {
								$term_text = '';
								foreach ($terms as $term) {
									$term_text .=$term->name . "<br />\n";
								}
								echo $term_text;
							}
							// Empty output buffer dat into variable 
							$ob_restaurant_type = ob_get_clean();

							/**
							 *  Filter for changing restaurant type html
							 *
							 *   This filter will allow you to customize the look of restaurant type taxonomy.
							 * 
							 * @since 0.1
							 *
							 * @param string  $var     Filter name
							 * @param string  $ob_restaurant_type  output string of restaurant type html
							 */
							$ob_restaurant_type = apply_filters('rt_restaurant_type_html', $ob_restaurant_type);

							/**
							 * Action to add fields in restaurant type display
							 * 
							 * @param string  $ob_restaurant_type  output string of restaurant type html
							 */
							do_action('rt_restaurants_type_address_frontend_display', $ob_restaurant_type);
							
							echo $ob_restaurant_type;
							?>
						<p>
					</div>

					<!-- Display Food Type -->
					<div class="food-type">
						<p class='labels' >Food Type</p>
						<?php
						// Output buffer starts 
						ob_start();
						//get food type for restaurant
						$terms = wp_get_post_terms($post->ID, 'food_type', '');
						if (!is_wp_error($terms) && $terms) {
							$term_text = "<ul>";
							foreach ($terms as $term) {
								$term_text .="<li>" . $term->name . "</li>";
							}
							$term_text.="</ul>";
							echo $term_text;
						}
						// Empty output buffer dat into variable 
						$ob_food_type = ob_get_clean();

						/**
						 *  Filter for changing food type html
						 *
						 *   This filter will allow you to customize the look of food type taxonomy.
						 * 
						 * @since 0.1
						 *
						 * @param string  $var     Filter name
						 * @param string  $ob_food_type  output string of food type html
						 */
						$ob_food_type = apply_filters('rt_restaurant_food_type_html', $ob_food_type);

						/**
						 * Action to add fields in food type display
						 * 
						 * @param string  $ob_food_type  output string of food type html
						 */
						do_action('rt_restaurants_food_type_frontend_display', $ob_food_type);
						
						echo $ob_food_type;
						?>
					</div>

				</div>
				<!--             Google Map for address and timing-->
				<div class="right">
					<div id="map"></div>

					<!-- Display Restaurant timing and close days -->
					<div class="restaurant-timing" itemprop="openingHours">
						<?php
						// Output buffer starts
						ob_start();
						
						//get current restaurant timing
						$current_post_timing = get_post_meta($post->ID, '_timing', true);
						$days = array("mon" => "Monday", "tue" => "Tuesday", "wed" => "Wednesday", "thu" => "Thursday", "fri" => "Friday", "sat" => "Saturday", "sun" => "Sunday");
						?>

						<p class="labels">Restaurant Timing</p>
						<table class="timing_table">
							<tr id="timing_title">
								<td>Day</td>
								<td>From</td>
								<td>To</td>
							</tr>
							<?php
							foreach ($current_post_timing as $key => $day) {
								?>
								<tr class='timing_data'>
									<td> <?php echo $days[$key] ?> </td>
									<?php if ($day['am'] == NULL && $day['pm'] == NULL) { ?>
										<td colspan='3' class='close'>Close</td>
									<?php } else {
										?>
										<td> <?php echo $current_post_timing[$key]['am'] ?>AM</td>
										<td> <?php echo $current_post_timing[$key]['pm'] ?>PM</td>
								<?php } ?>
								</tr>
								<?php
							}
							?>
						</table>
						<?php
						// Empty output buffer dat into variable 
						$ob_timing = ob_get_clean();

						/**
						 *  Filter for changing restaurant time html.
						 *
						 *   This filter will allow you to customize the look of restaurant time post meta.
						 * 
						 * @since 0.1
						 *
						 * @param string  $var     Filter name
						 * @param string  $ob_timing  output string of restaurant timing html
						 */
						$ob_timing = apply_filters('rt_restaurant_timing_table_html', $ob_timing);

						/**
						 * Action to add fields in restaurant timing display
						 * 
						 * @param string  $ob_timing  output string of restaurant timing html
						 */
						do_action('rt_restaurants_timing_frontend_display', $ob_timing);
						
						echo $ob_timing;
						?>
					</div>

				</div>
			</div>

			<!--         Slide show -->
			<div class="content_right">
				<p class="labels" >Gallery</p>
				<div class="image-gallery">
					<?php
					// Output buffer starts
					ob_start();
					/**
					 * Image gallery display
					 */
					$args = array(
					    'post_type' => 'attachment',
					    'numberposts' => -1,
					    'post_status' => null,
					    'post_parent' => $post->ID
					);

					$attachments = get_posts($args);
					if ($attachments) {
						foreach ($attachments as $attachment) {
							?>
							<div id="gallery-image"> 
							<?php echo wp_get_attachment_image($attachment->ID, 'full'); ?>
							</div>
							<?php
						}
					}
					// Empty output buffer dat into variable 
					$ob_gallery = ob_get_clean();

					/**
					 * Filter for changing restaurant image gallery html.
					 *
					 *   This filter will allow you to customize the look of restaurant image gallery.
					 * 
					 * @since 0.1
					 *
					 * @param string  $var     Filter name
					 * @param string  $ob_gallery  output string of restaurant image gallery html
					 */
					$ob_gallery = apply_filters('rt_restaurant_gallery_html', $ob_gallery);

					echo $ob_gallery;
					?>
				</div>
			</div>
		</section>
	</article>
	<?php
}