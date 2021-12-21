<?php
/**
 * Content Section for Comparison Page Template
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

require_once plugin_dir_path( __FILE__ ) . 'model-functions.php';

global $post;

$post_id = get_the_ID();

$tax_comparisons = ( isset( wmlp_get_options()['taxonomy_comp_links'] ) ) ? (bool)wmlp_get_options()['taxonomy_comp_links'] : false;
$overview = wmlp_get_overview_data();
$pagetabs = wmlp_get_pagetabs_data();

$comp_details = wmlp_get_comparisons_details();

$content = ( isset( $overview['overview-text'] ) ) ? '<div id="wmlp_content">' . wpautop( do_shortcode( $overview['overview-text'] ) ) . '</div>' : '<div id="wmlp_content__blank"></div>';
$msrp = ( isset( $overview['msrp'] ) ) ? '<div id="wmlp_msrp" class="col-lg-5 py-3"><p class="wmlp-msrp-start text-center"><strong>Starting at</strong></p><h2 class="wmlp-msrp text-center">' . $overview['msrp'] . '</h2></div>' : '<div id="wmlp_msrp__blank" class="col-lg-5"></div>';

if ( false !== $overview ) : ?>
<section id="model_content">
    <div class="wmlp-container container">
        <nav class="wmlp-tab-nav row justify-content-start align-items-center wmlp-sticky-top">
            <!-- Overview Button -->
            <div class="wmlp-btn-container">
                <label for="overview" class="wmlp-tab-btn"><i class="fas fa-file-alt"></i> Overview</label>
            </div>
            <!-- Page Tab Buttons -->
            <?php
            if( false !== $pagetabs ) : foreach( $pagetabs as $tn_i => $tn_data ) :
                /** Variables */
                $tn_icon = ( isset( $tn_data['page-tab-icon'] ) ) ? wmlp_get_tab_icon( $tn_data['page-tab-icon'] ) : '';
                $tn_title = ( isset( $tn_data['page-tab-title'] ) ) ? $tn_data['page-tab-title'] : '';
            ?>
                <div class="wmlp-btn-container">
                    <label for="tab-<?php echo $tn_i; ?>" class="wmlp-tab-btn"><?php echo $tn_icon; ?> <?php echo $tn_title ?></label>
                </div>
            <?php endforeach; endif; ?>
            <?php
                /** Comparisons Link List */
                if ( $tax_comparisons ) :
                    wmlp_compare_term_links( $post_id );
                else:
                    wmlp_compare_legacy_links( $overview );
                endif;
            ?>
        </nav>
        <input type="radio" id="overview" name="wmlp_tab_nav" class="wmlp-tab-toggle" checked="checked"/>
        <?php if( false !== $pagetabs ) : foreach( $pagetabs as $tn_cb_i => $tn_cb_data ) : ?><input type="radio" id="tab-<?php echo $tn_cb_i; ?>" name="wmlp_tab_nav" class="wmlp-tab-toggle" /><?php endforeach; endif; ?>
        <!-- Page Tabs -->
        <ul class="wmlp-tabs">
            <!-- Comparisons Images -->
            <li data-selected="overview" class="wmlp-tab-content">
                <div class="row justify-content-center align-items-center">
                    <?php wmlp_comparison_column( $comp_details, 1 ); ?>
                    <?php if ( isset( $comp_details ) && is_array( $comp_details ) && 1 < count( $comp_details ) ) : ?>
                        <div id="wmlp_vs_col" class="col-md-2 text-center">
                            <i class="wmlp-icon-vs"></i>
                        </div>
                    <?php endif; ?>
                    <?php wmlp_comparison_column( $comp_details, 2 ); ?>
                </div>
                <?php wmlp_comparison_specs( $comp_details ); ?>
                <?php echo $content; ?>
            </li>
            <!-- Page Tabs Content -->
            <?php
            if( false !== $pagetabs ) : foreach( $pagetabs as $tc_i => $tc_data ) :
                /** Variables */
                $tc_icon = ( isset( $tc_data['page-tab-icon'] ) ) ? wmlp_get_tab_icon( $tc_data['page-tab-icon'] ) : '';
                $tc_title = ( isset( $tc_data['page-tab-title'] ) ) ? $tc_data['page-tab-title'] : '';
                $tc_content = ( isset( $tc_data['page-tab-content'] ) ) ? wpautop( do_shortcode( $tc_data['page-tab-content'] ) ) : '';
                $tc_hero = ( isset( $tc_data['page-tab-main-img'] ) ) ? wmlp_hero_img( $tc_data['page-tab-main-img'], 'wmlp_hero' ) : '';
            ?>
            <li data-selected="tab-<?php echo $tc_i; ?>" class="wmlp-tab-content">
                <h2 class="wmlp-tab-title"><?php echo $tc_icon; ?> <?php echo $tc_title ?></h2>
                <?php
                    echo $tc_hero;
                    echo $tc_content;
                ?>
            </li>
            <?php endforeach; endif; ?>
        </ul>
    </div>
</section>

<?php endif;
