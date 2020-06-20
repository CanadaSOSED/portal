<?php
/**
 * The image carousel template.
 *
 * @package WP_Carousel_Free
 * @subpackage WP_Carousel_Free/public/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$gallery_ids         = $upload_data['wpcp_gallery'];
$the_image_title_at  = isset( $shortcode_data['wpcp_logo_link_nofollow'] ) ? $shortcode_data['wpcp_logo_link_nofollow'] : '';
$image_link_nofollow = true == $the_image_title_at ? ' rel="nofollow"' : '';
if ( empty( $gallery_ids ) ) {
			return;
}
echo '<div class="wpcp-carousel-wrapper wpcp-wrapper-' . $post_id . '">';
if ( $section_title ) {
	echo '<h2 class="sp-wpcpro-section-title">' . get_the_title( $post_id ) . '</h2>';
}
if ( $preloader ) {
	require WPCAROUSELF_PATH . '/public/templates/preloader.php';
}
echo '<div id="sp-wp-carousel-free-id-' . $post_id . '" class="' . $carousel_classes . '" ' . $wpcp_slick_options . ' dir="ltr">';
$attachments = explode( ',', $gallery_ids );
( ( 'rand' == $image_orderby ) ? shuffle( $attachments ) : '' );
if ( is_array( $attachments ) || is_object( $attachments ) ) :
	foreach ( $attachments as $attachment ) {
		$image_data           = get_post( $attachment );
		$image_title          = $image_data->post_title;
		$image_alt_titles     = $image_data->_wp_attachment_image_alt;
		$image_alt_title      = ! empty( $image_alt_titles ) ? $image_alt_titles : $image_title;
		$image_url            = wp_get_attachment_image_src( $attachment, $image_sizes );
		$the_image_title_attr = ' title="' . $image_title . '"';
		$image_title_attr     = 'true' === $show_image_title_attr ? $the_image_title_attr : '';

		$image = sprintf( '<img src="%1$s"%2$s alt="%3$s" width="%4$s" height="%5$s">', $image_url[0], $image_title_attr, $image_alt_title, $image_url[1], $image_url[2] );
		// Single Item.
		echo '<div class="wpcp-single-item">';
		echo sprintf( '<div class="wpcp-slide-image">%1$s</div>', $image );
		echo '</div>';
	} // End foreach.
endif;
echo '</div>';
echo '</div>'; // Carousel Wrapper.
