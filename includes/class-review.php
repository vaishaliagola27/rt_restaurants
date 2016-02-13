<?php

namespace rtCamp\WP\rtRestaurants;

if ( !class_exists( 'Admin' ) ) {

	/**
	 *  Review and rating
	 *
	 * @author Vaishali Agola <vaishaliagola27@gmail.com>
	 */
	class Review {

		public function init() {
			//add comment/review default fields
			add_filter( 'comment_form_default_fields', array( $this, 'custom_fields' ) );

			//action to store review meta data
			add_action( 'comment_post', array( $this, 'save_comment_meta_data' ) );

			//filter added for verify review details
			add_filter( 'preprocess_comment', array( $this, 'verify_comment_meta_data' ) );

			//action which extends review meta box for rating
			add_action( 'add_meta_boxes_comment', array( $this, 'extend_comment_add_meta_box' ) );

			//action for edit meta fields
			add_action( 'edit_comment', array( $this, 'extend_comment_edit_metafields' ) );

			//filter for comment template
			add_filter( 'comments_template', array( $this, 'review_template' ) );

			// to change comment form default fields
			add_filter( 'comment_form_defaults', array( $this, 'default_fields' ) );

			// additional fields of comment for logged in and other users
			add_action( 'comment_form_logged_in_after', array( $this, 'additional_fields' ) );
			add_action( 'comment_form_after_fields', array( $this, 'additional_fields' ) );
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
			$req = get_option( 'require_name_email' );
			$aria_req = ( $req ? " aria-required='true'" : '' );
			//Add custom fields
			$fields[ 'author' ] = require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/review-form-auther.php';

			$fields[ 'email' ] = require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/review-form-email.php';

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
			$fields = apply_filters( 'rt_restaurant_custom_comment_fields', $fields );

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
		public function save_comment_meta_data( $comment_id ) {
			//check for rating value
			if ( ( isset( $_POST[ 'rating' ] ) ) && ( $_POST[ 'rating' ] != '') )
				$rating = wp_filter_nohtml_kses( $_POST[ 'rating' ] );

			/**
			 *  Filter to change rating value
			 *
			 * @since 0.1
			 *
			 * @param string $var    Name of filter
			 * @param array  $address
			 */
			$rating = apply_filters( 'rt_restaurant_save_address', $rating );

			//Add or update rating
			add_comment_meta( $comment_id, 'rating', $rating );

			/**
			 * Action to change rating
			 * 
			 * @param array  $address
			 */
			do_action('rt_restaurants_save_rating',$rating);
			
			$this->add_transient_rating( $comment_id );
		}

		/**
		 * To check that rating is given or not
		 *
		 * This function will check if reviwer has also give rating to restaurant.
		 * @since 0.1
		 *
		 * @param array commentdata
		 */
		public function verify_comment_meta_data( $commentdata ) {

			if ( !isset( $_POST[ 'rating' ] ) && is_singular( 'restaurants' ) )
				wp_die( __( 'Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.' ) );
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
			$comment = get_comment();
			if ( 'restaurants' === get_post_field( 'post_type', $comment->comment_post_ID ) ) {
				add_meta_box( 'title', __( 'Comment Metadata - Extend Comment' ), array( $this, 'extend_comment_meta_box' ), 'comment', 'normal', 'high' );
			}
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
		public function extend_comment_meta_box( $comment, $args ) {

			// Output buffer starts
			ob_start();
			$rating = get_comment_meta( $comment->comment_ID, 'rating', true );
			//nonce field
			wp_nonce_field( 'rt_extend_comment_update', 'extend_comment_update', false );

			//includes restaurant timing html
			require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/review.php';

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
			$ob_rating_display_edit = apply_filters( 'rt_restaurant_rating_display_edit_html', $ob_rating_display_edit );
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
		public function extend_comment_edit_metafields( $comment_id ) {

			if ( !isset( $_POST[ 'extend_comment_update' ] ) || !wp_verify_nonce( $_POST[ 'extend_comment_update' ], 'rt_extend_comment_update' ) )
				return;

			if ( ( isset( $_POST[ 'rating' ] ) ) && ( $_POST[ 'rating' ] != '') ):
				$rating = wp_filter_nohtml_kses( $_POST[ 'rating' ] );
				update_comment_meta( $comment_id, 'rating', $rating );
			else :
				delete_comment_meta( $comment_id, 'rating' );
			endif;
			$this->add_transient_rating( $comment_id );
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
		public function add_transient_rating( $comment_id ) {
			$comment = get_comment( $comment_id );

			$total_comments = get_comments_number( $comment->comment_post_ID );
			$args = array(
			    'post_id' => $comment->comment_post_ID
			);

			// Retrives all comments for current post
			$comments = get_comments( $args );
			$rating = 0;
			$cnt = 0;
			foreach ( $comments as $cmnts ) {
				//retrieves rating from each comment and adds it to the $total_rating
				$rating += get_comment_meta( $cmnts->comment_ID, 'rating', true );
				$cnt += 1;
			}
			$average = $rating / $total_comments;

			$transient_args = array(
			    'post_id' => $comment->comment_post_ID,
			    'count' => $total_comments,
			    'rating' => $rating
			);
			//add or update transient
			set_transient( 'average_rating', $transient_args );

			// Post meta for average rating
			update_post_meta( $comment->comment_post_ID, '_average_rating', $average );
		}

		/**
		 *  Review field add, save review and display review
		 *
		 *  Function to change default fields of comment by providing them in array.
		 *
		 * @since 0.1
		 *
		 * @var array   $default    default fields of review
		 */
		public function default_fields() {

			$default [ 'comment_field' ] = require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/review-field.php';
			$default [ 'title_reply' ] = __( 'Review Us' );
			$default [ 'label_submit' ] = __( 'Post Review' );

			/**
			 *  Filter for change in default fields of comment
			 *
			 *  filter to change default fields of comment by providing them in array.
			 *
			 * @since 0.1
			 *
			 * @param string  $var     name of filter
			 * @param array   $default array for default fields of comment.
			 */
			$default = apply_filters( 'rt_restaurant_default_comment_fields', $default );

			return $default;
		}

		/**
		 *  Add field of rating in review
		 *
		 *  Adds rating form to Review.
		 *
		 * @since 0.1
		 *
		 * @var string  $ob_rating  output buffer value
		 */
		public function additional_fields() {
			if ( is_singular( 'restaurants' ) ) {
				// Output buffer starts
				ob_start();
				require \rtCamp\WP\rtRestaurants\PATH . 'includes/views/rating-fields.php';
				// Storing output buffer value into variable and clean it.
				$ob_rating = ob_get_clean();

				/**
				 *  change html of additional fields.
				 *
				 *  This filter will help user to change in display of additional fields of comments
				 *
				 * @since 0.1
				 *
				 * @param string $var name of the filter
				 * @param string $ob_rating
				 */
				$ob_rating = apply_filters( 'rt_restaurant_rating_html', $ob_rating );
				
				/**
				 * Action to display additional fields
				 * 
				 * @param string $ob_rating
				 */
				do_action('rt_restaurants_fields_comment',$ob_rating);
				
				echo $ob_rating;
			}
		}

		/**
		 * Display review of restaurants
		 *
		 *  Displays custom review display code for restaurant post type.
		 *
		 * @since 0.1
		 *
		 * @param array $review Array of comments
		 * @param string $args
		 * @param int $depth
		 */
		public static function reviews_html( $review, $args, $depth ) {
			
			if(locate_template('template-parts/content-review.php' )){
				require get_template_directory() . '/template-parts/content-review.php';
			}else{
				//includes restaurant timing html
				require \rtCamp\WP\rtRestaurants\PATH . 'templates/template-parts/content-review.php';
			}
			
		}

		/**
		 * add custom template for comment
		 *
		 * @since 0.1
		 *
		 * @param string $theme_template
		 *
		 */
		public function review_template( $theme_template ) {
			if(locate_template( 'comments-restaurants.php') ){
				
				$theme_template = get_template_directory() .'/comments-restaurants.php';
			}
			if ( is_singular( 'restaurants' ) && !locate_template( 'comments-restaurants.php')) {
				$theme_template = \rtCamp\WP\rtRestaurants\PATH . 'templates/comments-restaurants.php';
			}
			return $theme_template;
		}

	}

}
