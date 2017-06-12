<?php
/**
 * Footer widgets
 *
 * @package OceanWP WordPress theme
 */

namespace Elementor;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If is not PHP version 5.2+
if ( ! version_compare( PHP_VERSION, '5.2', '>=' ) ) {
    return;
}

// Get page
$get_page 	= oceanwp_footer_page_id();

// Get page ID
$get_id 	= get_theme_mod( 'ocean_footer_widgets_page_id' );

// Check if page is Elementor page
$elementor 	= get_post_meta( $get_id, '_elementor_edit_mode', true );

// Get footer widgets columns
$columns    = apply_filters( 'ocean_footer_widgets_columns', get_theme_mod( 'ocean_footer_widgets_columns', '4' ) );
$grid_class = oceanwp_grid_class( $columns );

// Responsive columns
$tablet_columns    = get_theme_mod( 'ocean_footer_widgets_tablet_columns' );
$mobile_columns    = get_theme_mod( 'ocean_footer_widgets_mobile_columns' );

// Visibility
$visibility = get_theme_mod( 'ocean_footer_widgets_visibility', 'all-devices' );

// Classes
$wrap_classes = array( 'clr' );
if ( ! empty( $tablet_columns ) ) {
	$wrap_classes[] = 'tablet-' . $tablet_columns . '-col';
}
if ( ! empty( $mobile_columns ) ) {
	$wrap_classes[] = 'mobile-' . $mobile_columns . '-col';
}
if ( 'all-devices' != $visibility ) {
	$wrap_classes[] = $visibility;
}
$wrap_classes = implode( ' ', $wrap_classes ); ?>

<?php do_action( 'ocean_before_footer_widgets' ); ?>

<div id="footer-widgets" class="oceanwp-row <?php echo esc_attr( $wrap_classes ); ?>">

	<?php do_action( 'ocean_before_footer_widgets_inner' ); ?>

	<div class="container">

        <?php
        // Check if there is page for the footer
        if ( $get_page ) :

		    // If Elementor
		    if ( class_exists( 'Elementor\Plugin' ) && $elementor ) {

				echo Plugin::instance()->frontend->get_builder_content_for_display( $get_id );

	    	}

	    	// If Beaver Builder
		    else if ( class_exists( 'FLBuilder' ) ) {

				echo do_shortcode( '[fl_builder_insert_layout id="' . $get_id . '"]' );

	    	}

	    	// Else
	    	else {

	        	// Display page content
	        	echo do_shortcode( $get_page );

	        }

		// Display widgets
		else :

			// Footer box 1 ?>
			<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-1">
				<?php dynamic_sidebar( 'footer-one' ); ?>
			</div><!-- .footer-one-box -->

			<?php
			// Footer box 2
			if ( $columns > '1' ) : ?>
				<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-2">
					<?php dynamic_sidebar( 'footer-two' ); ?>
				</div><!-- .footer-one-box -->
			<?php endif; ?>
			
			<?php
			// Footer box 3
			if ( $columns > '2' ) : ?>
				<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-3 ">
					<?php dynamic_sidebar( 'footer-three' ); ?>
				</div><!-- .footer-one-box -->
			<?php endif; ?>

			<?php
			// Footer box 4
			if ( $columns > '3' ) : ?>
				<div class="footer-box <?php echo esc_attr( $grid_class ); ?> col col-4">
					<?php dynamic_sidebar( 'footer-four' ); ?>
				</div><!-- .footer-box -->
			<?php endif; ?>

		<?php endif; ?>

	</div><!-- .container -->

	<?php do_action( 'ocean_after_footer_widgets_inner' ); ?>

</div><!-- #footer-widgets -->

<?php do_action( 'ocean_after_footer_widgets' ); ?>