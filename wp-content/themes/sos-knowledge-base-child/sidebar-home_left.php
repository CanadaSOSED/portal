<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package sos-knowledge-base
 */

if ( ! is_active_sidebar( 'home-col-left' ) ) {
	return;
}
?>

<div class="widget-area" role="complementary">

	<?php dynamic_sidebar( 'home-col-left' ); ?>

</div>
