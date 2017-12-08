<?php
/**
 * The sidebar containing the home left col widget area.
 *
 * @package sos-chapter
 */

if ( ! is_active_sidebar( 'home-col-middle' ) ) {
	return;
}
?>

<div class="widget-area" role="complementary">

	<?php dynamic_sidebar( 'home-col-middle' ); ?>

</div>
