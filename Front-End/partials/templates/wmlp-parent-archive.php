<?php
/**
 * Template Name: Wikimotive Models Parent Archive
 * Template Post Type: page
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/templates
 */

include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-styles.php';
include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-tracking.php';

$wmlp_archive_sidebar = ( ! empty( rwmb_meta( 'wmlp_archive_sidebar' ) ) ) ? rwmb_meta( 'wmlp_archive_sidebar' ) : false;
$wmlp_display_type = rwmb_meta( 'wmlp_archive_display' );

get_header();

wmlp_gtm_body();
wmlp_custom_body_scripts();

$gtm_opts = wmlp_get_events_opts();
$enable_gtm = ( isset( $gtm_opts['enable_gtm'] ) && false !== (bool)$gtm_opts['enable_gtm'] ) ? true : false;
if ( $enable_gtm && isset( $gtm_opts['archive_links'] ) ) :
    wp_enqueue_script( 'wmlp_dl_temp_arch' );
endif;

?>
<main id="wmlp_models_list" class="wmlp-container container py-3">
    <?php the_title( '<header id="wmlp_header"><h1 id="wmlp_model_archive_title" class="wmlp-archive-title">', '</h1></header>' ); ?>
    <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        endif;
    ?>
    <div id="wmlp_models_row" class="row">
        <div id="wmlp_model_links" class="col-lg">
            <?php
            switch( $wmlp_display_type ):
                case 'card': include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-archive-loop_cards.php'; break;
                case 'list': include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-archive-loop_list.php'; break;
                case 'details': include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-archive-loop_details.php'; break;
            endswitch;
            ?>
        </div>
        <?php if ( false !== $wmlp_archive_sidebar ): ?>
            <?php
            if ( is_active_sidebar( $wmlp_archive_sidebar ) ) :
                echo '<aside class="col-lg-3 py-4">';
                    echo '<ul class="wmlp-sticky-top sidebar vertical">';
                        dynamic_sidebar( $wmlp_archive_sidebar );
                    echo '</ul>';
                echo '</aside>';
            endif;
            ?>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
