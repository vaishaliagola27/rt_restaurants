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
            //add and save meta box of address
            add_action('add_meta_boxes', array($this, 'add_address'));
            add_action('save_post', array($this, 'save_address'));

            //add and save contact number meta box value
            add_action('add_meta_boxes', array($this, 'add_contactno'));
            add_action('save_post', array($this, 'save_contactno'));

            //add and save timing meta box 
            add_action('add_meta_boxes', array($this, 'add_timing'));
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
        }

        /**
         * Summary. add new meta box of address for restaurants post type.
         *
         * Description.
         *  Function to add address meta box.
         * 
         * @since Unknown 
         */
        public function add_address() {
            add_meta_box(
                    'restaurants-address', esc_html__('Address', 'Address'), array($this, 'add_address_meta_box'), 'restaurants', 'side', 'default'
            );
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
        public function add_address_meta_box($post) {
            // output buffer start
            ob_start();

            // Array for address fields
            $addr = array("streetAddress" => "Street Address", "addressLocality" => "Locality", "addressRegion" => "Region", "postalCode" => "Postal Code", "addressCountry" => "Country");

            // Retriving address post meta for particular post.
            $add = get_post_meta($post->ID, '_restaurant_address', true);
            ?>
            <table class="address_table">
                <?php
                foreach ($addr as $key => $value) {
                    if ($add != NULL && !empty($add)) {
                        $value = $add[0][$key];
                    } else {
                        $value = '';
                    }
                    ?>
                    <tr>
                        <td><label> <?php echo $addr[$key]; ?></label></td>
                        <td>
                            <input size="15" type="text" name="<?php echo "restaurant_add[" . $key . "]"; ?>" value="<?php echo $value; ?>" />
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
            $ob_address = apply_filters('rt_restaurant_address_html', $ob_address);
            echo $ob_address;
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
        public function save_address($post_id) {
            if (isset($_POST['restaurant_add'])) {
                $address = array($_POST['restaurant_add']);
                update_post_meta($post_id, '_restaurant_address', $address);
            }
        }

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
        public function save_contactno($post_id) {
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
        public function add_contactno() {
            add_meta_box(
                    'restaurants-contactno', esc_html__('Contact no.', 'Contact no.'), array($this, 'add_contactno_meta_box'), 'restaurants', 'side', 'default'
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
        public function add_contactno_meta_box($post) {
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
            $ob_contactno = ob_get_clean();

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
            $ob_contactno = apply_filters('rt_restaurant_contactno_html', $ob_contactno);
            echo $ob_contactno;
        }

        /**
         * Summary.   add meta box for timing
         *
         * @since Unknown
         */
        public function add_timing() {
            add_meta_box(
                    'restaurants-timing', esc_html__('Timing & Working Days', 'Timing & Working Days'), array($this, 'add_timing_meta_box'), 'restaurants', 'side', 'default'
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
        public function add_timing_meta_box($post) {

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
                            <td><input type="text" name="<?php echo "time[" . $key . "][]"; ?>" size="3" value=" <?php echo $am ?> ">AM</td>
                            <td><input type="text" name="<?php echo "time[" . $key . "][]"; ?>" size="3" value=" <?php echo $pm ?> ">PM</td>
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
            $ob_timing_working_days = apply_filters('rt_restaurant_timing_working_days_html', $ob_timing_working_days);
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
        public function save_timing($post_id) {
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
                $close_days = apply_filters('rt_restaurant_close_days', $close_days);
                update_post_meta($post_id, '_close_days', $close_days);
            }
        }

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
        public function custom_fields() {
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
            $fields = apply_filters('rt_restaurant_custom_comment_fields', $fields);

            return $fields;
        }

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
        public function save_comment_meta_data($comment_id) {
            if (( isset($_POST['rating']) ) && ( $_POST['rating'] != ''))
                $rating = wp_filter_nohtml_kses($_POST['rating']);

            add_comment_meta($comment_id, 'rating', $rating);
            $this->add_transient_rating($comment_id);
        }

        /**
         * Summary. To check that rating is given or not
         *
         * Description.
         *  This function will check if reviwer has also give rating to restaurant.
         * @since Unknown
         * 
         * @param array commentdata
         */
        public function verify_comment_meta_data($commentdata) {
            if (!isset($_POST['rating']))
                wp_die(__('Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.'));
            return $commentdata;
        }

        /**
         *  Summary. Add an comment meta box of rating
         *
         * Description.
         *  add comment meta box for rating of restaurant.
         *
         * @since Unknown
         */
        public function extend_comment_add_meta_box() {
            add_meta_box('title', __('Comment Metadata - Extend Comment'), array($this, 'extend_comment_meta_box'), 'comment', 'normal', 'high');
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
        public function extend_comment_meta_box($comment) {
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
            $ob_rating_display_edit = ob_get_clean();

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
            $ob_rating_display_edit = apply_filters('rt_restaurant_rating_display_edit_html', $ob_rating_display_edit);
            echo $ob_rating_display_edit;
        }

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
        public function extend_comment_edit_metafields($comment_id) {
            if (!isset($_POST['extend_comment_update']) || !wp_verify_nonce($_POST['extend_comment_update'], 'extend_comment_update'))
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

    }

}
