<?php

namespace Events;

use WP_REST_request;
use WP_REST_Response;
use WP_Error;

class WP_Events_Rest_Dates{

    /**
	 * Post type.
	 *
	 * @since 4.7.0
	 * @var string
	 */
    protected $post_type_associated;
    

    public function __construct(){
        // NULL
    }

    public function initialize(){
        $this->post_type_associated = 'event';

        //add_action( 'rest_api_init', array( $this, 'register_routes_all' ) );
        add_action( 'rest_api_init', array( $this, 'register_routes_dates' ) );

        // add_filter( 'rest_prepare_taxonomy', array( $this, 'rest_prepare_taxonomy' ), 10, 3 );
    }


    public function register_routes_dates(){

        register_rest_route( 'wpe/v1', '/dates', array(
            array(
                'methods'  => \WP_REST_Server::READABLE,
                'callback' => array( $this, 'get_dates' )
            ),
            // array(
            //     'methods'  => \WP_REST_Server::CREATABLE,
            //     'callback' => array( $this, 'rest_api_creat_dates' ),
            //     'permission_callback' => function () {
            //         return current_user_can( 'edit_posts' );
            //     }
            // )
        ) );

        register_rest_route( 'wpe/v1', '/dates/(?P<id>\d+)', array(
            'methods'  => \WP_REST_Server::ALLMETHODS,
            'callback' => array( $this, 'get_date' ),
            'args'     => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key){
                        return is_numeric( $param );
                    }
                )
            ),
            // 'permission_callback' => function () {
            //     return current_user_can( 'edit_posts' );
            // }
        ) );

    }


    /**
     * Get all dates registered
     */
    public function get_dates(){
        $query_args = array();
        $dates = new WP_Events_Query;
    
        return $dates->query( $query_args );
    }

    /**
	 * Get the event, if the ID is valid.
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
	 */
	protected function get_event( $id ) {
		$error = new \WP_Error(
			'rest_post_invalid_id',
			__( 'Invalid event ID.' ),
			array( 'status' => 404 )
		);

		if ( (int) $id <= 0 ) {
            var_dump('error');
			return $error;
		}

        $post = get_post( (int) $id );

		if ( empty( $post ) || empty( $post->ID ) || $this->post_type_associated !== $post->post_type ) {
			return $error;
		}

		return $post;
	}

    /**
     * Get all dates for one event
     */
    public function get_date( \WP_REST_request $request ){   
        $post = $this->get_event( $request['id'] );
        
        if ( is_wp_error( $post ) ) {
			return $post;
        }

        $query_args = array( 'id' => $request['id'] );

        $dates = new WP_Events_Query;

        return rest_ensure_response( $dates->query( $query_args ) );
    }

}