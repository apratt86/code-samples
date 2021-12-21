<?php
/**
 * Call to Action Buttons Section for Models
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

require_once plugin_dir_path( __FILE__ ) . 'model-functions.php';

$wmlp_opts = wmlp_get_options();
$overview_data = wmlp_get_overview_data();

$small_ctas = ( isset( $overview_data['sm-cta-btn'] ) && is_array( $overview_data['sm-cta-btn'] ) && 0 < count( $overview_data['sm-cta-btn'] ) ) ? $overview_data['sm-cta-btn'] : false;

$lg_cta_title = ( isset( $overview_data['lg-cta-btn-title'] ) ) ? $overview_data['lg-cta-btn-title'] : false;
/**
 * @since 1.5.1
 * Updated Large CTA Content
 * Includes the ability to override the action per page
 */

$lg_cta_global_action = false;
if ( isset( $wmlp_opts['gen_settings']['cta_shortcode'] ) ) :
    $lg_cta_global_action = $wmlp_opts['gen_settings']['cta_shortcode'];
endif;

$lg_cta_override_action = false;
if ( isset( $overview_data['lg-cta-btn-action'] ) ) :
    $lg_cta_override_action = $overview_data['lg-cta-btn-action'];
endif;

if ( false !== $lg_cta_override_action ) :
    $lg_cta_content = $lg_cta_override_action;
else:
    $lg_cta_content = $lg_cta_global_action;
endif;

$gtm_opts = wmlp_get_events_opts();
$enable_gtm = ( isset( $gtm_opts['enable_gtm'] ) && false !== (bool)$gtm_opts['enable_gtm'] ) ? true : false;

if ( $enable_gtm ) :
    wp_enqueue_script( 'wmlp_dl_mod_cta' );
endif;
?>

<section id="wmlp_ctas">
    <div class="wmlp-container container">
        <?php if ( isset( $gtm_opts['chat_now_click'] ) && isset( $gtm_opts['chat_now_qs'] ) ) : ?>
            <div class="row">
                <div class="col-lg-6">
        <?php endif; ?>
                <?php if ( false !== $lg_cta_content && false !== $lg_cta_title ) : 
                    if ( wmlp_contains_shortcode( $lg_cta_content ) ):?>
                    <label id="wmlp_contact_form" class="wmlp-lg-btn" for="lg_cta"><i class="fas fa-paper-plane"></i> <?php echo $lg_cta_title; ?></label>
                    <input type="checkbox" class="wmlp-modal-toggle" id="lg_cta" name="lg_cta" />
                    <div class="wmlp-modal-box">
                        <div class="wmlp-modal-content wmlp-container container h-100">
                            <div class="wmlp-close-container">
                                <label id="wmlp_contact_form" class="wmlp-close-modal" for="lg_cta">&times;</label>
                            </div>
                            <div class="row justify-content-center align-items-center h-100">
                                <div class="wmlp-contact-container col-8"><?php echo do_shortcode( $lg_cta_content ); ?></div>
                            </div>
                        </div>
                    </div>
                <?php else:?>
                    <a href="<?php echo $lg_cta_content; ?>" id="wmlp_lg_cta_link" class="wmlp-lg-btn" for="lg_cta"><i class="fas fa-paper-plane"></i> <?php echo $lg_cta_title; ?></a>
                <?php endif;
            endif;?>
        <?php if ( isset( $gtm_opts['chat_now_click'] ) && isset( $gtm_opts['chat_now_qs'] ) ) : ?>
            </div>
            <div class="col-lg-6">
                <label id="wmlp_chat_now" class="wmlp-lg-btn"><i class="fas fa-comments"></i> Chat Now!</label>
            </div>
        </div>
        <?php endif; ?>
        <?php
        if ( false !== $small_ctas ) :
            echo '<div class="row justify-content-center align-items-start py-3">';
            foreach( $small_ctas as $sm_btn_i => $sm_btn ):
                if ( wmlp_contains_shortcode( $sm_btn[1] ) ):
                    echo "<div class='col-md-6'><label role='button' class='wmlp-sm-cta wmlp-sm-btn wmlp_sm_cta_btn_tracker' for='wmlp_sm_cta_content_{$sm_btn_i}' id='wmlp_sm_cta_{$sm_btn_i}'>{$sm_btn[0]}</label>";
                    echo "<input type='checkbox' class='wmlp-cta-toggle' id='wmlp_sm_cta_content_{$sm_btn_i}' name='wmlp_sm_cta_content_{$sm_btn_i}' />";
                    echo '<div class="wmlp_sm_cta_content wmlp-modal-box"><div class="wmlp-modal-content wmlp-container container h-100">
                            <div class="wmlp-close-container">
                                <label id="wmlp_contact_form" class="wmlp-close-modal" for="wmlp_sm_cta_content_'.$sm_btn_i.'">&times;</label>
                            </div>
                            <div class="row justify-content-center align-items-center h-100">
                                <div class="wmlp-contact-container col-8">'.do_shortcode($sm_btn[1]).'</div>
                            </div>
                        </div></div>
                    </div>';
                else:
                    echo '<div class="col-md-6"><a href="' . $sm_btn[1] . '" class="wmlp-sm-cta wmlp-sm-btn" id="wmlp_sm_cta_link">'. $sm_btn[0] .'</a></div>';
                endif;
            endforeach;
            echo '</div>';
        endif;
        ?>
    </div>
</section>