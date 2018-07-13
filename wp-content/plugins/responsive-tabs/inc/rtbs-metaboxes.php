<?php
/**
 * Register metaboxes for Tab sets.
 */
function rtbs_register_group_metabox( ) {

    /* Custom sanitization call-back to allow HTML in most fields */
    function rtbs_html_allowed_sani_cb($content) {
        return wp_kses_post( $content );
    }

    /* Custom sanitization call-back for custom button field */
    function rtbs_custom_content_sani_cb($content) {
        return balanceTags( $content, true );
    }

    $prefix = '_rtbs_';
    $main_group = new_cmb2_box( array(
        'id' => $prefix . 'tab_metabox',
        'title' => '<span style="font-weight:400;">'.__( 'Manage Tabs', 'responsive-tabs' ).'</span> <a target="_blank" class="wpd_free_pro" title="'.__( 'Unlock more features with Responsive Tabs PRO!', 'responsive-tabs' ).'" href="http://wpdarko.com/items/responsive-tabs-pro"><span style="color:#8a7463;font-size:15px; font-weight:400; float:right; padding-right:14px;"><span class="dashicons dashicons-lock"></span> '.__( 'Free version', 'responsive-tabs' ).'</span></a>',
        'object_types' => array( 'rtbs_tabs' ),
        'priority' => 'high',
    ));

        $rtbs_tab_group = $main_group->add_field( array(
            'id' => $prefix . 'tabs_head',
            'type' => 'group',
            'options' => array(
                'group_title' => __( 'Tab {#}', 'responsive-tabs' ),
                'add_button' => __( 'Add another tab', 'responsive-tabs' ),
                'remove_button' => __( 'Remove tab', 'responsive-tabs' ),
                'sortable' => true,
                'single' => false,
            ),
        ));

            $main_group->add_group_field( $rtbs_tab_group, array(
                'name' => __( 'Tab content', 'responsive-tabs' ),
                'id' => $prefix . 'tab_header',
                'type' => 'title',
                'row_classes' => 'de_hundred de_heading',
            ));

            $main_group->add_group_field( $rtbs_tab_group, array(
                'name' => __( 'Title (label)', 'responsive-tabs' ),
                'id' => $prefix . 'title',
                'type' => 'text',
                'row_classes' => 'de_first de_hundred de_text de_input',
                'sanitization_cb' => 'rtbs_html_allowed_sani_cb',
            ));

            $main_group->add_group_field( $rtbs_tab_group, array(
                'name' => __( 'Inner section', 'responsive-tabs' ).' <a class="wpd_tooltip" title="'.__( 'Basic HTML allowed — Shortcodes allowed but not recommended', 'responsive-tabs' ).'"><span class="wpd_help_icon dashicons dashicons-editor-help"></span></a>',
        				'id' => $prefix . 'content',
        				'type' => 'wysiwyg',
                'options' => array(
                    'wpautop' => true,
                    'media_buttons' => true,
                    'textarea_rows' => get_option('default_post_edit_rows', 10),
                    'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                    'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
                ),
                'row_classes' => 'de_first de_hundred de_tinymce de_input',
                'sanitization_cb' => 'rtbs_custom_content_sani_cb',
            ));


    // Settings
    $side_group = new_cmb2_box( array(
        'id' => $prefix . 'settings_head',
        'title' => '<span style="font-weight:400;">'.__( 'Settings', 'responsive-tabs' ).'</span>',
        'object_types' => array( 'rtbs_tabs' ),
        'context' => 'side',
        'priority' => 'high',
    ));

        $side_group->add_field( array(
            'name' => __( 'General settings', 'responsive-tabs' ),
            'id'   => $prefix . 'general_settings_desc',
            'type' => 'title',
            'row_classes' => 'de_hundred de_heading_side',
        ));

        $side_group->add_field( array(
            'name' => __( 'Main color', 'responsive-tabs' ),
            'id' => $prefix . 'tabs_bg_color',
            'type' => 'colorpicker',
            'row_classes' => 'de_first de_hundred de_color de_input',
        ));

        $side_group->add_field( array(
            'name'    => __( 'Accordion breakpoint', 'responsive-tabs' ).' <a class="wpd_tooltip" title="'.__( 'Width at which the tabs turn into an accordion — In pixels, without the \'px\'', 'responsive-tabs' ).'"><span class="wpd_help_icon dashicons dashicons-editor-help"></span></a>',
			'id'      => $prefix . 'breakpoint',
			'type'    => 'text',
			'default' => '600',
            'row_classes' => 'de_hundred de_text_side',
        ));

        $side_group->add_field( array(
            'name' => __( 'Others', 'responsive-tabs' ),
            'id'   => $prefix . 'other_settings_desc',
            'type' => 'title',
            'row_classes' => 'de_hundred de_heading_side',
        ));

        $side_group->add_field( array(
            'name' => __( 'Force original fonts', 'responsive-tabs' ).' <a class="wpd_tooltip" title="'.__( 'Check this to use the plugin\'s font instead of your theme\'s', 'responsive-tabs' ).'"><span class="wpd_help_icon dashicons dashicons-editor-help"></span></a>',
            'desc' => __( 'Check to enable', 'responsive-tabs' ),
		    'id'   => $prefix . 'original_font',
		    'type' => 'checkbox',
            'row_classes' => 'de_hundred de_checkbox_side',
            'default' => false,
        ));


    // PRO version
    $pro_group = new_cmb2_box( array(
        'id' => $prefix . 'pro_metabox',
        'title' => '<span style="font-weight:400;">Upgrade to <strong>PRO version</strong></span>',
        'object_types' => array( 'rtbs_tabs' ),
        'context' => 'side',
        'priority' => 'low',
        'row_classes' => 'de_hundred de_heading',
    ));

        $pro_group->add_field( array(
            'name' => '',
                'desc' => '<div><span class="dashicons dashicons-yes"></span> Arrow on current tab<br/><span class="dashicons dashicons-yes"></span> Create links to specific tabs<br/><span class="dashicons dashicons-yes"></span> Icons for your tabs<br/><span class="dashicons dashicons-yes"></span> Link-only tabs<br/><span class="dashicons dashicons-yes"></span> Rounded borders<br/><span class="dashicons dashicons-arrow-right"></span> And more...<br/><br/><a style="display:inline-block; background:#33b690; padding:8px 25px 8px; border-bottom:3px solid #33a583; border-radius:3px; color:white;" class="wpd_pro_btn" target="_blank" href="http://wpdarko.com/items/responsive-tabs-pro">See all PRO features</a><br/><span style="display:block;margin-top:14px; font-size:13px; color:#0073AA; line-height:20px;"><span class="dashicons dashicons-tickets"></span> Code <strong>7832922</strong> (10% OFF)</span></div>',
                'id'   => $prefix . 'pro_desc',
                'type' => 'title',
                'row_classes' => 'de_hundred de_info de_info_side',
        ));


    // Help
    $help_group = new_cmb2_box( array(
        'id' => $prefix . 'help_metabox',
        'title' => '<span style="font-weight:400;">'.__( 'Help & Support', 'responsive-tabs' ).'</span>',
        'object_types' => array( 'rtbs_tabs' ),
        'context' => 'side',
        'priority' => 'low',
        'row_classes' => 'de_hundred de_heading',
    ));

        $help_group->add_field( array(
            'name' => '',
                'desc' => '<span style="font-size:15px;">'.__( 'Display your Tabs', 'responsive-tabs' ).'</span><br/><br/>'.__( 'To display your Tabs on your site, copy-paste the Tab set\'s <strong>[Shortcode]</strong> in your post/page. You can find this shortcode by clicking <strong>All Tab sets</strong> in the menu on the left.', 'responsive-tabs' ).'<br/><br/><span style="font-size:15px;">'.__( 'Get support', 'responsive-tabs' ).'</span><br/><br/><a style="font-size:13px !important;" target="_blank" href="http://wpdarko.com/support/">— '.__( 'Submit a ticket', 'responsive-tabs' ).'</a><br/><a style="font-size:13px !important;" target="_blank" href="https://wpdarko.zendesk.com/hc/en-us/articles/206303537-Get-started-with-the-Responsive-Tabs-plugin">— '.__( 'View documentation', 'responsive-tabs' ).'</a>',
                'id'   => $prefix . 'help_desc',
                'type' => 'title',
                'row_classes' => 'de_hundred de_info de_info_side',
        ));



}

?>
