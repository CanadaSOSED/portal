<?php 

/* Hooks the metabox. */
add_action('admin_init', 'dmb_rtbs_add_pro', 1);
function dmb_rtbs_add_pro() {
	add_meta_box( 
		'rtbs_pro', 
		'<span class="dashicons dashicons-unlock" style="color:#8ea93d;"></span> Get PRO&nbsp;', 
		'dmb_rtbs_pro_display', // Below
		'rtbs_tabs', 
		'side', 
		'high'
	);
}


/* Displays the metabox. */
function dmb_rtbs_pro_display() { ?>

	<div class="dmb_side_block">
		<div class="dmb_side_block_title">
			Tab styling
		</div>
		Add a small arrow below the current tab. Choose between squared and rounded tabs.
	</div>

	<div class="dmb_side_block">
		<div class="dmb_side_block_title">
			Links to specific tabs
		</div>
		Create links to your tab page with a specific tab open.
	</div>

	<div class="dmb_side_block">
		<div class="dmb_side_block_title">
			Icons for your tabs
		</div>
		Add icons to your tabs using the Font-Awesome free library.
	</div>

	<div class="dmb_side_block">
		<div class="dmb_side_block_title">
			Link-only tabs
		</div>
		Create tabs without content. Just links.
	</div>

	<a class="dmb_big_button_primary dmb_see_pro" target="_blank" href="https://wpdarko.com/items/responsive-tabs-pro/">
		Check out PRO features&nbsp;
	</a>

	<span style="display:block;margin-top:15px; font-size:12px; color:#0073AA; line-height:20px;">
		<span class="dashicons dashicons-cart"></span> Discount code 
		<strong>7832922</strong> (10% OFF)
	</span>

<?php } ?>