<?php

/**
 * WCMp Frontend Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Frontend {

    public $wcmp_shipping_fee_cost = 0;
    public $pagination_sale = array();
    public $give_tax_to_vendor = false;
    public $give_shipping_to_vendor = false;

    public function __construct() {
        //enqueue scripts
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
        //enqueue styles
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));
        add_action('woocommerce_archive_description', array(&$this, 'product_archive_vendor_info'), 10);
        add_filter('body_class', array(&$this, 'set_product_archive_class'));
        add_action('template_redirect', array(&$this, 'template_redirect'));

        add_action('woocommerce_order_details_after_order_table', array($this, 'display_vendor_msg_in_thank_you_page'), 100);
        $this->give_tax_to_vendor = get_wcmp_vendor_settings('give_tax', 'payment');
        $this->give_shipping_to_vendor = get_wcmp_vendor_settings('give_shipping', 'payment');
        add_action('wcmp_vendor_register_form', array(&$this, 'wcmp_vendor_register_form_callback'));
        add_action('woocommerce_register_post', array(&$this, 'wcmp_validate_extra_register_fields'), 10, 3);
        add_action('woocommerce_created_customer', array(&$this, 'wcmp_save_extra_register_fields'), 10, 3);
        // split woocommerce shipping packages
        add_filter('woocommerce_cart_shipping_packages', array(&$this, 'wcmp_split_shipping_packages'), 10);
        // Rename woocommerce shipping packages
        add_filter('woocommerce_shipping_package_name', array(&$this, 'woocommerce_shipping_package_name'), 10, 3);
        // Add extra vendor_id to shipping packages
        add_action('woocommerce_checkout_create_order_shipping_item', array(&$this, 'add_meta_date_in_shipping_package'), 10, 4);
        // processed woocomerce checkout order data
        add_action('woocommerce_checkout_order_processed', array(&$this, 'wcmp_checkout_order_processed'), 30, 3);
    }

    /**
     * Save the extra register fields.
     *
     * @param  int  $customer_id Current customer ID.
     *
     * @return void
     */
    function wcmp_save_extra_register_fields($customer_id) {
        global $WCMp;
        if (isset($_POST['wcmp_vendor_fields']) && isset($_POST['pending_vendor'])) {

            if (isset($_FILES['wcmp_vendor_fields'])) {
                $attacment_files = $_FILES['wcmp_vendor_fields'];
                $files = array();
                $count = 0;
                if (!empty($attacment_files) && is_array($attacment_files)) {
                    foreach ($attacment_files['name'] as $key => $attacment) {
                        foreach ($attacment as $key_attacment => $value_attacment) {
                            $files[$count]['name'] = $value_attacment;
                            $files[$count]['type'] = $attacment_files['type'][$key][$key_attacment];
                            $files[$count]['tmp_name'] = $attacment_files['tmp_name'][$key][$key_attacment];
                            $files[$count]['error'] = $attacment_files['error'][$key][$key_attacment];
                            $files[$count]['size'] = $attacment_files['size'][$key][$key_attacment];
                            $files[$count]['field_key'] = $key;
                            $count++;
                        }
                    }
                }
                $upload_dir = wp_upload_dir();
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                if (!function_exists('wp_handle_upload')) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                foreach ($files as $file) {
                    $uploadedfile = $file;
                    $upload_overrides = array('test_form' => false);
                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                    if ($movefile && !isset($movefile['error'])) {
                        $filename = $movefile['file'];
                        $filetype = wp_check_filetype($filename, null);
                        $attachment = array(
                            'post_mime_type' => $filetype['type'],
                            'post_title' => $file['name'],
                            'post_content' => '',
                            'post_status' => 'inherit',
                            'guid' => $movefile['url']
                        );
                        $attach_id = wp_insert_attachment($attachment, $movefile['file']);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        $_POST['wcmp_vendor_fields'][$file['field_key']]['value'][] = $attach_id;
                    }
                }
            }
            $wcmp_vendor_fields = $_POST['wcmp_vendor_fields'];
            $user_data = get_userdata($customer_id);
            $user_name = $user_data->user_login;
            $user_email = $user_data->user_email;


            // Create post object
            $my_post = array(
                'post_title' => $user_name,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'wcmp_vendorrequest'
            );

            // Insert the post into the database
            $register_vendor_post_id = wp_insert_post($my_post);
            update_post_meta($register_vendor_post_id, 'user_id', $customer_id);
            update_post_meta($register_vendor_post_id, 'username', $user_name);
            update_post_meta($register_vendor_post_id, 'email', $user_email);
            update_post_meta($register_vendor_post_id, 'wcmp_vendor_fields', $wcmp_vendor_fields);
            update_user_meta($customer_id, 'wcmp_vendor_registration_form_id', $register_vendor_post_id);
            $WCMp->user->wcmp_woocommerce_created_customer_notification();
        }
    }

    /**
     * Validate the extra register fields.
     *
     * @param  string $username          Current username.
     * @param  string $email             Current email.
     * @param  object $validation_errors WP_Error object.
     *
     * @return void
     */
    function wcmp_validate_extra_register_fields($username, $email, $validation_errors) {
        $wcmp_vendor_registration_form_data = get_option('wcmp_vendor_registration_form_data');
        if (isset($_POST['g-recaptcha-response']) && empty($_POST['g-recaptcha-response'])) {
            $validation_errors->add('recaptcha is not validate', __('Please Verify  Recaptcha', 'woocommerce'));
        }
        if (isset($_FILES['wcmp_vendor_fields'])) {
            $attacment_files = $_FILES['wcmp_vendor_fields'];
            if (!empty($attacment_files) && is_array($attacment_files)) {
                foreach ($attacment_files['name'] as $key => $value) {
                    $file_type = array();
                    foreach ($wcmp_vendor_registration_form_data[$key]['fileType'] as $key1 => $value1) {
                        if ($value1['selected']) {
                            array_push($file_type, $value1['value']);
                        }
                    }
                    foreach ($attacment_files['type'][$key] as $file_key => $file_value) {
                        if (!empty($attacment_files['name'][$key][$file_key])) {
                            if (!in_array($file_value, $file_type)) {
                                $validation_errors->add('file type error', __('Please Upload valid file', 'woocommerce'));
                            }
                        }
                    }
                    foreach ($attacment_files['size'][$key] as $file_size_key => $file_size_value) {
                        if (!empty($wcmp_vendor_registration_form_data[$key]['fileSize'])) {
                            if ($file_size_value > $wcmp_vendor_registration_form_data[$key]['fileSize']) {
                                $validation_errors->add('file size error', __('File upload limit exceeded', 'woocommerce'));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Populate vendor registration form
     * @global object $WCMp
     */
    function wcmp_vendor_register_form_callback() {
        global $WCMp;
        $wcmp_vendor_registration_form_data = get_option('wcmp_vendor_registration_form_data');
        $WCMp->template->get_template('vendor_registration_form.php', array('wcmp_vendor_registration_form_data' => $wcmp_vendor_registration_form_data));
    }

    /**
     * Display custom message in woocommerce thank you page
     * @global object $wpdb
     * @global object $WCMp
     * @param int $order_id
     */
    public function display_vendor_msg_in_thank_you_page($order_id) {
        global $wpdb, $WCMp;
        $order = wc_get_order($order_id);
        $items = $order->get_items('line_item');
        $vendor_array = array();
        $author_id = '';
        $customer_support_details_settings = get_option('wcmp_general_customer_support_details_settings_name');
        $is_csd_by_admin = '';
        foreach ($items as $item_id => $item) {
            $product_id = wc_get_order_item_meta($item_id, '_product_id', true);
            if ($product_id) {
                $author_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
                if (empty($author_id)) {
                    $product_vendors = get_wcmp_product_vendors($product_id);
                    if (isset($product_vendors) && (!empty($product_vendors))) {
                        $author_id = $product_vendors->id;
                    } else {
                        $author_id = get_post_field('post_author', $product_id);
                    }
                }
                if (isset($vendor_array[$author_id])) {
                    $vendor_array[$author_id] = $vendor_array[$author_id] . ',' . $item['name'];
                } else {
                    $vendor_array[$author_id] = $item['name'];
                }
            }
        }
        if (!empty($vendor_array)) {
            echo '<div style="clear:both">';
            $can_vendor_add_message_on_email_and_thankyou_page = apply_filters('can_vendor_add_message_on_email_and_thankyou_page', true);
            $is_customer_support_details = apply_filters('is_customer_support_details', true);
            if (isset($customer_support_details_settings['can_vendor_add_message_on_email_and_thankyou_page']) && $can_vendor_add_message_on_email_and_thankyou_page) {
                $WCMp->template->get_template('vendor_message_to_buyer.php', array('vendor_array' => $vendor_array, 'capability_settings' => $customer_support_details_settings, 'customer_support_details_settings' => $customer_support_details_settings));
            } elseif (get_wcmp_vendor_settings('is_customer_support_details', 'general') == 'Enable') {
                $WCMp->template->get_template('customer_support_details_to_buyer.php', array('vendor_array' => $vendor_array, 'capability_settings' => $customer_support_details_settings, 'customer_support_details_settings' => $customer_support_details_settings));
            }
            echo "</div>";
        }
    }

    /**
     * split woocommerce shipping packages 
     * @since 2.6.6
     * @param array $packages
     * @return array
     */
    public function wcmp_split_shipping_packages($packages) {
        // Reset all packages
        $packages = array();
        $split_packages = array();
        foreach (WC()->cart->get_cart() as $item) {
            if ($item['data']->needs_shipping()) {
                $product_id = $item['product_id'];
                $vendor = get_wcmp_product_vendors($product_id);
                if ($vendor) {
                    $split_packages[$vendor->id][] = $item;
                } else {
                    $split_packages[0][] = $item;
                }
            }
        }

        foreach ($split_packages as $vendor_id => $split_package) {
            $packages[$vendor_id] = array(
                'contents' => $split_package,
                'contents_cost' => array_sum(wp_list_pluck($split_package, 'line_total')),
                'applied_coupons' => WC()->cart->get_applied_coupons(),
                'user' => array(
                    'ID' => get_current_user_id(),
                ),
                'destination' => array(
                    'country' => WC()->customer->get_shipping_country(),
                    'state' => WC()->customer->get_shipping_state(),
                    'postcode' => WC()->customer->get_shipping_postcode(),
                    'city' => WC()->customer->get_shipping_city(),
                    'address' => WC()->customer->get_shipping_address(),
                    'address_2' => WC()->customer->get_shipping_address_2()
                )
            );
        }
        return apply_filters('wcmp_split_shipping_packages', $packages);
    }

    /**
     * 
     * @param object $item
     * @param sting $package_key as $vendor_id
     */
    public function add_meta_date_in_shipping_package($item, $package_key, $package, $order) {
        $item->add_meta_data('vendor_id', $package_key, true);
        $package_qty = array_sum(wp_list_pluck($package['contents'], 'quantity'));
        $item->add_meta_data('package_qty', $package_qty, true);
        do_action('wcmp_add_shipping_package_meta_data');
    }

    /**
     * Rename shipping packages 
     * @since 2.6.6
     * @param string $package_name
     * @param string $vendor_id
     * @param array $package
     * @return string
     */
    public function woocommerce_shipping_package_name($package_name, $vendor_id, $package) {
        global $WCMp;
        if ($vendor_id && $vendor_id != 0) {
            $vendor = get_wcmp_vendor($vendor_id);
            if ($vendor) {
                return $vendor->user_data->display_name . __(' Shipping', 'dc-woocommerce-multi-vendor');
            }
            return $package_name;
        }
        return $package_name;
    }

    /**
     * WCMp Calculate shipping for order
     *
     * @support flat rate per item 
     * @param int $order_id
     * @param object $order_posted
     * @return void
     */
    function wcmp_checkout_order_processed($order_id, $order_posted, $order) {
        global $wpdb, $WCMp;
        $vendor_shipping_array = get_post_meta($order_id, 'dc_pv_shipped', true);
        $mark_ship = 0;
        $items = $order->get_items('line_item');
        $shipping_items = $order->get_items('shipping');
        $vendor_shipping = array();
        foreach ($shipping_items as $shipping_item_id => $shipping_item) {
            $order_item_shipping = new WC_Order_Item_Shipping($shipping_item_id);
            $shipping_vendor_id = $order_item_shipping->get_meta('vendor_id', true);
            $vendor_shipping[$shipping_vendor_id] = array(
                'shipping' => $order_item_shipping->get_total()
                , 'shipping_tax' => $order_item_shipping->get_total_tax()
                , 'package_qty' => $order_item_shipping->get_meta('package_qty', true)
            );
        }
        foreach ($items as $order_item_id => $item) {
            $line_item = new WC_Order_Item_Product($item);
            $product_id = $item['product_id'];
            $variation_id = isset($item['variation_id']) ? $item['variation_id'] : 0;
            if ($product_id) {
                $product_vendors = get_wcmp_product_vendors($product_id);
                if ($product_vendors) {
                    if (isset($product_vendors->id) && is_array($vendor_shipping_array)) {
                        if (in_array($product_vendors->id, $vendor_shipping_array)) {
                            $mark_ship = 1;
                        }
                    }
                    $shipping_amount = $shipping_tax_amount = 0;
                    if (!empty($vendor_shipping) && isset($vendor_shipping[$product_vendors->id])) {
                        $shipping_amount = (float) round(($vendor_shipping[$product_vendors->id]['shipping'] / $vendor_shipping[$product_vendors->id]['package_qty']) * $line_item->get_quantity(), 2);
                        $shipping_tax_amount = (float) round(($vendor_shipping[$product_vendors->id]['shipping_tax'] / $vendor_shipping[$product_vendors->id]['package_qty']) * $line_item->get_quantity(), 2);
                    }
                    $wpdb->query(
                            $wpdb->prepare(
                                    "INSERT INTO `{$wpdb->prefix}wcmp_vendor_orders` 
                                        ( order_id
                                        , commission_id
                                        , vendor_id
                                        , shipping_status
                                        , order_item_id
                                        , product_id
                                        , variation_id
                                        , tax
                                        , line_item_type
                                        , quantity
                                        , commission_status
                                        , shipping
                                        , shipping_tax_amount
                                        ) VALUES ( %d
                                        , %d
                                        , %d
                                        , %s
                                        , %d
                                        , %d 
                                        , %d
                                        , %s
                                        , %s
                                        , %d
                                        , %s
                                        , %s
                                        , %s
                                        ) ON DUPLICATE KEY UPDATE `created` = now()"
                                    , $order_id
                                    , 0
                                    , $product_vendors->id
                                    , $mark_ship
                                    , $order_item_id
                                    , $product_id
                                    , $variation_id
                                    , $line_item->get_total_tax()
                                    , 'product'
                                    , $line_item->get_quantity()
                                    , 'unpaid'
                                    , $shipping_amount
                                    , $shipping_tax_amount
                            )
                    );
                }
            }
        }
    }

    /**
     * Get shipping fee
     * @deprecated since version 2.6.6
     * Now deprecated
     */
    function get_fee($fee, $total) {
        $woocommerce_flat_rate_settings = get_option('woocommerce_flat_rate_settings');
        if (strstr($fee, '%')) {
            $fee = ( $total / 100 ) * str_replace('%', '', $fee);
        }
        if (!empty($woocommerce_flat_rate_settings['minimum_fee']) && $woocommerce_flat_rate_settings['minimum_fee'] > $fee) {
            $fee = $woocommerce_flat_rate_settings['minimum_fee'];
        }
        return $fee;
    }

    /**
     * Add frontend scripts
     * @return void
     */
    function frontend_scripts() {
        global $WCMp;
        $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $pluginURL = str_replace(array('http:', 'https:'), '', $WCMp->plugin_url);
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        if (is_vendor_dashboard()) {
            wp_enqueue_script('wcmp_frontend_vdashboard_js', $frontend_script_path . 'wcmp_vendor_dashboard' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        }
        // Enqueue your frontend javascript from here
        wp_enqueue_script('frontend_js', $frontend_script_path . 'frontend' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        wp_localize_script('frontend_js', 'front_end_param', array('report_abuse_msg' => __('Report has been sent', 'dc-woocommerce-multi-vendor')));
        if (is_vendor_order_by_product_page()) {
            wp_enqueue_script('vendor_order_by_product_js', $frontend_script_path . 'vendor_order_by_product' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        }

        if (is_single()) {
            wp_enqueue_script('simplepopup_js', $frontend_script_path . 'simplepopup' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        }

        wp_register_script('gmaps-api', '//maps.google.com/maps/api/js?sensor=false&amp;language=en', array('jquery'));
        wp_register_script('gmap3', $frontend_script_path . 'gmap3.min.js', array('jquery', 'gmaps-api'), '6.0.0', false);
        if (is_tax('dc_vendor_shop') || is_singular('product')) {
            wp_enqueue_script('gmap3');
        }
        if (is_vendor_page()) {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('wcmp_new_vandor_dashboard_js', $frontend_script_path . '/vendor_dashboard' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        }
        if (is_tax('dc_vendor_shop')) {
            $queried_object = get_queried_object();
            if (isset($queried_object->term_id) && !empty($queried_object)) {
                $vendor = get_wcmp_vendor_by_term($queried_object->term_id);
                $vendor_id = $vendor->id;
            }

            wp_enqueue_script('wcmp_seller_review_rating_js', $frontend_script_path . '/vendor_review_rating' . $suffix . '.js', array('jquery'), $WCMp->version, true);
            $vendor_review_rating_msg_array = array(
                'rating_error_msg_txt' => __('Please rate the vendor', 'dc-woocommerce-multi-vendor'),
                'review_error_msg_txt' => __('Please review your vendor and minimum 10 Character required', 'dc-woocommerce-multi-vendor'),
                'review_success_msg_txt' => __('Your review submitted successfully', 'dc-woocommerce-multi-vendor'),
                'review_failed_msg_txt' => __('Error in system please try again later', 'dc-woocommerce-multi-vendor'),
                'ajax_url' => trailingslashit(get_admin_url()) . 'admin-ajax.php',
                'vendor_id' => $vendor_id ? $vendor_id : ''
            );
            wp_localize_script('wcmp_seller_review_rating_js', 'wcmp_review_rating_msg', $vendor_review_rating_msg_array);
        }
        if (is_singular('product')) {
            wp_enqueue_script('wcmp_single_product_multiple_vendors', $frontend_script_path . '/single-product-multiple-vendors' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        }
        // Enqueue popup script
        wp_enqueue_script('popup_js', $frontend_script_path . 'wcmp-popup' . $suffix . '.js', array('jquery'), $WCMp->version, true);
    }

    /**
     * Add frontend styles
     * @return void
     */
    function frontend_styles() {
        global $WCMp;
        $frontend_style_path = $WCMp->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace(array('http:', 'https:'), '', $frontend_style_path);
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';



        if (is_tax('dc_vendor_shop')) {
            wp_enqueue_style('frontend_css', $frontend_style_path . 'frontend' . $suffix . '.css', array(), $WCMp->version);
        }

        wp_enqueue_style('product_css', $frontend_style_path . 'product' . $suffix . '.css', array(), $WCMp->version);

        if (is_vendor_order_by_product_page()) {
            wp_enqueue_style('vendor_order_by_product_css', $frontend_style_path . 'vendor_order_by_product' . $suffix . '.css', array(), $WCMp->version);
        }

//        $link_color = isset($WCMp->vendor_caps->frontend_cap['catalog_colorpicker']) ? $WCMp->vendor_caps->frontend_cap['catalog_colorpicker'] : '#000000';
//        $hover_link_color = isset($WCMp->vendor_caps->frontend_cap['catalog_hover_colorpicker']) ? $WCMp->vendor_caps->frontend_cap['catalog_hover_colorpicker'] : '#000000';
//
//        $custom_css = "
//                .by-vendor-name-link:hover{
//                        color: {$hover_link_color} !important;
//                }
//                .by-vendor-name-link{
//                        color: {$link_color} !important;
//                }";
//        wp_add_inline_style('product_css', $custom_css);
        if (is_vendor_page()) {
            wp_enqueue_style('dashicons');
            wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
            wp_enqueue_style('wcmp_new_vandor_dashboard_css', $frontend_style_path . 'vendor_dashboard' . $suffix . '.css', array(), $WCMp->version);
            wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', array(), $WCMp->version);
        }
        if (is_tax('dc_vendor_shop')) {
            $current_theme = get_option('template');
            if ($current_theme == 'storefront') {
                wp_enqueue_style('wcmp_review_rating', $frontend_style_path . 'review_rating_storefront' . $suffix . '.css', array(), $WCMp->version);
            } else {
                wp_enqueue_style('wcmp_review_rating', $frontend_style_path . 'review_rating' . $suffix . '.css', array(), $WCMp->version);
            }
        }
        wp_enqueue_style('multiple_vendor', $frontend_style_path . 'multiple-vendor' . $suffix . '.css', array(), $WCMp->version);
    }

    /**
     * Add html for vendor taxnomy page
     * @return void
     */
    function product_archive_vendor_info() {
        global $WCMp;
        if (is_tax('dc_vendor_shop')) {
            // Get vendor ID
            $vendor_id = get_queried_object()->term_id;
            // Get vendor info
            $vendor = get_wcmp_vendor_by_term($vendor_id);
            $image = '';
            $image = $vendor->image;
            if (!$image)
                $image = $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
            $description = $vendor->description;

            $address = '';

            if ($vendor->city) {
                $address = $vendor->city . ', ';
            }
            if ($vendor->state) {
                $address .= $vendor->state . ', ';
            }
            if ($vendor->country) {
                $address .= $vendor->country;
            }
            $WCMp->template->get_template('archive_vendor_info.php', array('vendor_id' => $vendor->id, 'banner' => $vendor->banner, 'profile' => $image, 'description' => stripslashes($description), 'mobile' => $vendor->phone, 'location' => $address, 'email' => $vendor->user_data->user_email));
        }
    }

    /**
     * Add 'woocommerce' class to body tag for vendor pages
     *
     * @param  arr $classes Existing classes
     * @return arr          Modified classes
     */
    public function set_product_archive_class($classes) {
        if (is_tax('dc_vendor_shop')) {

            // Add generic classes
            $classes[] = 'woocommerce';
            $classes[] = 'product-vendor';

            // Get vendor ID
            $vendor_id = get_queried_object()->term_id;

            // Get vendor info
            $vendor = get_wcmp_vendor_by_term($vendor_id);

            // Add vendor slug as class
            if ('' != $vendor->slug) {
                $classes[] = $vendor->slug;
            }
        }
        return $classes;
    }

    /**
     * template redirect function
     * @return void
     */
    function template_redirect() {
        //redirect to my account or vendor dashbord page if user loggedin
        if (is_user_logged_in() && is_page_vendor_registration()) {
            if (is_user_wcmp_vendor(get_current_user_id())) {
                wp_safe_redirect(get_permalink(wcmp_vendor_dashboard_page_id()));
            } else {
                wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
            }
            exit();
        }
    }

    /**
     * Calculate order falt rate shipping
     * @deprecated since version 2.6.6
     * @support WC 2.4
     */
    public function evaluate_flat_shipping_cost($sum, $args = array()) {
        include_once( WC()->plugin_path() . '/includes/shipping/flat-rate/includes/class-wc-eval-math.php' );

        add_shortcode('fee', array($this, 'wcmp_shipping_fee_calculation'));
        $this->wcmp_shipping_fee_cost = $args['cost'];

        $sum = rtrim(ltrim(do_shortcode(str_replace(
                                        array(
            '[qty]',
            '[cost]'
                                        ), array(
            $args['qty'],
            $args['cost']
                                        ), $sum
                        )), "\t\n\r\0\x0B+*/"), "\t\n\r\0\x0B+-*/");

        remove_shortcode('fee', array($this, 'wcmp_shipping_fee_calculation'));

        return $sum ? WC_Eval_Math::evaluate($sum) : 0;
    }

    /**
     * Calculate order flat rate shipping
     * @deprecated since version 2.6.6
     * @support WC 2.6
     */
    public function calculate_flat_rate_shipping_cost($sum, $args = array()) {
        include_once( WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php' );
        $WC_Shipping_Flat_Rate = new WC_Shipping_Flat_Rate();
        // Allow 3rd parties to process shipping cost arguments
        $args = apply_filters('woocommerce_evaluate_shipping_cost_args', $args, $sum, $this);
        $locale = localeconv();
        $decimals = array(wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point']);
        $this->fee_cost = $args['cost'];

        // Expand shortcodes
        add_shortcode('fee', array($WC_Shipping_Flat_Rate, 'fee'));

        $sum = do_shortcode(str_replace(
                        array(
            '[qty]',
            '[cost]'
                        ), array(
            $args['qty'],
            $args['cost']
                        ), $sum
        ));

        remove_shortcode('fee', array($WC_Shipping_Flat_Rate, 'fee'));

        // Remove whitespace from string
        $sum = preg_replace('/\s+/', '', $sum);

        // Remove locale from string
        $sum = str_replace($decimals, '.', $sum);

        // Trim invalid start/end characters
        $sum = rtrim(ltrim($sum, "\t\n\r\0\x0B+*/"), "\t\n\r\0\x0B+-*/");

        // Do the math
        return $sum ? WC_Eval_Math::evaluate($sum) : 0;
    }

    /**
     * Calculate flat rate shipping fee
     * @deprecated since version 2.6.6
     * @support WC 2.4
     */
    public function wcmp_shipping_fee_calculation($atts) {
        $atts = shortcode_atts(array(
            'percent' => '',
            'min_fee' => ''
                ), $atts);

        $calculated_fee = 0;

        if ($atts['percent']) {
            $calculated_fee = $this->wcmp_shipping_fee_cost * ( floatval($atts['percent']) / 100 );
        }
        if ($atts['min_fee'] && $calculated_fee < $atts['min_fee']) {
            $calculated_fee = $atts['min_fee'];
        }

        return $calculated_fee;
    }

}
