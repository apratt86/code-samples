<?php

/**
 * Comparison Posts Loop
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

$post_id = get_the_ID();

$gtm_opts = wmlp_get_events_opts();
$enable_gtm = ( isset( $gtm_opts['enable_gtm'] ) && false !== (bool)$gtm_opts['enable_gtm'] ) ? true : false;

if ( $enable_gtm && isset( $gtm_opts['related_comparison'] ) ) :
    wp_enqueue_script( 'wmlp_dl_mod_rel_comp' );
endif;

/**
 * Start the Loop
 */
function wmlp_comparison_posts( $post_id = null ) {

    $comparison_terms = wp_get_post_terms( $post_id, array( 'wmlp_mod_year', 'wmlp_mod_type' ) );

    $settings = rwmb_meta('wmlp_related_posts');
    $comps_enabled = ( isset( $settings['enable_comparisons'] ) && '1' === $settings['enable_comparisons'] ) ? true : false;

    if ( $comparison_terms && $comps_enabled ) :
        /**
         * Get the post Objects
         */
        $args['tax_query'] = array();
        foreach ( $comparison_terms as $comparison_term ):
            switch( $comparison_term->taxonomy ) :
                case 'wmlp_mod_year':
                    array_push( $args['tax_query'], array( 'taxonomy' => 'wmlp_mod_year', 'field' => 'term_id', 'terms' => array( $comparison_term->term_id ) ) );
                break;
                case 'wmlp_mod_type':
                    array_push( $args['tax_query'], array( 'taxonomy' => 'wmlp_mod_type', 'field' => 'term_id', 'terms' => array( $comparison_term->term_id ) ) );
                break;
            endswitch;
        endforeach;

        $args['post__not_in'] = array( $post_id );
        $args['posts_per_page'] = 8;
        $args['order'] = 'DESC';
        $args['orderby'] = 'ID';
        $args['post_type'] = 'wiki_comparison';

        $comparison_posts = new WP_Query( $args );

        /**
         * The Related Posts Loop
         */
        if( $comparison_posts->have_posts() ) :
        echo '<section id="wmlp_related_comparisons" class="py-3">
        <div class="wmlp-container container">';
        echo '<h3 class="text-center"><i class="wmlp-icon-vs"></i> Comparison Models</h3>';
            echo '<div class="row align-items-stretch">';
            while ( $comparison_posts->have_posts() ) : $comparison_posts->the_post();
            $comp_id = get_the_ID();
            $overview = wmlp_get_overview_data( $comp_id );
                echo '<div id="wmlp_related_comparison" class="col-6 col-md-4 col-lg-3">';
                if ( has_post_thumbnail() ) :
                    $thumb_id = get_post_thumbnail_id( $comp_id );
                    echo '<a id="comparison_'.$comp_id.'" href="' . get_the_permalink() .'" class="wmlp-related-comp-img">' . wmlp_hero_img( $thumb_id, 'medium' ) . '</a>';
                    $excerpt = ( isset( $overview['overview-text'] ) ) ? wmlp_overview_excerpt( $overview['overview-text'], 55, ' [&#x2026;] '.'<a id="comparison_'.$comp_id.'" href="' . get_the_permalink() . '" class="wmlp-related-comp-readmore"><span>Read More &raquo;</span></a>' ) : '<a id="comparison_'.$comp_id.'" href="' . get_the_permalink() . '" class="wmlp-related-comp-readmore"><span>Read More &raquo;</span></a>';
                else:
                    echo '<a id="comparison_'.$comp_id.'" href="' . get_the_permalink() .'" class="wmlp-related-comp-img">' . wmlp_hero_img( false, 'medium' ) . '</a>';
                    $excerpt = ( isset( $overview['overview-text'] ) ) ? wmlp_overview_excerpt( $overview['overview-text'], 70, ' [&#x2026;] '.'<a id="comparison_'.$comp_id.'" href="' . get_the_permalink() . '" class="wmlp-related-comp-readmore"><span>Read More &raquo;</span></a>' ) : '<a id="comparison_'.$comp_id.'" href="' . get_the_permalink() . '" class="wmlp-related-comp-readmore"><span>Read More &raquo;</span></a>';
                endif;

                    echo '<a id="comparison_'.$comp_id.'" href="' . get_the_permalink() . '" class="wmlp-related-comp-title"><h4 class="text-center">' . get_the_title() . '</h4></a>';
                    echo $excerpt;
                echo '</div>';
            endwhile;
            echo '</div>';
        echo '</div></section>';
        endif;
        wp_reset_query();
    endif;

}

wmlp_comparison_posts( $post_id );
