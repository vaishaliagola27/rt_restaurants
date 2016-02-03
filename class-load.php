<?php

namespace rtCamp\WP\rtRestaurants;
if (!class_exists('Load')) {

    /**
     *  This class will load files and data at theme and plugin loading time.
     * 
     * @author Vaishali Agola <vaishaliagola27@gmail.com>
     */
    class Load {

        /**
         * initialize hooks and other classes' method called
         */
        public function init() {
            //action for register post types
            add_action('init', array($this, 'register_post_type'));

            //action for register taxomoies
            add_action('init', array($this, 'register_taxonomy'));
            
            // create other classes' objects and call init() 
            $class_names = array('theme', 'admin');
            
            /**
             * Filter for initilize class objects and call init().
             *
             * Description.
             *  This filter will allow user to change classes names for creating objects and call init().
             * 
             * @since Unknown
             *
             * @param string $var           Description. Name of filter
             * @param array $class_names    Description. array to store classes name
             */
            $class_names = apply_filters('wp_hrt_class_loader', $class_names);
            
            foreach( $class_names as $class){
                //capitalize first letter of class name
                $class_uc = ucfirst($class);
                
                //create class object and call init() of every class
		
		$class_name = "rtCamp\WP\\rtRestaurants\\".$class_uc;
		${$class} = new $class_name();
                ${$class}->init();
                
            }
        }

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
            $taxonomy = array('restaurants_type', 'food_type');

            // Array of arguments of custom post type restaurant.
            $args = array(
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
            $new_post_types = array('restaurants' => $args);

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
            $new_post_types = apply_filters('rt_restaurant_custom_post_type', $new_post_types);

            // Loop to register all custom post types.
            foreach ($new_post_types as $key => $args) {
                register_post_type($key, $args);
            }
        }

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
        public function register_taxonomy() {
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

    }

}
