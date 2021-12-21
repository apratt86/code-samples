<?php
/**
 * Inventory Section for Models
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

require_once plugin_dir_path( __FILE__ ) . 'model-functions.php';

$overview_data = wmlp_get_overview_data();

$gtm_opts = wmlp_get_events_opts();
$enable_gtm = ( isset( $gtm_opts['enable_gtm'] ) && false !== (bool)$gtm_opts['enable_gtm'] ) ? true : false;

if ( isset( $overview_data['inventory-sc'] ) && $enable_gtm && isset( $gtm_opts['inventory_engagement'] ) ) :
    wp_enqueue_script( 'wmlp_dl_mod_inv' );
endif;

echo ( isset( $overview_data['inventory-sc'] ) ) ? '<section id="wmlp_model_inventory"><div class="wmlp-container container">' . do_shortcode( $overview_data['inventory-sc'] ) . '</div></section>' : '';
