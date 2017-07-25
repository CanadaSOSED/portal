<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @class 		WCMp_Capabilities
 * @version		1.0.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Capabilities {

    public $capability;
    public $general_cap;
    public $vendor_cap;
    public $frontend_cap;
    public $payment_cap;
    public $wcmp_capability = array();

    public function __construct() {
        $this->wcmp_capability = array_merge(
                $this->wcmp_capability
                , (array) get_option('wcmp_general_settings_name', array())
                , (array) get_option('wcmp_capabilities_product_settings_name', array())
                , (array) get_option('wcmp_capabilities_order_settings_name', array())
                , (array) get_option('wcmp_capabilities_miscellaneous_settings_name', array())
        );

        //$this->capability = get_option("wcmp_product_settings_name");
        //$this->general_cap = get_option("wcmp_general_settings_name");
        //$this->vendor_cap = get_option("wcmp_capabilities_settings_name");
        $this->frontend_cap = get_option("wcmp_frontend_settings_name");
        $this->payment_cap = get_option("wcmp_payment_settings_name");

        add_filter('product_type_selector', array(&$this, 'wcmp_product_type_selector'), 10, 1);
        add_filter('product_type_options', array(&$this, 'wcmp_product_type_options'), 10);
        add_filter('wc_product_sku_enabled', array(&$this, 'wcmp_wc_product_sku_enabled'), 30);
        add_filter('woocommerce_product_data_tabs', array(&$this, 'wcmp_woocommerce_product_data_tabs'), 30);
        add_action('admin_print_styles', array(&$this, 'output_capability_css'));
        add_action('woocommerce_get_item_data', array(&$this, 'add_sold_by_text_cart'), 30, 2);
        //add_action('woocommerce_order_status_processing', array(&$this, 'payment_complete_vendor_mail'), 10, 1);
        add_action('woocommerce_add_order_item_meta', array(&$this, 'order_item_meta_2'), 20, 2);
        add_action('woocommerce_after_shop_loop_item_title', array($this, 'wcmp_after_add_to_cart_form'), 30);
        /* for single product */
        add_action('woocommerce_product_meta_start', array($this, 'wcmp_after_add_to_cart_form'), 25);
        //add_action('woocommerce_order_status_changed', array($this, 'wcmp_order_hold_to_completed'), 10, 3);
    }

    /**
     * Vendor Capability from Product Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_can($cap) {
        if (is_array($this->wcmp_capability) && array_key_exists($cap, $this->wcmp_capability)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vendor Capability from General Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_general_settings($cap) {
        if (is_array($this->wcmp_capability) && array_key_exists($cap, $this->wcmp_capability)) {
            return true;
        } else
            return false;
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_capabilities_settings($cap) {
        if (is_array($this->wcmp_capability) && array_key_exists($cap, $this->wcmp_capability)) {
            return true;
        } else
            return false;
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_frontend_settings($cap) {
        if (is_array($this->frontend_cap) && array_key_exists($cap, $this->frontend_cap)) {
            return true;
        } else
            return false;
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_payment_settings($cap) {
        if (is_array($this->payment_cap) && array_key_exists($cap, $this->payment_cap)) {
            return true;
        } else
            return false;
    }

    /**
     * Get Vendor Product Types
     *
     * @param product_types
     * @return product_types 
     */
    public function wcmp_product_type_selector($product_types) {
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user) && $product_types) {
            foreach ($product_types as $product_type => $value) {
                $vendor_can = $this->vendor_can($product_type);
                if (!$vendor_can) {
                    unset($product_types[$product_type]);
                }
            }
        }
        return $product_types;
    }

    /**
     * Get Vendor Product Types Options
     *
     * @param product_type_options
     * @return product_type_options 
     */
    public function wcmp_product_type_options($product_type_options) {
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user) && $product_type_options) {
            foreach ($product_type_options as $product_type_option => $value) {
                $vendor_can = $this->vendor_can($product_type_option);
                if (!$vendor_can) {
                    unset($product_type_options[$product_type_option]);
                }
            }
        }
        return $product_type_options;
    }

    /**
     * Check if Vendor Product SKU Enable
     *
     * @param state
     * @return boolean 
     */
    public function wcmp_wc_product_sku_enabled($state) {
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user)) {
            $vendor_can = $this->vendor_can('sku');
            if ($vendor_can) {
                return true;
            } else
                return false;
        }
        return true;
    }

    /**
     * Set woocommerce product tab according settings
     *
     * @param panels
     * @return panels 
     */
    public function wcmp_woocommerce_product_data_tabs($panels) {
        $settings_product = get_option('wcmp_product_settings_name');
        $user = wp_get_current_user();
        if (is_user_wcmp_vendor($user)) {
            $vendor_can = $this->vendor_can('inventory');
            if (!$vendor_can) {
                unset($panels['inventory']);
            }
            $vendor_can = $this->vendor_can('shipping');
            if (!$vendor_can) {
                unset($panels['shipping']);
            }
            if (!$this->vendor_can('linked_products')) {
                unset($panels['linked_product']);
            }
            $vendor_can = $this->vendor_can('attribute');
            if (!$vendor_can) {
                unset($panels['attribute']);
            }
            $vendor_can = $this->vendor_can('advanced');
            if (!$vendor_can) {
                unset($panels['advanced']);
            }
        }
        return $panels;
    }

    /**
     * Set output capability css
     */
    function output_capability_css() {
        global $post;
        $screen = get_current_screen();

        $custom_css = '';
        if (isset($screen->id) && in_array($screen->id, array('product'))) {
            if (is_user_wcmp_vendor(get_current_user_id())) {
                if (!$this->vendor_can('taxes')) {
                    $custom_css .= '
					._tax_status_field, ._tax_class_field {
						display: none !important;
					}
					';
                }
                if (!$this->vendor_can('add_comment')) {
                    $custom_css .= '
					.comments-box {
						display: none !important;
					}
					';
                }
                if (!$this->vendor_can('comment_box')) {
                    $custom_css .= '
					#add-new-comment {
						display: none !important;
					}
					';
                }
                if ($this->vendor_can('stylesheet')) {
                    $custom_css .= $this->capability['stylesheet'];
                }

                $vendor_id = get_current_user_id();
                $vendor = get_wcmp_vendor($vendor_id);
                if ($vendor && $post->post_author != $vendor_id) {
                    $custom_css .= '.options_group.pricing.show_if_simple.show_if_external {
														display: none !important;
													}';
                }
                wp_add_inline_style('woocommerce_admin_styles', $custom_css);
            }
        }
    }

    /**
     * Add Sold by Vendor text
     *
     * @param array, cart_item
     * @return array 
     */
    function add_sold_by_text_cart($array, $cart_item) {
        global $WCMp;

        if ($this->vendor_frontend_settings('sold_by_cart_and_checkout')) {
            $general_cap = isset($this->frontend_cap['sold_by_text']) ? $this->frontend_cap['sold_by_text'] : '';
            if (!$general_cap)
                $general_cap = __('Sold By', 'dc-woocommerce-multi-vendor');
            $vendor = get_wcmp_product_vendors($cart_item['product_id']);
            if ($vendor) {
                $array = array_merge($array, array(array('name' => $general_cap, 'value' => $vendor->user_data->display_name)));
                do_action('after_sold_by_text_cart_page', $vendor);
            }
        }
        return $array;
    }

    /**
     * Add Sold by Vendor text
     *
     * @return void 
     */
    function wcmp_after_add_to_cart_form() {
        global $post, $WCMp;
        if ($this->vendor_frontend_settings('sold_by_catalog')) {
            $vendor = get_wcmp_product_vendors($post->ID);
            $general_cap = isset($this->frontend_cap['sold_by_text']) ? $this->frontend_cap['sold_by_text'] : '';
            if (!$general_cap)
                $general_cap = __('Sold By', 'dc-woocommerce-multi-vendor');
            if ($vendor) {
                echo '<a class="by-vendor-name-link" style="display: block;" href="' . $vendor->permalink . '">' . $general_cap . ' ' . $vendor->user_data->display_name . '</a>';
                do_action('after_sold_by_text_shop_page', $vendor);
            }
        }
    }

    /**
     * Send if order completed directly from hold Mail
     *
     * @param order_id
     * @return void 
     */
    public function wcmp_order_hold_to_completed($order_id, $old_status, $new_status) {
        global $post, $WCMp;
        if (!empty($old_status) && $old_status == 'on-hold' && $new_status == 'completed' && !empty($new_status)) {
            $this->payment_complete_vendor_mail($order_id);
        }
    }

    /**
     * Send Order Processing Mail
     *
     * @param order_id
     * @return void 
     */
    public function payment_complete_vendor_mail($order_id) {
        $email_admin = WC()->mailer()->emails['WC_Email_Vendor_New_Order'];
        $email_admin->trigger($order_id);
    }

    /**
     * Save sold by text in database
     *
     * @param item_id, cart_item
     * @return void 
     */
    function order_item_meta_2($item_id, $cart_item) {
        global $WCMp;
        if ($WCMp->vendor_caps->vendor_frontend_settings('sold_by_cart_and_checkout')) {
            $general_cap = isset($this->frontend_cap['sold_by_text']) ? $this->frontend_cap['sold_by_text'] : '';
            if (!$general_cap) {
                $general_cap = 'Sold By';
            }
            $vendor = get_wcmp_product_vendors($cart_item['product_id']);
            if ($vendor) {
                wc_add_order_item_meta($item_id, $general_cap, $vendor->user_data->display_name);
                wc_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
            }
        }
    }

}

?>