<?php

/**
 * Defines all functions for HTTP API Requests
 *
 * @link       https://github.com/apratt86
 * @since      1.0.0
 * @author     Aaron Pratt <aaronprattdesign@gmail.com>
 */

class API_Call {

    private $response, $response_body, $response_code, $response_body_array;

    public function __construct( $endpoint, $method = 'GET', $args = [], $query_params = [] ) {
        /** Input Values */
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->args = $args;
        $this->query_params = $query_params;

        /** Return Values */
        $args_error = '';
        if ( $this->args_check( $args_error ) ):
            $this->args['method'] = $this->method();
            $this->response = wp_remote_request( $this->endpoint(), $this->args );
            $this->response_body = wp_remote_retrieve_body( $this->response );
            $this->response_code = wp_remote_retrieve_response_code( $this->response );
            $this->response_body_array = json_decode( $this->response_body, true );
        else:
            wp_die( $args_error, 'API_Call Class Error', [ 'back_link' => true, 'response' => 500 ] );
        endif;
    }

    /**
     * Private Input Methods
     */
    private function method() {
        switch ( $this->method ) :
            /** GET, POST, PUT, TRACE, CONNECT */
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'TRACE':
            case 'CONNECT':
                $r = $this->method;
            break;
            /** DEFAULT GET Method */
            default:
                wp_die( 'Method Accepts All Caps GET, POST, PUT, TRACE, and CONNECT HTTP Requests.', 'Unaccepted API Call Method' );
            break;
        endswitch;

        /** Return the passed method */
        return $r;
    }

    private function args_check( &$args_error ) {
        $success = true;
        $args_set = [
            'timeout',
            'redirection',
            'httpversion',
            'blocking',
            'headers',
            'body',
            'cookies',
        ];

        if ( is_array( $this->args ) && 0 < count( $this->args ) ):
            foreach( $this->args as $key => $value ):
                if ( ! in_array( $key, $args_set ) ):
                    $success = false;
                    $args_error .= 'PHP Class Object API_Call Returned Error - Invalid Argument Key: ' . $key . '<br>';
                endif;
            endforeach;
        endif;

        return $success;
    }

    private function endpoint() {
        return add_query_arg( $this->query_params, $this->endpoint );
    }

    /**
     * Public Return Methods
     */
    public function response() {
        return $this->response;
    }

    public function get_response_body_array() {
        return $this->response_body_array;
    }

    public function get_response_body() {
        return $this->response_body;
    }

    public function get_response_code() {
        return $this->response_code;
    }

}