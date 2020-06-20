<?php
/**
 * The image carousel template.
 *
 * @package WP_Carousel_Pro
 * @subpackage WP_Carousel_Pro/public/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$preloader_image = WPCAROUSELF_URL . 'public/css/ajax-loader.gif';
if ( ! empty( $preloader_image ) ) {
	echo '<div id="wpcp-preloader-' . $post_id . '" class="wpcp-carousel-preloader">';
	echo '<img src=" ' . $preloader_image . ' "/>';
	echo '</div>';
}
