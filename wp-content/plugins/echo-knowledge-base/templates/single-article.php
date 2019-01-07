<?php
/**
 * The template for displaying single KB Article.
 *
 * @author 		Echo Plugins
 */

global $epkb_password_checked;

// this is KB Article URL so get KB ID
$kb_id = isset($GLOBALS['post']->post_type) ? EPKB_KB_Handler::get_kb_id_from_post_type( $GLOBALS['post']->post_type ) : EPKB_KB_Config_DB::DEFAULT_KB_ID;
if ( is_wp_error($kb_id) ) {
    $kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
}

$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

/**
 * Display ARTICLE PAGE content
 */
get_header();

$template_style1 = EPKB_Utilities::get_inline_style(
    ' padding-top::       templates_for_kb_article_padding_top,
	        padding-bottom::    templates_for_kb_article_padding_bottom,
	        padding-left::      templates_for_kb_article_padding_left,
	        padding-right::     templates_for_kb_article_padding_right,
	        margin-top::        templates_for_kb_article_margin_top,
	        margin-bottom::     templates_for_kb_article_margin_bottom,
	        margin-left::       templates_for_kb_article_margin_left,
	        margin-right::      templates_for_kb_article_margin_right,', $kb_config );

//CSS Article Reset / Defaults
$article_class = '';

if ( $kb_config[ 'templates_for_kb_article_reset'] === 'on' ) {
	$article_class .= 'eckb-article-resets ';
}
if ( $kb_config[ 'templates_for_kb_article_defaults'] === 'on' ) {
	$article_class .= 'eckb-article-defaults ';
}		?>

	<div class="eckb-kb-template <?php echo $article_class; ?>" <?php echo $template_style1; ?>>	      <?php

		while ( have_posts() ) {

		    the_post();

			if ( post_password_required() ) {
				echo get_the_password_form();
				echo '</div>';
				get_footer();
				return;
			}
			$epkb_password_checked = true;

			the_content();

		}          	?>

	</div> <?php

get_footer();