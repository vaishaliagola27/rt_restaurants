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
//namespace declaration

namespace rtCamp\WP\rtRestaurants;

//define constant for plugin directory path
define( 'rtCamp\WP\rtRestaurants\PATH', plugin_dir_path( __FILE__ ) );

//define constant for plugin directory url
define( 'rtCamp\WP\rtRestaurants\URL', plugin_dir_url( __FILE__ ) );

//include classes
require_once \rtCamp\WP\rtRestaurants\PATH . 'includes/class-load.php';
require_once \rtCamp\WP\rtRestaurants\PATH . 'includes/class-theme.php';
require_once \rtCamp\WP\rtRestaurants\PATH . 'includes/class-admin.php';
require_once \rtCamp\WP\rtRestaurants\PATH . 'includes/class-review.php';

//instanciate class Load and call init()
$load_data = new Load();
$load_data->init();
