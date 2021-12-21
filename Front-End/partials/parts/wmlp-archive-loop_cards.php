<?php
/**
 * Models Loop for Card Style Archive Page Template
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

require_once plugin_dir_path( __FILE__ ) . 'model-functions.php';

$wmlp_model_types = ( isset( rwmb_meta( 'wmlp_archive' )['cpt_selection'] ) ) ? rwmb_meta( 'wmlp_archive' )['cpt_selection'] : array( 'new_model_wiki', 'used_model_wiki', 'wiki_comparison' );

/**
 * New Models
 */

function wmlp_new_model_cards(){
    /**
     * Options
     */
    $tax_comparisons = ( isset( wmlp_get_options()['taxonomy_comp_links'] ) ) ? (bool)wmlp_get_options()['taxonomy_comp_links'] : false;
    $wmlp_archive_opts = rwmb_meta( 'wmlp_archive' );
    $wmlp_show_comps = ( isset( $wmlp_archive_opts['show_comps'] ) ) ? (bool)$wmlp_archive_opts['show_comps'] : false;

    /**
     * Years
     */
    $wmlp_years = get_terms( array(
        'taxonomy' => 'wmlp_mod_year',
        'post_type' => 'new_model_wiki',
        'order' => 'DESC',
        'orderby' => 'name',
        )
    );

    /**
     * All Models
     */
    $wmlp_models_all = get_posts(
        array(
            'numberposts' => -1,
            'post_type' => 'new_model_wiki',
            'order' => $wmlp_archive_opts['order'],
            'orderby' => $wmlp_archive_opts['orderby'],
            'fields' => 'ids',
        )
    );

    /**
     * Output
     */
    if ( is_array( $wmlp_models_all ) && 0 < count( $wmlp_models_all ) ) :
        echo '<section id="wmlp_new_list" class="py-3">';
            echo '<h2 class="display-4">New Models</h2>';
            foreach( $wmlp_years as $wmlp_year ) :
                $wmlp_models_by_year = get_posts(
                    array(
                        'numberposts' => -1,
                        'post_type' => 'new_model_wiki',
                        'order' => $wmlp_archive_opts['order'],
                        'orderby' => $wmlp_archive_opts['orderby'],
                        'fields' => 'ids',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'wmlp_mod_year',
                                'field' => 'term_id',
                                'terms' => $wmlp_year->term_id,
                            ),
                        ),
                    )
                );

                if ( 0 < count( $wmlp_models_by_year ) ) :
                    echo '<h3>' . $wmlp_year->name . '</h3>';
                    echo '<div id="new_model_year_'. wmlp_filter_id( $wmlp_year->name, '_' ).'" class="row align-items-stretch">';
                    foreach ( $wmlp_models_by_year as $m_by_yr ) :
                        if ( ( $del_key = array_search( $m_by_yr, $wmlp_models_all ) ) !== false ) :
                            unset($wmlp_models_all[$del_key]);
                            $m_by_yr_overview = rwmb_meta( 'wiki_new_model_page-overview', array(), $m_by_yr );
                            echo "<div id='wmlp_model_{$m_by_yr}' class='col-lg-4 col-md-6 pb-3'>";
                                echo '<div class="d-flex flex-column justify-content-between h-100">';
                                    $m_by_yr_link = get_permalink( $m_by_yr );
                                    $m_by_yr_title = get_the_title( $m_by_yr );
                                    $m_by_yr_image = ( has_post_thumbnail( $m_by_yr ) ) 
                                        ? "<a href='{$m_by_yr_link}' class='text-center my-3' title='{$m_by_yr_title}'>" . wmlp_hero_img( get_post_thumbnail_id( $m_by_yr ), 'medium' ) . '</a>'
                                        : "<a href='{$m_by_yr_link}' class='text-center my-3' title='{$m_by_yr_title}'>" . wmlp_hero_img( ( isset( $m_by_yr_overview['overview-image'] ) ) ? $m_by_yr_overview['overview-image'] : false, 'medium' ) . '</a>';
                                    /**
                                     * Output
                                     */
                                    echo "<h4 id='wmlp_model_title'><a href='{$m_by_yr_link}' class='text-center' title='{$m_by_yr_title}'>" . $m_by_yr_title . '</a></h4>';
                                    echo $m_by_yr_image;
                                    echo '<div class="row align-items-center">';
                                        echo "<div class='col'><a href='{$m_by_yr_link}' class='wmlp-sm-btn'>Learn More</a></div>";
                                        if ( $wmlp_show_comps ) :
                                            if ( $tax_comparisons ) :
                                                wmlp_thumb_compare_term_links( $m_by_yr, 'wiki_comparison', "Compare", "_{$m_by_yr}", 'w-100 wmlp-comp-link-list-container' );
                                            else:
                                                if ( isset( $m_by_yr_overview['comparison-posts'] ) && is_array( $m_by_yr_overview['comparison-posts'] ) && 0 < count( $m_by_yr_overview['comparison-posts'] ) ) :
                                                    wmlp_thumb_compare_legacy_links( $m_by_yr_overview, 'Compare', "_{$m_by_yr}", 'w-100 wmlp-comp-link-list-container' );
                                                endif;
                                            endif;
                                        endif;
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        endif;
                    endforeach;
                    echo '</div>';
                endif;
            endforeach;

            if ( is_array( $wmlp_models_all ) && 0 < count( $wmlp_models_all ) ) :
                echo '<h3>Other Years</h3>';
                echo '<div id="new_model_year_other" class="row align-items-stretch">';
                foreach( $wmlp_models_all as $wmlp_model ) :
                    $m_rm_overview = rwmb_meta( 'wiki_new_model_page-overview', array(), $wmlp_model );
                    echo "<div id='wmlp_model_" . wmlp_filter_id( $wmlp_year->name, '_' ) . "' class='col-lg-4 col-md-6 pb-3'>";
                        echo '<div class="d-flex flex-column justify-content-between h-100">';
                            $m_rm_link = get_permalink( $wmlp_model );
                            $m_rm_title = get_the_title( $wmlp_model );
                            $m_rm_image = ( has_post_thumbnail( $wmlp_model ) ) 
                                ? "<a href='{$m_rm_link}' class='text-center my-3' title='{$m_rm_title}'>" . wmlp_hero_img( get_post_thumbnail_id( $wmlp_model ), 'medium' ) . '</a>'
                                : "<a href='{$m_rm_link}' class='text-center my-3' title='{$m_rm_title}'>" . wmlp_hero_img( ( isset( $m_rm_overview['overview-image'] ) ) ? $m_rm_overview['overview-image'] : false, 'medium' ) . '</a>';
                            /**
                             * Output
                             */
                            echo "<h4 id='wmlp_model_title'><a href='{$m_rm_link}' class='text-center' title='{$m_rm_title}'>" . $m_rm_title . '</a></h4>';
                            echo $m_rm_image;
                            echo '<div class="row align-items-center">';
                                echo "<div class='col'><a href='{$m_rm_link}' class='wmlp-sm-btn'>Learn More</a></div>";
                                if ( $wmlp_show_comps ) :
                                    if ( $tax_comparisons ) :
                                        wmlp_thumb_compare_term_links( $wmlp_model, 'wiki_comparison', "Compare", "_{$wmlp_model}", 'w-100 wmlp-comp-link-list-container' );
                                    else:
                                        if ( isset( $m_rm_overview['comparison-posts'] ) && is_array( $m_rm_overview['comparison-posts'] ) && 0 < count( $m_rm_overview['comparison-posts'] ) ) :
                                            wmlp_thumb_compare_legacy_links( $m_rm_overview, 'Compare', "_{$wmlp_model}", 'w-100 wmlp-comp-link-list-container' );
                                        endif;
                                    endif;
                                endif;
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                endforeach;
                echo '</div>';
            endif;
        echo '</section>';
    endif;
}

