<?php

namespace rtCamp\WP\rtRestaurants;

if ( !class_exists( 'Load' ) ) {

	/**
	 *  Loads all files and data at theme and plugin loading time.
	 *
	 * @author Vaishali Agola <vaishaliagola27@gmail.com>
	 */
	class Load {

		/**
		 * initialize hooks and other classes' method called
		 */
		public function init() {

			//plugin activation
			register_activation_hook( \rtCamp\WP\rtRestaurants\PATH . 'rt-restaurants.php', array( $this, 'restaurants_flush_rewrites' ) );

			//action for register post types
			add_action( 'init', array( $this, 'register_post_type' ) );

			//action for register taxomoies
			add_action( 'init', array( $this, 'register_taxonomy' ) );

			//function call for class initialization
			$this->classes_init();
		}

		/**
<<<<<<< Updated upstream
=======
		 * 
		 */
		public function create_db_advertisement() {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			 
			$wpdb->rt_restaurants_advertisements = $wpdb->prefix . 'advertisement_images';
			
			$sql = "CREATE TABLE $wpdb->rt_restaurants_advertisements (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				user_id mediumint(9) NOT NULL,
				image_id mediumint(9) NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
		/**
>>>>>>> Stashed changes
		 * call init method of classes
		 */
		public function classes_init() {

			// create other classes' objects and call init()
			$class_names = array( 'theme', 'admin', 'review' );

			foreach ( $class_names as $class ) {
				//capitalize first letter of class name
				$class_uc = ucfirst( $class );

				//Instanciate class and call init() of every class
				$class_name = "rtCamp\WP\\rtRestaurants\\" . $class_uc;
				${$class} = new $class_name();
				${$class}->init();
			}
		}

		/**
		 * Flushes the rewrite rules.
		 */
		public function restaurants_flush_rewrites() {
			flush_rewrite_rules();
		}

		/**
		 * add new post type of restaurants
		 *
		 *  add one or more custom post types.
		 *
		 * @since 0.1
		 */
		public function register_post_type() {

			// Array of labels for restaurant post type
			$labels = array(
			    'name' => 'Restaurants',
			    'singular_name' => 'Restaurants',
			    'slug' => 'restaurants',
			    'menu_name' => 'Restaurants',
			    'parent_item_colon' => 'Parent Restaurants',
			    'all_items' => 'All Restaurants',
			    'view_item' => 'View Restaurants',
			    'add_new_item' => 'Add New Restaurants',
			    'add_new' => 'Add New',
			    'edit_item' => 'Edit Restaurants',
			    'update_item' => 'Update Restaurants',
			    'search_items' => 'Search Restaurants',
			    'not_found' => 'Not Found',
			    'not_found_in_trash' => 'Not found in Trash',
			);

			// Array of current taxonomy of restaurant post type
			$taxonomy = array( 'restaurants_type', 'food_type' );

			// Array of arguments of custom post type restaurant.
			$args = array(
			    'public' => true,
			    'taxonomies' => $taxonomy,
			    'supports' => array( 'title', 'comments', 'editor', 'thumbnail' ),
			    'label' => 'Restaurants',
			    'labels' => $labels,
			    'hierarchical' => false,
			    'public' => true,
			    'show_ui' => true,
			    'show_in_menu' => true,
			    'show_in_nav_menus' => true,
			    'show_in_admin_bar' => true,
			    'menu_position' => 5,
			    'can_export' => true,
			    'has_archive' => true,
			    'exclude_from_search' => false,
			    'publicly_queryable' => true,
			    'capability_type' => 'page',
			);

			// Array to store new post types for registration.
			$new_post_types = array( 'restaurants' => $args );

			/**
			 * Filter for add multiple custom post types.
			 *
			 *  filter to add multiple custom post types at once. USer just need to pass
			 *  name and arguments of custom post types.
			 *
			 * @since 0.1
			 *
			 * @param string $var Name of filter
			 * @param array $new_post_types {
			 *     @type string $var Name of custom post type.
			 *     @type array  $var Array of arguments of custom post type.
			 * }
			 * @param type  $var Description.
			 */
			$new_post_types = apply_filters( 'rt_restaurant_custom_post_type', $new_post_types );

			// Loop to register all custom post types.
			foreach ( $new_post_types as $key => $args ) {
				register_post_type( $key, $args );
			}
			
		}

		/**
		 * register new texonomy to post type restaurants
		 *
		 * This function will register one more taxonomy for custom post type.
		 *
		 * @since 0.1
		 *
		 */
		public function register_taxonomy() {

			// Array of taxomy name and label to register.
			$taxonomy = array( 'restaurants_type' => 'Restaurants Type', 'food_type' => 'Food Type' );

			/**
			 * Filter to register more than one taxonomies.
			 *
			 * filter to register more than 1 taxonomy at a time by giving key and label of each
			 * taxonomy in array.
			 *
			 * @since 0.1
			 *
			 * @param string  $var Filter name.
			 * @param array $args {
			 *     @type string $var key for taxonomy
			 *     @type string $var Label for taxonomy
			 * }
			 */
			$taxonomy = apply_filters( 'rt_restaurant_get_taxonomies_with_label', $taxonomy );

			$post_type = 'restaurants';

			foreach ( $taxonomy as $name => $label ) {
				$args = array(
				    'show_ui' => true,
				    'show_admin_column' => true,
				    'label' => $label
				);

				/**
				 * Filter to change taxonomy arguments
				 *
				 * This filter allow user to change taxonomy arguments by passing arguments array in filter.
				 *
				 * @since 0.1
				 *
				 * @param string $var    Filter name
				 * @param array  $args   Array of arguments for taxonomy
				 */
				$args = apply_filters( 'rt_restaurant_taxonomy_args', $args );

				register_taxonomy( $name, $post_type, $args );
			}
		}

	}

}
