<?php
/**
 * Footer Template Part for Models
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

require_once plugin_dir_path( __FILE__ ) . 'model-functions.php';

$wmlp_opts = wmlp_get_options();
$data = wmlp_get_footer_data();

$disclaimer = '<p><sup>*</sup>Images, fuel economy, and other stats, may reflect higher trim levels, options, or features that may result in a higher cost from the starting MSRP.<br>MSRP excludes tax, license, registration, additional options, and possible destination charges. Dealers set own prices.</p>';

if ( isset( $wmlp_opts['gen_settings']['disclaimer'] ) ):
    $disclaimer = wpautop( $wmlp_opts['gen_settings']['disclaimer'] );
endif;

if ( isset( $data['disclaimer_override'] ) ):
    $disclaimer = wpautop( $data['disclaimer_override'] );
endif;

?>

<section id="model_footer">
    <div class="wmlp-container container">
        <?php
        /**
         * Display the footer logo
         */
            wmlp_footer_logo();
        /**
         * Display the footer content
         */
        if ( is_array( $data ) && 0 < count( $data ) ) :
            if ( isset( $data['footer-title'] ) ) :
                echo "<h3 id='wmlp-ftr-title'><i class='fas fa-info-circle'></i> {$data['footer-title']}</h3>";
            endif;
            if ( isset( $data['footer-text'] ) ) :
                echo '<div id="wmlp-ftr-content">' . wpautop( do_shortcode( $data['footer-text'] ) ) . '</div>';
            endif;
        endif;
        ?>
    </div>
    <div class="wmlp-container container wmlp-disclaimer">
        <?php echo $disclaimer; ?>
    </div>
</section>
