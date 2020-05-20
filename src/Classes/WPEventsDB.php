<?php

namespace Events\Classes;

class WPEventsDB
{

    public function __construct(){
        // NULL
    }

    public function initialize(){
        global $wpdb;

        // Add table to register dates from events
        $this->register_table( 'wpe_dates' );
        $this->install_table( 'wpe_dates', "
            wpe_date_id bigint(20) unsigned NOT NULL auto_increment,
            wpe_date_start datetime NOT NULL,
            wpe_date_end datetime NOT NULL,
            wpe_event_id bigint(20) unsigned NOT NULL,
            wpe_place_id int(20) unsigned NULL,
            PRIMARY KEY  (wpe_date_id),
            KEY wpe_date_start (wpe_date_start),
            KEY wpe_date_end (wpe_date_end),
            KEY wpe_event_id (wpe_event_id),
            KEY wpe_place_id (wpe_place_id),
            FOREIGN KEY (`wpe_event_id`) REFERENCES `{$wpdb->posts}` (`ID`) ON UPDATE CASCADE ON DELETE CASCADE
        " );

    }


    /**
     * Register a table with $wpdb.
     *
     * @param string $key The key to be used on the $wpdb object.
     * @param string $name (optional) The actual name of the table, without $wpdb->prefix.
     *
     * @return void
     */
    private function register_table( $key, $name = false ) {
        global $wpdb;
        if ( ! $name ) {
            $name = $key;
        }
        $wpdb->tables[] = $name;
        $wpdb->$key = $wpdb->prefix . $name;
    }

    /**
     * Runs the SQL query for installing/upgrading a table.
     *
     * @param string $key The key used in register_table().
     * @param string $columns The SQL columns for the CREATE TABLE statement.
     * @param array $opts (optional) Various other options.
     *
     * @return void
     */
    private function install_table( $key, $columns, $opts = array() ) {
        global $wpdb;
        $full_table_name = $wpdb->$key;
        if ( is_string( $opts ) ) {
            $opts = array( 'upgrade_method' => $opts );
        }
        $opts = wp_parse_args( $opts, array(
            'upgrade_method' => 'dbDelta',
            'table_options' => '',
        ) );
        $charset_collate = '';
        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( ! empty( $wpdb->charset ) ) {
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            }
            if ( ! empty( $wpdb->collate ) ) {
                $charset_collate .= " COLLATE $wpdb->collate";
            }
        }
        $table_options = $charset_collate . ' ' . $opts['table_options'];
        if ( 'dbDelta' == $opts['upgrade_method'] ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( "CREATE TABLE $full_table_name ( $columns ) $table_options" );
            return;
        }
        if ( 'delete_first' == $opts['upgrade_method'] ) {
            $wpdb->query( "DROP TABLE IF EXISTS $full_table_name;" );
        }
        $wpdb->query( "CREATE TABLE IF NOT EXISTS $full_table_name ( $columns ) $opts;" );
    }


    static function deregister_table(){
        // TODO
    }

}