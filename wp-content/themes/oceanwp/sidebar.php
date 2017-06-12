<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package OceanWP WordPress theme
 */

if ( in_array( oceanwp_post_layout(), array( 'full-screen', 'full-width' ) ) ) {
	return;
} ?>

<?php do_action( 'ocean_before_sidebar' ); ?>

<aside id="sidebar" class="sidebar-container widget-area sidebar-primary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">

	<?php do_action( 'ocean_before_sidebar_inner' ); ?>

	<div id="sidebar-inner" class="clr">

		<?php
		if ( $sidebar = oceanwp_get_sidebar() ) {
			dynamic_sidebar( $sidebar );
		} ?>

	</div><!-- #sidebar-inner -->

	<?php do_action( 'ocean_after_sidebar_inner' ); ?>

</aside><!-- #sidebar -->

<?php do_action( 'ocean_after_sidebar' ); ?>