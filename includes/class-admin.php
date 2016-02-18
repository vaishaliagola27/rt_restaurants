<?php

namespace rtCamp\WP\rtRestaurants;

if ( !class_exists( 'Admin' ) ) {

	/**
	 *  Allow change in front-end/Admin side.
	 *
	 * @author Vaishali Agola <vaishaliagola27@gmail.com>
	 */
	class Admin {

		/**
		 * initialize hooks
		 */
		public function init() {

			//add meta boxes
			add_action( 'add_meta_boxes', array( $this, 'add_all_meta_boxes' ) );

			//save meta box of address
			add_action( 'save_post', array( $this, 'save_address' ) );

			//save contact number meta box value
			add_action( 'save_post', array( $this, 'save_contactno' ) );

			//save timing meta box
			add_action( 'save_post', array( $this, 'save_timing' ) );

			//save meta box of related restaurants
			add_action( 'save_post', array( $this, 'save_related_restaurants' ) );

			//for display column in list
			add_filter( 'manage_restaurants_posts_columns', array( $this, 'add_restaurants_columns' ) );

			//for data into new columns
			add_action( 'manage_restaurants_posts_custom_column', array( $this, 'manage_restaurants_columns' ), 10, 2 );

			//quick edit for new columns
			add_action( 'quick_edit_custom_box', array( $this, 'display_custom_quickedit_restaurant' ), 10, 2 );

			//add script admin_edit
			add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts' ) );

			//call for related restaurants
			add_action( 'wp_ajax_related_restaurants', array( $this, 'related_restaurants' ) );

			//setting page for advertisement
			add_action( 'edit_user_profile', array( $this, 'advertisement_setting' ) );
			add_action( 'show_user_profile', array( $this, 'advertisement_setting' ) );

			//save image of advertisement
			add_action( 'personal_options_update', array( $this, 'save_advertisement' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_advertisement' ) );
						
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
			    ),
			    'related_restaurants' => array(
				'id' => 'related-restaurants',
				'title' => 'Related Restaurants',
				'callback' => 'add_related_restaurants_meta_box',
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
			 * @param array $meta_box_args
			 */
			$meta_box_args = apply_filters( 'rt_restaurants_add_meta_boxes', $meta_box_args );


			/**
			 * action to run code before meta boxes added
			 * 
			 * @param array $meta_box_args
			 */
			do_action( 'rt_restaurants_before_add_meta_boxes', $meta_box_args );

			//add all meta boxes in array
			foreach ( $meta_box_args as $key => $value ) {
				add_meta_box(
					$value[ "id" ], esc_html__( $value[ "title" ], $value[ "title" ] ), array( $this, $value[ "callback" ] ), $value[ "screen" ], $value[ "context" ], $value[ "priority" ] );
			}
		}

		/**
		 * 
		 * @param array $post
		 */
		private function display_relative_restaurants( $post ) {
			//output buffer start
			ob_start();

			wp_nonce_field( 'rt_restaurant_related_restaurants_nonce', 'related_restaurants_nonce', false );

			//includes html
			require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/related_restaurants.php';

			//clean and save output buffer value
			$ob_related_restaurants = ob_get_clean();
			return $ob_related_restaurants;
		}

		/**
		 * 
		 * @param type $post_id
		 * @return type
		 */
		public function save_related_restaurants( $post_id ) {
			//check for empty post id
			if ( empty( $post_id ) ) {
				$post_id = $_POST[ 'post_ID' ];
			}
			if ( empty( $post_id ) ) {
				return;
			}

			//verify nonce for restaurants
			$related_restaurants_nonce = !empty( $_POST[ 'related_restaurants_nonce' ] ) ? $_POST[ 'related_restaurants_nonce' ] : '';
			if ( empty( $related_restaurants_nonce ) ) {
				return;
			}

			if ( !wp_verify_nonce( $_POST[ 'related_restaurants_nonce' ], 'rt_restaurant_related_restaurants_nonce' ) ) {
				return;
			}

			//fetch value of related restaurants
			$related_restaurants = isset( $_POST[ 'related_restaurants_ids' ] ) ? $_POST[ 'related_restaurants_ids' ] : '';
			if ( empty( $related_restaurants ) ) {
				return;
			}

			$related_restaurants = explode( ",", $related_restaurants );
			$count = count( $related_restaurants );
			unset( $related_restaurants[ $count - 1 ] );

			/**
			 *  Filter to change related restaurants value
			 *
			 * @since 0.1
			 *
			 * @param string $var    Name of filter
			 * @param array  $related_restaurants
			 */
			$related_restaurants = apply_filters( 'rt_restaurant_related_resaturant_save', $related_restaurants );

			/**
			 * Action to run code before saving related restaurants
			 * 
			 * @param array  $related_restaurants
			 */
			do_action( 'rt_restaurants_before_save_related_restaurants', $related_restaurants );

			//add or update address post meta
			update_post_meta( $post_id, '_related_restaurant', $related_restaurants );
		}

		/**
		 * 
		 * @param array $post
		 */
		public function add_related_restaurants_meta_box( $post ) {
			echo $this->display_relative_restaurants( $post );
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
		public function display_address( $post ) {
			// output buffer start
			ob_start();

			//nonce field for address meta box
			wp_nonce_field( 'rt_restaurant_address_nonce', 'restaurant_address_nonce', false );

			// Array for address fields
			$addr = array( "streetAddress" => "Street Address", "addressLocality" => "Locality", "addressRegion" => "Region", "postalCode" => "Postal Code", "addressCountry" => "Country" );

			// Retriving address post meta for particular post.
			$add = get_post_meta( $post->ID, '_restaurant_address', true );

			//includes address html
			require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/address.php';

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
			$ob_address = apply_filters( 'rt_restaurant_address_html', $ob_address );


			/**
			 * Action to add extra fields in address display
			 * 
			 * @param string $ob_address
			 */
			do_action( 'rt_restaurants_address_display', $ob_address );

			return $ob_address;
		}

		public function add_address_meta_box( $post ) {
			echo $this->display_address( $post );
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
		public function save_address( $post_id ) {
			//check for empty post id
			if ( empty( $post_id ) ) {
				$post_id = $_POST[ 'post_ID' ];
			}
			if ( empty( $post_id ) ) {
				return;
			}

			//verify nonce for address
			$address_nonce = !empty( $_POST[ 'restaurant_address_nonce' ] ) ? $_POST[ 'restaurant_address_nonce' ] : '';
			if ( empty( $address_nonce ) ) {
				return;
			}

			if ( !wp_verify_nonce( $_POST[ 'restaurant_address_nonce' ], 'rt_restaurant_address_nonce' ) ) {
				return;
			}

			//fetch value of address
			$address = isset( $_POST[ 'restaurant_add' ] ) ? $_POST[ 'restaurant_add' ] : '';
			if ( empty( $address ) ) {
				return;
			}

			//sanitize address values
			foreach ( $address as $key => $value ) {
				$address[ $key ] = sanitize_text_field( $value );
			}

			/**
			 *  Filter to change address value
			 *
			 * @since 0.1
			 *
			 * @param string $var    Name of filter
			 * @param array  $address
			 */
			$address = apply_filters( 'rt_restaurant_save_address', $address );

			/**
			 * Action to run code before saving address
			 * 
			 * @param array  $address
			 */
			do_action( 'rt_restaurants_before_save_address', $address );

			//add or update address post meta
			update_post_meta( $post_id, '_restaurant_address', $address );
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
		public function save_contactno( $post_id ) {
			//check for empty post id
			if ( empty( $post_id ) ) {
				$post_id = $_POST[ 'post_ID' ];
			}

			if ( empty( $post_id ) ) {
				return;
			}

			//verify nonce
			$contactno_nonce = !empty( $_POST[ 'restaurant_contactno_nonce' ] ) ? $_POST[ 'restaurant_contactno_nonce' ] : '';

			if ( empty( $contactno_nonce ) ) {
				return;
			}

			if ( !wp_verify_nonce( $_POST[ 'restaurant_contactno_nonce' ], 'rt_restaurant_contactno_nonce' ) ) {
				return;
			}

			//Fetch contact number post meta
			$contact_no = isset( $_POST[ 'restaurant_contact_no' ] ) ? $_POST[ 'restaurant_contact_no' ] : '';
			if ( empty( $contact_no ) ) {
				return;
			}

			/**
			 *  Filter to change contact number value
			 *
			 * @since 0.1
			 *
			 * @param string $var    Name of filter
			 * @param string  $contact_no
			 */
			$contact_no = apply_filters( 'rt_restaurant_save_contact_no', $contact_no );

			//sanitize contact number data
			$contact_no = sanitize_text_field( $contact_no );

			/**
			 * Action to run code before saving contact number
			 * 
			 * @param string  $contact_no
			 */
			do_action( 'rt_restaurants_before_save_contactno', $contact_no );

			//Adds or updates contact number post meta
			update_post_meta( $post_id, '_restaurant_contactno', $contact_no );
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
		public function display_contactno( $post ) {
			// Output buffering start
			ob_start();

			//nonce field for contact number
			wp_nonce_field( 'rt_restaurant_contactno_nonce', 'restaurant_contactno_nonce', false );

			$restaurant_contact = "";

			// Retriving contact number of restaurant
			$val = get_post_meta( $post->ID, '_restaurant_contactno', true );

			// Check if contact number is already exists for restaurant
			if ( $val != NULL && !empty( $val ) ) {
				$restaurant_contact = $val;
			}

			//includes contact number html
			require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/contactno.php';

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
			$ob_contactno = apply_filters( 'rt_restaurant_contactno_html', $ob_contactno );


			/**
			 * Action to add data to display contact number
			 * 
			 * @param string $ob_contactno
			 */
			do_action( 'rt_restaurants_contactno_display', $ob_contactno );

			return $ob_contactno;
		}

		/**
		 * display contact number meta box
		 *
		 * @param \rtCamp\WP\rtRestaurants\type $post
		 */
		public function add_contactno_meta_box( $post ) {
			echo $this->display_contactno( $post );
		}

		/**
		 * display timing meta box
		 *
		 * @param \rtCamp\WP\rtRestaurants\type $post
		 */
		public function add_timing_meta_box( $post ) {
			echo $this->display_timing( $post );
		}

		/**
		 *  add timing meta box on restaurant post display
		 *
		 * @since 0.1
		 *
		 * @param int $post
		 *
		 */
		public function display_timing( $post ) {
			// Output buffer starts
			ob_start();

			//includes restaurant timing html
			require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/timing.php';

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
			$ob_timing_working_days = apply_filters( 'rt_restaurant_timing_working_days_html', $ob_timing_working_days );

			/**
			 * Action to add data to display Timing
			 * 
			 * @param string $ob_timing_working_days
			 */
			do_action( 'rt_restaurants_timing_display', $ob_timing_working_days );

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
		public function save_timing( $post_id ) {
			//check for empty post id
			if ( empty( $post_id ) ) {
				$post_id = $_POST[ 'post_ID' ];
			}

			if ( empty( $post_id ) ) {
				return;
			}

			//verify nonce
			$timing_nonce = !empty( $_POST[ 'restaurant_timing_nonce' ] ) ? $_POST[ 'restaurant_timing_nonce' ] : '';

			if ( empty( $timing_nonce ) ) {
				return;
			}

			if ( !wp_verify_nonce( $_POST[ 'restaurant_timing_nonce' ], 'rt_restaurant_timing_nonce' ) ) {
				return;
			}

			//fetch timing data
			$time = isset( $_POST[ 'time' ] ) ? $_POST[ 'time' ] : '';
			if ( empty( $time ) ) {
				return;
			}

			/**
			 *  Filter to change timing value
			 *
			 * @since 0.1
			 *
			 * @param string $var    Name of filter
			 * @param array  $time
			 */
			$time = apply_filters( 'rt_restaurant_save_time', $time );

			/**
			 * Action to run code before saving timing of restaurant
			 * 
			 * @param array  $time
			 */
			do_action( 'rt_restaurants_before_save_timing', $time );

			//Add or update timing post meta
			update_post_meta( $post_id, '_timing', $time );
			// Computing close days
			$close_days = array();
			$i = 0;
			foreach ( $time as $key => $day ) {
				if ( $day[ 0 ] == '' && $day[ 1 ] == '' ) {
					$close_days[ $i++ ] = ($key);
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
			$close_days = apply_filters( 'rt_restaurant_close_days', $close_days );

			//Add or update close day post meta
			update_post_meta( $post_id, '_close_days', $close_days );
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
		public function add_restaurants_columns( $columns ) {
			//add new columns
			$new_columns = array_merge( $columns, array(
			    'address' => __( 'Address' ),
			    'contactno' => __( 'Contact No' ),
			    'timing' => __( 'Restaurant Time' ),
				) );

			/**
			 *  Filter for columns in back end
			 *
			 * @since 0.1
			 *
			 * @param string $var           name of filter
			 * @param array  $new_columns   
			 */
			$new_columns = apply_filters( 'rt_restaurants_columns', $new_columns );

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
		public function manage_restaurants_columns( $column, $post_id ) {
			global $post;

			//add columns to all post table display
			switch ( $column ) {
				case 'address':
					$address = get_post_meta( $post_id, '_restaurant_address', true );
					if ( empty( $address ) ) {
						echo "Unknown";
					} else {
						//includes restaurant address column
						require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/address-column.php';
					}
					break;
				case 'timing' :
					$current_post_timing = get_post_meta( $post->ID, '_timing', true );
					$days = array( "mon" => "Monday", "tue" => "Tuesday", "wed" => "Wednesday", "thu" => "Thursday", "fri" => "Friday", "sat" => "Saturday", "sun" => "Sunday" );

					//includes restaurant timing html
					require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/timing-column.php';

					break;
				case 'contactno' :
					$contact = get_post_meta( $post_id, '_restaurant_contactno', true );
					if ( empty( $contact ) ) {
						echo "Unknown";
					} else {
						echo $contact;
					}
					break;
				default :
					break;
			}

			//Action to display other column data
			do_action( 'rt_restaurants_column_data' );
		}

		/**
		 * Display contents to quick edit
		 *
		 * @since 0.1
		 *
		 * @param string $column_name
		 * @param string $post_type
		 */
		public function display_custom_quickedit_restaurant( $column_name, $post_type ) {
			global $post;
			//display post meta into quick edit
			switch ( $column_name ) {
				case 'address' :
					echo "Address\n";
					echo $this->display_address( $post );
					break;

				case 'timing' :
					echo "Restaurant Timing\n";
					echo $this->display_timing( $post );
					break;

				case 'contactno' :
					echo "Contact No.\n";
					echo $this->display_contactno( $post );
					break;

				default:
					break;
			}

			//Action to display quick edit of columns
			do_action( 'rt_restaurants_column_quick_edit' );
		}

		/**
		 * add script for admin quick edit
		 *
		 * @since 0.1
		 * @param array $hook
		 */
		public function wp_admin_enqueue_scripts( $hook ) {
			$template_directory_uri = \rtCamp\WP\rtRestaurants\URL;
			//enqueue admin edit script for quick edit
			if ( 'edit.php' === $hook &&
				isset( $_GET[ 'post_type' ] ) &&
				'restaurants' === $_GET[ 'post_type' ] ) {
				wp_enqueue_script( 'my_custom_script', plugins_url( 'rt_restaurants/assets/js/admin_edit.js', \rtCamp\WP\rtRestaurants\PATH ), false, null, true );
			}

			$screen = get_current_screen();
			if ( ('post-new.php' === $hook || 'post.php' === $hook) &&
				$screen->post_type ) {
				
				//js for related restaurants
				wp_register_script( 'related-restaurants-js', $template_directory_uri . '/assets/js/related_restaurants.js' );
				wp_enqueue_script( 'related-restaurants-js' );
				wp_localize_script( 'related-restaurants-js', 'auto', array( 'admin_url' => admin_url( 'admin-ajax.php' ) ) );

				//register script for google map
				wp_register_script( 'google-map', "http://maps.googleapis.com/maps/api/js?sensor=false" );
				wp_enqueue_script( 'google-map' );

				//address map
				wp_register_script( 'address-map-js', $template_directory_uri . '/assets/js/addressmap_admin.js' );
				wp_enqueue_script( 'address-map-js' );
				
				//validation
				wp_register_script( 'validation-js', $template_directory_uri . '/assets/js/admin_validation.js' );
				wp_enqueue_script( 'validation-js' );

				//timepicker js and css
				wp_register_script( 'timepicker-js', $template_directory_uri . '/lib/timepicker/jquery.timepicker.js' );
				wp_enqueue_script( 'timepicker-js' );
				wp_register_script( 'timepicker-restaurant-js', $template_directory_uri . '/assets/js/restaurant_timing.js' );
				wp_enqueue_script( 'timepicker-restaurant-js' );

				//timepicker style
				wp_enqueue_style( "timepicker_css", $template_directory_uri . '/lib/timepicker/jquery.timepicker.css' );

				//tooltip style and js
				wp_register_script( 'tooltip-js', $template_directory_uri . '/lib/tooltipster/js/jquery.tooltipster.min.js' );
				wp_enqueue_script( 'tooltip-js' );
				wp_register_script( 'tooltip-admin-js', $template_directory_uri . '/assets/js/admin-timing-tooltip.js' );
				wp_enqueue_script( 'tooltip-admin-js' );
				wp_localize_script( 'tooltip-admin-js', 'url', array( 'theme_url' => $template_directory_uri ) );

				wp_enqueue_style( "tooltip_css", $template_directory_uri . '/lib/tooltipster/css/tooltipster.css' );
			}
			
			if ( 'profile.php' === $hook ) {
				wp_enqueue_media();
				//script for image display
				wp_register_script( 'advertisement-js', $template_directory_uri . 'assets/js/advertisement-admin.js' );
				wp_enqueue_script( 'advertisement-js' );
			}

			//jquery ui 
			wp_register_script( 'jquery-ui', "//code.jquery.com/ui/1.11.4/jquery-ui.js" );
			wp_enqueue_script( 'jquery-ui' );
				
			//admin css
			wp_enqueue_style( "restaurants_admin_css", $template_directory_uri . 'assets/css/admin.css' );
				
			//Action to add other column scripts
			do_action( 'rt_restaurants_enqueue_edit_script' );
		}

		public function related_restaurants() {
			$args = array(
			    'post_type' => 'restaurants',
			    'numberposts' => -1,
			);
			$posts = get_posts( $args );
			$id_title = array();
			foreach ( $posts as $key => $value ) {
				$id_title[] = array(
				    'label' => $value->post_title,
				    'value' => $value->ID,
				);
			}
			echo json_encode( $id_title );
			wp_die();
		}

		/**
		 * Set advertisement section and field
		 * 
		 * @param type $user
		 */
		public function advertisement_setting( $user ) {
			//include file for html
			require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/advertisement-admin.php';
		}

		/**
		 * 
		 * @param type $user_id
		 */
		public function save_advertisement( $user_id ) {
			if ( !current_user_can( 'edit_user', $user_id ) )
				return false;
			//update_usermeta( $user_id, '_restaurant_advertisement', $_POST['advertise_image'] );
			if ( empty( $user_id ) ) {
				$user_id = $_POST[ 'user_id' ];
			}

			if ( empty( $user_id ) ) {
				return;
			}

			//verify nonce
			$image_nonce = !empty( $_POST[ '_wpnonce' ] ) ? $_POST[ '_wpnonce' ] : '';

			if ( empty( $image_nonce ) ) {
				return;
			}

//			if ( !wp_verify_nonce( $_POST[ '_wpnonce' ] ) ) {
//				return;
//			}
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'advertisement_images';
			
			$wpdb->insert(
				$table_name, array(
				'time' => current_time( 'mysql' ),
				'user_id' => $_POST['user_id'],
				'image_id'=> $_POST['custom-img-id'],
				)
			);
		}

	}

}
