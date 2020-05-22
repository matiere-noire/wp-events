<?php

namespace Events\Rest;

use Events\Classes\WPEventsQuery;
use WP_REST_request;
use WP_REST_Response;
use WP_Error;
use WP_Post;

use function \wpe_insert_date;

class RestDates{

    private $table_name;
    /**
	 * Post type.
	 *
	 * @since 4.7.0
	 * @var string
	 */
    protected $post_type_associated;
    

    public function __construct(){
        $this->table_name = 'wp_wpe_dates';
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
             array(
                 'methods'  => \WP_REST_Server::CREATABLE,
                 'callback' => array( $this, 'createDate' ),
                 'permission_callback' => static function () {
                     return current_user_can( 'edit_posts' );
                 }
             )
        ) );

        register_rest_route( 'wpe/v1', '/dates/(?P<id>\d+)', array(
            'methods'  => \WP_REST_Server::ALLMETHODS,
            'callback' => array( $this, 'get_date' ),
            'args'     => array(
                'id' => array(
                    'validate_callback' => function($param){
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
     * SET DATE FROM ONE EVENT
     *
     * @param WP_REST_request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function createDate( $request ){

        global $wpdb;

        $result = $wpdb->insert(
            $this->table_name,
            array(
                'wpe_date_start' => get_date_from_gmt( $request['date_start'] ),
                'wpe_date_end'   => get_date_from_gmt ( $request['date_end'] ),
                'wpe_event_id'   => $request['event_id'],
                'wpe_place_id'   => $request['place_id']
            ),
            array(
                '%s', '%s', '%d', '%d'
            )
        );

        $date_id = $wpdb->insert_id;

        if( $result ):
            return new WP_REST_Response( $date_id , 200 );
        else:
            return new WP_Error( 'error', $wpdb->last_error );
        endif;


    }

    /**
     * Get all dates registered
     */
    public function get_dates(){
        $query_args = array();
        $dates = new WPEventsQuery();
    
        return $dates->query( $query_args );
    }

    /**
	 * Get the event, if the ID is valid.
	 *
	 * @param int $id Supplied ID.
	 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
	 */
	protected function get_event( $id ) {

		if ( (int) $id <= 0 ) {
			return new \WP_Error(
                'rest_post_invalid_id',
                sprintf( __( 'Invalid event ID : %s' ), $id ),
                array( 'status' => 404 )
            );
		}

        $post = get_post( (int) $id );

		if ( empty( $post ) || empty( $post->ID ) || $this->post_type_associated !== $post->post_type ) {
			return new \WP_Error(
                'rest_post_invalid_id',
                sprintf( __( 'Invalid event type : %s' ), $post->post_type ),
                array( 'status' => 404 )
            );;
		}

		return $post;
	}

    /**
     * Get all dates for one event
     * @param WP_REST_request $request
     * @return WP_Post|mixed|WP_Error|\WP_HTTP_Response|WP_REST_Response
     */
    public function get_date( WP_REST_request $request ){

        //$post = $this->get_event( $request['event_id'] );


        $error = new WP_Error();
        $result = false;

        switch ( $request->get_method() ) {
            case 'GET':
                $result = new WPEventsQuery( [ 'id' => $request['id'] ]);
                break;

            case 'POST':
                // 'POST' REQUEST NO NEED URI ID
                $error->add( 'no-post', 'Method POST don‘t require "id"');
                break;

            case 'PATCH':
            case 'PUT':
                // 'PUT' CAN CREATE OR UPDATE ON URI ID WITH THE COMPLETE OBJECT

                /**
                 * Check if object contain all require datas
                 */

                $result = wpe_insert_date( $request );
                break;
            case 'DELETE':

                $result = wpe_delete_date( $request['id'] );
                break;

            default:
                $error->add( 'no-method', 'Don‘t no request method');
                break;
        }

        // Dispatching errors
        if( ! empty( $error->get_error_codes() ) ){
            return $error;
        }

        if( is_wp_error( $result ) ){
            return $result;
        }

        if( $request->get_method() === 'GET' ):
            return new WP_REST_Response( $result , 200 );
        else:
            return new WP_REST_Response( $result . __( ' row(s) affected', 'utopiales' ) , 200 );
        endif;


    }

}