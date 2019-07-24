<?php 

/* Hooks the metabox. */
add_action('admin_init', 'dmb_rtbs_add_help', 1);
function dmb_rtbs_add_help() {
	add_meta_box( 
		'rtbs_help', 
		'<span class="dashicons dashicons-editor-code"></span> '.__('Shortcode', RTBS_TXTDM ), 
		'dmb_rtbs_help_display', // Below
		'rtbs_tabs', 
		'side', 
		'high'
	);
}


/* Displays the metabox. */
function dmb_rtbs_help_display() { ?>

	<div class="dmb_side_block">
		<p>
			<?php 
				global $post;
				$slug = '';
				$slug = $post->post_name;
				$shortcode = '<span style="display:inline-block;border:solid 2px lightgray; background:white; padding:0 8px; font-size:13px; line-height:25px; vertical-align:middle;">[rtbs name="'.$slug.'"]</span>';
				$shortcode_unpublished = "<span style='display:inline-block;color:#849d3a'>" . /* translators: Leave HTML tags */ __("<strong>Publish</strong> your tab set before you can see your shortcode.", RTBS_TXTDM ) . "</span>";
				echo ($slug != '') ? $shortcode : $shortcode_unpublished;
			?>
		</p>
		<p>
			<?php /* translators: Leave HTML tags */ _e('To display your tab set on your site, copy-paste the <strong>[Shortcode]</strong> above in your post/page.', RTBS_TXTDM ) ?>
		</p>	
	</div>

	<div class="dmb_side_block">
		<div class="dmb_help_title">
			Get support
		</div>
		<a target="_blank" href="https://wpdarko.zendesk.com/hc/en-us/requests/new">Submit a ticket</a><br/>
		<a target="_blank" href="https://wpdarko.zendesk.com/hc/en-us/articles/206303537-Get-started-with-the-Responsive-Tabs-plugin">View documentation</a>
	</div>

<?php } ?>