<?php

namespace rtCamp\WP\rtRestaurants;

if (!class_exists('Admin')) {

	/**
	 *  This class will allow change in front-end/Admin side.
	 * 
	 * @author Vaishali Agola <vaishaliagola27@gmail.com>
	 */
	class Admin {

		/**
		 * initialize hooks
		 */
		public function init() {

			//add meta boxes
			add_action('add_meta_boxes', array($this, 'add_all_meta_boxes'));

			//save meta box of address
			add_action('save_post', array($this, 'save_address'));

			//save contact number meta box value
			add_action('save_post', array($this, 'save_contactno'));

			//save timing meta box 
			add_action('save_post', array($this, 'save_timing'));

			//add comment/review default fields
			add_filter('comment_form_default_fields', array($this, 'custom_fields'));

			//action to store review meta data
			add_action('comment_post', array($this, 'save_comment_meta_data'));

			//filter added for verify review details
			add_filter('preprocess_comment', array($this, 'verify_comment_meta_data'));

			//action which extends review meta box for rating
			add_action('add_meta_boxes_comment', array($this, 'extend_comment_add_meta_box'));

			//action for edit meta fields
			add_action('edit_comment', array($this, 'extend_comment_edit_metafields'));

			/**
			 * photo gallery code for thumbnails
			 */
			add_theme_support('post-thumbnails', array('restaurants'));
			set_post_thumbnail_size(50, 50);
			add_image_size('single-post-thumbnail', 400, 9999);

			//for display column in list
			add_filter('manage_restaurants_posts_columns', array($this, 'add_restaurants_columns'));

			//for data into new columns
			add_action('manage_restaurants_posts_custom_column', array($this, 'manage_restaurants_columns'), 10, 2);

			//quick edit for new columns
			add_action('quick_edit_custom_box', array($this, 'display_custom_quickedit_restaurant'), 10, 2);

			//add script admin_edit
			add_action('admin_enqueue_scripts', array($this, 'wp_admin_enqueue_scripts'));
		}

		/**
		 * Adds meta boxes 
		 * 
		 * @since 0.1
		 */
		public function add_all_meta_boxes() {

			$meta_box_args = array(
			    'address' => array(
				'id' => 'restaurants-address',
				'title' => 'Address',
				'callback' => 'add_address_meta_box',
				'screen' => 'restaurants',
				'context' => 'side',
				'priority' => 'default'
			    ),
			    'contact' => array(
				'id' => 'restaurants-contactno',
				'title' => 'Contact no.',
				'callback' => 'add_contactno_meta_box',
				'screen' => 'restaurants',
				'context' => 'side',
				'priority' => 'default'
			    ),
			    'timing' => array(
				'id' => 'restaurants-timing',
				'title' => 'Timing & Working Days',
				'callback' => 'add_timing_meta_box',
				'screen' => 'restaurants',
				'context' => 'side',
				'priority' => 'default'
			    )
			);
			/**
			 * Filter to add one or more meta boxes
			 * 
			 * @since 0.1
			 *
			 * @param string $var    Filter name
			 * @param array $args
			 */
			$meta_box_args = apply_filters('rt_restaurants_add_meta_boxes', $meta_box_args);

			//add all meta boxes in array
			foreach ($meta_box_args as $key => $value) {
				add_meta_box(
					$value["id"], esc_html__($value["title"], $value["title"]), array($this, $value["callback"]), $value["screen"], $value["context"], $value["priority"]);
			}
		}

		/**
		 *  display/add meta box on restaurants post.
		 *
		 *  Adds meta box for restaurant address.
		 * 
		 * @since 0.1
		 * 
		 * @param array $post
		 */
		public function display_address($post) {
			// output buffer start
			ob_start();

			//nonce field for address meta box
			wp_nonce_field('rt_restaurant_address_nonce', 'restaurant_address_nonce', false);

			// Array for address fields
			$addr = array("streetAddress" => "Street Address", "addressLocality" => "Locality", "addressRegion" => "Region", "postalCode" => "Postal Code", "addressCountry" => "Country");

			// Retriving address post meta for particular post.
			$add = get_post_meta($post->ID, '_restaurant_address', true);
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
			// Get output buffer value into variable and clear output buffer
			$ob_address = ob_get_clean();

			/**
			 *  Filter for change post meta box display of address post meta.
			 *
			 * This filter allow user to change meta box of address post meta by passing output string.
			 * 
			 * @since 0.1
			 *
			 * @param string $var    Filter name
			 * @param string $ob_address
			 */
			$ob_address = apply_filters('rt_restaurant_address_html', $ob_address);
			return $ob_address;
		}

		public function add_address_meta_box($post) {
			echo $this->display_address($post);
		}

		/**
		 *  Saves or Update address postmeta 
		 *
		 *  save or update post meta of restaurant address.
		 * 
		 * @since 0.1
		 * 
		 * @param int $post_id  
		 * 
		 * @var array   $address    address of restaurant
		 */
		public function save_address($post_id) {
			//check for empty post id
			if (empty($post_id)) {
				$post_id = $_POST['post_ID'];
			}
			if (empty($post_id)) {
				return;
			}

			//verify nonce for address
			$address_nonce = !empty($_POST['restaurant_address_nonce']) ? $_POST['restaurant_address_nonce'] : '';
			if (empty($address_nonce)) {
				return;
			}

			if (!wp_verify_nonce($_POST['restaurant_address_nonce'], 'rt_restaurant_address_nonce')) {
				return;
			}

			//fetch value of address
			$address = isset($_POST['restaurant_add']) ? $_POST['restaurant_add'] : '';
			if (empty($address)) {
				return;
			}

			//sanitize address values
			foreach ($address as $key => $value) {
				$address[$key] = sanitize_text_field($value);
			}

			//add or update address post meta 
			update_post_meta($post_id, '_restaurant_address', $address);
		}

		/**
		 *  Saves or update contact number of restaurant
		 *  
		 *  Add or update post meta of contact number of restaurants.
		 * 
		 * @since 0.1
		 * 
		 * @param int $post_id
		 * 
		 * @var int $contactno  contact number of restaurant
		 */
		public function save_contactno($post_id) {
			//check for empty post id
			if (empty($post_id)) {
				$post_id = $_POST['post_ID'];
			}

			if (empty($post_id)) {
				return;
			}

			//verify nonce 
			$contactno_nonce = !empty($_POST['restaurant_contactno_nonce']) ? $_POST['restaurant_contactno_nonce'] : '';

			if (empty($contactno_nonce)) {
				return;
			}

			if (!wp_verify_nonce($_POST['restaurant_contactno_nonce'], 'rt_restaurant_contactno_nonce')) {
				return;
			}

			//Fetch contact number post meta 
			$contact_no = isset($_POST['restaurant_contact_no']) ? $_POST['restaurant_contact_no'] : '';
			if (empty($contact_no)) {
				return;
			}
			//sanitize contact number data
			$contact_no = sanitize_text_field($contact_no);

			//Adds or updates contact number post meta
			update_post_meta($post_id, '_restaurant_contactno', $contact_no);
		}

		/**
		 * display/add meta box on restaurants post
		 * 
		 * Add contact number meta box into restaurant post type.
		 * 
		 * @since 0.1
		 * 
		 * @param array $post
		 * 
		 */
		public function display_contactno($post) {
			// Output buffering start
			ob_start();

			//nonce field for contact number
			wp_nonce_field('rt_restaurant_contactno_nonce', 'restaurant_contactno_nonce', false);

			$restaurant_contact = "";

			// Retriving contact number of restaurant
			$val = get_post_meta($post->ID, '_restaurant_contactno', true);

			// Check if contact number is already exists for restaurant
			if ($val != NULL && !empty($val)) {
				$restaurant_contact = $val;
			} 
			?>
			<input type='text' id='contact-no' value='<?php echo $restaurant_contact?>' name='restaurant_contact_no' />
			<?php
			// Storing output buffer value into variable and clean output buffer.
			$ob_contactno = ob_get_clean();

			/**
			 *  Filter for Change in contact number meta box
			 *
			 *  Filter to change display of post meta contact number
			 * 
			 * @since 0.1
			 *
			 * @param string $var    Name of filter
			 * @param string $ob_contactno 
			 */
			$ob_contactno = apply_filters('rt_restaurant_contactno_html', $ob_contactno);

			return $ob_contactno;
		}

		/**
		 * display contact number meta box
		 * 
		 * @param \rtCamp\WP\rtRestaurants\type $post
		 */
		public function add_contactno_meta_box($post) {
			echo $this->display_contactno($post);
		}

		/**
		 * display timing meta box
		 * 
		 * @param \rtCamp\WP\rtRestaurants\type $post
		 */
		public function add_timing_meta_box($post) {
			echo $this->display_timing($post);
		}

		/**
		 *  add timing meta box on restaurant post display
		 *
		 * @since 0.1
		 * 
		 * @param int $post
		 * 
		 */
		public function display_timing($post) {
			// Output buffer starts
			ob_start();
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
			// Storing output buffer data into variable
			$ob_timing_working_days = ob_get_clean();

			/**
			 *  Filter for display change of working days meta box.
			 *
			 * filter to change display of working days and time on admin side by passing html text as arguments. 
			 *
			 * @since 0.1
			 *
			 * @param string $var    name of filter
			 * @param string $ob_timing_working_days
			 */
			$ob_timing_working_days = apply_filters('rt_restaurant_timing_working_days_html', $ob_timing_working_days);
			return $ob_timing_working_days;
		}

		/**
		 *  save timing postmeta for restaurant
		 * 
		 *  Function to save time and close days.
		 * 
		 * @since 0.1
		 * 
		 * @param int $post_id
		 */
		public function save_timing($post_id) {
			//check for empty post id
			if (empty($post_id)) {
				$post_id = $_POST['post_ID'];
			}

			if (empty($post_id)) {
				return;
			}

			//verify nonce
			$timing_nonce = !empty($_POST['restaurant_timing_nonce']) ? $_POST['restaurant_timing_nonce'] : '';

			if (empty($timing_nonce)) {
				return;
			}

			if (!wp_verify_nonce($_POST['restaurant_timing_nonce'], 'rt_restaurant_timing_nonce')) {
				return;
			}

			//fetch timing data 
			$time = isset($_POST['time']) ? $_POST['time'] : '';
			if (empty($time)) {
				return;
			}

			//Add or update timing post meta
			update_post_meta($post_id, '_timing', $time);
			// Computing close days
			$close_days = array();
			$i = 0;
			foreach ($time as $key => $day) {
				if ($day['am'] == '' && $day['pm'] == '') {
					$close_days[$i++] = ($key);
				}
			}
			/**
			 *  Filter for close day calculation
			 * 
			 * filter to make change in close day calculation code   
			 * 
			 * @since 0.1
			 *
			 * @param string $var           name of filter
			 * @param string $close_days    text change in close day calculation 
			 */
			$close_days = apply_filters('rt_restaurant_close_days', $close_days);

			//Add or update close day post meta
			update_post_meta($post_id, '_close_days', $close_days);
		}

		/**
		 *  add fields to review.
		 *
		 *  Function to add custom fields in comment of custom post.
		 * 
		 * @since 0.1
		 * 
		 */
		public function custom_fields() {
			$commenter = wp_get_current_commenter();
			$req = get_option('require_name_email');
			$aria_req = ( $req ? " aria-required='true'" : '' );
			//Add custom fields 
			$fields['author'] = '<p class="comment-form-author">' .
				'<label for="author">' . __('Name') . '</label>' .
				( $req ? '<span class="required">*</span>' : '' ) .
				'<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) .
				'" size="30" ' . $aria_req . ' /></p>';

			$fields['email'] = '<p class="comment-form-email">' .
				'<label for="email">' . __('Email') . '</label>' .
				( $req ? '<span class="required">*</span>' : '' ) .
				'<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) .
				'" size="30" ' . $aria_req . ' /></p>';

			/**
			 *  filter for custom fields of comment
			 *   
			 *  filter to add custom fields in comment of custom post
			 * 
			 * @since 0.1
			 *
			 * @param string  $var    name of filter
			 * @param array $fields    array of custom fields for comment
			 */
			$fields = apply_filters('rt_restaurant_custom_comment_fields', $fields);

			return $fields;
		}

		/**
		 *  Add amd Save the comment meta rating along with comment
		 *
		 *  add comment meta rating and save to comment. 
		 * 
		 * @since 0.1
		 * 
		 * @param int $comment_id
		 * 
		 */
		public function save_comment_meta_data($comment_id) {
			//check for rating value
			if (( isset($_POST['rating']) ) && ( $_POST['rating'] != ''))
				$rating = wp_filter_nohtml_kses($_POST['rating']);

			//Add or update rating
			add_comment_meta($comment_id, 'rating', $rating);

			$this->add_transient_rating($comment_id);
		}

		/**
		 * To check that rating is given or not
		 *  
		 * This function will check if reviwer has also give rating to restaurant.
		 * @since 0.1
		 * 
		 * @param array commentdata
		 */
		public function verify_comment_meta_data($commentdata) {
			if (!isset($_POST['rating']))
				wp_die(__('Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.'));
			return $commentdata;
		}

		/**
		 * Add an comment meta box of rating
		 *  
		 * Add comment meta box for rating of restaurant.
		 *
		 * @since 0.1
		 */
		public function extend_comment_add_meta_box() {
			add_meta_box('title', __('Comment Metadata - Extend Comment'), array($this, 'extend_comment_meta_box'), 'comment', 'normal', 'high');
		}

		/**
		 * Edit comment meta box. 
		 * 
		 * This function will extend comment of custom post type restaurants.
		 *  
		 * @since 0.1
		 * 
		 * @param array $comment
		 * 
		 */
		public function extend_comment_meta_box($comment) {
			// Output buffer starts
			ob_start();
			$rating = get_comment_meta($comment->comment_ID, 'rating', true);
			//nonce field
			wp_nonce_field('rt_extend_comment_update', 'extend_comment_update', false);
			?>
			<p>
				<label for="rating"><?php _e('Rating: '); ?></label>
				<span class="commentratingbox">
					<?php
					for ($i = 1; $i <= 5; $i++) {
						?>
						<span class="commentrating">
							<input type="radio" name="rating" id="rating" value="<?php echo $i?>"
						<?php
						if ($rating == $i)
							echo ' checked="checked"';
						?>
						/><?php echo $i?> </span>
						<?php
					}
					?>
				</span>
			</p>
			<?php
			// Store output buffer value into variable and clean it.
			$ob_rating_display_edit = ob_get_clean();

			/**
			 *  change display of rating
			 *  
			 *  change display of rating by this filter. output will store in $ob_rating_display_edit variable.
			 *
			 * @since 0.1
			 *
			 * @param string  $var .
			 * @param string $ob_rating_display_edit 
			 */
			$ob_rating_display_edit = apply_filters('rt_restaurant_rating_display_edit_html', $ob_rating_display_edit);
			echo $ob_rating_display_edit;
		}

		/**
		 * Update comment meta data from comment editing screen 
		 *  
		 * add or update new comment.
		 * 
		 * @since 0.1
		 *
		 * @param int $comment_id
		 * 
		 */
		public function extend_comment_edit_metafields($comment_id) {
			if (!isset($_POST['extend_comment_update']) || !wp_verify_nonce($_POST['extend_comment_update'], 'rt_extend_comment_update'))
				return;

			if (( isset($_POST['rating']) ) && ( $_POST['rating'] != '')):
				$rating = wp_filter_nohtml_kses($_POST['rating']);
				update_comment_meta($comment_id, 'rating', $rating);
			else :
				delete_comment_meta($comment_id, 'rating');
			endif;
			$this->add_transient_rating($comment_id);
		}

		/**
		 *  Set transient to store ratting and postmeta to store average
		 *
		 *  create transient to store ratting total and total count of comment. It also create or update 
		 *  restaurant_ratting post meta.
		 * 
		 * @since 0.1
		 * 
		 * @param int $comment_id	Current comment id
		 * 
		 */
		public function add_transient_rating($comment_id) {
			$comment = get_comment($comment_id);

			$total_comments = get_comments_number($comment->comment_post_ID);
			$args = array(
			    'post_id' => $comment->comment_post_ID
			);

			// Retrives all comments for current post
			$comments = get_comments($args);
			$rating = 0;
			$cnt = 0;
			foreach ($comments as $cmnts) {
				//retrieves rating from each comment and adds it to the $total_rating
				$rating += get_comment_meta($cmnts->comment_ID, 'rating', true);
				$cnt += 1;
			}
			$average = $rating / $total_comments;

			$transient_args = array(
			    'post_id' => $comment->comment_post_ID,
			    'count' => $total_comments,
			    'rating' => $rating
			);
			//add or update transient
			set_transient('average_rating', $transient_args);

			// Post meta for average rating
			update_post_meta($comment->comment_post_ID, '_average_rating', $average);
		}

		/**
		 *  Add columns in display of all restaurants
		 * 
		 * @since 0.1
		 * 
		 * @param	array	$columns
		 * 
		 * @return	array	$new_columns
		 */
		public function add_restaurants_columns($columns) {
			//add new columns
			$new_columns = array_merge($columns, array(
			    'address' => __('Address'),
			    'contactno' => __('Contact No'),
			    'timing' => __('Restaurant Time'),
			));
			return $new_columns;
		}

		/**
		 * display data into columns
		 * 
		 * @since 0.1
		 * 
		 * @global array $post
		 * 
		 * @param array $column
		 * @param int $post_id
		 */
		public function manage_restaurants_columns($column, $post_id) {
			global $post;
			
			//add columns to all post table display
			switch ($column) {
				case 'address':
					$address = get_post_meta($post_id, '_restaurant_address', true);
					if (empty($address)) {
						echo "Unknown";
					} else {

						foreach ($address as $key => $val) {
							?>
							<span itemprop="<?php echo $key?>"> <?php echo $val?>
							</span>
							<?php
						}
					}
					break;
				case 'timing' :
					$current_post_timing = get_post_meta($post->ID, '_timing', true);
					$days = array("mon" => "Monday", "tue" => "Tuesday", "wed" => "Wednesday", "thu" => "Thursday", "fri" => "Friday", "sat" => "Saturday", "sun" => "Sunday");
					foreach ($current_post_timing as $key => $day) {
						?>
						<p> <?php echo $days[$key] ?> </p>
						<?php if ($day['am'] == NULL && $day['pm'] == NULL) { ?>
							<p>Close</p>
						<?php } else {
							?>
							<?php echo "<span id='" . $key . "-am'>" . $current_post_timing[$key]['am'] . "</span>" ?>AM To 
							<?php echo "<span id='" . $key . "-pm'>" . $current_post_timing[$key]['pm'] . "</span>" ?>PM
							<?php
						}
					}

					break;
				case 'contactno' :
					$contact = get_post_meta($post_id, '_restaurant_contactno', true);
					if (empty($contact)) {
						echo "Unknown";
					} else {
						echo $contact;
					}
					break;
				default :
					break;
			}
		}

		/**
		 * Display contents to quick edit
		 * 
		 * @since 0.1
		 * 
		 * @param string $column_name
		 * @param string $post_type
		 */
		public function display_custom_quickedit_restaurant($column_name, $post_type) {
			global $post;
			//display post meta into quick edit
			switch ($column_name) {
				case 'address' :
					?>
					<label>Address</label>
					<?php
					echo $this->display_address($post);
					break;

				case 'timing' :
					?>
					<div style="float:left;">
					<br /><br /><label>Restaurant Timing</label>
					<?php $this->display_timing($post); ?>
					</div>
					<?php
					break;

				case 'contactno' :
					?>
					<label>Contact Number</label>
					<?php
					$this->display_contactno($post);
					break;

				default:
					break;
			}
		}

		/**
		 * add script for admin quick edit
		 * 
		 * @since 0.1
		 * @param array $hook
		 */
		public function wp_admin_enqueue_scripts($hook) {
			//enqueue admin edit script for quick edit
			if ('edit.php' === $hook &&
				isset($_GET['post_type']) &&
				'restaurants' === $_GET['post_type']) {
				wp_enqueue_script('my_custom_script', plugins_url('rt_restaurants/assets/js/admin_edit.js', \rtCamp\WP\rtRestaurants\PATH), false, null, true);
			}
		}

	}

}