<?php

class WCMp_Settings_Capabilities_Miscellaneous {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "vendor_messages" => array("title" => __('', $WCMp->text_domain), // Section one
                    "fields" => array(
                        "can_vendor_add_message_on_email_and_thankyou_page" => array('title' => __('Message to buyer', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'can_vendor_add_message_on_email_and_thankyou_page', 'label_for' => 'can_vendor_add_message_on_email_and_thankyou_page', 'name' => 'can_vendor_add_message_on_email_and_thankyou_page', 'value' => 'Enable', 'text' => __('Allow vendors to add vendor shop specific message in "Thank you" page and order mail.', $WCMp->text_domain)), // Checkbox
                        "is_vendor_add_external_url" => array('title' => __('Enable store url', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_vendor_add_external_url', 'label_for' => 'is_vendor_add_external_url', 'name' => 'is_vendor_add_external_url', 'text' => __('Vendor can add external store url.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                        "is_hide_option_show" => array('title' => __('Enable hide option for vendor', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_hide_option_show', 'label_for' => 'is_hide_option_show', 'name' => 'is_hide_option_show', 'text' => __('Vendor can hide some details from shop.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                    )
                )
            )
        );

        $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_capabilities_miscellaneous_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();

        $hasError = false;

        if (isset($input['can_vendor_add_message_on_email_and_thankyou_page'])) {
            $new_input['can_vendor_add_message_on_email_and_thankyou_page'] = sanitize_text_field($input['can_vendor_add_message_on_email_and_thankyou_page']);
        }

        if (isset($input['is_vendor_add_external_url'])) {
            $new_input['is_vendor_add_external_url'] = sanitize_text_field($input['is_vendor_add_external_url']);
        }

        if (isset($input['is_hide_option_show'])) {
            $new_input['is_hide_option_show'] = sanitize_text_field($input['is_hide_option_show']);
        }


        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Vendor Settings Updated', $WCMp->text_domain), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

    /**
     * Print the Section text
     */
    public function vendor_messages_info() {
        global $WCMp;
    }

    /**
     * Print the Section text
     */
    public function vendor_customer_support_info() {
        global $WCMp;
    }

    /**
     * Print the Section text
     */
    public function default_settings_section_type_option_info() {
        global $WCMp;
    }

    /**
     * Print the Section text
     */
    public function default_settings_section_miscellaneous_info() {
        global $WCMp;
    }

    public function default_settings_section_policies_info() {
        global $WCMp;
    }

    public function default_settings_section_policiessettings_info() {
        global $WCMp;
    }

}
