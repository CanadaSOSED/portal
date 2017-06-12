<?php

class WCMp_Settings_Capabilities_Product {

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
                "products_capability" => array(
                  "title" => __('Products Capability', $WCMp->text_domain),
                    "fields" => array(
                        "is_submit_product" => array('title' => __('Submit Products', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_submit_product', 'label_for' => 'is_submit_product', 'text' => __('Allow vendors to submit products for approval/publishing.', $WCMp->text_domain), 'name' => 'is_submit_product', 'value' => 'Enable'), // Checkbox
                        "is_published_product" => array('title' => __('Publish Products', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_published_product', 'label_for' => 'is_published_product', 'name' => 'is_published_product', 'text' => __('If checked, products uploaded by vendors will be directly published without admin approval.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                        "is_edit_delete_published_product" => array('title' => __('Edit Publish Products', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_edit_delete_published_product', 'label_for' => 'is_edit_delete_published_product', 'name' => 'is_edit_delete_published_product', 'text' => __('Allow vendors to Edid published products.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                        "is_submit_coupon" => array('title' => __('Submit Coupons', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_submit_coupon', 'label_for' => 'is_submit_coupon', 'name' => 'is_submit_coupon', 'text' => __('Allow vendors to create coupons.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                        "is_published_coupon" => array('title' => __('Publish Coupons', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_published_coupon', 'label_for' => 'is_published_coupon', 'name' => 'is_published_coupon', 'text' => __('If checked, coupons added by vendors will be directly published without admin approval.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                        "is_edit_delete_published_coupon" => array('title' => __('Edit Publish Coupons', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_edit_delete_published_coupon', 'label_for' => 'is_edit_delete_published_coupon', 'name' => 'is_edit_delete_published_coupon', 'text' => __('Allow Vendor To edit delete published shop coupons.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                        "is_upload_files" => array('title' => __('Upload Media Files', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'is_upload_files', 'label_for' => 'is_upload_files', 'name' => 'is_upload_files', 'text' => __('Allow vendors to upload media files.', $WCMp->text_domain), 'value' => 'Enable'), // Checkbox
                    )
                ),
                "default_settings_section_left_pnl" => array("title" => __('Product Side Panel ', $WCMp->text_domain), // Section one
                    "fields" => array(
                        "inventory" => array('title' => __('Inventory', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'inventory', 'label_for' => 'inventory', 'name' => 'inventory', 'value' => 'Enable'), // Checkbox
                        "shipping" => array('title' => __('Shipping', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'shipping', 'label_for' => 'shipping', 'name' => 'shipping', 'value' => 'Enable'), // Checkbox
                        "linked_products" => array('title' => __('Linked Products', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'linked_products', 'label_for' => 'linked_products', 'name' => 'linked_products', 'value' => 'Enable'), // Checkbox
                        "attribute" => array('title' => __('Attributes', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'attribute', 'label_for' => 'attribute', 'name' => 'attribute', 'value' => 'Enable'), // Checkbox
                        "advanced" => array('title' => __('Advanced', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'advanced', 'label_for' => 'advanced', 'name' => 'advanced', 'value' => 'Enable'), // Checkbox
                    )
                ),
                "default_settings_section_types" => array("title" => __('Product Types ', $WCMp->text_domain), // Section one
                    "fields" => apply_filters("wcmp_vendor_product_types", array(
                        "simple" => array('title' => __('Simple', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'simple', 'label_for' => 'simple', 'name' => 'simple', 'value' => 'Enable'), // Checkbox
                        "variable" => array('title' => __('Variable', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'variable', 'label_for' => 'variable', 'name' => 'variable', 'value' => 'Enable'), // Checkbox
                        "grouped" => array('title' => __('Grouped', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'grouped', 'label_for' => 'grouped', 'name' => 'grouped', 'value' => 'Enable'), // Checkbox
                        "external" => array('title' => __('External / Affiliate', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'external', 'label_for' => 'external', 'name' => 'external', 'value' => 'Enable'), // Checkbox
                            )
                    )
                ),
                "default_settings_section_type_option" => array("title" => __('Type Options ', $WCMp->text_domain), // Section one
                    "fields" => array(
                        "virtual" => array('title' => __('Virtual', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'virtual', 'label_for' => 'virtual', 'name' => 'virtual', 'value' => 'Enable'), // Checkbox
                        "downloadable" => array('title' => __('Downloadable', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'downloadable', 'label_for' => 'downloadable', 'name' => 'downloadable', 'value' => 'Enable'), // Checkbox
                    )
                ),
                "default_settings_section_miscellaneous" => array("title" => __('Miscellaneous ', $WCMp->text_domain), // Section one sku
                    "fields" => array(
                        "sku" => array('title' => __('SKU', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'sku', 'label_for' => 'sku', 'name' => 'sku', 'value' => 'Enable'), // Checkbox	
                        "taxes" => array('title' => __('Taxes', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'taxes', 'label_for' => 'taxes', 'name' => 'taxes', 'value' => 'Enable'), // Checkbox
                        "add_comment" => array('title' => __('Add Comment', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'add_comment', 'label_for' => 'add_comment', 'name' => 'add_comment', 'value' => 'Enable'), // Checkbox
                        "comment_box" => array('title' => __('Comment Box', $WCMp->text_domain), 'type' => 'checkbox', 'id' => 'comment_box', 'label_for' => 'comment_box', 'name' => 'comment_box', 'value' => 'Enable'), // Checkbox                                                                                                           
                        "stylesheet" => array('title' => __('Stylesheet', $WCMp->text_domain), 'type' => 'textarea', 'id' => 'stylesheet', 'label_for' => 'stylesheet', 'name' => 'stylesheet', 'cols' => 50, 'rows' => 6, 'text' => __('You can add CSS in the text area that will be loaded on the product page.', $WCMp->text_domain)), // Textarea
                    )
                ),
            )
        );

        $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_capabilities_product_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();

        $hasError = false;

        if (isset($input['is_upload_files'])) {
            $new_input['is_upload_files'] = sanitize_text_field($input['is_upload_files']);
            add_wcmp_users_caps('upload_files');
        } else {
            remove_wcmp_users_caps('is_upload_files');
        }

        if (isset($input['is_published_product'])) {
            $new_input['is_published_product'] = sanitize_text_field($input['is_published_product']);
            add_wcmp_users_caps('publish_products');
        } else {
            remove_wcmp_users_caps('is_published_product');
        }
        
        if(isset($input['is_edit_delete_published_product'])){
            $new_input['is_edit_delete_published_product'] = $input['is_edit_delete_published_product'];
            add_wcmp_users_caps('edit_delete_published_product');
        } else{
            remove_wcmp_users_caps('edit_delete_published_product');
        }

        if (isset($input['is_submit_product'])) {
            $new_input['is_submit_product'] = sanitize_text_field($input['is_submit_product']);
            add_wcmp_users_caps('is_submit_product');
        } else {
            remove_wcmp_users_caps('is_submit_product');
            if (isset($input['is_published_product'])) {
                unset($new_input['is_published_product']);
            }
        }

        if (isset($input['is_published_coupon'])) {
            $new_input['is_published_coupon'] = sanitize_text_field($input['is_published_coupon']);
            add_wcmp_users_caps('publish_shop_coupons');
        } else {
            remove_wcmp_users_caps('is_published_coupon');
        }

        if (isset($input['is_submit_coupon'])) {
            $new_input['is_submit_coupon'] = sanitize_text_field($input['is_submit_coupon']);
            add_wcmp_users_caps('is_submit_coupon');
        } else {
            remove_wcmp_users_caps('is_submit_coupon');
            if (isset($input['is_published_coupon'])) {
                unset($new_input['is_published_coupon']);
            }
        }
        
        if(isset($input['is_edit_delete_published_coupon'])){
            $new_input['is_edit_delete_published_coupon'] = $input['is_edit_delete_published_coupon'];
            add_wcmp_users_caps('edit_delete_published_coupons');
        } else{
            remove_wcmp_users_caps('edit_delete_published_coupons');
        }
        if (isset($input['inventory'])) {
            $new_input['inventory'] = sanitize_text_field($input['inventory']);
        }
        if (isset($input['shipping'])) {
            $new_input['shipping'] = sanitize_text_field($input['shipping']);
        }
        if (isset($input['linked_products'])) {
            $new_input['linked_products'] = sanitize_text_field($input['linked_products']);
        }
        if (isset($input['attribute'])) {
            $new_input['attribute'] = sanitize_text_field($input['attribute']);
        }
        if (isset($input['advanced'])) {
            $new_input['advanced'] = sanitize_text_field($input['advanced']);
        }
        if (isset($input['simple'])) {
            $new_input['simple'] = sanitize_text_field($input['simple']);
        }
        if (isset($input['variable'])) {
            $new_input['variable'] = sanitize_text_field($input['variable']);
        }
        if (isset($input['grouped'])) {
            $new_input['grouped'] = sanitize_text_field($input['grouped']);
        }
        if (isset($input['external'])) {
            $new_input['external'] = sanitize_text_field($input['external']);
        }
        if (isset($input['virtual'])) {
            $new_input['virtual'] = sanitize_text_field($input['virtual']);
        }
        if (isset($input['downloadable'])) {
            $new_input['downloadable'] = sanitize_text_field($input['downloadable']);
        }
        if (isset($input['sku'])) {
            $new_input['sku'] = sanitize_text_field($input['sku']);
        }
        if (isset($input['taxes'])) {
            $new_input['taxes'] = sanitize_text_field($input['taxes']);
        }
        if (isset($input['add_comment'])) {
            $new_input['add_comment'] = sanitize_text_field($input['add_comment']);
        }
        if (isset($input['comment_box'])) {
            $new_input['comment_box'] = sanitize_text_field($input['comment_box']);
        }
        if (isset($input['stylesheet'])) {
            $new_input['stylesheet'] = sanitize_text_field($input['stylesheet']);
        }


        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Vendor Settings Updated', $WCMp->text_domain), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }
    
    public function products_capability_info(){
        
    }

    /**
     * Print the Section text
     */
    public function default_settings_section_left_pnl_info() {
        global $WCMp;
    }

    /**
     * Print the Section text
     */
    public function default_settings_section_types_info() {
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
