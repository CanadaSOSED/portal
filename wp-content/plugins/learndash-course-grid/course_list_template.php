<?php
/**
 * @package nmbs
 */
$col = empty($shortcode_atts["col"])? 3:intval($shortcode_atts["col"]);
$smcol = $col/1.5;
$col = empty($col)? 1:($col >= 12)? 12:$col;
$smcol = empty($smcol)? 1:($smcol >= 12)? 12:$smcol;
$col = intVal(12/$col);
$smcol = intVal(12/$smcol);

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

$button_text = isset( $button_text ) && ! empty( $button_text ) ? $button_text : __( 'See more...', 'learndash_course_grid' );

$button_text = apply_filters( 'learndash_course_grid_custom_button_text', $button_text, $post_id );

$options = get_option('sfwd_cpt_options');
$currency = null;

if ( ! is_null( $options ) ) {
	if ( isset($options['modules'] ) && isset( $options['modules']['sfwd-courses_options'] ) && isset( $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'] ) )
	$currency = $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'];
}

if( is_null( $currency ) )
	$currency = 'USD';

$course_options = get_post_meta($post_id, "_sfwd-courses", true);
$price = $course_options && isset($course_options['sfwd-courses_course_price']) ? $course_options['sfwd-courses_course_price'] : __( 'Free', 'learndash_course_grid' );
$short_description = @$course_options['sfwd-courses_course_short_description'];

$has_access   = sfwd_lms_has_access( $course_id, $user_id );
$is_completed = learndash_course_completed( $user_id, $course_id );

if( $price == '' )
	$price .= __( 'Free', 'learndash_course_grid' );

if ( is_numeric( $price ) ) {
	if ( $currency == "USD" )
		$price = '$' . $price;
	else
		$price .= ' ' . $currency;
}

$class       = '';
$ribbon_text = '';

if ( $has_access && ! $is_completed ) {
	$class = 'ld_course_grid_price ribbon-enrolled';
	$ribbon_text = __( 'Enrolled', 'learndash_course_grid' );
} elseif ( $has_access && $is_completed ) {
	$class = 'ld_course_grid_price';
	$ribbon_text = __( 'Completed', 'learndash_course_grid' );
} else {
	$class = ! empty( $course_options['sfwd-courses_course_price'] ) ? 'ld_course_grid_price price_' . $currency : 'ld_course_grid_price free';
	$ribbon_text = $price;
}

?>
<div class="ld_course_grid col-sm-<?php echo $smcol;?> col-md-<?php echo $col; ?>">
	<article id="post-<?php the_ID(); ?>" <?php post_class('thumbnail course'); ?>>
		
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
			<?php the_post_thumbnail('course-thumb'); ?>
		</a>
		<?php else :?>
		<a href="<?php the_permalink(); ?>" rel="bookmark">
			<img alt="" src="<?php echo plugins_url( 'no_image.jpg', LEARNDASH_COURSE_GRID_FILE); ?>"/>
		</a>
		<?php endif;?>
		<div class="caption">
			<h3 class="entry-title"><?php the_title(); ?></h3>
			<?php if(!empty($short_description)) { ?>
			<p class="entry-content"><?php echo htmlspecialchars_decode( do_shortcode( $short_description ) ); ?></p>
			<?php  } ?>
			<p class="ld_course_grid_button"><a class="btn btn-primary" role="button" href="<?php the_permalink(); ?>" rel="bookmark"><?php echo esc_attr( $button_text ); ?></a></p>
			<?php if ( isset( $shortcode_atts['progress_bar'] ) && $shortcode_atts['progress_bar'] == 'true' ) : ?>
			<p><?php echo do_shortcode( '[learndash_course_progress course_id="' . get_the_ID() . '" user_id="' . get_current_user_id() . '"]' ); ?></p>
			<?php endif; ?>
		</div><!-- .entry-header -->
	</article><!-- #post-## -->
</div>
