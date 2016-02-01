<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!class_exists('Theme')) {

    /**
     * Description of class-theme
     *
     * @author rtcamp
     */
    class Theme {

        //put your code here
        public function init() {
            add_action('wp_enqueue_scripts', array($this, 'add_css_js'));

            add_filter('comment_form_defaults', array($this, 'default_fields'));

            add_action('comment_form_logged_in_after', array($this, 'additional_fields'));
            add_action('comment_form_after_fields', array($this, 'additional_fields'));
            
        }

        /**
         * Summary.   enqueue css for restaurant post type
         *
         * @since Unknown.
         * 
         * @var string  $template_directory_uri stores current template directory uri
         */
        public function add_css_js() {
            $template_directory_uri = plugin_dir_url(__FILE__);


            wp_enqueue_script('jquery');
            wp_localize_script('jquery', 'ajax_object', admin_url('admin-ajax.php'));

            // Enqueuing styles 
            wp_enqueue_style("restaurants_css", $template_directory_uri . '/assets/css/restaurant.css');
            wp_enqueue_style("Slick_css", $template_directory_uri . 'lib/slick/slick/slick.css');
            wp_enqueue_style("Slick_theme_css", $template_directory_uri . 'lib/slick/slick/slick-theme.css');

            // Registering slick script
            wp_register_script('slick-js1', $template_directory_uri . 'lib/slick/slick/slick.min.js');
            wp_enqueue_script('slick-js1');

            //register script for google map
            wp_register_script('google-map', "http://maps.googleapis.com/maps/api/js?sensor=false");
            wp_enqueue_script('google-map');
//    wp_register_script('jquery-migrate-js', $template_directory_uri . '/js/jquery-migrate-1.2.1.min.js');
//    wp_enqueue_script('jquery-migrate-js');
            // Registering restaurant js
            wp_register_script('slider-js', $template_directory_uri . '/assets/js/restaurants.js');
            wp_enqueue_script('slider-js');
        }

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
        public function default_fields() {
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
        public function additional_fields() {
            // Output buffer starts
            ob_start();
            echo '<p class="comment-form-rating">' .
            '<label for="rating">' . __('Rating') . '<span class="required">*</span></label>
             <span class="commentratingbox">';

            for ($i = 1; $i <= 5; $i++)
                echo '<span class="commentrating"><input type="radio" name="rating" id="rating" value="' . $i . '"/> ' . $i . '</span>';

            echo'</span> \n </p>';

            // Storing output buffer value into variable and clean it.
            $ob_rating = ob_get_clean();

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
            $ob_rating = apply_filters('rt_restaurant_rating_html', $ob_rating);
            echo $ob_rating;
        }

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
        public function reviews_html($review, $args, $depth) {
            // Output buffer starts
            ob_start();

            $GLOBALS['comment'] = $review;
            extract($args, EXTR_SKIP);
            if ('div' == $args['style']) {
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
                    <?php if ($args['avatar_size'] != 0) echo get_avatar($review, $args['avatar_size']); ?>
                    <?php echo get_comment_author_link(); ?>
                </legend>

                <?php if ($review->comment_approved == '0') : ?>
                    <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.'); ?></em>
                    <br />
                <?php endif; ?>

                <div class="comment-meta commentmetadata" itemprop="datePublished">
                    <?php
                    /* translators: 1: date, 2: time */
                    printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time());
                    ?></a><?php edit_comment_link(__('(Edit)'), '  ', '');
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
                    <img src="<?php echo get_template_directory_uri() . '/star/' . $commentrating . 'star.png'; ?>" />
                    <br/>
                    Rating: 
                    <strong itemprop="ratingValue">
                        <?php echo $commentrating; ?>
                        / <span itemprop="bestRating">5</span>
                    </strong>
                </p>
                <div class="reply">
                    <?php comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                </div>
            </fieldset>
            <?php
            // Store output buffer into variable and clean it
            $ob_review_all = ob_get_clean();

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
            $ob_review_all = apply_filters('rt_restaurant_review_display', $ob_review_all);
            echo $ob_review_all;
        }

    }

}
