<?php

class WCMp_Settings_Frontend {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmp_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "sections" => array(
                "default_settings_section" => array("title" => '', // Section one
                    "fields" => array(
                        "sold_by_catalog" => array('title' => __('Enable "Sold by"', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'sold_by_catalogg', 'label_for' => 'sold_by_catalogg', 'text' => stripslashes(__('On shop page.', $WCMp->text_domain)), 'name' => 'sold_by_catalog', 'value' => 'Enable'), // Checkbox
                        "sold_by_cart_and_checkout_email" => array('title' => __('', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'sold_by_cart_and_checkoutt', 'label_for' => 'sold_by_cart_and_checkoutt', 'text' => stripslashes(__('On cart and checkout page.', $WCMp->text_domain)), 'name' => 'sold_by_cart_and_checkout', 'value' => 'Enable'), // Checkbox
                        "enable_vendor_tab" => array('title' => __('Vendor Tab', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'enable_vendor_tab', 'label_for' => 'enable_vendor_tab', 'text' => stripslashes(__('Display vendor details on product page.', $WCMp->text_domain)), 'name' => 'enable_vendor_tab', 'value' => 'Enable'), // Checkbox
                        "sold_by_text" => array('title' => __('"Sold by" label', $WCMp->text_domain), 'type' => 'text', 'id' => 'sold_by_textt', 'label_for' => 'sold_by_textt', 'name' => 'sold_by_text', 'desc' => stripslashes(__('Add the text you want to replace the phrase \"Sold by {vendor name}\".', $WCMp->text_domain))), // Text
                        "sold_by_textt" => array('title' => __('Vendor Slug', $WCMp->text_domain), 'type' => 'text', 'id' => 'sold_by_texttt', 'label_for' => 'sold_by_texttt', 'name' => 'sold_by_textt', 'desc' => stripslashes(sprintf(__('To change the slug (/vendor/) , go to %s\"Settings/Permalinks\"%s . Type in your desired slug in the "\Vendor Shop base\" text box. Eg: yoursite.com/slug/[vendor_name].', $WCMp->text_domain), '<a target="_blank" href="options-permalink.php">', '</a>'))), // Text
                        "show_report_abuse" => array('title' => __('Show "Report abuse" link', $WCMp->text_domain), 'type' => 'select', 'id' => 'show_report_abuse', 'name' => 'show_report_abuse', 'label_for' => 'show_report_abuse', 'desc' => stripslashes(__('A "Report abuse" link will appear in single product page.', $WCMp->text_domain)), 'options' => array('all_products' => __('All Products', $WCMp->text_domain), 'only_vendor_products' => __("Only for Vendor's Products", $WCMp->text_domain), 'disable' => __('Disable', $WCMp->text_domain))), // select
                        "report_abuse_text" => array('title' => __('"Report Abuse" label', $WCMp->text_domain), 'type' => 'text', 'id' => 'report_abuse_text', 'label_for' => 'report_abuse_text', 'name' => 'report_abuse_text'), // Text
                        "show_related_products" => array('title' => __('"Related product" settings', $WCMp->text_domain), 'type' => 'select', 'id' => 'show_related_products', 'name' => 'show_related_products', 'label_for' => 'show_related_products', 'desc' => stripslashes(__('Select related products to show in single product pages.', $WCMp->text_domain)), 'options' => array('all_related' => __('Related Products from Entire Store', $WCMp->text_domain), 'vendors_related' => __("Related Products from Vendor's Store", $WCMp->text_domain), 'disable' => __('Disable', $WCMp->text_domain))), // select
                        "block_vendor_desc" => array('title' => stripslashes(__('Blocked Vendor Notice', $WCMp->text_domain)), 'type' => 'wpeditor', 'id' => 'block_vendor_descc', 'label_for' => 'block_vendor_descc', 'name' => 'block_vendor_desc', 'rows' => 5), // Textarea
//                        "catalog_colorpicker" => array('title' => __('Vendor Name Label Color', $WCMp->text_domain), 'type' => 'colorpicker', 'id' => 'catalog_colorpickerr', 'label_for' => 'catalog_colorpickerr', 'name' => 'catalog_colorpicker', 'default' => '000000'), // Colorpicker
//                        "catalog_hover_colorpicker" => array('title' => __('Vendor Name Label Color(on hover)', $WCMp->text_domain), 'type' => 'colorpicker', 'id' => 'catalog_hover_colorpickerr', 'label_for' => 'catalog_hover_colorpickerr', 'name' => 'catalog_hover_colorpicker', 'default' => '000000',), // Colorpicker
                    )
                ),
            )
        );

        $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_frontend_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();

        $hasError = false;


        if (isset($input['sold_by_cart_and_checkout']))
            $new_input['sold_by_cart_and_checkout'] = sanitize_text_field($input['sold_by_cart_and_checkout']);

        if (isset($input['sold_by_cart_and_checkout']))
            $new_input['sold_by_cart_and_checkout'] = sanitize_text_field($input['sold_by_cart_and_checkout']);

        if (isset($input['sold_by_text']))
            $new_input['sold_by_text'] = sanitize_text_field($input['sold_by_text']);

        if (isset($input['sold_by_catalog']))
            $new_input['sold_by_catalog'] = sanitize_text_field($input['sold_by_catalog']);

        if (isset($input['catalog_colorpicker']))
            $new_input['catalog_colorpicker'] = sanitize_text_field($input['catalog_colorpicker']);

        if (isset($input['catalog_hover_colorpicker']))
            $new_input['catalog_hover_colorpicker'] = sanitize_text_field($input['catalog_hover_colorpicker']);

        if (isset($input['block_vendor_desc']))
            $new_input['block_vendor_desc'] = sanitize_text_field($input['block_vendor_desc']);

        if (isset($input['show_report_abuse']))
            $new_input['show_report_abuse'] = sanitize_text_field($input['show_report_abuse']);

        if (isset($input['report_abuse_text']))
            $new_input['report_abuse_text'] = sanitize_text_field($input['report_abuse_text']);

        if (isset($input['show_related_products']))
            $new_input['show_related_products'] = sanitize_text_field($input['show_related_products']);
        
        if(isset($input['enable_vendor_tab'])){
            $new_input['enable_vendor_tab'] = sanitize_text_field($input['enable_vendor_tab']);
        }


        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_settings_name", esc_attr("wcmp_{$this->tab}_settings_admin_updated"), __('Frontend Settings Updated', $WCMp->text_domain), 'updated'
            );
        }

        return apply_filters("settings_{$this->tab}_tab_new_input", $new_input, $input);
    }

    /**
     * Print the Section text
     */
    public function default_settings_section_info() {
        global $WCMp;
    }

    /**
     * Print the Section text
     */
    public function custom_settings_section_info() {
        global $WCMp;
    }

}
