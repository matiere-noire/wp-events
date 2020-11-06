<?php
/*
Plugin Name: WP Events
Plugin URI: https://github.com/matiere-noire/
Description: Gestion des événements
Author: Matière Noire
Version: 1.4.1
Author URI: https://github.com/matiere-noire/
Text Domain: mn-wp-events
Domain Path: /languages
*/

namespace Events;

use Events\Classes\WPQueryEventsFilters;

require_once plugin_dir_path(__FILE__) . '/functions.php';

if (file_exists(plugin_dir_path(__FILE__) . '/vendor/autoload.php')) {
    require plugin_dir_path(__FILE__) . '/vendor/autoload.php';
}


$wpevents = new WPEvents();
$wpevents->initialize(__FILE__);

$WPQueryEventsFilters = new WPQueryEventsFilters();
$WPQueryEventsFilters->init();


register_activation_hook(__FILE__, array( $wpevents, 'plugin_activate' ));

add_action( 'plugins_loaded', 'Events\wp_event_load_plugin_textdomain' );

function wp_event_load_plugin_textdomain() {
    load_plugin_textdomain( 'mn-wp-events', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