if ( in_array( 'new_model_wiki', $wmlp_model_types ) ) :
    wmlp_new_model_cards();
endif;

/**
 * Used Models
 */

function wmlp_used_cards(){
    $wmlp_archive_opts = rwmb_meta( 'wmlp_archive' );
    $wmlp_used_models = get_posts(
        array(
            'numberposts' => -1,
            'post_type' => 'used_model_wiki',
            'order' => $wmlp_archive_opts['order'],
            'orderby' => $wmlp_archive_opts['orderby'],
            'fields' => 'ids',
        )
    );

    if ( is_array( $wmlp_used_models ) && 0 < count( $wmlp_used_models ) ) :
        echo '<section id="wmlp_used_list" class="py-3">';
            echo '<h2 class="display-4">Used Models</h2>';
            echo '<div class="row align-items-stretch">';
            foreach( $wmlp_used_models as $used_model ) :
                $used_model_overview = rwmb_meta( 'wiki_used_model_page-overview', array(), $used_model );
                
                echo "<div id='wmlp_model_{$used_model}' class='col-lg-4 col-md-6 pb-3 d-flex flex-column justify-content-between'>";
                    $used_model_link = get_permalink( $used_model );
                    $used_model_title = get_the_title( $used_model );
                    $used_model_image = ( has_post_thumbnail( $used_model ) )
                        ? "<a href='{$used_model_link}' class='text-center' title='{$used_model_title}'>" . wmlp_hero_img( get_post_thumbnail_id( $used_model ), 'medium' ) . '</a>'
                        : "<a href='{$used_model_link}' class='text-center' title='{$used_model_title}'>" . wmlp_hero_img( ( isset( $used_model_overview['overview-image'] ) ) ? $used_model_overview['overview-image'] : false, 'medium' ) . '</a>';
                    /**
                     * Output
                     */
                    echo "<h4 id='wmlp_model_title' class='mb-3'><a href='{$used_model_link}' class='text-center' title='{$used_model_title}'>" . $used_model_title . '</a></h4>';
                    echo $used_model_image;
                    echo "<a href='{$used_model_link}' class='wmlp-sm-btn mt-3'>Learn More</a>";
                echo '</div>';
            endforeach;
            echo '</div>';
        echo '</section>';
    endif;
}

