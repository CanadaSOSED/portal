<?php
/**
 * The template for displaying Breadcrumb for KB Article.
 *
 * This template can be overridden by copying it to yourtheme/kb_templates/feature-breadcrumb.php.
 *
 * HOWEVER, on occasion Echo Plugins will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		Echo Plugins
 *
 */
/** @var WP_Post $article */
/** @var EPKB_KB_Config_DB $kb_config */

if ( empty($article) || ! $article instanceof WP_Post  ) {
    return;
}

$breadcrumb_tree = EPKB_Templates_Various::get_article_breadcrumb( $kb_config, $article->ID );

// setup breadcrumb links
$breadcrumb = array( $kb_config['breadcrumb_home_text'] => EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config ));

foreach( $breadcrumb_tree as $category_id => $category_name ) {
    $term_link = get_term_link( $category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_config['id']) );
    if ( is_wp_error( $term_link ) ) {
        $term_link = '';
    }
    $breadcrumb += array( $category_name => $term_link );
}

$breadcrumb += array( $article->post_title => '#' );

//Saved Setting values

$breadcrumb_style1 = EPKB_Utilities::get_inline_style('
	                   padding-top::    breadcrumb_padding_top, 
	                   padding-right:: breadcrumb_padding_right,
				       padding-bottom:: breadcrumb_padding_bottom, 
				       padding-left:: breadcrumb_padding_left,
				       margin-top::    breadcrumb_margin_top, 
	                   margin-right:: breadcrumb_margin_right,
				       margin-bottom:: breadcrumb_margin_bottom, 
				       margin-left:: breadcrumb_margin_left,
				       font-size::breadcrumb_font_size', $kb_config );
$breadcrumb_style2 = EPKB_Utilities::get_inline_style( 'color:: breadcrumb_text_color', $kb_config );         ?>

<div class="eckb-breadcrumb" <?php echo $breadcrumb_style1; ?>>
	<div class="eckb-breadcrumb-label"><?php echo esc_html($kb_config['breadcrumb_description_text']); ?></div>
	<ul class="eckb-breadcrumb-nav">       <?php

    $ix = 0;
	foreach ( $breadcrumb as $text => $link ) {

		echo '<li>';
		echo '	<span class="eckb-breadcrumb-link">';

        $ix++;
        $text = empty($text) && $ix == 1 ? __( 'KB Home', 'echo-knowledge-base' ) : $text;
        $text = empty($text) && $ix > 1 ? __( 'Link ', 'echo-knowledge-base' ) . ($ix - 1) : $text;

        // output URL if not the last crumb
        if ( $ix < sizeof($breadcrumb) ) {
            if ( empty($link) ) {
                echo '<span ' . $breadcrumb_style2 . ' >' . esc_html( $text ) . '</span>';
            } else {
                echo '<a href="' . esc_url($link) . '"><span ' . $breadcrumb_style2 . ' >' . esc_html( $text ) . '</span></a>';
            }
            echo '<span class="eckb-breadcrumb-link-icon ' . esc_html($kb_config['breadcrumb_icon_separator']) . '"></span>';
        } else {
            echo '<span ' . $breadcrumb_style2 . ' >' . esc_html( $text ) . '</span>';
        }

		echo '	</span>';
		echo '</li>';

	}       ?>

	</ul>
</div>          <?php
