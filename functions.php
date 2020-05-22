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


/**
 * @param array $args
 * @return bool|false|int|WP_Error
 */
function wpe_insert_date( $args = [] )
{
    global $wpdb;
    $params = [];
    $error = new WP_Error();

    foreach ([ 'date_start', 'date_end', 'event_id'] as $value) {
        if( !isset( $args[ $value ] ) ):
            $error->add( 'missing-field', "\"{$value}\" is require");
            break;
        else:
            if( $value === 'date_start' || $value === 'date_end' ){
                $params[ 'wpe_' . $value ] = get_date_from_gmt( $args[ $value ] );
            }else{
                $params[ 'wpe_' . $value ] = $args[ $value ];
            }
        endif;
    }

    if( isset( $params['wpe_event_id'] ) ){
        return $wpdb->update(
            $wpdb->wpe_dates,
            $params,
            array(
                'wpe_date_id'  => $args['id'],
                'wpe_event_id' => $args['event_id'],
            ),
            array(
                '%s', '%s', '%s'
            ),
            array(
                '%d', '%d'
            )
        );

    }

    return $error;
}


/**
 * @param int $date_id
 * @return bool|false|int
 */
function wpe_delete_date( $date_id ){
    global $wpdb;

    return $wpdb->delete(
        $wpdb->wpe_dates,
        array(
            'uto_date_id' => $date_id
        ),
        array(
            '%d'
        )
    );
}