if ( in_array( 'used_model_wiki', $wmlp_model_types ) ) :
    wmlp_used_cards();
endif;

/**
 * Comparisons
 */

function wmlp_comparison_cards(){
    /**
     * Options
     */
    $tax_comparisons = ( isset( wmlp_get_options()['taxonomy_comp_links'] ) ) ? (bool)wmlp_get_options()['taxonomy_comp_links'] : false;
    $wmlp_archive_opts = rwmb_meta( 'wmlp_archive' );
    $wmlp_show_comps = ( isset( $wmlp_archive_opts['show_comps'] ) ) ? (bool)$wmlp_archive_opts['show_comps'] : false;

    /**
     * Years
     */
    $wmlp_years = get_terms( array(
        'taxonomy' => 'wmlp_mod_year',
        'post_type' => 'wiki_comparison',
        'order' => 'DESC',
        'orderby' => 'name',
        )
    );

    /**
     * All Models
     */
    $wmlp_models_all = get_posts(
        array(
            'numberposts' => -1,
            'post_type' => 'wiki_comparison',
            'order' => $wmlp_archive_opts['order'],
            'orderby' => $wmlp_archive_opts['orderby'],
            'fields' => 'ids',
        )
    );

    /**
     * Output
     */
    if ( is_array( $wmlp_models_all ) && 0 < count( $wmlp_models_all ) ) :
        echo '<section id="wmlp_comp_list" class="py-3">';
            echo '<h2 class="display-4">Comparisons</h2>';
            foreach( $wmlp_years as $wmlp_year ) :
                $wmlp_models_by_year = get_posts(
                    array(
                        'numberposts' => -1,
                        'post_type' => 'wiki_comparison',
                        'order' => $wmlp_archive_opts['order'],
                        'orderby' => $wmlp_archive_opts['orderby'],
                        'fields' => 'ids',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'wmlp_mod_year',
                                'field' => 'term_id',
                                'terms' => $wmlp_year->term_id,
                            ),
                        ),
                    )
                );
                if ( 0 < count( $wmlp_models_by_year ) ) :
                    echo '<h3>' . $wmlp_year->name . '</h3>';
                    echo '<div id="comparison_year_'.wmlp_filter_id( $wmlp_year->name, '_' ).'" class="row align-items-stretch">';
                    foreach ( $wmlp_models_by_year as $m_by_yr ) :
                        if ( ( $del_key = array_search( $m_by_yr, $wmlp_models_all ) ) !== false ) :
                            unset($wmlp_models_all[$del_key]);
                            $comp_mod_data = rwmb_meta( 'wiki_comp_model_comparison-models', array(), $m_by_yr );
                            $comp_thumb_1 = ( isset( $comp_mod_data["model-1-image"] ) ) ? wmlp_hero_img( $comp_mod_data["model-1-image"], 'medium' ) : wmlp_hero_img( false, 'medium' );
                            $comp_thumb_2 = ( isset( $comp_mod_data["model-2-image"] ) ) ? wmlp_hero_img( $comp_mod_data["model-2-image"], 'wmlp_comparison_icon' ) : wmlp_hero_img( false, 'wmlp_comparison_icon' );

                            echo "<div id='wmlp_model_{$m_by_yr}' class='col-lg-6 pb-3 d-flex flex-column justify-content-between'>";
                                $comp_mod_link = get_permalink( $m_by_yr );
                                $comp_mod_title = get_the_title( $m_by_yr );

                                echo "<h4 id='wmlp_comparison_title' class='mb-3'><a href='{$comp_mod_link}' class='text-center' title='{$comp_mod_title}'>" . $comp_mod_title . '</a></h4>';
                                echo '<a href="' . $comp_mod_link . '" class="row align-items-center justify-content-center">';
                                    echo '<div class="col-md-5">' . $comp_thumb_1  . '</div>';
                                    echo '<div class="col-md-2 text-center"><h4 class="wmlp-vs"><i class="wmlp-icon-vs"></i></h4></div>';
                                    echo '<div class="col-md-5">' . $comp_thumb_2 . '</div>';
                                echo '</a>';
                                echo "<a href='{$comp_mod_link}' class='wmlp-sm-btn mt-3'>Learn More</a>";
                            echo '</div>';
                        endif;
                    endforeach;
                    echo '</div>';
                endif;
            endforeach;

            if ( is_array( $wmlp_models_all ) && 0 < count( $wmlp_models_all ) ) :
                echo '<h3>Other Years</h3>';
                echo '<div id="comparison_year_other" class="row align-items-stretch">';
                foreach( $wmlp_models_all as $wmlp_model ) :
                    $comp_mod_data = rwmb_meta( 'wiki_comp_model_comparison-models', array(), $wmlp_model );
                    $comp_thumb_1 = ( isset( $comp_mod_data["model-1-image"] ) ) ? wmlp_hero_img( $comp_mod_data["model-1-image"], 'medium' ) : wmlp_hero_img( false, 'medium' );
                    $comp_thumb_2 = ( isset( $comp_mod_data["model-2-image"] ) ) ? wmlp_hero_img( $comp_mod_data["model-2-image"], 'wmlp_comparison_icon' ) : wmlp_hero_img( false, 'wmlp_comparison_icon' );

                    echo "<div id='wmlp_model_{$wmlp_model}' class='col-lg-6 pb-3 d-flex flex-column justify-content-between'>";
                        $comp_mod_link = get_permalink( $wmlp_model );
                        $comp_mod_title = get_the_title( $wmlp_model );

                        echo "<h4 id='wmlp_comparison_title' class='mb-3'><a href='{$comp_mod_link}' class='text-center' title='{$comp_mod_title}'>" . $comp_mod_title . '</a></h4>';
                        echo '<a href="' . $comp_mod_link . '" class="row align-items-center justify-content-center">';
                            echo '<div class="col-md-5">' . $comp_thumb_1  . '</div>';
                            echo '<div class="col-md-2 text-center"><h4 class="wmlp-vs"><i class="wmlp-icon-vs"></i></h4></div>';
                            echo '<div class="col-md-5">' . $comp_thumb_2 . '</div>';
                        echo '</a>';
                        echo "<a href='{$comp_mod_link}' class='wmlp-sm-btn mt-3'>Learn More</a>";
                    echo '</div>';
                endforeach;
                echo '</div>';
            endif;
        echo '</section>';
    endif;
}

if ( in_array( 'wiki_comparison', $wmlp_model_types ) ) :
    wmlp_comparison_cards();
endif;
