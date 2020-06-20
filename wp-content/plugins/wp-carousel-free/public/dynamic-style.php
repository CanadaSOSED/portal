<?php
/**
 * The style file for the WP Carousel pro.
 *
 * @since    3.0.0
 * @package WP Carousel
 * @subpackage wp-carousel-free/public
 */

$section_title_dynamic_css = '';
$section_title             = isset( $shortcode_data['section_title'] ) ? $shortcode_data['section_title'] : '';

if ( $section_title ) {
	$old_section_title_margin   = isset( $shortcode_data['section_title_margin_bottom'] ) && is_numeric( $shortcode_data['section_title_margin_bottom'] ) ? $shortcode_data['section_title_margin_bottom'] : '30';
	$section_title_margin       = isset( $shortcode_data['section_title_margin_bottom']['all'] ) && ( $shortcode_data['section_title_margin_bottom']['all'] >= 0 ) ? $shortcode_data['section_title_margin_bottom']['all'] : $old_section_title_margin;
	$section_title_dynamic_css .= '
    .wpcp-wrapper-' . $post_id . ' .sp-wpcpro-section-title {
        margin-bottom: ' . $section_title_margin . 'px;
    }';
}

$slide_border           = isset( $shortcode_data['wpcp_slide_border'] ) ? $shortcode_data['wpcp_slide_border'] : '';
$old_slide_border_width = isset( $slide_border['width'] ) && ! empty( $slide_border['width'] ) ? $slide_border['width'] : '1';
$slide_border_width     = isset( $shortcode_data['wpcp_slide_border']['all'] ) && ! empty( $shortcode_data['wpcp_slide_border']['all'] ) ? $shortcode_data['wpcp_slide_border']['all'] : $old_slide_border_width;

// Product Image Border.
$image_border_width = isset( $shortcode_data['wpcp_product_image_border']['all'] ) && ! empty( $shortcode_data['wpcp_product_image_border']['all'] ) ? $shortcode_data['wpcp_product_image_border']['all'] : $old_slide_border_width;
$image_border_style = isset( $shortcode_data['wpcp_product_image_border']['style'] ) ? $shortcode_data['wpcp_product_image_border']['style'] : '1';
$image_border_color = isset( $shortcode_data['wpcp_product_image_border']['color'] ) ? $shortcode_data['wpcp_product_image_border']['color'] : '#ddd';

$custom_css = wpcf_get_option( 'wpcp_custom_css' );

if ( 'product-carousel' == $carousel_type ) {
	$wpcp_product_css = '#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . '.wpcp-product-carousel .wpcp-slide-image {
		border: ' . $image_border_width . 'px ' . $image_border_style . ' ' . $image_border_color . ';
	}';
} else {
	$wpcp_product_css = '#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .wpcp-single-item {
		border: ' . $slide_border_width . 'px ' . $slide_border['style'] . ' ' . $slide_border['color'] . ';
	}';
}

// Preloader.
if ( $preloader ) {
	$preloader_dynamic_style = '
		.wpcp-carousel-wrapper.wpcp-wrapper-' . $post_id . '{
			position: relative;
		}
		#sp-wp-carousel-free-id-' . $post_id . '{
			opacity: 0;
		}
		#wpcp-preloader-' . $post_id . '{
			position: absolute;
			left: 0;
			top: 0;
			height: 100%;
			width: 100%;
			text-align: center;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		';
} else {
	$preloader_dynamic_style = '
	.wpcp-carousel-section.wpcp-standard {
		display: none;
	}
	.wpcp-carousel-section.wpcp-standard.slick-initialized {
		display: block;		
	}';
}

// Nav Style.
$nav_dynamic_style = '';
if ( 'hide' !== $wpcp_arrows ) {
	$wpcp_nav_color       = isset( $shortcode_data['wpcp_nav_colors']['color1'] ) ? $shortcode_data['wpcp_nav_colors']['color1'] : '#aaa';
	$wpcp_nav_hover_color = isset( $shortcode_data['wpcp_nav_colors']['color2'] ) ? $shortcode_data['wpcp_nav_colors']['color2'] : '#fff';
	$nav_dynamic_style   .= '
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .slick-prev,
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .slick-next,
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .slick-prev:hover,
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .slick-next:hover {
		background: none;
		border: none;
		font-size: 30px;
	}
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .slick-prev i,
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .slick-next i {
		color: ' . $wpcp_nav_color . ';
	}
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .slick-prev i:hover,
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' .slick-next i:hover {
		color: ' . $wpcp_nav_hover_color . ';
	}';
}
$pagination_dynamic_style = '';
if ( 'hide' !== $wpcp_dots ) {
	$wpcp_dot_color           = isset( $shortcode_data['wpcp_pagination_color']['color1'] ) ? $shortcode_data['wpcp_pagination_color']['color1'] : '#ccc';
	$wpcp_dot_active_color    = isset( $shortcode_data['wpcp_pagination_color']['color2'] ) ? $shortcode_data['wpcp_pagination_color']['color2'] : '#52b3d9';
	$pagination_dynamic_style = '
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' ul.slick-dots li button {
		background-color: ' . $wpcp_dot_color . ';
	}
	#sp-wp-carousel-free-id-' . $post_id . '.sp-wpcp-' . $post_id . ' ul.slick-dots li.slick-active button {
		background-color: ' . $wpcp_dot_active_color . ';
	}
	';
}


/**
 * The Dynamic Style CSS.
 */
$the_wpcf_dynamic_css  = '<style>';
$the_wpcf_dynamic_css .= $wpcp_product_css;
$the_wpcf_dynamic_css .= $section_title_dynamic_css;
$the_wpcf_dynamic_css .= $nav_dynamic_style;
$the_wpcf_dynamic_css .= $pagination_dynamic_style;
$the_wpcf_dynamic_css .= $preloader_dynamic_style;
if ( 'post-carousel' === $carousel_type ) {
	$the_wpcf_dynamic_css .= '
	.wpcp-carousel-wrapper #sp-wp-carousel-free-id-' . $post_id . '.wpcp-post-carousel .wpcp-single-item {
		background: ' . ( isset( $shortcode_data['wpcp_slide_background'] ) ? $shortcode_data['wpcp_slide_background'] : '#f9f9f9' ) . ';
	}';
}

if ( 'hide_mobile' === $wpcp_arrows ) {
	$the_wpcf_dynamic_css .= '
	@media screen and (max-width: 479px) {
		#sp-wp-carousel-free-id-' . $post_id . '.nav-vertical-center {
			padding: 0;
		}
	}';
}
$the_wpcf_dynamic_css .= $custom_css;
$the_wpcf_dynamic_css .= '</style>';
