<?php

/**
 * File that defines methods to send post requests to the Backup API
 *
 * @link       https://github.com/apratt86
 * @since      1.0.0
 * @author     Aaron Pratt <aaronprattdesign@gmail.com>
 */

class Backup {

    /** This Class Variables */
    /**
     * TO DO:
     * Convert Public Member Variables to private and create methods for modifying members
     */
    public $gateway, $backup_api_obj;

    private $post_types, $api_call_obj, $opts, $hash, $filesize, $start_time;

    public function __construct( $post_types, $opts ) {

        $this->start_time = microtime(true);

        $this->post_types = $post_types;
        $this->opts = $opts;

        /** Define the top level body array */
        $site_url = get_bloginfo( 'url' );
        $this->domain = str_replace(array('http://','https://'), '', $site_url);

        /** Init Variables */
        if( isset($this->opts['backup_endpoint']) ) :
            $this->endpoint = $this->opts['backup_endpoint'];
        else :
            $this->endpoint = "";
        endif;

        $this->api_key = ( isset( $this->opts['backup_api_key'] ) ) ? $this->opts['backup_api_key'] : false;

        /** Compile the Post Request Body */
        // Generate JSON formatted post body.
        $json_post_body = json_encode( $this->post_body() );
        // Generate and store hash for JSON post body.
        $this->hash = sha1( $json_post_body );
        /**
         * Updated the second arg for 8bit measurement of data, accurate representation of bytes in the post body string.
         */
        $this->filesize = mb_strlen( $json_post_body, '8bit' );

        /** Establish Connection */
        $this->gateway = $this->gateway();
        $this->gateway_run_time = microtime(true);
        $gateway_body = json_decode( $this->gateway->get_response_body(), true );
        $first_api_call_runtime = microtime(true) - $this->start_time;

        // If the gateway response code is not 200.
        // Log gateway error message.
        if ( 200 !== $this->gateway->get_response_code() ) :
            $gateway_error = json_decode($this->gateway->get_response_body(), true)['message'];
            Log_Message::store_message( 'ERROR',
                "<h3 class='log'>Gateway API Returned ERROR CODE</h3> {$this->gateway->get_response_code()} | "
              . "{$gateway_error}<br><strong>Time to Exec:</strong> {$first_api_call_runtime} seconds" );
        // if gateway resonse is 200.
        else:
            // if gateway_body IS NOT set.
            if ( !isset($gateway_body) ):
                Log_Message::store_message( 'ERROR', "<h3 class='log'>Gateway body is empty.</h3><strong>Time to Exec:</strong> {$first_api_call_runtime} seconds" );
            else:
                // if gateway body is set.
                // check if status_code is 100 (proceed).
                if ( 100 === $gateway_body['statusCode'] ) :
                    // log message to proceed with backup.
                    $signed_url = $gateway_body['body']['url'];
                    Log_Message::store_message( 'SUCCESS', "<h3 class='log'>Gateway API Returned Code 100 - Proceed with backup.</h3><strong>Time to Exec:</strong> {$first_api_call_runtime} seconds" );
                    // Create backup api object and send backup to s3.
                    $this->backup_api_obj = $this->send_backup( $json_post_body, $signed_url );

                    $storage_run_time = microtime(true) - $this->start_time;
                    // Make sure backup gives back a response. If not log error.
                    if( empty($this->backup_api_obj->get_response_code() ) ):
                        Log_Message::store_message( 'ERROR', "<h3 class='log'>Storage API ERROR</h3>Could Not Store Data, Response Code Empty. Execution Timeout.<br><strong>Time to Exec:</strong> {$storage_run_time} seconds" );
                    else:
                        // If backup response is not 200. Failed backup, log error.
                        if (200 !== $this->backup_api_obj->get_response_code()):
                            Log_Message::store_message( 'ERROR', "<h3 class='log'>Storage API Returned ERROR CODE</h3> Response code: {$this->backup_api_obj->get_response_code()}<br><strong>Time to Exec:</strong> {$storage_run_time} seconds" );

                        // Backup Successful, log success message.
                        else:
                            Log_Message::store_message( 'SUCCESS', "<h3 class='log'>Successfully Stored Backup</h3><strong>Posts:</strong> {$this->post_count} | <strong>File Size:</strong> {$this->filesize} bytes<br><strong>Hash:</strong> {$this->hash}<br><strong>Time to Exec:</strong> {$storage_run_time} seconds" );
                        endif;
                    endif;

                // Backup is not needed log no change response.
                elseif ( 304 === $gateway_body['statusCode'] ):
                    Log_Message::store_message( 'NO_CHANGE', "<h3 class='log'>Gateway API Returned: No Change</h3> <strong>Posts:</strong> {$this->post_count} | <strong>File Size:</strong> {$this->filesize} bytes<br><strong>Hash:</strong> {$this->hash}<br><strong>Time to Exec:</strong> {$first_api_call_runtime} seconds" );

                // If gateway_body does not include a proper status code log unexpected response.
                else:
                    Log_Message::store_message( 'ERROR', "<h3 class='log'>An unexpected response was recieved from the gateway.</h3><br><strong>Time to Exec:</strong> {$first_api_call_runtime} seconds" );
                endif;

            endif;

        endif;

    }

