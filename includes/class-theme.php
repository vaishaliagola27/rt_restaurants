<?php

namespace rtCamp\WP\rtRestaurants;

if ( !class_exists( 'Theme' ) ) {

	/**
	 *  Changes into front-end side display.
	 *
	 * @author Vaishali Agola <vaishaliagola27@gmail.com>
	 */
	class Theme {

		/**
		 * initialize hooks
		 */
		public function init() {
			//enqueue all scripts and styles for restaurant
			add_action( 'wp_enqueue_scripts', array( $this, 'add_css_js' ) );

			// add template and call load-template
			add_filter( 'template_include', array( $this, 'load_template_single' ) );

			//template for archive page
			add_filter( 'template_include', array( $this, 'load_archive_restaurants' ) );
		}

		/**
		 * enqueue css for restaurant post type
		 *
		 * @since 0.1
		 *
		 */
		public function add_css_js() {
			$template_directory_uri = \rtCamp\WP\rtRestaurants\URL;

			//Local scripts for admin
			wp_enqueue_script( 'jquery' );
			wp_localize_script( 'jquery', 'ajax_object', admin_url( 'admin-ajax.php' ) );

			// Enqueuing styles
			wp_enqueue_style( "restaurants_css", $template_directory_uri . 'assets/css/restaurant.css' );
			wp_enqueue_style( "grid_css", $template_directory_uri . 'assets/css/grid-layout.css' );
			wp_enqueue_style( "Slick_css", $template_directory_uri . 'lib/slick/slick/slick.css' );
			wp_enqueue_style( "Slick_theme_css", $template_directory_uri . 'lib/slick/slick/slick-theme.css' );
			
			
			// Registering slick script
			wp_register_script( 'slick-js1', $template_directory_uri . 'lib/slick/slick/slick.min.js' );
			wp_enqueue_script( 'slick-js1' );

			//register script for google map
			wp_register_script( 'google-map', "http://maps.googleapis.com/maps/api/js?sensor=false" );
			wp_enqueue_script( 'google-map' );

			// Registering restaurant js
			wp_register_script( 'slider-js', $template_directory_uri . '/assets/js/restaurants.js' );
			wp_enqueue_script( 'slider-js' );
		}

		/**
		 * loads template for single restaurant page
		 *
		 * @since 0.1
		 *
		 * @param array $template array of file paths
		 *
		 */
		public function load_template_single( $template ) {

			//check post type
			if ( is_singular( 'restaurants' ) ) {
				$template = $this->load_template( $template, "single" );
			}
			return $template;
		}

		/**
		 * loads archive page of template restaurants
		 *
		 * @since 0.1
		 *
		 * @param array $template  array of file paths
		 *
		 */
		public function load_archive_restaurants( $template ) {

			if ( is_post_type_archive( 'restaurants' ) ) {
				$template = $this->load_template( $template, "archive" );
			}
			return $template;
		}

		/**
		 * Loads perticular template of $type
		 *
		 * @since 0.1
		 *
		 * @param array $template
		 * @param string $type
		 * @return array
		 */
		public function load_template( $template, $type ) {
			//convert string into array
			$path = explode( "/", $template );

			//reverse array
			$path = array_reverse( $path );

			//compare file name
			if ( strcmp( $path[ 0 ], $type . ".php" ) === 0 ) {
				$path_template = \rtCamp\WP\rtRestaurants\PATH . 'templates/' . $type . '-restaurants.php';
				$template = $path_template;
			}
			
			/**
			 * Filter to change template path
			 *
			 * @since 0.1
			 *
			 * @param string $var    Filter name
			 * @param array $template
			 */
			$template = apply_filters('rt_restaurants_template_path',$template);
			
			return $template;
		}

	}

}
