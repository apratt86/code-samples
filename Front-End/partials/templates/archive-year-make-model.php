<?php
/**
 * Archive Template for Models
 * 
 * Template Name: Model Page Archive
 * 
 * @link       https://wikimotive.com
 * @since      1.4.0
 * 
 * @package    wikimotive-model-comparison-plugin
 * @subpackage wikimotive-model-comparison-plugin/public/templates
 */

include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-styles.php';
include_once plugin_dir_path( __DIR__ ) . 'parts/wmlp-tracking.php';

get_header();

wmlp_gtm_body();
wmlp_custom_body_scripts();
?>
<main id="wmlp_models_list" class="wmlp-container container py-3">
<?php 
    the_archive_title( '<header id="wmlp_header"><h1 id="wmlp_model_archive_title" class="wmlp-archive-title">', '</h1></header>' );
    /**
     * The Models Loop
     */
    if ( have_posts() ) :
        echo '<div class="row align-items-stretch">';
        while ( have_posts() ) : the_post();
            $post_id = get_the_ID();
            if ( in_array ( get_post_type(), [ 'new_model_wiki', 'used_model_wiki', 'wiki_comparison' ] ) ) :
            ?>
            <div id="wmlp_model" class="col-lg-4 col-md-6 d-flex flex-column justify-content-between">
                <?php
                the_title( '<h3><a href="' . get_permalink() . '">', '</a></h3>' );
                if ( has_post_thumbnail() ) :
                    echo '<a href="'.get_permalink().'" class="text-center my-3" title="'.get_the_title().'">' . wmlp_hero_img( get_post_thumbnail_id(), 'medium' ) . '</a>';
                endif;
                echo '<a href="' . get_permalink() . '" class="wmlp-sm-btn mt-3">Learn More</a>';
                ?>
            </div>
            <?php
            endif;
        endwhile;
        echo '</div>';
    else:
        echo '<p>No models available.</p>';
    endif;
?>
</main>

<?php
get_footer();
