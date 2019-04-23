<?php
/**
 * @package nmbs
 */
$col   = empty( $shortcode_atts['col'] ) ? LEARNDASH_COURSE_GRID_COLUMNS :intval( $shortcode_atts['col'] );
$col   = $col > 6 ? 6 : $col;
$smcol = $col == 1 ? 1 : $col / 2;
$col   = 12 / $col;
$smcol = intval( ceil( 12 / $smcol ) );
$col   = is_float( $col ) ? number_format( $col, 1 ) : $col;
$col   = str_replace( '.', '-', $col );

global $post; $post_id = $post->ID;

$course_id = $post_id;
$user_id   = get_current_user_id();

$enable_video = get_post_meta( $post->ID, '_learndash_course_grid_enable_video_preview', true );
$embed_code   = get_post_meta( $post->ID, '_learndash_course_grid_video_embed_code', true );
$button_text  = get_post_meta( $post->ID, '_learndash_course_grid_custom_button_text', true );

// Retrive oembed HTML if URL provided
if ( preg_match( '/^http/', $embed_code ) ) {
	$embed_code = wp_oembed_get( $embed_code, array( 'height' => 600, 'width' => 400 ) );
}

if ( isset( $shortcode_atts['course_id'] ) ) {
	$button_link = learndash_get_step_permalink( get_the_ID(), $shortcode_atts['course_id'] );
} else {
	$button_link = get_permalink();
}

$button_link = apply_filters( 'learndash_course_grid_custom_button_link', $button_link, $post_id );

$button_text = isset( $button_text ) && ! empty( $button_text ) ? $button_text : __( 'See more...', 'learndash-course-grid' );
$button_text = apply_filters( 'learndash_course_grid_custom_button_text', $button_text, $post_id );

$options = get_option( 'sfwd_cpt_options' );
$currency_setting = class_exists( 'LearnDash_Settings_Section' ) ? LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_PayPal', 'paypal_currency' ) : null;
$currency = '';

