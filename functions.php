<?php


/**
 * Retrieves an array of the latest dates, or posts matching the given criteria.
 *
 * @param array $args
 * @return WP_Post[]|int[] Array of post objects or post IDs.
 */
function get_wpe_dates( $args = [] ) {
    
    $defaults = array(
		// TODO
	);
    $parsed_args = wp_parse_args( $args, $defaults );

	$get_dates = new Events\Classes\WPEventsQuery;
    return $get_dates->query( $parsed_args );

}