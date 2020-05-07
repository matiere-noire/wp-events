<?php
/*
Plugin Name: WP Events
Plugin URI: https://github.com/matiere-noire/
Description: Gestion des événements
Author: Matière Noire
Version: 0.1
Author URI: https://github.com/matiere-noire/
*/

namespace Events;

if( file_exists( plugin_dir_path( __FILE__ ) . '/vendor/autoload.php' ) ){
    require plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';
}

$wpevents = new WPEvents();
$wpevents->initialize( __FILE__ );

register_activation_hook( __FILE__, array( $wpevents, 'plugin_activate' ) );