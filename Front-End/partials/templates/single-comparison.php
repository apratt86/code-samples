<?php
/**
 * Template Name: Comparisons Template
 * Template Post Type: wiki_comparison
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 * 
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/templates
 */

include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-styles.php';
include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-tracking.php';

$structured_data_opts = wmlp_get_sdata_opts();

if ( isset( $structured_data_opts['biz_enable'] ) && false !== (bool)$structured_data_opts['biz_enable'] ) :
    include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-structured_data-local-business.php';
endif;

if ( isset( $structured_data_opts['tech_article'] ) && false !== (bool)$structured_data_opts['tech_article'] ) :
    include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-structured_data-tech-article.php';
endif;

get_header();

    wmlp_gtm_body();
    wmlp_custom_body_scripts();

    include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-model_title.php';
    include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-model_ctas.php';

    /**
     * Content
     */
    include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-comparison_content.php';

    /**
     * Common Template Parts
     */
    include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-model_inventory.php';
    include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-model_footer.php';

        /**
         * Related Links and Comparisons
         */
    if ( ! wmlp_is_legacy() ) :
        include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-model_related-comparisons.php';
        include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-model_related-posts.php';
    endif;

get_footer();
