<?php

namespace Events;

use Events\Classes\WPEventsDB;
use Events\Rest\RestDates;


class WPEvents
{

    private $name;
    private $basename;
    private $path;
    private $url;
    private $slug;
    /**
     * @var string
     */
    private $normalizeName;
    /**
     * @var WPEventsDB
     */
    private $eventDBClass;

    public function __construct()
    {
        // Empty to ensure WP Events is only intialize once
    }

    public function initialize( $file )
    {

        $this->name     = __('WP Events');
        $this->normalizeName = htmlspecialchars( strtolower( $this->name ) );
        $this->basename = plugin_basename( $file );
        $this->path     = plugin_dir_path( $file );
        $this->url      = plugin_dir_url( $file );
        $this->slug     = dirname( $this->basename );

        add_action( 'init', array( $this, 'register_cpts') );
        $this->register_routes();
        $this->eventDBClass = new WPEventsDB();

        add_action( 'init', array( $this, 'register_scripts') );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets') );
    }

    public function register_cpts(){
        $wpeCPTs = new CPT\CustomPostTypesAndTax();
        $wpeCPTs->initialize();
    }

    public function register_routes(){
        $wpeRoutes = new RestDates();
        $wpeRoutes->initialize();
    }

    public function register_scripts(){
        // Scripts
        wp_register_script(
            'wpe-admin-events-js',
            "{$this->url}/build/events.js",
            array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-compose', 'wp-components', 'wp-data', 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-i18n' )
        );
    }

    public function enqueue_block_editor_assets(){

        $screen = get_current_screen();

        if( $screen && $screen->post_type === apply_filters( 'wpe/event_post_type_name', 'event' ) ){
            wp_enqueue_script( 'wpe-admin-events-js' );
            wp_enqueue_style( 'wpe-admin-events-css' );
            wp_set_script_translations( 'wpe-admin-events-js', 'mn-wp-events', $this->path . 'languages' );
        }

    }

    /**
     * Fire only once, when plugin is activated
     */
    public function plugin_activate(){
        $this->eventDBClass->initialize();
    }
}