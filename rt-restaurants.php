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

register_activation_hook(__FILE__, 'rt_restaurants_flush_rewrites');

/**
 * To flush the rewrite rules for plugin.
 */
function rt_restaurants_flush_rewrites() {
    flush_rewrite_rules();
}

require_once 'class-load.php';
require_once 'class-theme.php';
require_once 'class-admin.php';

$load_data = new Load();
$load_data->init();

/**
 * Summary. call Theme class function reviews_html 
 *
 * @since Unknown
 * 
 * @param array $review Description. Array of comments
 * @param string $args
 * @param int $depth
 */
function rt_restaurants_reviews_html($review, $args, $depth) {
    Theme::reviews_html($review, $args, $depth);
}
