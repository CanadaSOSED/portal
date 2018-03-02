<?php
/**
 * Refer a Friend Guest Text
 *
 * Available variables: $guest_text (Backend Guest text),
 *
 * @see     http://wpgens.helpscoutdocs.com/article/34-how-to-edit-template-files-and-keep-them-after-plugin-update
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="gens-refer-a-friend">
    <div id="gens-raf-message"><?php _e( $guest_text,'gens-raf'); ?></div>
</div>