<?php
/**
 * Technical Article Structured Data for Models
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

require_once plugin_dir_path( __FILE__ ) . 'model-functions.php';

function wmlp_sdata_tech_article() {
    global $post;
    /**
     * General Model Variables
     */
    $post_id = get_the_ID();
    $post_type = get_post_type( $post_id );
    $author_id = $post->post_author;
    $details = wmlp_get_overview_data();
    $ymm_terms = wp_get_post_terms( $post_id, array( 'wmlp_mod_year', 'wmlp_make', 'wmlp_mod_type' ) );
    if ( ! empty( $ymm_terms ) ) :
        $model = '';
        foreach( $ymm_terms as $mod_term ) :
            $model .= $mod_term->name . ' ';
        endforeach;
        $model = rtrim( $model, ' ' );
    else:
        $model = get_the_title();
    endif;

    $brand_terms = wp_get_post_terms( $post_id, 'wmlp_make' );
    if ( ! empty( $brand_terms ) ) :
        $brand = '';
        foreach( $brand_terms as $brand_data ) :
            $brand .= $brand_data->name . ' ';
        endforeach;
        $brand = rtrim( $brand, ' ' );
    else:
        $brand = get_bloginfo( 'name' );
    endif;

    /**
     * Plugin Settings Variables
     */
    $biz_info = wmlp_get_biz_addr();
    $biz_name = ( isset( $biz_info['name'] ) ) ? $biz_info['name'] : get_bloginfo('name');
    $biz_logo = wmlp_get_the_logo_url();

    /**
     * Output Variable
     */
    $tart = [];
    /** Schema Heading */
    $tart['@context'] = 'http://schema.org';
    $tart['@type'] = 'TechArticle';
    
    /** The Page */
    $tart['mainEntityOfPage'] = [
        '@type' => 'WebPage',
        '@id' => get_permalink(),
    ];

    /** Dates */
    $tart['datePublished'] = get_the_date( 'c' );
    $tart['dateModified'] = get_the_modified_date( 'c' );

    /** Headline */
    $tart['headline'] = get_the_title();

    /** Author */
    $tart['author'] = [
        '@type' => 'Person',
        'name' => get_the_author_meta( 'display_name' , $author_id ),
    ];

    /** About */
    $tart['about'] = [
        '@type' => 'Car',
        'name' => get_the_title(),
        'model' => $model,
        'brand' => $brand,
    ];

    /**
     * Structured Data Options Panel
     */
    $s_data_opts = wmlp_get_sdata_opts();

    /** Model Stats */
    if ( isset( $details['model-spec'] ) && is_array( $details['model-spec'] ) && 0 < count( $details['model-spec'] ) ) :
        $tart['about']['vehicleConfiguration'] = '';
        foreach( $details['model-spec'] as $modstat ):
            $stat_name = ( isset( $modstat[0] ) ) ? $modstat[0] : '';
            $stat_value = ( isset( $modstat[1] ) ) ? $modstat[1] : '';
            $tart['about']['vehicleConfiguration'] .= $stat_name . ' : ' . $stat_value . ', ';
        endforeach;
        $tart['about']['vehicleConfiguration'] = rtrim( $tart['about']['vehicleConfiguration'], ', ' );
    endif;

    /** Offer */
    $query_prefix = ( isset( $s_data_opts['offer_query_prefix'] ) ) ? '/' . $s_data_opts['offer_query_prefix'] : '/?q=';
    $search_term = rawurlencode( $model );
    switch( $post_type ) :
        case 'new_model_wiki':
            $srp = ( isset( $s_data_opts['new_offers_slug'] ) ) ? get_site_url() . '/' . wmlp_filter_id( $s_data_opts['new_offers_slug'] ) : get_site_url() . '/' . 'new-vehicles';
            $msrp = ( isset( $details['msrp'] ) ) ? wmlp_filter_price( $details['msrp'] ) : 0;
            $tart['about']['offers'] = [
                '@type' => 'Offer',
                'url' => $srp . $query_prefix . $search_term,
                'itemCondition' => 'NewCondition',
                'price' => $msrp,
                'priceCurrency' => 'USD',
            ];
        break;
        case 'used_model_wiki':
            $srp = ( isset( $s_data_opts['used_offers_slug'] ) ) ? get_site_url() . '/' . wmlp_filter_id( $s_data_opts['used_offers_slug'] ) : get_site_url() . '/' . 'used-vehicles';
            $tart['about']['offers'] = [
                '@type' => 'Offer',
                'url' => $srp . $query_prefix . $search_term,
                'itemCondition' => 'UsedCondition',
            ];
        break;
        case 'wiki_comparison':
            $srp = ( isset( $s_data_opts['comp_offers_slug'] ) ) ? get_site_url() . '/' . wmlp_filter_id( $s_data_opts['comp_offers_slug'] ) : get_site_url() . '/' . 'new-vehicles';
            $comp_details = wmlp_get_comparisons_details();
            $msrp = ( isset( $comp_details['model-1-msrp'] ) ) ? wmlp_filter_price( $comp_details['model-1-msrp'] ) : 0;
            $tart['about']['offers'] = [
                '@type' => 'Offer',
                'url' => $srp . $query_prefix . $search_term,
                'itemCondition' => 'NewCondition',
                'price' => $msrp,
                'priceCurrency' => 'USD',
            ];
        break;
        default:
            $srp = get_site_url() . '/' . 'new-vehicles/';
            $tart['about']['offers'] = [
                '@type' => 'Offer',
                'url' => $srp,
                'itemCondition' => 'NewCondition',
            ];
        break;
    endswitch;

    /** Description */
    if ( isset( $details['overview-text'] ) ) :
        $tart['about']['description'] = $details['overview-text'];
    endif;

    /** Image (featured) */
    if ( has_post_thumbnail() ):
        $tart['image'] = get_the_post_thumbnail_url();
        $tart['about']['image'] = get_the_post_thumbnail_url();
    endif;

    /** Publisher */
    $tart['publisher'] = [
        '@type' => 'Organization',
        'name' => $biz_name,
    ];
    if ( false !== $biz_logo ):
        $tart['publisher']['logo']['@type'] = 'ImageObject';
        $tart['publisher']['logo']['url'] = $biz_logo;
    endif;

    /**
     * Echo the Script in the Head
     */
    echo '<script id="wmlp_techarticle" type="application/ld+json">';
    echo json_encode( $tart );
    echo '</script>';
}

add_action( 'wp_head', 'wmlp_sdata_tech_article' );
