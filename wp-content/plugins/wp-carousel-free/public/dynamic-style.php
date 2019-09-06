<?php
/**
 * The style file for the WP Carousel pro.
 *
 * @since    3.0.0
 * @package WP Carousel
 * @subpackage wp-carousel-free/public
 */

$section_title_dynamic_css = '';
if ( $section_title ) {
	$section_title_dynamic_css .= '
	.wpcp-wrapper-' . $post_id . ' .sp-wpcpro-section-title {
		margin-bottom: ' . $section_title_margin . 'px;
	}';
}

$slide_border = isset( $shortcode_data['wpcp_slide_border'] ) ? $shortcode_data['wpcp_slide_border'] : '';
$custom_css   = sp_get_option( 'wpcp_custom_css' );

if ( 'product-carousel' == $carousel_type ) {
	$wpcp_product_css = '.wpcp-carousel-section.sp-wpcp-' . $post_id . '.wpcp-product-carousel .wpcp-slide-image {
		border: ' . $slide_border['width'] . 'px ' . $slide_border['style'] . ' ' . $slide_border['color'] . ';
	}';
} else {
	$wpcp_product_css = '.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .wpcp-single-item {
		border: ' . $slide_border['width'] . 'px ' . $slide_border['style'] . ' ' . $slide_border['color'] . ';
	}';
}

// Nav Style.
$nav_dynamic_style  = '';
$nav_dynamic_style .= '
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .slick-prev,
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .slick-next,
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .slick-prev:hover,
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .slick-next:hover {
	background: none;
	border: none;
	font-size: 30px;
}
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .slick-prev i,
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .slick-next i {
	color: ' . $shortcode_data['wpcp_nav_colors']['color1'] . ';
}
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .slick-prev i:hover,
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' .slick-next i:hover {
	color: ' . $shortcode_data['wpcp_nav_colors']['color2'] . ';
}';

/**
 * The Dynamic Style CSS.
 */
$the_wpcf_dynamic_css  = '<style>';
$the_wpcf_dynamic_css .= $wpcp_product_css;
$the_wpcf_dynamic_css .= $section_title_dynamic_css;
$the_wpcf_dynamic_css .= $nav_dynamic_style;
$the_wpcf_dynamic_css .= '
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' ul.slick-dots li button {
	background-color: ' . $shortcode_data['wpcp_pagination_color']['color1'] . ';
}
.wpcp-carousel-section.sp-wpcp-' . $post_id . ' ul.slick-dots li.slick-active button {
	background-color: ' . $shortcode_data['wpcp_pagination_color']['color2'] . ';
}';
if ( 'hide_mobile' === $wpcp_arrows ) {
	$the_wpcf_dynamic_css .= '
	@media screen and (max-width: 479px) {
		.wpcp-carousel-section.nav-vertical-center {
			padding: 0;
		}
	}';
}
$the_wpcf_dynamic_css .= $custom_css;
$the_wpcf_dynamic_css .= '</style>';
