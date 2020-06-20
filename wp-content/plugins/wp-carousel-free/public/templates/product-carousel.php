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
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$number_of_total_products = $upload_data['wpcp_total_products'];

// $show_product_image           = $shortcode_data['show_image'];
$show_product_name   = $shortcode_data['wpcp_product_name'];
$show_product_price  = $shortcode_data['wpcp_product_price'];
$show_product_rating = $shortcode_data['wpcp_product_rating'];
$show_product_cart   = $shortcode_data['wpcp_product_cart'];
	$default_args    = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $number_of_total_products,
		'order'               => $post_order,
		'orderby'             => $post_order_by,
		'meta_query'          => array(
			array(
				'key'     => '_stock_status',
				'value'   => 'outofstock',
				'compare' => 'NOT IN',
			),
		),
	);
	$product_query   = new WP_Query( $default_args );

	// Carousel Wrapper Start.
	echo '<div class="wpcp-carousel-wrapper wpcp-wrapper-' . $post_id . '">';
	if ( $section_title ) {
		echo '<h2 class="sp-wpcpro-section-title">' . get_the_title( $post_id ) . '</h2>';
	}
	if ( $preloader ) {
		require WPCAROUSELF_PATH . '/public/templates/preloader.php';
	}
	echo '<div id="sp-wp-carousel-free-id-' . $post_id . '" class="' . $carousel_classes . '" ' . $wpcp_slick_options . ' dir="ltr">';
	if ( $product_query->have_posts() ) {
		while ( $product_query->have_posts() ) :
			$product_query->the_post();
			global $product, $woocommerce;
			echo '<div class="wpcp-single-item">';

			$product_thumb_id       = get_post_thumbnail_id();
			$product_thumb_alt_text = get_post_meta( $product_thumb_id, '_wp_attachment_image_alt', true );
			$image_url              = wp_get_attachment_image_src( $product_thumb_id, $image_sizes );
			$the_image_title_attr   = ' title="' . get_the_title() . '"';
			$image_title_attr       = 'true' == $show_image_title_attr ? $the_image_title_attr : '';

			// Product Thumbnail.
			$wpcp_product_image = '';
			if ( ! empty( $image_url[0] ) && $show_slide_image ) {
						$wpcp_product_thumb = sprintf( '<img class="wpcp-product-image" src="%1$s"%2$s alt="%3$s" width="%4$s" height="%5$s"/>', $image_url[0], $image_title_attr, $product_thumb_alt_text, $image_url[1], $image_url[2] );

						$wpcp_product_image = sprintf( '<div class="wpcp-slide-image"><a href="%1$s">%2$s</a></div>', get_the_permalink(), $wpcp_product_thumb );
			}

			// Product name.
			$wpcp_product_name = sprintf( '<h2 class="wpcp-product-title"><a href="%1$s">%2$s</a></h2>', get_the_permalink(), get_the_title() );

			$price_html = $product->get_price_html();
			if ( $price_html ) {
				$wpcp_product_price = sprintf( '<div class="wpcp-product-price">%1$s</div>', $price_html );
			}

			// Product rating.
			$av_rating      = $product->get_average_rating();
			$average_rating = ( $av_rating / 5 ) * 100;
			if ( $average_rating > 0 ) {
				$wpcp_product_rating = sprintf( '<div class="wpcp-product-rating woocommerce"><div class="woocommerce-product-rating"><div class="star-rating" title="%1$s %2$s %3$s"><span style="width:%4$s"></span></div></div></div>', __( 'Rated ', 'wp-carousel-free' ), $av_rating, __( ' out of 5', 'wp-carousel-free' ), $average_rating . '%' );
			}

			// Add to cart button.
				$wpcp_cart        = apply_filters( 'wpcp_filter_product_cart', do_shortcode( '[add_to_cart id="' . get_the_ID() . '" show_price="false" style="none"]' ) );
				$wpcp_cart_button = sprintf( '<div class="wpcp-cart-button">%1$s</div>', $wpcp_cart );

			if ( $show_product_name || $show_product_rating || $show_product_price || $show_product_cart ) {
				$wpcp_product_details = '<div class="wpcp-all-captions">' . ( ( $show_product_name ) && isset( $wpcp_product_name ) ? $wpcp_product_name : '' ) . ( $show_product_price && isset( $wpcp_product_price ) ? $wpcp_product_price : '' ) . ( $show_product_rating && isset( $wpcp_product_rating ) ? $wpcp_product_rating : '' ) . ( $show_product_cart ? $wpcp_cart_button : '' ) . '</div>';
			}
			echo $wpcp_product_image . $wpcp_product_details;
			echo '</div>';

		endwhile;
		wp_reset_postdata();
	} else {
		$outline .= '<h2 class="sp-not-found-any-post" >' . esc_html__( 'No products found', 'wp-carousel-free' ) . '</h2>';
	}
	echo '</div>
</div>'; // Carousel Wrapper.
