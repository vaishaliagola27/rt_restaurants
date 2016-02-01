<?php
/*
Plugin Name: rtRestaurants
Plugin URI:  http://rtcamp.com
Description: Restaurat directory
Version:     0.1
Author:      vaishuagola27
Author URI:  http://rtcamp.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: rt-restaurants
*/
// Custom code starts here

register_activation_hook( __FILE__, 'rt_restaurants_flush_rewrites' );

/**
 * To flush the rewrite rules for plugin.
 */
function rt_restaurants_flush_rewrites(){
    flush_rewrite_rules();
}

add_action('init', 'rt_restaurant_create_post_type');

/**
 * Summary. add new post type of restaurants
 *
 * Description.
 *  This function will add one or more custom post types.
 * 
 * @since Unknown
 * 
 * @var array   $labels         labels for custom post type
 * @var array   $taxonomy       taxonomies for custom post type
 * @var array   $args           arguments for custom post type
 * @var string  $new_post_types new post type to register
 */
function rt_restaurant_create_post_type() {
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
    $taxonomy=array('restaurants_type','food_type');
    
    // Array of arguments of custom post type restaurant.
    $args=array(
        'public' => true,
        'taxonomies' => $taxonomy,
        'supports' => array('title', 'comments', 'editor', 'thumbnail'),
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
    $new_post_types = array('restaurants' => $args );
    
     /**
     * Summary. Filter for add multiple custom post types.
     *
     * Description.
     *  This filter will allow user to add multiple custom post types at once. USer just need to pass
      *     name and arguments of custom post types.  
     * 
     * @since Unknown
     *
     * @param string $var Description. Name of filter
     * @param array $new_post_types {
     *     Short description about this hash.
     *
     *     @type string $var Description. Name of custom post type.
     *     @type array  $var Description. Array of arguments of custom post type.
     * }
     * @param type  $var Description.
     */
    $new_post_types = apply_filters('rt_restaurant_custom_post_type',$new_post_types);
    
    // Loop to register all custom post types.
    foreach($new_post_types as $key => $args){
        register_post_type( $key , $args);
    }
    
}

add_action('init', 'rt_restaurant_reg_taxonomy');

/**
 * Summary. register new texonomy to post type restaurants
 *
 * Description.
 *  This function will register one more taxonomy for custom post type.
 * 
 * @since Unknown
 * 
 * @var array   $taxonomy   taxonomy to register
 * @var array   $args       arguments of taxonomy
 * @var string  $post_type  post type for taxonomy
 */
function rt_restaurant_reg_taxonomy() {
    // Array of taxomy name and label to register.
    $taxonomy = array('restaurants_type' => 'Restaurants Type', 'food_type' => 'Food Type');
    
     /**
     * Summary. Filter to register more than one taxonomies.
     *
     * Description.
      *     This filter will allow user to register more than 1 taxonomy at a time by giving key and label of each
      *     taxonomy in array. 
     *
     * @since Unknown
     *
     * @param string  $var Description. Filter name.
     * @param array $args {
     *     @type string $var Description. key for taxonomy
     *     @type string $var Description. Label for taxonomy
     * }
     */
    $taxonomy = apply_filters('rt_restaurant_get_taxonomies_with_label', $taxonomy);
    
    $post_type = 'restaurants';

    foreach ($taxonomy as $name => $label) {
        $args = array(
            'show_ui' => true,
            'show_admin_column' => true,
            'label' => $label
        );
        
        /**
        * Summary. Filter to change taxonomy arguments
        *
        * Description.
        *   This filter allow user to change taxonomy arguments by passing arguments array in filter.
        * 
        * @since Unknown
        *
        * @param string $var    Description. Filter name
        * @param array  $args   Description. Array of arguments for taxonomy
        */
        $args = apply_filters('rt_restaurant_taxonomy_args', $args);
        
        register_taxonomy($name, $post_type, $args);
    }
}



add_action('add_meta_boxes', 'rt_restaurant_add_address');
add_action('save_post', 'rt_restaurant_save_address');
/**
 * Summary. add new meta box of address for restaurants post type.
 *
 * Description.
 *  Function to add address meta box.
 * 
 * @since Unknown 
 */
function rt_restaurant_add_address() {
    add_meta_box(
            'restaurants-address', esc_html__('Address', 'Address'), 'rt_restaurant_add_address_meta_box', 'restaurants', 'side', 'default'
    );
}

/**
 * Summary. Saves or Update address postmeta 
 *
 * Description.
 *  This function will save or update post meta of restaurant address.
 * 
 * @since Unknown
 * 
 * @param int $post_id  
 * 
 * @var array   $address    address of restaurant
 */
function rt_restaurant_save_address($post_id) {
    if (isset($_POST['restaurant_add'])) {
        $address = array($_POST['restaurant_add']);
        update_post_meta($post_id, '_restaurant_address', $address);
    }
}

/**
 * Summary. display/add meta box on restaurants post.
 *
 * Description.
 *  Function for adding meta box for restaurant address.
 * 
 * @since Unknown
 * 
 * @param array $post
 * 
 * @var array   $addr       array for address fields
 * @var array   $add        current existing address
 * @var string  $ob_address output buffer value
 */
function rt_restaurant_add_address_meta_box($post) {
    // output buffer start
    ob_start();
    
    // Array for address fields
    $addr = array("streetAddress" => "Street Address", "addressLocality" => "Locality", "addressRegion" => "Region", "postalCode" => "Postal Code", "addressCountry" => "Country");
    
    // Retriving address post meta for particular post.
    $add = get_post_meta($post->ID, '_restaurant_address', true);
    
    ?>
    <table class="address_table">
        <?php 
       foreach($addr as $key => $value){
           if ($add != NULL && !empty($add)) {
                $value = $add[0][$key];    
           }
           else
           {
               $value='';
           }
           ?>
            <tr>
                <td><label> <?php echo $addr[$key]; ?></label></td>
                <td>
                    <input size="15" type="text" name="<?php echo "restaurant_add[".$key."]"; ?>" value="<?php echo $value;?>" />
                </td> 
            </tr>
            <?php
       }
       ?>
    </table>
    <?php
    
    // Get output buffer value into variable and clear output buffer
    $ob_address=  ob_get_clean();
    
     /**
     * Summary. Filter for change post meta box display of address post meta.
     *
     * Description.
     *  This filter allow user to change meta box of address post meta by passing output string.
      * 
     * @since Unknown
     *
     * @param string $var Description. Filter name
     * @param string $ob_address
     */
    $ob_address = apply_filters('rt_restaurant_address_html',$ob_address);
    echo $ob_address;
}


add_action('add_meta_boxes', 'rt_restaurant_add_contactno');
add_action('save_post', 'rt_restaurant_save_contactno');
    
/**
 * Summary.Saves or update contact number of restaurant
 *
 * Description.
 *  This function will add or update post meta of contact number of restaurants.
 * 
 * @since Unknown
 * 
 * @param int $post_id
 * 
 * @var int $contactno  contact number of restaurant
 */
function rt_restaurant_save_contactno($post_id) {
    if (isset($_POST['restaurant_contact_no'])) {
        $contactno = $_POST['restaurant_contact_no'];
        update_post_meta($post_id, '_restaurant_contactno', $contactno);
    }
}

/**
 * Summary.add new meta box for contact number
 * 
 * @since Unknown.
 */
function rt_restaurant_add_contactno() {
    add_meta_box(
            'restaurants-contactno', esc_html__('Contact no.', 'Contact no.'), 'rt_restaurant_add_contactno_meta_box', 'restaurants', 'side', 'default'
    );
}

/**
 * Summary. display/add meta box on restaurants post
 * 
 * Description.
 *  Function to add contact number meta box into restaurant post type.
 * 
 * @since Unknown
 * 
 * @param array $post
 * 
 * @var int     $restaurant_contact contact number
 * @var int     $val                current existing contact number
 * @var string  $ob_contactno       output buffer value
 */
function rt_restaurant_add_contactno_meta_box($post) {
    // Output buffering start
    ob_start();
    $restaurant_contact = "";
    
    // Retriving contact number of restaurant
    $val = get_post_meta($post->ID, '_restaurant_contactno', true);
    
    // Check if contact number is already exists for restaurant
    if ($val != NULL && !empty($val)) {
        $restaurant_contact = $val;
    }
    echo "<input type='text' id='contact-no' value='" . $restaurant_contact . "' name='restaurant_contact_no' />";
 
    // Storing output buffer value into variable and clean output buffer.
    $ob_contactno=  ob_get_clean();
    
     /**
     * Summary. Filter for Change in contact number meta box
     *
     * Description.
     *  This filter will allow user to change display of post meta contact number
      * 
     * @since Unknown
     *
     * @param string $var Description. Name of filter
     * @param string $ob_contactno 
     */
    $ob_contactno = apply_filters('rt_restaurant_contactno_html',$ob_contactno);
    echo $ob_contactno;
}


add_action('add_meta_boxes', 'rt_restaurant_add_timing');
add_action('save_post', 'rt_restaurant_save_timing');

/**
 * Summary.   add meta box for timing
 *
 * @since Unknown
 */
function rt_restaurant_add_timing() {
    add_meta_box(
            'restaurants-timing', esc_html__('Timing & Working Days', 'Timing & Working Days'), 'rt_restaurant_add_timing_meta_box', 'restaurants', 'side', 'default'
    );
}

/**
 * Summary. add timing meta box on restaurant post display
 *
 * Description.
 *  add timing meta box.
 *
 * @since Unknown
 * 
 * @param int $post
 * 
 * @var array   $time                   current time of restaurant
 * @var array   $days                   array for key and name of days
 * @var string  $ob_timing_working_days output buffer value
 */
function rt_restaurant_add_timing_meta_box($post) {
    
    // Output buffer starts
    ob_start();
    ?>
    <form name="restaurant_timing">
        <table style="font-size: 12px;margin:auto">
            <tr style="text-align: center;font-size: 12px; font-weight: bold">
                <td>Day</td>
                <td>From</td>
                <td>To</td>
            </tr>
            <?php
            $time = get_post_meta($post->ID, '_timing', true);
            
            $days = array("mon" => "Monday", "tue" => "Tuesday", "wed" => "Wednesday", "thu" => "Thursday", "fri" => "Friday", "sat" => "Saturday", "sun" => "Sunday");
            foreach ($days as $key => $day) {
                $am = $pm = NULL;
                
                // Check if time is not already set for restaurant
                if (!empty($time) && is_array($time)) {
                    if ($time[0][$key][0] != NULL) {
                        $am = $time[0][$key][0];
                    }
                    if ($time[0][$key][1] != NULL) {
                        $pm = $time[0][$key][1];
                    }
                }
                ?>
                <tr>
                    <td name=" <?php echo $day ?> "> <?php echo $day ?> </td>
                    <td><input type="text" name="<?php echo "time[".$key."][]";?>" size="3" value=" <?php echo $am ?> ">AM</td>
                    <td><input type="text" name="<?php echo "time[".$key."][]";?>" size="3" value=" <?php echo $pm ?> ">PM</td>
                </tr>
            <?php
            }
            ?>
        </table>
    </form>
    <?php
    
    // Storing output buffer data into variable
    $ob_timing_working_days=  ob_get_clean();
    
     /**
     * Summary. Filter for display change of working days meta box.
     *
     * Description.
     *  This filter will allow user to change display of working days and time on admin side by passing html text as arguments. 
     *
     * @since Unknown
     *
     * @param string $var Description. name of filter
     * @param string $ob_timing_working_days
     */
    $ob_timing_working_days = apply_filters('rt_restaurant_timing_working_days_html',$ob_timing_working_days);
    echo $ob_timing_working_days;
}

/**
 * Summary. save timing postmeta for restaurant
 * 
 * Description.
 *  Function to save time and close days.
 * 
 * @since Unknown
 * 
 * @param int $post_id
 * 
 * @var array   $time       time data for current post
 * @var array   $close_days close days for restaurant
 */
function rt_restaurant_save_timing($post_id) {
    if (isset($_POST['time'])) {
        $time = array($_POST['time']);
        
        update_post_meta($post_id, '_timing', $time);
        // Computing close days
        $close_days = array();
        $i = 0;
        
        foreach ($time[0] as $key => $day) {
            if ($day[0] == NULL && $day[1] == NULL) {
                $close_days[$i++] = ($key);
            }
        }
        
        /**
        * Summary. Filter for close day calculation
        *
        * Description.
        *   This filter help user to make change in close day calculation code   
        * 
        * @since Unknown
        *
        * @param string $var        Description. name of filter
        * @param string $close_days Description. text change in close day calculation 
        */
        $close_days = apply_filters('rt_restaurant_close_days' , $close_days);
        update_post_meta($post_id, '_close_days', $close_days);
    }
}

add_action('wp_enqueue_scripts', 'rt_restaurant_add_css_js');

/**
 * Summary.   enqueue css for restaurant post type
 *
 * @since Unknown.
 * 
 * @var string  $template_directory_uri stores current template directory uri
 */
function rt_restaurant_add_css_js() {
    $template_directory_uri = plugin_dir_url( __FILE__ ) ;
    
    
    wp_enqueue_script('jquery');
    wp_localize_script('jquery', 'ajax_object', admin_url('admin-ajax.php'));

    // Enqueuing styles 
    wp_enqueue_style("restaurants_css", $template_directory_uri . '/assets/css/restaurant.css');
    wp_enqueue_style("Slick_css", $template_directory_uri . 'lib/slick/slick/slick.css');
    wp_enqueue_style("Slick_theme_css", $template_directory_uri . 'lib/slick/slick-theme.css');

    // Registering slick script
    wp_register_script('slick-js1', $template_directory_uri . '/lib/slick/slick.min.js');
    wp_enqueue_script('slick-js1');

//    wp_register_script('jquery-migrate-js', $template_directory_uri . '/js/jquery-migrate-1.2.1.min.js');
//    wp_enqueue_script('jquery-migrate-js');
 
    // Registering restaurant js
    wp_register_script('slider-js', $template_directory_uri . '/assets/js/restaurants.js');
    wp_enqueue_script('slider-js');
}


add_filter('comment_form_defaults', 'rt_restaurant_default_fields');

/**
 * Summary.   Review field add, save review and display review
 *
 * Description.
 *  Function to change default fields of comment by providing them in array.
 * 
 * @since Unknown
 * 
 * @var array   $default    default fields of review
 */
function rt_restaurant_default_fields() {
    $default ['comment_field'] = '<p class="comment-form-comment"><label for="Review">' . _x('Review', 'noun') . '</label> <br />'
            . '<textarea id="review_area" name="comment" cols="20" rows="5" width=50% aria-required="true" required="required"></textarea></p>';
    $default ['title_reply'] = __('Review Us');
    $default ['label_submit'] = __('Post Review');
    
     /**
     * Summary. Filter for change in default fields of comment
     *
     * Description.
     *  This filter will help user to change default fields of comment by providing them in array.  
     * 
     * @since Unknown
     *
     * @param string  $var     Description. name of filter
     * @param array   $default Description. array for default fields of comment.
     */
    $default = apply_filters('rt_restaurant_default_comment_fields', $default);
    
    return $default;
}

add_filter('comment_form_default_fields', 'rt_restaurant_custom_fields');

/**
 * Summary.   add fields to review.
 *
 * Description.
 *  Function to add custom fields in comment of custom post.
 * 
 * @since Unknown
 * 
 * @var array   $commenter  current commentor data
 * @var boolean $req        fields require value
 * @var boolean $aria_req   set value for area required or not
 * @var array   $fields     custom fields for review 
 */
function rt_restaurant_custom_fields() {
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ( $req ? " aria-required='true'" : '' );

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
     * Summary. filter for custom fields of comment
     *
     * Description.
     *  This filter will help user to add custom fields in comment of custom post
      * 
     * @since Unknown
     *
     * @param string  $var Description. name of filter
     * @param array $fields Description. array of custom fields for comment
     */
    $fields = apply_filters('rt_restaurant_custom_comment_fields',$fields);
    
    return $fields;
}

add_action('comment_form_logged_in_after', 'rt_restaurant_additional_fields');
add_action('comment_form_after_fields', 'rt_restaurant_additional_fields');

/**
 * Summary. Add field of rating in review
 *
 * Description.
 *  This function add rating form to Review.
 * 
 * @since Unknown
 * 
 * @var string  $ob_rating  output buffer value
 */
function rt_restaurant_additional_fields() {
    // Output buffer starts
    ob_start();
    echo '<p class="comment-form-rating">' .
    '<label for="rating">' . __('Rating') . '<span class="required">*</span></label>
  <span class="commentratingbox">';

    for ($i = 1; $i <= 5; $i++)
        echo '<span class="commentrating"><input type="radio" name="rating" id="rating" value="' . $i . '"/> ' . $i . '</span>';

    echo'</span>\n</p>';
    
    // Storing output buffer value into variable and clean it.
    $ob_rating=  ob_get_clean();
    
     /**
     * Summary. change html of additional fields.
     *
     * Description.
     *  This filter will help user to change in display of additional fields of comments
     * 
     * @since Unknown
     *
     * @param string $var Description. name of the filter
     * @param string $ob_rating
     */
    $ob_rating = apply_filters('rt_restaurant_rating_html',$ob_rating);
    echo $ob_rating;
}

add_action('comment_post', 'rt_restaurant_save_comment_meta_data');

/**
 * Summary. Add amd Save the comment meta rating along with comment
 *
 * Description.
 *  This function will add comment meta rating and save to comment. 
 * 
 * @since Unknown
 * 
 * @param int $comment_id
 * 
 * @var int $rating rating value of particular review
 */
function rt_restaurant_save_comment_meta_data($comment_id) {
    if (( isset($_POST['rating']) ) && ( $_POST['rating'] != ''))
        $rating = wp_filter_nohtml_kses($_POST['rating']);
    
    add_comment_meta($comment_id, 'rating', $rating);
    rt_restaurant_add_transient_rating($comment_id);
}


add_filter('preprocess_comment', 'rt_restaurant_verify_comment_meta_data');

/**
 * Summary. To check that rating is given or not
 *
 * Description.
 *  This function will check if reviwer has also give rating to restaurant.
 * @since Unknown
 * 
 * @param array commentdata
 */
function rt_restaurant_verify_comment_meta_data($commentdata) {
    if (!isset($_POST['rating']))
        wp_die(__('Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.'));
    return $commentdata;
}


add_action('add_meta_boxes_comment', 'rt_restaurant_extend_comment_add_meta_box');

/**
 *  Summary. Add an comment meta box of rating
 *
 * Description.
 *  add comment meta box for rating of restaurant.
 *
 * @since Unknown
 */
function rt_restaurant_extend_comment_add_meta_box() {
    add_meta_box('title', __('Comment Metadata - Extend Comment'), 'rt_restaurant_extend_comment_meta_box', 'comment', 'normal', 'high');
}

/**
 * Summary. edit comment meta box. 
 *
 * Description.
 *  This function will extend comment of custom post type restaurants.
 *  
 * @since Unknown
 * 
 * @param array $comment
 * 
 * @var int     $rating                 rating value of review
 * @var sting   $ob_rating_display_edit output buffer value
 */
function rt_restaurant_extend_comment_meta_box($comment) {
    // Output buffer starts
    ob_start();
    $rating = get_comment_meta($comment->comment_ID, 'rating', true);
    wp_nonce_field('extend_comment_update', 'extend_comment_update', false);
    ?>
    <p>
        <label for="rating"><?php _e('Rating: '); ?></label>
        <span class="commentratingbox">
    <?php
    for ($i = 1; $i <= 5; $i++) {
        echo '<span class="commentrating"><input type="radio" name="rating" id="rating" value="' . $i . '"';
        if ($rating == $i)
            echo ' checked="checked"';
        echo ' />' . $i . ' </span>';
    }
    ?>
        </span>
    </p>
    <?php
    // Store output buffer value into variable and clean it.
    $ob_rating_display_edit=  ob_get_clean();
    
     /**
     * Summary. change display of rating
     *
     * Description.
      *     user can change display of rating by this filter. output will store in $ob_rating_display_edit variable.
     *
     * @since Unknown
     *
     * @param string  $var .
     * @param string $ob_rating_display_edit 
     */
    $ob_rating_display_edit = apply_filters('rt_restaurant_rating_display_edit_html',$ob_rating_display_edit);
    echo $ob_rating_display_edit;
}


add_action('edit_comment', 'rt_restaurant_extend_comment_edit_metafields');

/**
 * Summary. Update comment meta data from comment editing screen 
 *
 * Description.
 *  This function will add or update new comment.
 * 
 * @since Unknown
 *
 * @param int $comment_id
 * 
 * @var int $rating rating of restaurant
 */
function rt_restaurant_extend_comment_edit_metafields($comment_id) {
    if (!isset($_POST['extend_comment_update']) || !wp_verify_nonce($_POST['extend_comment_update'], 'extend_comment_update'))
        return;

    if (( isset($_POST['rating']) ) && ( $_POST['rating'] != '')):
        $rating = wp_filter_nohtml_kses($_POST['rating']);
        update_comment_meta($comment_id, 'rating', $rating);
    else :
        delete_comment_meta($comment_id, 'rating');
    endif;
    rt_restaurant_add_transient_rating($comment_id);
}

/**
 * Summary.   Scripts add for map
 *
 * Description.
 *  This function will add Google map script.
 * 
 * @since Unknown
 */
add_action('get_footer', 'rt_restaurant_javascript_maps');

function rt_restaurant_javascript_maps() {
    ?>
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <?php
}

/**
 * photo gallery code
 */
add_theme_support('post-thumbnails', array('restaurants'));
set_post_thumbnail_size(50, 50);
add_image_size('single-post-thumbnail', 400, 9999);

/**
 * Summary. Display review of restaurants
 *
 * Description.
 *  This function will display custom review display code for restaurant post type.
 * @since Unknown
 * 
 * @param array $review Description. Array of comments
 * @param string $args
 * @param int $depth
 * 
 * @var string  $tag            for html tag
 * @var string  $add_below      html tag class
 * @var int     $commentrating  rating for review
 * @var string  $ob_review_all  output buffer value
 */
function rt_restaurants_reviews_html($review, $args, $depth){
    // Output buffer starts
    ob_start();
    
    $GLOBALS['comment'] = $review;
    extract($args, EXTR_SKIP);
    if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
    ?>
    <fieldset id="div-comment-<?php comment_ID() ?>" class="comment-body" itemprop="review" itemscope itemtype="http://schema.org/Review">
        <legend class="comment-author" itemprop="author">
            <!-- display avatar of reviewer -->
            <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $review, $args['avatar_size'] ); ?>
            <?php echo  get_comment_author_link() ; ?>
        </legend>
        
        <?php if ( $review->comment_approved == '0' ) : ?>
		<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em>
		<br />
	<?php endif; ?>

        <div class="comment-meta commentmetadata" itemprop="datePublished">
		<?php
			/* translators: 1: date, 2: time */
			printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)' ), '  ', '' );
		?>
	</div>
        
                <div itemprop="description">
                    <?php echo $review->comment_content; ?>
                </div>
                <?php
                    // fetching rating value for review
                    $commentrating = get_comment_meta(get_comment_ID(), 'rating', true);
                ?>
                <p class="comment-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                    <img src="<?php echo get_template_directory_uri() . '/star/' . $commentrating . 'star.png' ; ?>" />
                    <br/>
                    Rating: 
                    <strong itemprop="ratingValue">
                        <?php echo $commentrating ."\n";?>
                        / <span itemprop="bestRating">5</span>
                    </strong>
                </p>
        <div class="reply">
            <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
	</div>
    </fieldset>
    <?php
    
    // Store output buffer into variable and clean it
    $ob_review_all=  ob_get_clean();
    
     /**
     * Summary. Allow to change review display
     *
     * Description.
     *      User can change display of reviews by using this filter. Add output string into $ob_review_all variable.
     *
     * @since Unknown
     *
     * @param string  $var 
     * @param string $ob_review_all  
     */
    $ob_review_all = apply_filters('rt_restaurant_review_display',$ob_review_all);
    echo $ob_review_all;
}

/**
 * Summary. Set transient to store ratting and postmeta to store average
 *
 * Description.
 *  This function will create transient to store ratting total and total count of comment. It also create or update 
 *      restaurant_ratting post meta.
 * @since Unknown
 * 
 * @param int $comment_id Description. Current comment id
 * 
 * @var array   $comment        current review data
 * @var array   $total_comments total reviews for restaurant
 * @var array   $args           arguments for fetching all reviews
 * @var int     $rating         total rating for restaurant
 * @var int     $cnt            Total number of ratings
 * @var float   $average        average rating for restaurant
 * @var array   $transient_args arguments for transient
 * 
 */
function rt_restaurant_add_transient_rating($comment_id) {
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
    $average = intval($rating / $total_comments);

    echo $average;

    $transient_args = array(
        'post_id' => $comment->comment_post_ID,
        'count' => $total_comments,
        'rating' => $rating
    );

    set_transient('average_rating', $transient_args);
    
    // Post meta for average rating
    update_post_meta($comment->comment_post_ID, '_average_rating', $average);
}
