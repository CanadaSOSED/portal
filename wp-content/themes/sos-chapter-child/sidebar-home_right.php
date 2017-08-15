<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package sos-chapter
 */

if ( ! is_active_sidebar( 'home-col-right' ) ) {
	return;
}
?>

<div class="widget-area" role="complementary">

	<?php dynamic_sidebar( 'home-col-right' ); ?>

</div>
