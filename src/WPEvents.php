<?php

namespace Events;

require_once 'includes/class-wp-wpe-storage.php';
require_once 'includes/class-wp-wpe-query.php';
require_once 'includes/class-wp-wpe-rest-dates.php';
require_once 'includes/date.php';

class WPEvents
{

    private $name;
    private $basename;
    private $path;
    private $url;
    private $slug;

    public function __construct()
    {
        // Empty to ensure WP Events is only intialize once
    }

    public function initialize( $file )
    {

        $this->name     = __('WP Events');
        $this->normalizename = htmlspecialchars( strtolower( $this->name ) );
        $this->basename = plugin_basename( $file );
        $this->path     = plugin_dir_path( $file );
        $this->url      = plugin_dir_url( $file );
        $this->slug     = dirname( $this->basename );

        $this->register_cpts();
        $this->register_routes();

        add_action( 'init', array( $this, 'register_scripts') );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets') );
        add_action( 'admin_menu', array( $this, 'admin_menu_entry' ), 9 );   
    }

    public function register_cpts(){
        $wpeCPTs = new CPT\CustomPostTypesAndTax();
        $wpeCPTs->initialize();
    }

    public function register_routes(){
        $wpeRoutes = new WP_Events_Rest_Dates();
        $wpeRoutes->initialize();
    }

    public function register_scripts(){
        // Scripts
        wp_register_script(
            'wpe-admin-events-js',
            "{$this->url}/build/events.js",
            array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-compose', 'wp-components', 'wp-data', 'wp-blocks', 'wp-i18n', 'wp-editor' )
        );
    }

    public function enqueue_block_editor_assets(){

        $screen = get_current_screen();

        if( $screen && $screen->post_type === 'event' ){
            wp_enqueue_script( 'wpe-admin-events-js' );
            wp_enqueue_style( 'wpe-admin-events-css' );
        }

    }

    /**
     * Fire only once, when plugin is activated
     */
    public function plugin_activate(){
        $mnemStorage = new WP_Events_Storage();
        $mnemStorage->initialize();
    }

    /**
     * Add page in the admin menu
     * 
     * https://developer.wordpress.org/reference/functions/add_menu_page/
     */
    public function admin_menu_entry(){

        add_menu_page(  $this->name, $this->name, 'administrator', $this->normalizename, array( $this, 'admin_menu_entry_dashboard' ), 'dashicons-calendar-alt', 26 );

    }

    /**
     * Display settings on a dashboard page
     */
    public function admin_menu_entry_dashboard(){
        require_once "{$this->url}/src/includes/dashboard.php";
    }


}