if ( isset( $currency_setting ) || ! empty( $currency_setting ) ) {
	$currency = $currency_setting;
} elseif ( isset( $options['modules'] ) && isset( $options['modules']['sfwd-courses_options'] ) && isset( $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'] ) ) {
	$currency = $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'];
}

if ( class_exists( 'NumberFormatter' ) ) {
	
	$locale = get_locale();
	$number_format = new NumberFormatter( $locale . '@currency=' . $currency, NumberFormatter::CURRENCY );
	$currency = $number_format->getSymbol( NumberFormatter::CURRENCY_SYMBOL );
}

/**
 * Currency symbol filter hook
 * 
 * @param string $currency Currency symbol
 * @param int    $course_id
 */
$currency = apply_filters( 'learndash_course_grid_currency', $currency, $course_id );

$course_options = get_post_meta($post_id, "_sfwd-courses", true);
$price = $course_options && isset($course_options['sfwd-courses_course_price']) ? $course_options['sfwd-courses_course_price'] : __( 'Free', 'learndash-course-grid' );
$price_type = $course_options && isset( $course_options['sfwd-courses_course_price_type'] ) ? $course_options['sfwd-courses_course_price_type'] : '';
$short_description = @$course_options['sfwd-courses_course_short_description'];

/**
 * Filter: individual grid class
 * 
 * @param int 	$course_id Course ID
 * @param array $course_options Course options
 * @var string
 */
$grid_class = apply_filters( 'learndash_course_grid_class', '', $course_id, $course_options );

$has_access   = sfwd_lms_has_access( $course_id, $user_id );
$is_completed = learndash_course_completed( $user_id, $course_id );

$price_text = '';

if ( is_numeric( $price ) && ! empty( $price ) ) {
	$price_format = apply_filters( 'learndash_course_grid_price_text_format', '{currency}{price}' );

	$price_text = str_replace(array( '{currency}', '{price}' ), array( $currency, $price ), $price_format );
} elseif ( is_string( $price ) && ! empty( $price ) ) {
	$price_text = $price;
} elseif ( empty( $price ) ) {
	$price_text = __( 'Free', 'learndash-course-grid' );
}

$class       = 'ld_course_grid_price';
$ribbon_text = get_post_meta( $post->ID, '_learndash_course_grid_custom_ribbon_text', true );
$ribbon_text = isset( $ribbon_text ) && ! empty( $ribbon_text ) ? $ribbon_text : '';

if ( $has_access && ! $is_completed && $price_type != 'open' && empty( $ribbon_text ) ) {
	$class .= ' ribbon-enrolled';
	$ribbon_text = __( 'Enrolled', 'learndash-course-grid' );
} elseif ( $has_access && $is_completed && $price_type != 'open' && empty( $ribbon_text ) ) {
	$class .= '';
	$ribbon_text = __( 'Completed', 'learndash-course-grid' );
} elseif ( $price_type == 'open' && empty( $ribbon_text ) ) {
	if ( is_user_logged_in() && ! $is_completed ) {
		$class .= ' ribbon-enrolled';
		$ribbon_text = __( 'Enrolled', 'learndash-course-grid' );
	} elseif ( is_user_logged_in() && $is_completed ) {
		$class .= '';
		$ribbon_text = __( 'Completed', 'learndash-course-grid' );
	} else {
		$class .= ' ribbon-enrolled';
		$ribbon_text = '';
	}
} elseif ( $price_type == 'closed' && empty( $price ) ) {
	$class .= ' ribbon-enrolled';

	if ( is_numeric( $price ) ) {
		$ribbon_text = $price_text;
	} else {
		$ribbon_text = '';
	}
} else {
	if ( empty( $ribbon_text ) ) {
		$class .= ! empty( $course_options['sfwd-courses_course_price'] ) ? ' price_' . $currency : ' free';
		$ribbon_text = $price_text;
	} else {
		$class .= ' custom';
	}
}

/**
 * Filter: individual course ribbon text
 *
 * @param string $ribbon_text Returned ribbon text
 * @param int    $course_id   Course ID
 * @param string $price_type  Course price type
 */
$ribbon_text = apply_filters( 'learndash_course_grid_ribbon_text', $ribbon_text, $course_id, $price_type );

if ( '' == $ribbon_text ) {
	$class = '';
}

/**
 * Filter: individual course ribbon class names
 *
 * @param string $class     	 Returned class names
 * @param int    $course_id 	 Course ID
 * @param array  $course_options Course's options
 * @var string
 */
$class = apply_filters( 'learndash_course_grid_ribbon_class', $class, $course_id, $course_options );

$thumb_size = isset( $shortcode_atts['thumb_size'] ) && ! empty( $shortcode_atts['thumb_size'] ) ? $shortcode_atts['thumb_size'] : 'course-thumb';

?>
<div class="ld_course_grid col-sm-<?php echo $smcol; ?> col-md-<?php echo $col; ?> <?php echo esc_attr( $grid_class ); ?>">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'thumbnail course' ); ?>>
	
		<?php if ( $shortcode_atts['show_thumbnail'] == 'true' ) : ?>		

			<?php if ( $post->post_type == 'sfwd-courses' ) : ?>
			<div class="<?php echo esc_attr( $class ); ?>">
				<?php echo esc_attr( $ribbon_text ); ?>
			</div>
			<?php endif; ?>

			<?php if ( 1 == $enable_video && ! empty( $embed_code ) ) : ?>
			<div class="ld_course_grid_video_embed">
			<?php echo $embed_code; ?>
			</div>
			<?php elseif( has_post_thumbnail() ) :?>
			<a href="<?php the_permalink(); ?>" rel="bookmark">
				<?php the_post_thumbnail( $thumb_size ); ?>
			</a>
			<?php else :?>
			<a href="<?php echo esc_url( $button_link ); ?>" rel="bookmark">
				<img alt="" src="<?php echo plugins_url( 'no_image.jpg', LEARNDASH_COURSE_GRID_FILE); ?>"/>
			</a>
			<?php endif;?>
		<?php endif; ?>

		<?php if ( $shortcode_atts['show_content'] == 'true' ) : ?>
			
			<div class="caption">
				<h3 class="entry-title"><?php the_title(); ?></h3>
				<?php if ( ! empty( $short_description ) ) : ?>
				<p class="entry-content"><?php echo do_shortcode( htmlspecialchars_decode( $short_description ) ); ?></p>
				<?php endif; ?>
				<p class="ld_course_grid_button"><a class="btn btn-primary" role="button" href="<?php echo esc_url( $button_link ); ?>" rel="bookmark"><?php echo esc_attr( $button_text ); ?></a></p>
				<?php if ( isset( $shortcode_atts['progress_bar'] ) && $shortcode_atts['progress_bar'] == 'true' ) : ?>
				<p><?php echo do_shortcode( '[learndash_course_progress course_id="' . get_the_ID() . '" user_id="' . get_current_user_id() . '"]' ); ?></p>
				<?php endif; ?>
			</div><!-- .entry-header -->
		<?php endif; ?>
	</article><!-- #post-## -->
</div>
