<?php
require_once plugin_dir_path(__DIR__) . '/sc/functions-shortcode.php';
add_shortcode('list_posts', 'list_posts_output');
function list_posts_output( $atts ) {
	$atts = shortcode_atts(
		array(
			'title' => 'Posts',
			'post_type' => array('post', 'page'),
			'post_name' => '',
			'display' => 'details',
			'limit' => -1,
			'order' => 'ASC',
			'orderby' => 'post_title',
		),
		$atts,
		'list_posts'
	);

	$title = ( !empty( $atts['title'] ) ) ? $atts['title'] : '';
	$postType = ( is_array( $atts['post_type'] ) ) ? $atts['post_type'] : explode( ',', $atts['post_type'] );
	$postNames = ( !empty( $atts['post_name'] ) ) ? explode( ',', $atts['post_name'] ) : false;
	$displayType = ( $atts['display'] == 'grid' ) ? 'grid' : 'details';

	if ( $postNames !== false && is_array($postNames) ) {
		$getPostArgs = array(
			'numberposts' => $atts['limit'],
			'post_type' => $postType,
			'post_name__in' => $postNames,
			'order' => $atts['order'],
			'orderby' => $atts['orderby'],
		);
	}
	else{
		$getPostArgs = array(
			'numberposts' => $atts['limit'],
			'post_type' => $postType,
			'order' => $atts['order'],
			'orderby' => $atts['orderby'],
		);
	}

	$thePosts = get_posts( $getPostArgs );

	$post_list = "
		<style>
			.research-img-wrap {
				max-height:165px;
				min-height:165px;
			}
			.research-row {
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-ms-flex-wrap: wrap;
				flex-wrap: wrap;
				margin-right: -15px;
				margin-left: -15px;
				-webkit-box-orient: horizontal!important;
				-webkit-box-direction: normal!important;
				-ms-flex-direction: row!important;
				flex-direction: row!important;
				display: -webkit-box!important;
				display: -ms-flexbox!important;
				display: flex!important;
				-ms-flex-pack: distribute!important;
				-webkit-box-pack: start!important;
				-ms-flex-pack: start!important;
				justify-content: flex-start!important;
			}
			.research-item-container {
				text-align: center!important;
				margin-bottom: 3rem!important;
				position: relative;
				width: 100%;
				min-height: 1px;
				padding-right: 15px;
				padding-left: 15px;
			}
			@media (min-width: 576px) {
				.research-item-container {
					-webkit-box-flex: 0;
					-ms-flex: 0 0 33.33333%;
					flex: 0 0 33.33333%;
					max-width: 33.33333%;
				}
			}
			.research-item-inner {
				height: 100%!important;
				-webkit-box-pack: justify!important;
				-ms-flex-pack: justify!important;
				justify-content: space-between!important;
				-webkit-box-orient: vertical!important;
				-webkit-box-direction: normal!important;
				-ms-flex-direction: column!important;
				flex-direction: column!important;
				display: -webkit-box!important;
				display: -ms-flexbox!important;
				display: flex!important;
			}
			.research-img {
				margin:0 auto;
				display:block;
				max-width:100%;
				height:auto;
			}
		</style>
	";
	
	$post_list .= "<h2 id='research_title'>{$title}</h2>";

	if ( $displayType == 'grid' ) {
		$containerStart = "<div id='research_row' class='research-row'>";
		$containerEnd = "</div>";
	}
	else {
		$containerStart = "";
		$containerEnd = "";
	}
	
// Post Output Loop:
	$post_list .= $containerStart;
	foreach ( $thePosts as $postItems => $postItem ) {
		$postID = $postItem->ID;
		$imageSize = ( $displayType == 'grid' ) ? 'medium' : 'thumbnail';
		$postThumb = get_the_post_thumbnail( $postID, $imageSize, array('class' => 'research-img attachment-thumbnail size-thumbnail wp-post-image lazyload-loading') );
		$postUrl = get_the_permalink($postID);
		$postTitle = $postItem->post_title;
		$postDesc = wp_trim_words($postItem->post_content, 55);
		if ( $displayType == 'grid' ) {
			$post_list .= "
			<div id='research_item' class='research-item-container'>
				<div id='research_item_inner' class='research-item-inner'>
					<h3 id='research_item_title'>{$postTitle}</h3>
					{$postThumb}
					<p id='research_item_button' class='text-center'>
					<a class='btn btn-primary button primary-button' style='margin:10px 0;' href='{$postUrl}' role='button' target='_blank' rel='noopener' title='{$postTitle}'>LEARN MORE</a>
					</p>
				</div>
			</div>";
		}
		else {
			$post_list .= "
			<div id='research_post_item' class='post-content'>
				<div id='research_post_thumb' class='post-thumbnail'>
					{$postThumb}
				</div>
				<div id='research_post_entry' class='entry'>
					<h3 id='research_post_title'><a class='entry-title' href='{$postUrl}' rel='bookmark' title='{$postTitle}'>{$postTitle}</a></h3>
					<p id='research_post_excerpt'>{$postDesc}</p>
					<p id='research_post_link' class='excerptlink'><a class='moretag' href='{$postUrl}'>Read More</a></p>
				</div>
			</div>";
		}
	}
	$post_list .= $containerEnd;
	
	global $custom_wmcp_css_has_run;
	if ( $custom_wmcp_css_has_run == false ) { $post_list .= custom_wmcp_css(); }
	
	return $post_list;
}