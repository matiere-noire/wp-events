<?php

namespace Events;

/**
 * Retrieves an array of the latest dates, or posts matching the given criteria.
 *
 * @return WP_Post[]|int[] Array of post objects or post IDs.
 */
function get_wpe_dates( $args = null ) {
    
    $defaults = array(
		// TODO
	);

    $parsed_args = wp_parse_args( $args, $defaults );

	$get_dates = new WP_Events_Query;
    return $get_dates->query( $parsed_args );

}