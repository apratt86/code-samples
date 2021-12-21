<?php
/**
 * Model Title Section
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 *
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/partials/parts
 */

?>

<section id="wmlp_header">
    <div class="py-3">
        <div class="wmlp-container container">
            <?php
            echo '<div class="wmlp-breadcrumbs" id="breadcrumbs">';
            /** SEO Press Breadcrumbs */
            if ( function_exists( 'seopress_display_breadcrumbs' ) ) :
                seopress_display_breadcrumbs();
            /** Yoast Breadcrumbs */
            elseif ( function_exists('yoast_breadcrumb') && ! function_exists( 'seopress_display_breadcrumbs' ) ) :
                yoast_breadcrumb('<p id="breadcrumbs">','</p>');
            endif;
            echo '</div>';
            ?>
        </div>
        <div class="wmlp-container container">
            <?php the_title( '<h1 id="wmlp_title" class="wmlp-title">', '</h1>' ); ?>
        </div>
    </div>
</section>
