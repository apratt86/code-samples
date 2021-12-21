<?php
/**
 * Local Business Structured Data
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

require_once plugin_dir_path( __FILE__ ) . 'model-functions.php';

function wmlp_sdata_biz_hours() {
    $hours = wmlp_get_biz_hours();
    if ( false !== $hours && is_array( $hours ) ) :
        $hr_json['openingHoursSpecification'] = [];

        foreach ( $hours as $day => $d_opt ) :
            switch( $day ):
                case 'mon' : $day_of_week = 'Monday'; break;
                case 'tue' : $day_of_week = 'Tuesday'; break;
                case 'wed' : $day_of_week = 'Wednesday'; break;
                case 'thu' : $day_of_week = 'Thursday'; break;
                case 'fri' : $day_of_week = 'Friday'; break;
                case 'sat' : $day_of_week = 'Saturday'; break;
                case 'sun' : $day_of_week = 'Sunday'; break;
            endswitch;

            if ( true !== isset( $d_opt['closed_all_day'] ) && true !== (bool)$d_opt['closed_all_day'] ) :
                $opens = ( isset( $d_opt['opens'] ) ) ? $d_opt['opens'] : '00:00';
                $closes = ( isset( $d_opt['closes'] ) ) ? $d_opt['closes'] : '00:00';
                array_push( $hr_json['openingHoursSpecification'], [ '@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $day_of_week, 'opens' => $opens, 'closes' => $closes ] );
            endif;

        endforeach;

        return $hr_json;
    endif;
    
    return [];
}

function wmlp_sdata_biz_addr() {
    $addr = wmlp_get_biz_addr();
    if ( false !== $addr && is_array( $addr ) ) :

        $addr_json['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => ( isset( $addr['street'] ) ) ? $addr['street'] : '',
            'addressLocality' => ( isset( $addr['city'] ) ) ? $addr['city'] : '',
            'addressRegion' => ( isset( $addr['region'] ) ) ? $addr['region'] : '',
            'postalCode' => ( isset( $addr['postal_code'] ) ) ? $addr['postal_code'] : '',
            'addressCountry' => ( isset( $addr['country'] ) ) ? $addr['country'] : ''
        ];

        $addr_json['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => ( isset( $addr['lat'] ) ) ? $addr['lat'] : '',
            'longitude' => ( isset( $addr['lon'] ) ) ? $addr['lon'] : ''
        ];
        return $addr_json;
    else :
        return [];
    endif;
}

function wmlp_sdata_biz_details() {
    $addr = wmlp_get_biz_addr();
    $biz_area = ( isset( $addr['country'] ) ) ? $addr['country'] : 'US';
    $biz_name = ( isset( $addr['name'] ) ) ? $addr['name'] : get_bloginfo( 'name' );
    $c_info = wmlp_get_biz_contact();
    $logo = ( isset( wmlp_get_options()['gen_settings']['logo'] ) ) ? wp_get_attachment_url( wmlp_get_options()['gen_settings']['logo'], 'medium', false ) : wp_get_attachment_url( get_custom_logo() );

    $details = [];
    $details['image'] = $logo;

    /**
     * Only output contact points if available
     */
    if ( isset( $c_info['phone_link'] ) || isset( $c_info['sales_link'] ) ) :
        $details['contactPoint'] = [];
    endif;

    foreach ( $c_info as $c_det_key => $c_det ) :
        switch( $c_det_key ):
            case 'phone_link':
                $details['telephone'] = $c_det;
            break;
            case 'sales_link':
                $sales_contact = [
                    '@type' => 'ContactPoint',
                    'contactType' => 'sales department',
                    'areaServed' => $biz_area,
                    'telephone' => $c_det,
                ];

                if ( isset( $c_info['sales_email'] ) ) :
                    $sales_contact['email'] = 'mailto:' . $c_info['sales_email'];
                endif;

                array_push( $details['contactPoint'], $sales_contact);
            break;
            case 'service_link':
                $service_contact = [
                    '@type' => 'ContactPoint',
                    'contactType' => 'service department',
                    'areaServed' => $biz_area,
                    'telephone' => $c_det,
                ];
                
                if ( isset( $c_info['service_email'] ) ) :
                    $service_contact['email'] = 'mailto:' . $c_info['service_email'];
                endif;

                array_push( $details['contactPoint'], $service_contact );
            break;
            case 'primary_email':
                $details['email'] = 'mailto:' . $c_det;
            break;
        endswitch;
    endforeach;

    $details['name'] = $biz_name;

    return $details;
}

/**
 * ======================================
 * | Output Functions Uses wp_head Hook |
 * ======================================
 */

function wmlp_sdata_local_business() {
    $biz = [];
    $biz['@context'] = 'http://schema.org';
    $biz['@type'] = 'LocalBusiness';
    $biz['@id'] = get_site_url();

    $s_data_opts = wmlp_get_sdata_opts();

    if ( isset( $s_data_opts['price_range'] ) ):
        $biz['priceRange'] = $s_data_opts['price_range'];
    endif;

    echo '<script id="wmlp_business" type="application/ld+json">';
    echo json_encode( $biz + wmlp_sdata_biz_addr() + wmlp_sdata_biz_hours() + wmlp_sdata_biz_details() );
    echo '</script>';
}

add_action( 'wp_head', 'wmlp_sdata_local_business' );
