<?php

/**
 * Inspired by https://developer.wordpress.org/reference/classes/wp_query/
 * File: wp-includes/class-wp-query.php
 */

namespace Events;


class WP_EVENTS_Query{

    /**
	 * Query vars set by the user
	 */
	public $query;

	/**
	 * Query vars, after parsing
	 */
    public $query_vars = array();
    
    /**
	 * Get post database query.
	 */
	public $request;

    /**
	 * List of dates.
	 */
    public $dates;

    /**
	 * The amount of dates for the current query.
	 */
	public $dates_count = 0;
    

	/**
	 * Constructor.
	 *
	 * Sets up the WordPress query, if parameter is not empty.
	 *
	 * @param string|array $query URL query string or array of vars.
	 */
    public function __construct( $query = '' ){
        if ( ! empty( $query ) ) {
            $this->query( $query );
        }
    }

    /**
	 * Sets up the WordPress query by parsing query string.
	 *
	 * @param string|array $query URL query string or array of query arguments.
	 * @return WP_Post[]|int[] Array of date objects or date IDs.
	 */
    public function query( $query ) {
        $this->init();
        
        // Transform string url in array
        // See more here https://developer.wordpress.org/reference/functions/wp_parse_args/
        $this->query      = wp_parse_args( $query );
        
		$this->query_vars = $this->query;
		return $this->get_dates();
    }
    
    /**
	 * Initiates object properties and sets default values.
	 */
    public function init(){
        unset( $this->dates );
        unset( $this->query );
        unset( $this->request );
        $this->query_vars = array();
        $this->post_count   = 0;
    }

    /**
	 * Fills in the query variables, which do not exist within the parameter.
	 *
	 * @param array $array Defined query variables.
	 * @return array Complete query variables with undefined ones filled in empty.
	 */
	public function fill_query_vars( $array ) {
		$keys = array(
			'event_id'
		);

		foreach ( $keys as $key ) {
			if ( ! isset( $array[ $key ] ) ) {
				$array[ $key ] = '';
			}
		}

		$array_keys = array(
		);

		foreach ( $array_keys as $key ) {
			if ( ! isset( $array[ $key ] ) ) {
				$array[ $key ] = array();
			}
        }
        
		return $array;
	}

    /**
	 * Parse a query string and set query type booleans.
	 */
	public function parse_query( $query = '' ) {
        if ( ! empty( $query ) ) {
			$this->init();
			$this->query      = wp_parse_args( $query );
			$this->query_vars = $this->query;
		} elseif ( ! isset( $this->query ) ) {
			$this->query = $this->query_vars;
		}

		$this->query_vars = $this->fill_query_vars( $this->query_vars );
    }

    /**
	 * Retrieves an array of dates based on query variables.
	 *
	 * There are a few filters and actions that can be used to modify the date
	 * database query.
	 *
	 * @since 1.5.0
	 *
	 * @return WP_Post[]|int[] Array of date objects or date IDs.
	 */
	public function get_dates() {
		global $wpdb;

        $this->parse_query();

        // Shorthand.
        $q = &$this->query_vars;

        $where = '';

        $this->request = "
            SELECT d.wpe_date_id, d.wpe_date_start, d.wpe_date_end, d.wpe_event_id, d.wpe_place_id
            FROM {$wpdb->prefix}wpe_dates d
            LEFT OUTER JOIN {$wpdb->posts} AS p_event ON d.wpe_event_id = p_event.ID
            WHERE 1 = 1
        ";

        // We will update request in regards of args passed to the query
        if ( '' !== $q['event_id'] ) {
            $where .= " AND d.wpe_event_id = {$event_id} ";
        }

        $this->request .= $where;
        
        $this->dates = $wpdb->get_results( $this->request );
        $this->dates_count = count( $this->dates );

        return $this->dates;
    }

}