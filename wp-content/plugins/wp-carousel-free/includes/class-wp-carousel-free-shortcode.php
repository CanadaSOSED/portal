<?php

/**
 * The file that defines the plugin shortcode class.
 *
 * A class definition that define main carousel shortcode of the plugin.
 *
 * @link       https://shapedplugin.com/
 * @since      3.0.0
 *
 * @package    WP_Carousel_Free
 * @subpackage WP_Carousel_Free/includes
 */

/**
 * The Shortcode class.
 *
 * This is used to define shortcode, shortcode attributes, and carousel types.
 *
 * @since      3.0.0
 * @package    WP_Carousel_Free
 * @subpackage WP_Carousel_Free/includes
 * @author     Shamim Mia <shamhagh@gmail.com>
 */
class WP_Carousel_Free_Shortcode {

	/**
	 * Holds the class object.
	 *
	 * @since 2.0.0
	 * @var object
	 */
	public static $instance;

	/**
	 * Undocumented variable
	 *
	 * @var string $post_id The post id of the carousel shortcode.
	 */
	public $post_id;


	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since 2.0.0
	 * @static
	 * @return WP_Carousel_Free_Shortcode Shortcode instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * A shortcode for rendering the carousel.
	 *
	 * @param integer $attributes The ID the shortcode.
	 * @return void
	 */
	public function sp_wp_carousel_shortcode( $attributes ) {
		if ( empty( $attributes['id'] ) ) {
			return;
		}

		$post_id = intval( $attributes['id'] );

		// Video Carousel.
		$upload_data = get_post_meta( $post_id, 'sp_wpcp_upload_options', true );
		if ( empty( $upload_data ) ) {
			return;
		}
		$carousel_type = isset( $upload_data['wpcp_carousel_type'] ) ? $upload_data['wpcp_carousel_type'] : '';

		$shortcode_data = get_post_meta( $post_id, 'sp_wpcp_shortcode_options', true );

		// Image Carousel.
		$image_orderby    = isset( $shortcode_data['wpcp_image_order_by'] ) ? $shortcode_data['wpcp_image_order_by'] : '';
		$show_slide_image = isset( $shortcode_data['show_image'] ) ? $shortcode_data['show_image'] : '';
		$show_img_title   = isset( $shortcode_data['wpcp_post_title'] ) ? $shortcode_data['wpcp_post_title'] : '';

		$_image_title_att      = isset( $shortcode_data['_image_title_attr'] ) ? $shortcode_data['_image_title_attr'] : '';
		$show_image_title_attr = ( true == $_image_title_att ) ? 'true' : 'false';
		$image_sizes           = isset( $shortcode_data['wpcp_image_sizes'] ) ? $shortcode_data['wpcp_image_sizes'] : '';

		// Carousel Column.
		$column_number         = isset( $shortcode_data['wpcp_number_of_columns'] ) ? $shortcode_data['wpcp_number_of_columns'] : '';
		$old_column_lg_desktop = isset( $column_number['column1'] ) ? $column_number['column1'] : '5';
		$column_lg_desktop     = isset( $column_number['lg_desktop'] ) && ! empty( $column_number['lg_desktop'] ) ? $column_number['lg_desktop'] : $old_column_lg_desktop;
		$old_column_desktop    = isset( $column_number['column2'] ) ? $column_number['column2'] : '4';
		$column_desktop        = isset( $column_number['desktop'] ) && ! empty( $column_number['desktop'] ) ? $column_number['desktop'] : $old_column_desktop;
		$old_column_sm_desktop = isset( $column_number['column3'] ) ? $column_number['column3'] : '3';
		$column_sm_desktop     = isset( $column_number['laptop'] ) && ! empty( $column_number['laptop'] ) ? $column_number['laptop'] : $old_column_sm_desktop;
		$old_column_tablet     = isset( $column_number['column4'] ) ? $column_number['column4'] : '2';
		$column_tablet         = isset( $column_number['tablet'] ) && ! empty( $column_number['tablet'] ) ? $column_number['tablet'] : $old_column_tablet;
		$old_column_mobile     = isset( $column_number['column5'] ) ? $column_number['column5'] : '1';
		$column_mobile         = isset( $column_number['mobile'] ) && ! empty( $column_number['mobile'] ) ? $column_number['mobile'] : $old_column_mobile;

		// Carousel Settings.
		$preloader          = isset( $shortcode_data['wpcp_preloader'] ) ? $shortcode_data['wpcp_preloader'] : true;
		$auto_play          = $shortcode_data['wpcp_carousel_auto_play'] ? 'true' : 'false';
		$old_autoplay_speed = isset( $shortcode_data['carousel_auto_play_speed'] ) && is_numeric( $shortcode_data['carousel_auto_play_speed'] ) ? $shortcode_data['carousel_auto_play_speed'] : '3000';
		$autoplay_speed     = isset( $shortcode_data['carousel_auto_play_speed']['all'] ) && ! empty( $shortcode_data['carousel_auto_play_speed']['all'] ) ? $shortcode_data['carousel_auto_play_speed']['all'] : $old_autoplay_speed;
		$old_speed          = isset( $shortcode_data['standard_carousel_scroll_speed'] ) && is_numeric( $shortcode_data['standard_carousel_scroll_speed'] ) ? $shortcode_data['standard_carousel_scroll_speed'] : '600';
		$speed              = isset( $shortcode_data['standard_carousel_scroll_speed']['all'] ) && ! empty( $shortcode_data['standard_carousel_scroll_speed']['all'] ) ? $shortcode_data['standard_carousel_scroll_speed']['all'] : $old_speed;
		$infinite           = $shortcode_data['carousel_infinite'] ? 'true' : 'false';
		$pause_on_hover     = $shortcode_data['carousel_pause_on_hover'] ? 'true' : 'false';
		$draggable          = $shortcode_data['slider_draggable'] ? 'true' : 'false';
		$swipe              = $shortcode_data['slider_swipe'] ? 'true' : 'false';

		$post_order_by = ( isset( $shortcode_data['wpcp_post_order_by'] ) ? $shortcode_data['wpcp_post_order_by'] : '' );
		$post_order    = ( isset( $shortcode_data['wpcp_post_order'] ) ? $shortcode_data['wpcp_post_order'] : '' );

		$wpcp_dots = isset( $shortcode_data['wpcp_pagination'] ) ? $shortcode_data['wpcp_pagination'] : '';
		switch ( $wpcp_dots ) {
			case 'show':
				$dots        = 'true';
				$dots_mobile = 'true';
				break;
			case 'hide':
				$dots        = 'false';
				$dots_mobile = 'false';
				break;
			case 'hide_mobile':
				$dots        = 'true';
				$dots_mobile = 'false';
				break;
		}
		$wpcp_arrows = isset( $shortcode_data['wpcp_navigation'] ) ? $shortcode_data['wpcp_navigation'] : 'show';
		switch ( $wpcp_arrows ) {
			case 'show':
				$arrows        = 'true';
				$arrows_mobile = 'true';
				break;
			case 'hide':
				$arrows        = 'false';
				$arrows_mobile = 'false';
				break;
			case 'hide_mobile':
				$arrows        = 'true';
				$arrows_mobile = 'false';
				break;
		}

		// Dynamic CSS.
		require WPCAROUSELF_PATH . '/public/dynamic-style.php';

		// Carousel Classes.
		$carousel_classes = 'wpcp-carousel-section sp-wpcp-' . $post_id . ' nav-vertical-center';
		if ( 'image-carousel' === $carousel_type ) {
			$carousel_classes .= ' wpcp-image-carousel';
		} elseif ( 'post-carousel' === $carousel_type ) {
			$carousel_classes .= ' wpcp-post-carousel';
		} elseif ( 'product-carousel' === $carousel_type ) {
			$carousel_classes .= ' wpcp-product-carousel';
		}

		// Carousel Configurations.
		if ( wpcf_get_option( 'wpcp_slick_js', true ) ) {
			wp_enqueue_script( 'wpcf-slick' );
		}
		wp_enqueue_script( 'wpcf-slick-config' );

		// Preloader classes.
		if ( $preloader ) {
			wp_enqueue_script( 'wpcp-preloader' );
			$carousel_classes .= ' wpcp-preloader';
		}

		$carousel_classes  .= ' wpcp-standard';
		$wpcp_slick_options = 'data-slick=\'{ "accessibility":true, "arrows":' . $arrows . ', "autoplay":' . $auto_play . ', "autoplaySpeed":' . $autoplay_speed . ', "dots":' . $dots . ', "infinite":' . $infinite . ', "speed":' . $speed . ', "pauseOnHover":' . $pause_on_hover . ', "slidesToShow":' . $column_lg_desktop . ', "responsive":[ { "breakpoint":1200, "settings": { "slidesToShow":' . $column_desktop . ' } }, { "breakpoint":980, "settings":{ "slidesToShow":' . $column_sm_desktop . ' } }, { "breakpoint":736, "settings": { "slidesToShow":' . $column_tablet . ' } }, {"breakpoint":480, "settings":{ "slidesToShow":' . $column_mobile . ', "arrows": ' . $arrows_mobile . ', "dots": ' . $dots_mobile . ' } } ], "swipe": ' . $swipe . ', "swipeToSlide": true, "draggable": ' . $draggable . ' }\' ';

		if ( 'image-carousel' === $carousel_type ) {
			ob_start();
			echo $the_wpcf_dynamic_css;
			require WPCAROUSELF_PATH . '/public/templates/image-carousel.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'sp_wpcp_image_carousel', $html, $post_id );
		}
		if ( 'post-carousel' === $carousel_type ) {
			ob_start();
			echo $the_wpcf_dynamic_css;
			require WPCAROUSELF_PATH . '/public/templates/post-carousel.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'sp_wpcp_post_carousel', $html, $post_id );
		}
		if ( 'product-carousel' === $carousel_type ) {
			ob_start();
			echo $the_wpcf_dynamic_css;
			require WPCAROUSELF_PATH . '/public/templates/product-carousel.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'sp_wpcp_product_carousel', $html, $post_id );
		}

		return '';
	}
}
