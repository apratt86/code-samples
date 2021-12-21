<?php

/**
 * File that defines methods to commit data to custom tables
 *
 * @link       https://github.com/apratt86
 * @since      1.0.0
 * @author     Aaron Pratt <aaronprattdesign@gmail.com>
 */

define( 'LOG_TABLE', 'backup_log' );

class Log_Message {

    static public function store_message( $type = 'message', $message = '', $wp_prefix = true ) {
        global $wpdb;

        $type = sanitize_text_field( $type );
        $message = wp_kses_post( $message );

        if ( $wp_prefix ):
            $table_prefix = $wpdb->prefix;
        else:
            $table_prefix = '';
        endif;

        $wpdb->insert( $table_prefix . LOG_TABLE, [ 'type' => $type, 'message' => $message, 'log_date' => current_time( 'mysql' ) ] );
    }

}