    /**
     * Get the count of all the posts included in the backup
     */
    private function get_post_count( $posts ) {
        if ( is_array( $posts ) && 0 < count( $posts ) ):
            $r = count( $posts );
        else:
            $r = 0;
        endif;

        return $r;
    }

    /**
     * Get all the meta data for each post and map the fields for the backup file
     */
    private function compile( $posts = [] ) {

        $r = [];

        if ( is_array( $posts ) && 0 < count( $posts ) ):
            foreach( $posts as $key => $post ):
                $r[$key] = [
                    'ID' => $post->ID,
                    'permalink' => get_permalink( $post->ID ),
                    /** wp_posts table columns */
                    'post_author' => "{$post->post_author}",
                    'post_date' => "{$post->post_date}",
                    'post_date_gmt' => "{$post->post_date_gmt}",
                    'post_content' => "{$post->post_content}",
                    'post_title' => "{$post->post_title}",
                    'post_excerpt' => "{$post->post_excerpt}",
                    'post_status' => "{$post->post_status}",
                    'post_password' => "{$post->post_password}",
                    'post_name' => "{$post->post_name}",
                    'to_ping' => "{$post->to_ping}",
                    'pinged' => "{$post->pinged}",
                    'post_modified' => "{$post->post_modified}",
                    'post_modified_gmt' => "{$post->post_modified_gmt}",
                    'post_content_filtered' => "{$post->post_content_filtered}",
                    'post_parent' => "{$post->post_parent}",
                    'guid' => "{$post->guid}",
                    'menu_order' => "{$post->menu_order}",
                    'post_type' => "{$post->post_type}",
                    'post_mime_type' => "{$post->post_mime_type}",
                    'comment_count' => "{$post->comment_count}",
                    'filter' => "{$post->filter}",
                    'post_meta' => maybe_serialize( get_post_meta( $post->ID ) ),
                    // Retrieve the terms with the following method, because it is the most performance efficient way discovered.
                    // Get the post's terms based on taxonomy types associated with post.
                    'terms' => maybe_serialize(
                        wp_get_post_terms(
                            $post->ID,
                            get_object_taxonomies( $post )
                        )
                    ),
                    'attachments' => maybe_serialize( get_attached_media( 'image', $post ) ),
                ];
            endforeach;
        endif;

        return $r;

    }

    /**
     * Create the backup data:
     * - Add the domain to the post request body
     * - Get all the Posts
     * - Run posts through the compile method to add all the meta data for each post and map all the fields
     * - Return the body of the request
     */
    private function post_body() {
        $authors = ( isset( $this->opts['exclude_authors'] ) ) ? $this->opts['exclude_authors'] : false;

        /** NOTE: DOMAIN SHOULD INCLUDE TLD (.com, .net, .co, etc.) */
        $body['domain'] = $this->domain;

        /** Get the posts data */
        $returned_posts = [];

        //post types passed in through Backup constructor.
        $post_types_temp_arr = $this->post_types;
        $post_and_page_types_array = [];
        $wm_post_types_array = [];
        //separating posts and pages into separate array from all other post types.
        foreach ( $post_types_temp_arr as $post_type1 ):
            if ( ( $post_type1 == 'post' ) || ( $post_type1 == 'page' ) ):
                array_push( $post_and_page_types_array, $post_type1 );
            else:
                array_push( $wm_post_types_array, $post_type1 );
            endif;
        endforeach;

        //getting posts and/or pages for not excluded authors and appending to returned_posts array.
        if ( count($post_and_page_types_array) > 0 ):
            $post_args = [
                'numberposts' => -1,
                'author__not_in' => $authors,
                'post_type' => $post_and_page_types_array,
                'post_status' => ['publish', 'draft', 'future', 'trash', 'pending', 'private']
            ];
            $temp_posts = get_posts( $post_args );
            foreach ( $temp_posts as $post_index => $post ):
                array_push( $returned_posts, $post );
            endforeach;
        endif;

        //getting all other post types and appending to returned_posts array.
        if ( count($wm_post_types_array) > 0 ):
            $post_args = [
                'numberposts' => -1,
                'post_type' => $wm_post_types_array,
                'post_status' => ['publish', 'draft', 'future', 'trash', 'pending', 'private']
            ];
            $temp_posts = get_posts( $post_args );
            foreach ( $temp_posts as $post_index => $post ):
                array_push( $returned_posts, $post );
            endforeach;
        endif;

        $this->post_count = $this->get_post_count( $returned_posts );
        //gathering post_meta for each post
        $body['content'] = $this->compile( $returned_posts );

        return $body;
    }

    /**
     * Send the request to the gateway API lambda function
     */
    private function gateway() {
        $gateway = new API_Call(
            $this->endpoint,
            'POST',
            [
                'timeout' => 5,
                'httpversion' => '1.1',
                'headers' => [
                    'x-api-key' => $this->api_key,
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(
                    [
                        'domain' => $this->domain,
                        'hash' => $this->hash,
                        'filesize' => $this->filesize,
                    ]
                ),
            ] /** gateway Post Data */
        );

        return $gateway;
    }


    /**
     * Send the backup to the Storage API
     */
    private function send_backup( $json_post_body, $signed_url ) {
        $backup = new API_Call(
            $signed_url,
            'PUT',
            [
                'timeout' => 120,
                'httpversion' => '1.1',
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $json_post_body,
            ] /** gateway Post Data */
        );

        return $backup;
    }

}
