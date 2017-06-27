<?php
if (!defined('ABSPATH'))
    exit;

/**
 * @class 		WCMp User Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_User {

    private $post_type;

    public function __construct() {

        // Add dc_pending_vendor, dc_vendor, dc_rejected_vendor role
        $this->register_user_role();

        // Set vendor role
        add_action('user_register', array(&$this, 'vendor_registration'), 10, 1);

        // Add column product in users dashboard
        add_filter('manage_users_columns', array(&$this, 'column_register_product'));
        add_filter('manage_users_custom_column', array(&$this, 'column_display_product'), 10, 3);

        // Set vendor_action links in user dashboard
        add_filter('user_row_actions', array(&$this, 'vendor_action_links'), 10, 2);

        // Add addistional user fields
        add_action('show_user_profile', array(&$this, 'additional_user_fields'));
        add_action('edit_user_profile', array(&$this, 'additional_user_fields'));

        // Validate addistional user fields
        add_action('user_profile_update_errors', array(&$this, 'validate_user_fields'), 10, 3);

        // Save addistional user fields
        add_action('personal_options_update', array(&$this, 'save_vendor_data'));
        add_action('edit_user_profile_update', array(&$this, 'save_vendor_data'));

        //add_action( 'profile_update', array( &$this, 'save_vendor_data') );
        add_action('set_user_role', array(&$this, 'save_vendor_data'));

        // Delete vendor
        add_action('delete_user', array(&$this, 'delete_vendor'));

        add_action('admin_head', array($this, 'profile_admin_buffer_start'));
        add_action('admin_footer', array($this, 'profile_admin_buffer_end'));

        // Add vednor registration checkbox in front-end
        //add_action( 'woocommerce_register_form', array($this, 'wcmp_woocommerce_register_form'));
        // Created customer notification
        add_action('woocommerce_created_customer_notification', array($this, 'wcmp_woocommerce_created_customer_notification'), 9, 3);

        add_action('set_user_role', array(&$this, 'set_user_role'), 30, 3);
        add_action('add_user_role', array(&$this, 'add_user_role'), 30, 2);

        // Add message in my account page after vendore registrtaion
        add_action('woocommerce_before_my_account', array(&$this, 'woocommerce_before_my_account'));

        add_filter('woocommerce_resend_order_emails_available', array($this, 'wcmp_order_emails_available'));

        add_filter('woocommerce_registration_redirect', array($this, 'vendor_login_redirect'), 30, 1);

        $this->register_vendor_from_vendor_dashboard();

        add_filter('woocommerce_login_redirect', array($this, 'wcmp_vendor_login'), 10, 2);
        add_filter('login_redirect', array($this, 'wp_wcmp_vendor_login'), 10, 3);
    }

    function wp_wcmp_vendor_login($redirect_to, $request, $user) {
        global $WCMp;
        //is there a user to check?
        if (isset($user->roles) && is_array($user->roles)) {
            //check for admins
            if (in_array('dc_vendor', $user->roles)) {
                // redirect them to the default place
                $redirect_to = get_permalink(wcmp_vendor_dashboard_page_id());
                return $redirect_to;
            } else {
                return $redirect_to;
            }
        } else {
            return $redirect_to;
        }
    }

    function wcmp_vendor_login($redirect, $user) {
        if (is_array($user->roles)) {
            if (in_array('dc_vendor', $user->roles)) {
                $redirect = get_permalink(wcmp_vendor_dashboard_page_id());
            }
        } else if ($user->roles == 'dc_vendor') {
            $redirect = get_permalink(wcmp_vendor_dashboard_page_id());
        }
        return $redirect;
    }

    function register_vendor_from_vendor_dashboard() {
        global $WCMp;
        $user = wp_get_current_user();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['wcmp_vendor_fields']) && isset($_POST['pending_vendor']) && isset($_POST['vendor_apply'])) {
                $customer_id = $user->ID;
                $validation_errors = new WP_Error();
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
                                if (!in_array($file_value, $file_type)) {
                                    $validation_errors->add('file type error', __('Please Upload valid file', 'woocommerce'));
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

                if ($validation_errors->get_error_code()) {
                    WC()->session->set('wc_notices', array('error' => array($validation_errors->get_error_message())));
                    return;
                }

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
            }

            if (isset($_POST['vendor_apply']) && $user) {
                if (isset($_POST['pending_vendor']) && ( $_POST['pending_vendor'] == 'true' )) {
                    $this->vendor_registration($user->ID);
                    $this->wcmp_customer_new_account($user->ID);
                    wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
                    exit;
                }
            }
        }
    }

    /**
     * Vendor login template redirect
     */
    function vendor_login_redirect($redirect_to) {
        if (isset($_POST['email'])) {
            $user = get_user_by('email', $_POST['email']);
            if (is_object($user) && isset($user->ID) && is_user_wcmp_vendor($user->ID)) {
                $redirect_to = get_permalink(wcmp_vendor_dashboard_page_id());
                return $redirect_to;
            }
            return apply_filters('wcmp_vendor_login_redirect', $redirect_to, $user);
        }
        return apply_filters('wcmp_vendor_login_redirect', $redirect_to, $user);
    }

    /**
     * WCMp Vendor message at WC myAccount
     * @access public
     * @return void
     */
    public function woocommerce_before_my_account() {
        global $WCMp;
        $current_user = wp_get_current_user();
        if (is_user_wcmp_pending_vendor($current_user)) {
            _e('Congratulations! You have successfully applied as a Vendor. Please wait for further notifications from the admin.', 'dc-woocommerce-multi-vendor');
            do_action('add_vendor_extra_information_my_account');
        }
        if (is_user_wcmp_vendor($current_user)) {
            $dashboard_page_link = wcmp_vendor_dashboard_page_id() ? get_permalink(wcmp_vendor_dashboard_page_id()) : '#';
            echo apply_filters('wcmp_vendor_goto_dashboard', '<a href="' . $dashboard_page_link . '">' . __('Dashboard - manage your account here', 'dc-woocommerce-multi-vendor') . '</a>');
        }
    }

    /**
     * Set vendor user role and associate capabilities
     *
     * @access public
     * @param user_id, new role, old role
     * @return void
     */
    public function set_user_role($user_id, $new_role, $old_role) {
        global $WCMp;
        $user = new WP_User($user_id);
        switch ($new_role) {
            case 'dc_rejected_vendor':
                $user->remove_all_caps();
                $user->add_role('dc_rejected_vendor');
                $user_dtl = get_userdata(absint($user_id));
                $email = WC()->mailer()->emails['WC_Email_Rejected_New_Vendor_Account'];
                $email->trigger($user_id, $user_dtl->user_pass);
                if (in_array('dc_vendor', $old_role)) {
                    $vendor = get_wcmp_vendor($user_id);
                    if ($vendor) {
                        wp_delete_term($vendor->term_id, 'dc_vendor_shop');
                    }
                }
                break;
            case 'dc_pending_vendor':
                $user->remove_all_caps();
                $user->add_role('dc_pending_vendor');
                break;
            case 'dc_vendor':
                $this->update_vendor_meta($user_id);

                $caps = $this->get_vendor_caps($user_id);
                foreach ($caps as $cap) {
                    $user->add_cap($cap);
                }
                $shipping_class_id = get_user_meta($user_id, 'shipping_class_id', true);
                $add_vendor_shipping_class = apply_filters('wcmp_add_vendor_shipping_class', true);
                if (empty($shipping_class_id) && $add_vendor_shipping_class) {
                    $shipping_term = wp_insert_term($user->user_login . '-' . $user_id, 'product_shipping_class');
                    if (!is_wp_error($shipping_term)) {
                        update_user_meta($user_id, 'shipping_class_id', $shipping_term['term_id']);
                        add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_id', $user_id);
                        add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_shipping_origin', get_option('woocommerce_default_country'));
                    }
                }
                break;
            default :
                break;
        }
        do_action('wcmp_set_user_role', $user_id, $new_role, $old_role);
    }

    /**
     * Add vendor user role and associate capabilities
     *
     * @access public
     * @param user_id, new role, old role
     * @return void
     */
    public function add_user_role($user_id, $new_role) {
        global $WCMp;
        $user = new WP_User($user_id);
        if ($new_role == 'dc_vendor') {
            $this->update_vendor_meta($user_id);

            $caps = $this->get_vendor_caps($user_id);
            foreach ($caps as $cap) {
                $user->add_cap($cap);
            }
            $shipping_class_id = get_user_meta($user_id, 'shipping_class_id', true);
            $add_vendor_shipping_class = apply_filters('wcmp_add_vendor_shipping_class', true);
            if (empty($shipping_class_id) && $add_vendor_shipping_class) {
                $shipping_term = wp_insert_term($user->user_login . '-' . $user_id, 'product_shipping_class');
                if (!is_wp_error($shipping_term)) {
                    update_user_meta($user_id, 'shipping_class_id', $shipping_term['term_id']);
                    add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_id', $user_id);
                    add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_shipping_origin', get_option('woocommerce_default_country'));
                }
            }
        }
    }

    /**
     * Register vendor user role
     *
     * @access public
     * @return void
     */
    public function register_user_role() {
        global $wp_roles, $WCMp;
        if (class_exists('WP_Roles'))
            if (!isset($wp_roles))
                $wp_roles = new WP_Roles();
        if (is_object($wp_roles)) {
            if (get_role('dc_vendor') == null) {
                // Vendor role
                add_role('dc_vendor', apply_filters('dc_vendor_role', __('Vendor', 'dc-woocommerce-multi-vendor')), array(
                    'read' => true,
                    'manage_product' => true,
                    'edit_posts' => true,
                    'delete_posts' => false,
                    'view_woocommerce_reports' => true,
                ));
            }
            if (get_role('dc_pending_vendor') == null) {
                // Pending Vendor role
                add_role('dc_pending_vendor', apply_filters('dc_pending_vendor_role', __('Pending Vendor', 'dc-woocommerce-multi-vendor')), array(
                    'read' => true,
                    'edit_posts' => false,
                    'delete_posts' => false,
                ));
            }
            if (get_role('dc_rejected_vendor') == null) {
                // Pending Vendor role
                add_role('dc_rejected_vendor', apply_filters('dc_rejected_vendor_role', __('Rejected Vendor', 'dc-woocommerce-multi-vendor')), array(
                    'read' => true,
                    'edit_posts' => false,
                    'delete_posts' => false,
                ));
            }
//            if (isset($wordpress_default_role))
//                update_option('default_role', $wordpress_default_role);
        }
    }

    /**
     * Set up array of vendor admin capabilities
     *
     * @access private
     * @param int $user_id
     * @return arr Vendor capabilities
     */
    private function get_vendor_caps($user_id) {
        global $WCMp;
        $caps = array();
        $caps[] = "assign_product_terms";
        if ($WCMp->vendor_caps->vendor_capabilities_settings('is_upload_files')) {
            $caps[] = "upload_files";
        }
        if ($WCMp->vendor_caps->vendor_capabilities_settings('is_submit_product')) {
            $vendor_submit_products = get_user_meta($user_id, '_vendor_submit_product', true);
            if ($vendor_submit_products) {
                $caps[] = "edit_product";
                $caps[] = "delete_product";
                $caps[] = "edit_products";
                $caps[] = "delete_products";
                if ($WCMp->vendor_caps->vendor_capabilities_settings('is_published_product')) {
                    $caps[] = "publish_products";
                }
                if ($WCMp->vendor_caps->vendor_capabilities_settings('is_edit_delete_published_product')) {
                    $caps[] = "edit_published_products";
                    $caps[] = 'delete_published_products';
                }
            }
        }

        $caps[] = "read_product";
        $caps[] = "read_shop_coupon";

        if ($WCMp->vendor_caps->vendor_capabilities_settings('is_submit_coupon')) {
            $vendor_submit_coupon = get_user_meta($user_id, '_vendor_submit_coupon', true);
            if ($vendor_submit_coupon) {
                $caps[] = 'edit_shop_coupon';
                $caps[] = 'edit_shop_coupons';
                $caps[] = 'delete_shop_coupon';
                $caps[] = 'delete_shop_coupons';
                if ($WCMp->vendor_caps->vendor_capabilities_settings('is_published_coupon')) {
                    $caps[] = "publish_shop_coupons";
                }
                if ($WCMp->vendor_caps->vendor_capabilities_settings('is_edit_delete_published_coupon')) {
                    $caps[] = "edit_published_shop_coupons";
                    $caps[] = "delete_published_shop_coupons";
                }
            }
        }
        return apply_filters('vendor_capabilities', $caps, $user_id);
    }

    /**
     * Add capabilities to vendor admins
     *
     * @param int $user_id User ID of vendor admin
     */
    public function add_vendor_caps($user_id = 0) {
        if ($user_id > 0) {
            $caps = $this->get_vendor_caps($user_id);
            $user = new WP_User($user_id);
            foreach ($caps as $cap) {
                //echo $cap;
                $user->add_cap($cap);
            }
        }
        //die;
    }

    /**
     * Get vendor details
     *
     * @param $user_id
     * @access public
     * @return array
     */
    public function get_vendor_fields($user_id) {
        global $WCMp;

        $vendor = new WCMp_Vendor($user_id);
        $settings_capabilities = array_merge(
                (array) get_option('wcmp_general_settings_name', array())
                , (array) get_option('wcmp_capabilities_product_settings_name', array())
                , (array) get_option('wcmp_capabilities_order_settings_name', array())
                , (array) get_option('wcmp_capabilities_miscellaneous_settings_name', array())
        );
        $policies_settings = get_option('wcmp_general_policies_settings_name');

        $fields = apply_filters('wcmp_vendor_fields', array(
            "vendor_page_title" => array(
                'label' => __('Vendor Page Title', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->page_title,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_page_slug" => array(
                'label' => __('Vendor Page Slug', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->page_slug,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_description" => array(
                'label' => __('Description', 'dc-woocommerce-multi-vendor'),
                'type' => 'wpeditor',
                'value' => $vendor->description,
                'class' => "user-profile-fields"
            ), //Wp Eeditor
            "vendor_hide_address" => array(
                'label' => __('Hide address in frontend', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->hide_address,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            ),
            "vendor_hide_phone" => array(
                'label' => __('Hide phone in frontend', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->hide_phone,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            ),
            "vendor_hide_email" => array(
                'label' => __('Hide email in frontend', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->hide_email,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            ),
            "vendor_hide_description" => array(
                'label' => __('Hide description in frontend', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->hide_description,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            ),
            "vendor_company" => array(
                'label' => __('Company Name', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->company,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_address_1" => array(
                'label' => __('Address 1', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->address_1,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_address_2" => array(
                'label' => __('Address 2', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->address_2,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_city" => array(
                'label' => __('City', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->city,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_postcode" => array(
                'label' => __('Postcode', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->postcode,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_external_store_url" => array(
                'label' => __('External store URL', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->external_store_url,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_external_store_label" => array(
                'label' => __('External store URL label', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->external_store_label,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_state" => array(
                'label' => __('State', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->state,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_country" => array(
                'label' => __('Country', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->country,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_phone" => array(
                'label' => __('Phone', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->phone,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_fb_profile" => array(
                'label' => __('Facebook Profile', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->fb_profile,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_twitter_profile" => array(
                'label' => __('Twitter Profile', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->twitter_profile,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_google_plus_profile" => array(
                'label' => __('Google+ Profile', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->google_plus_profile,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_linkdin_profile" => array(
                'label' => __('LinkedIn Profile', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->linkdin_profile,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_youtube" => array(
                'label' => __('YouTube Channel', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->youtube,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_instagram" => array(
                'label' => __('Instagram Profile', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->instagram,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_image" => array(
                'label' => __('Logo', 'dc-woocommerce-multi-vendor'),
                'type' => 'upload',
                'prwidth' => 125,
                'value' => $vendor->image,
                'class' => "user-profile-fields"
            ), // Upload
            "vendor_banner" => array(
                'label' => __('Banner', 'dc-woocommerce-multi-vendor'),
                'type' => 'upload',
                'prwidth' => 600,
                'value' => $vendor->banner,
                'class' => "user-profile-fields"
            ), // Upload			
            "vendor_csd_return_address1" => array(
                'label' => __('Customer address1', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->csd_return_address1,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_csd_return_address2" => array(
                'label' => __('Customer address2', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->csd_return_address2,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_csd_return_country" => array(
                'label' => __('Customer Country', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->csd_return_country,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_csd_return_state" => array(
                'label' => __('Customer Return State', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->csd_return_state,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_csd_return_city" => array(
                'label' => __('Customer Return City', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->csd_return_city,
                'class' => "user-profile-fields"
            ), // Text 
            "vendor_csd_return_zip" => array(
                'label' => __('Customer Return Zip', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->csd_return_zip,
                'class' => "user-profile-fields"
            ), // Text  
            "vendor_customer_phone" => array(
                'label' => __('Customer Phone', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->customer_phone,
                'class' => "user-profile-fields"
            ), // Text
            "vendor_customer_email" => array(
                'label' => __('Customer Email', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->customer_email,
                'class' => "user-profile-fields"
            ), // Text
                ), $user_id);

        $is_vendor_add_external_url_field = apply_filters('is_vendor_add_external_url_field', true);
        if (!$WCMp->vendor_caps->vendor_capabilities_settings('is_vendor_add_external_url') || !$is_vendor_add_external_url_field) {
            unset($fields['vendor_external_store_url']);
            unset($fields['vendor_external_store_label']);
        }

        $payment_admin_settings = get_option('wcmp_payment_settings_name');
        $payment_mode = array();
        if (isset($payment_admin_settings['payment_method_paypal_masspay']) && $payment_admin_settings['payment_method_paypal_masspay'] = 'Enable') {
            $payment_mode['paypal_masspay'] = __('PayPal Masspay', 'dc-woocommerce-multi-vendor');
        }
        if (isset($payment_admin_settings['payment_method_paypal_payout']) && $payment_admin_settings['payment_method_paypal_payout'] = 'Enable') {
            $payment_mode['paypal_payout'] = __('PayPal Payout', 'dc-woocommerce-multi-vendor');
        }
        if (isset($payment_admin_settings['payment_method_direct_bank']) && $payment_admin_settings['payment_method_direct_bank'] = 'Enable') {
            $payment_mode['direct_bank'] = __('Direct Bank', 'dc-woocommerce-multi-vendor');
        }

        $fields["vendor_payment_mode"] = array(
            'label' => __('Payment Mode', 'dc-woocommerce-multi-vendor'),
            'type' => 'select',
            'options' => apply_filters('wcmp_vendor_payment_mode', $payment_mode),
            'value' => $vendor->payment_mode,
            'class' => "user-profile-fields"
        ); // Text

        $fields["vendor_bank_account_type"] = array(
            'label' => __('Bank Account Type', 'dc-woocommerce-multi-vendor'),
            'type' => 'select',
            'options' => array('current' => __('Current', 'dc-woocommerce-multi-vendor'), 'savings' => __('Savings', 'dc-woocommerce-multi-vendor')),
            'value' => $vendor->bank_account_type,
            'class' => "user-profile-fields"
        ); // Text

        $fields["vendor_bank_account_number"] = array(
            'label' => __('Bank Account Name', 'dc-woocommerce-multi-vendor'),
            'type' => 'text',
            'value' => $vendor->bank_account_number,
            'class' => "user-profile-fields"
        ); // Text

        $fields["vendor_bank_name"] = array(
            'label' => __('Bank Name', 'dc-woocommerce-multi-vendor'),
            'type' => 'text',
            'value' => $vendor->bank_name,
            'class' => "user-profile-fields"
        ); // Text

        $fields["vendor_aba_routing_number"] = array(
            'label' => __('ABA Routing Number', 'dc-woocommerce-multi-vendor'),
            'type' => 'text',
            'value' => $vendor->aba_routing_number,
            'class' => "user-profile-fields"
        ); // Text

        $fields["vendor_bank_address"] = array(
            'label' => __('Bank Address', 'dc-woocommerce-multi-vendor'),
            'type' => 'textarea',
            'value' => $vendor->bank_address,
            'class' => "user-profile-fields"
        ); // Text

        $fields["vendor_destination_currency"] = array(
            'label' => __('Destination Currency', 'dc-woocommerce-multi-vendor'),
            'type' => 'text',
            'value' => $vendor->destination_currency,
            'class' => "user-profile-fields"
        ); // Text

        $fields["vendor_iban"] = array(
            'label' => __('IBAN', 'dc-woocommerce-multi-vendor'),
            'type' => 'text',
            'value' => $vendor->iban,
            'class' => "user-profile-fields"
        ); // Text

        $fields["vendor_account_holder_name"] = array(
            'label' => __('Account Holder Name', 'dc-woocommerce-multi-vendor'),
            'type' => 'text',
            'value' => $vendor->account_holder_name,
            'class' => "user-profile-fields"
        ); // Text
        $fields["vendor_paypal_email"] = array(
            'label' => __('PayPal Email', 'dc-woocommerce-multi-vendor'),
            'type' => 'text',
            'value' => $vendor->paypal_email,
            'class' => "user-profile-fields"
        ); // Text

        if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($policies_settings['can_vendor_edit_policy_tab_label'])) {

            $fields['vendor_policy_tab_title'] = array(
                'label' => __('Enter the title of Policies Tab', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->policy_tab_title,
                'class' => 'user-profile-fields'
            );
        }
        if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($policies_settings['can_vendor_edit_cancellation_policy']) && isset($policies_settings['is_cancellation_on'])) {
            $fields['vendor_cancellation_policy'] = array(
                'label' => __('Cancellation/Return/Exchange Policy', 'dc-woocommerce-multi-vendor'),
                'type' => 'textarea',
                'value' => $vendor->cancellation_policy,
                'class' => 'user-profile-fields'
            );
        }
        if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($policies_settings['can_vendor_edit_refund_policy']) && isset($policies_settings['is_refund_on'])) {
            $fields['vendor_refund_policy'] = array(
                'label' => __('Refund Policy', 'dc-woocommerce-multi-vendor'),
                'type' => 'textarea',
                'value' => $vendor->refund_policy,
                'class' => 'user-profile-fields'
            );
        }
        if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($policies_settings['can_vendor_edit_shipping_policy']) && isset($policies_settings['is_shipping_on'])) {
            $fields['vendor_shipping_policy'] = array(
                'label' => __('Shipping Policy', 'dc-woocommerce-multi-vendor'),
                'type' => 'textarea',
                'value' => $vendor->shipping_policy,
                'class' => 'user-profile-fields'
            );
        }
        if (isset($settings_capabilities['can_vendor_add_message_on_email_and_thankyou_page'])) {
            $fields['vendor_message_to_buyers'] = array(
                'label' => __('Message to Buyers', 'dc-woocommerce-multi-vendor'),
                'type' => 'textarea',
                'value' => $vendor->message_to_buyers,
                'class' => 'user-profile-fields'
            );

            $fields['vendor_hide_message_to_buyers'] = array(
                'label' => __('Is Message to buyer Hide From Users', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->hide_message_to_buyers,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            );
        }
        $user = wp_get_current_user();
        if (is_array($user->roles) && in_array('administrator', $user->roles)) {
            $fields['vendor_commission'] = array(
                'label' => __('Commission Amount', 'dc-woocommerce-multi-vendor'),
                'type' => 'text',
                'value' => $vendor->commission,
                'class' => "user-profile-fields"
            ); // Text   
            $fields['vendor_submit_product'] = array(
                'label' => __('Submit products', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->submit_product,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            );
            $fields['vendor_publish_product'] = array(
                'label' => __('Disallow direct publishing of products', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->publish_product,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            );
            $fields['vendor_submit_coupon'] = array(
                'label' => __('Submit Coupons', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->submit_coupon,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            );
            $fields['vendor_publish_coupon'] = array(
                'label' => __('Disallow direct publishing of coupons', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->publish_coupon,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            );
            $fields['vendor_give_tax'] = array(
                'label' => __('Withhold Tax', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->give_tax,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            );
            $fields['vendor_give_shipping'] = array(
                'label' => __('Withhold Shipping', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->give_shipping,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            );
            $fields['vendor_turn_off'] = array(
                'label' => __('Block this vendor with all items', 'dc-woocommerce-multi-vendor'),
                'type' => 'checkbox',
                'dfvalue' => $vendor->turn_off,
                'value' => 'Enable',
                'class' => 'user-profile-fields'
            );



            if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {
                unset($fields['vendor_commission']);
                $fields['vendor_commission_percentage'] = array(
                    'label' => __('Commission Percentage(%)', 'dc-woocommerce-multi-vendor'),
                    'type' => 'text',
                    'value' => $vendor->commission_percentage,
                    'class' => 'user-profile-fields'
                );
                $fields['vendor_commission_fixed_with_percentage'] = array(
                    'label' => __('Commission(fixed), Per Transaction', 'dc-woocommerce-multi-vendor'),
                    'type' => 'text',
                    'value' => $vendor->commission_fixed_with_percentage,
                    'class' => 'user-profile-fields'
                );
            }

            if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {
                unset($fields['vendor_commission']);
                $fields['vendor_commission_percentage'] = array(
                    'label' => __('Commission Percentage(%)', 'dc-woocommerce-multi-vendor'),
                    'type' => 'text',
                    'value' => $vendor->commission_percentage,
                    'class' => 'user-profile-fields'
                );
                $fields['vendor_commission_fixed_with_percentage_qty'] = array(
                    'label' => __('Commission Fixed Per Unit', 'dc-woocommerce-multi-vendor'),
                    'type' => 'text',
                    'value' => $vendor->commission_fixed_with_percentage_qty,
                    'class' => 'user-profile-fields'
                );
            }
        }

        if (!$WCMp->vendor_caps->vendor_capabilities_settings('is_submit_product')) {
            unset($fields['vendor_submit_product']);
        }
        if (!$WCMp->vendor_caps->vendor_capabilities_settings('is_published_product')) {
            unset($fields['vendor_publish_product']);
        }

        if (!$WCMp->vendor_caps->vendor_capabilities_settings('is_submit_coupon')) {
            unset($fields['vendor_submit_coupon']);
        }
        if (!$WCMp->vendor_caps->vendor_capabilities_settings('is_published_coupon')) {
            unset($fields['vendor_publish_coupon']);
        }

        return $fields;
    }

    /**
     * Actions at Vendor Registration
     *
     * @access public
     * @param $user_id
     */
    public function vendor_registration($user_id) {
        global $WCMp;
        $is_approve_manually = $WCMp->vendor_caps->vendor_general_settings('approve_vendor_manually');
        if (isset($_POST['pending_vendor']) && ($_POST['pending_vendor'] == 'true') && !is_user_wcmp_vendor($user_id) && $is_approve_manually) {
            $user = new WP_User(absint($user_id));
            $user->remove_role('customer');
            $user->remove_role('Subscriber');
            $user->add_role('dc_pending_vendor');
        }

        if (isset($_POST['pending_vendor']) && ($_POST['pending_vendor'] == 'true') && !is_user_wcmp_vendor($user_id) && !$is_approve_manually) {
            $user = new WP_User(absint($user_id));
            $user->remove_role('customer');
            $user->remove_role('Subscriber');
            $user->add_role('dc_vendor');
            $this->update_vendor_meta($user_id);
        }

        if (is_user_wcmp_vendor($user_id)) {
            $this->update_vendor_meta($user_id);
            $this->add_vendor_caps($user_id);
            $vendor = get_wcmp_vendor($user_id);
            $vendor->generate_term();
        }
    }

    /**
     * ADD commission column on user dashboard
     *
     * @access public
     * @return array
     */
    function column_register_product($columns) {
        global $WCMp;
        $columns['product'] = __('Products', 'dc-woocommerce-multi-vendor');
        return $columns;
    }

    /**
     * Display commission column on user dashboard
     *
     * @access public
     * @return string
     */
    function column_display_product($empty, $column_name, $user_id) {
        if ('product' != $column_name)
            return $empty;
        $vendor = get_wcmp_vendor($user_id);
        if ($vendor) {
            $product_count = count($vendor->get_products());
            return "<a href='edit.php?post_type=product&dc_vendor_shop=" . $vendor->user_data->user_login . "'><strong>{$product_count}</strong></a>";
        } else
            return "<strong></strong>";
    }

    /**
     * Add vendor action link in user dashboard
     *
     * @access public
     * @return array
     */
    function vendor_action_links($actions, $user_object) {
        global $WCMp;

        if (is_user_wcmp_vendor($user_object)) {
            $vendor = get_wcmp_vendor($user_object->ID);
            if ($vendor) {
                $actions['view_vendor'] = "<a target=_blank class='view_vendor' href='" . $vendor->permalink . "'>" . __('View', 'dc-woocommerce-multi-vendor') . "</a>";
            }
        }

        if (is_user_wcmp_pending_vendor($user_object)) {
            $vendor = get_wcmp_vendor($user_object->ID);
            $actions['activate'] = "<a class='activate_vendor' data-id='" . $user_object->ID . "'href=#>" . __('Approve', 'dc-woocommerce-multi-vendor') . "</a>";
            $actions['reject'] = "<a class='reject_vendor' data-id='" . $user_object->ID . "'href=#>" . __('Reject', 'dc-woocommerce-multi-vendor') . "</a>";
        }

        if (is_user_wcmp_rejected_vendor($user_object)) {
            $vendor = get_wcmp_vendor($user_object->ID);
            $actions['activate'] = "<a class='activate_vendor' data-id='" . $user_object->ID . "'href=#>" . __('Approve', 'dc-woocommerce-multi-vendor') . "</a>";
        }


        return $actions;
    }

    /**
     * Additional user  fileds at Profile page
     *
     * @access private
     * @param $user obj
     * @return void
     */
    function additional_user_fields($user) {
        global $WCMp;
        $vendor = get_wcmp_vendor($user->ID);
        if ($vendor) {
            ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="View Vendor" > <?php _e('View Vendor', 'dc-woocommerce-multi-vendor'); ?></label>
                        </th>
                        <td>
                            <a class="button-primary" target="_blank" href=<?php echo $vendor->permalink; ?>>View</a>
                        </td>
                    </tr>
                    <?php $WCMp->wcmp_wp_fields->dc_generate_form_field($this->get_vendor_fields($user->ID), array('in_table' => 1)); ?>
                </tbody>
            </table>
            <?php
        }
    }

    /**
     * Validate user additional fields
     */
    function validate_user_fields(&$errors, $update, &$user) {
        global $WCMp;
        if (isset($_POST['vendor_page_slug'])) {
            if (!$update) {
                if (term_exists(sanitize_title($_POST['vendor_page_slug']), 'dc_vendor_shop')) {
                    $errors->add('vendor_slug_exists', __('Slug Already Exists', 'dc-woocommerce-multi-vendor'));
                }
            } else {
                if (is_user_wcmp_vendor($user->ID)) {
                    $vendor = get_wcmp_vendor($user->ID);
                    if (isset($vendor->term_id))
                        $vendor_term = get_term($vendor->term_id, 'dc_vendor_shop');
                    if (isset($_POST['vendor_page_slug']) && isset($vendor_term->slug) && $vendor_term->slug != $_POST['vendor_page_slug']) {
                        if (term_exists(sanitize_title($_POST['vendor_page_slug']), 'dc_vendor_shop')) {
                            $errors->add('vendor_slug_exists', __('Slug already exists', 'dc-woocommerce-multi-vendor'));
                        }
                    }
                }
            }
        }
    }

    /**
     * Saves additional user fields to the database
     * function save_vendor_data
     * @access private
     * @param int $user_id
     * @return void
     */
    function save_vendor_data($user_id) {
        global $WCMp;
        $user = new WP_User($user_id);
        // only saves if the current user can edit user profiles
        if (!current_user_can('edit_user', $user_id))
            return false;
        $errors = new WP_Error();

        if ((!is_user_wcmp_vendor($user_id) && isset($_POST['role']) && $_POST['role'] == 'dc_vendor') || (isset($_REQUEST['new_role']) && $_REQUEST['new_role'] == 'dc_vendor') || (isset($_REQUEST['new_role2']) && $_REQUEST['new_role2'] == 'dc_vendor')) {
            $user->add_role('dc_vendor');
            $this->update_vendor_meta($user_id);
            $this->add_vendor_caps($user_id);
            $vendor = get_wcmp_vendor($user_id);
            $vendor->generate_term();
            $user_dtl = get_userdata(absint($user_id));
            $email = WC()->mailer()->emails['WC_Email_Approved_New_Vendor_Account'];
            $email->trigger($user_id, $user_dtl->user_pass);
        }

        $fields = $this->get_vendor_fields($user_id);

        $vendor = get_wcmp_vendor($user_id);
        foreach ($fields as $fieldkey => $value) {
            if (isset($_POST[$fieldkey])) {
                if ($fieldkey == 'vendor_page_title') {
                    if ($vendor && !$vendor->update_page_title(wc_clean($_POST[$fieldkey]))) {
                        $errors->add('vendor_title_exists', __('Title Update Error', 'dc-woocommerce-multi-vendor'));
                    } else {
                        wp_update_user(array('ID' => $user_id, 'display_name' => $_POST[$fieldkey]));
                    }
                } elseif ($fieldkey == 'vendor_page_slug') {
                    if ($vendor && !$vendor->update_page_slug(wc_clean($_POST[$fieldkey]))) {
                        $errors->add('vendor_slug_exists', __('Slug already exists', 'dc-woocommerce-multi-vendor'));
                    }
                } elseif ($fieldkey == 'vendor_publish_product') {
                    $user->remove_cap('publish_products');
                    update_user_meta($user_id, '_' . $fieldkey, wc_clean($_POST[$fieldkey]));
                } elseif ($fieldkey == 'vendor_publish_coupon') {
                    $user->remove_cap('publish_shop_coupons');
                    update_user_meta($user_id, '_' . $fieldkey, wc_clean($_POST[$fieldkey]));
                } elseif ($fieldkey == 'vendor_description') {
                    update_user_meta($user_id, '_' . $fieldkey, $_POST[$fieldkey]);
                } else {
                    update_user_meta($user_id, '_' . $fieldkey, wc_clean($_POST[$fieldkey]));
                }
            } else if (!isset($_POST['vendor_submit_product']) && $fieldkey == 'vendor_submit_product') {
                delete_user_meta($user_id, '_vendor_submit_product');
            } else if (!isset($_POST['vendor_submit_coupon']) && $fieldkey == 'vendor_submit_coupon') {
                delete_user_meta($user_id, '_vendor_submit_coupon');
            } else if (!isset($_POST['vendor_hide_description']) && $fieldkey == 'vendor_hide_description') {
                delete_user_meta($user_id, '_vendor_hide_description');
            } else if (!isset($_POST['vendor_hide_address']) && $fieldkey == 'vendor_hide_address') {
                delete_user_meta($user_id, '_vendor_hide_address');
            } else if (!isset($_POST['vendor_hide_message_to_buyers']) && $fieldkey == 'vendor_hide_message_to_buyers') {
                delete_user_meta($user_id, '_vendor_hide_message_to_buyers');
            } else if (!isset($_POST['vendor_hide_phone']) && $fieldkey == 'vendor_hide_phone') {
                delete_user_meta($user_id, '_vendor_hide_phone');
            } else if (!isset($_POST['vendor_hide_email']) && $fieldkey == 'vendor_hide_email') {
                delete_user_meta($user_id, '_vendor_hide_email');
            } else if (!isset($_POST['vendor_give_tax']) && $fieldkey == 'vendor_give_tax') {
                delete_user_meta($user_id, '_vendor_give_tax');
            } else if (!isset($_POST['vendor_give_shipping']) && $fieldkey == 'vendor_give_shipping') {
                delete_user_meta($user_id, '_vendor_give_shipping');
            } else if (!isset($_POST['vendor_turn_off']) && $fieldkey == 'vendor_turn_off') {
                delete_user_meta($user_id, '_vendor_turn_off');
            } else if (!isset($_POST['vendor_publish_product']) && $fieldkey == 'vendor_publish_product') {
                delete_user_meta($user_id, '_vendor_publish_product');
                if ($WCMp->vendor_caps->vendor_capabilities_settings('is_published_product')) {
                    $user->add_cap('publish_products');
                }
            } else if (!isset($_POST['vendor_publish_coupon']) && $fieldkey == 'vendor_publish_coupon') {
                if ($WCMp->vendor_caps->vendor_capabilities_settings('is_published_coupon')) {
                    $user->add_cap('publish_shop_coupons');
                }
                delete_user_meta($user_id, '_vendor_publish_coupon');
            } else if (!isset($_POST['vendor_is_policy_off']) && $fieldkey == 'vendor_is_policy_off') {
                delete_user_meta($user_id, '_vendor_is_policy_off');
            }
        }
        $this->user_change_cap($user_id);

        if (is_user_wcmp_vendor($user_id) && isset($_POST['role']) && $_POST['role'] != 'dc_vendor') {
            $vendor = get_wcmp_vendor($user_id);
            $user->remove_role('dc_vendor');
            if ($_POST['role'] != 'dc_pending_vendor') {
                $user->remove_role('dc_pending_vendor');
            }
            wp_delete_term($vendor->term_id, 'dc_vendor_shop');
        }
    }

    /**
     * Delete vendor data on user delete
     * function delete_vendor
     * @access private
     * @param int $user_id
     * @return void
     */
    function delete_vendor($user_id) {
        global $WCMp;
        $wcmp_vendor_registration_form_id = get_user_meta($user_id, 'wcmp_vendor_registration_form_id', true);
        if ($wcmp_vendor_registration_form_id) {
            wp_delete_post($wcmp_vendor_registration_form_id);
        }
        if (is_user_wcmp_vendor($user_id)) {

            $vendor = get_wcmp_vendor($user_id);

            do_action('delete_dc_vendor', $vendor);

            if (isset($_POST['reassign_user']) && !empty($_POST['reassign_user']) && ( $_POST['delete_option'] == 'reassign' )) {
                if (is_user_wcmp_vendor(absint($_POST['reassign_user']))) {
                    if ($products = $vendor->get_products(array('fields' => 'ids'))) {
                        foreach ($products as $product_id) {
                            $new_vendor = get_wcmp_vendor(absint($_POST['reassign_user']));
                            wp_set_object_terms($product_id, absint($new_vendor->term_id), $WCMp->taxonomy->taxonomy_name);
                        }
                    }
                } else {
                    wp_die(__('Select a Vendor.', 'dc-woocommerce-multi-vendor'));
                }
            }

            wp_delete_term($vendor->term_id, $WCMp->taxonomy->taxonomy_name);
        }
    }

    /**
     * Change user capability
     *
     * @access public
     * @return void
     */
    function user_change_cap($user_id) {
        global $WCMp;

        $user = new WP_User($user_id);

        $product_caps = array("edit_product", "delete_product", "edit_products", "delete_published_products", "delete_products", "edit_published_products");
        $is_submit_product = get_user_meta($user_id, '_vendor_submit_product', true);
        foreach ($product_caps as $product_cap_remove) {
            $user->remove_cap($product_cap_remove);
        }
        if ($WCMp->vendor_caps->vendor_capabilities_settings('is_submit_product')) {
            if ($is_submit_product) {
                $caps = array();
                $caps[] = "edit_product";
                $caps[] = "delete_product";
                $caps[] = "edit_products";
                $caps[] = "delete_products";
                if ($WCMp->vendor_caps->vendor_capabilities_settings('is_edit_delete_published_product')) {
                    $caps[] = "edit_published_products";
                    $caps[] = 'delete_published_products';
                }
                foreach ($caps as $cap) {
                    $user->add_cap($cap);
                }
            }
        }

        $coupon_caps = array("edit_shop_coupon", "delete_shop_coupon", "edit_shop_coupons", "delete_published_shop_coupons", "delete_shop_coupons", "edit_published_shop_coupons");
        $is_submit_coupon = get_user_meta($user_id, '_vendor_submit_coupon', true);
        foreach ($coupon_caps as $coupon_cap_remove) {
            $user->remove_cap($coupon_cap_remove);
        }
        if ($WCMp->vendor_caps->vendor_capabilities_settings('is_submit_coupon')) {
            if ($is_submit_coupon) {
                $caps = array();
                $caps[] = 'edit_shop_coupon';
                $caps[] = 'edit_shop_coupons';
                $caps[] = 'delete_shop_coupon';
                $caps[] = 'delete_shop_coupons';
                if ($WCMp->vendor_caps->vendor_capabilities_settings('is_edit_delete_published_coupon')) {
                    $caps[] = "edit_published_shop_coupons";
                    $caps[] = "delete_published_shop_coupons";
                }
                foreach ($caps as $cap) {
                    $user->add_cap($cap);
                }
            }
        }
    }

    function profile_admin_buffer_start() {
        ob_start(array($this, 'remove_plain_bio'));
    }

    function profile_admin_buffer_end() {
        $screen = get_current_screen();
        if (in_array($screen->id, array('users'))) {
            ob_end_flush();
        }
    }

    /**
     * remove_plain_bio
     *
     * @access public
     * @return $buffer
     */
    function remove_plain_bio($buffer) {
        $titles = array('#<h3>About Yourself</h3>#', '#<h3>About the user</h3>#');
        $buffer = preg_replace($titles, '<h3>Password</h3>', $buffer, 1);
        $biotable = '#<h3>Password</h3>.+?<table.+?/tr>#s';
        $buffer = preg_replace($biotable, '<h3>Password</h3> <table class="form-table">', $buffer, 1);
        return $buffer;
    }

    /**
     * Add vendor form in woocommece registration form
     *
     * @access public
     * @return void
     */
    public function wcmp_woocommerce_register_form() {
        global $WCMp;
        $customer_can = $WCMp->vendor_caps->vendor_general_settings('enable_registration');
        if ($customer_can) {
            ?>
            <tr>
            <p class="form-row form-row-wide">
                <input type="checkbox" name="pending_vendor" value="true"> <?php echo apply_filters('wcmp_vendor_registration_checkbox', __('Apply to become a vendor? ', 'dc-woocommerce-multi-vendor')); ?>
            </p>
            </tr>
            <?php
        }
    }

    /**
     * Add vendor form in woocommece registration form
     *
     * @access public
     * @return void
     */
    public function wcmp_woocommerce_add_vendor_form() {
        global $WCMp;
        $customer_can = $WCMp->vendor_caps->vendor_general_settings('enable_registration');
        if ($customer_can) {
            ?>
            <tr>
            <p class="form-row form-row-wide">
                <input type="checkbox" name="pending_vendor" value="true"> <?php echo apply_filters('wcmp_vendor_registration_checkbox', __('Apply to become a vendor? ', 'dc-woocommerce-multi-vendor')); ?>
            </p>
            </tr>
            <tr><input type="submit" name="vendor_apply" value="<?php _e('Save', 'dc-woocommerce-multi-vendor') ?>"></tr>
            <?php
        }
    }

    /**
     * created customer notification
     *
     * @access public
     * @return void
     */
    function wcmp_woocommerce_created_customer_notification() {
        if (isset($_POST['pending_vendor']) && !empty($_POST['pending_vendor'])) {
            remove_action('woocommerce_created_customer_notification', array(WC()->mailer(), 'customer_new_account'), 10, 3);
            add_action('woocommerce_created_customer_notification', array($this, 'wcmp_customer_new_account'), 10, 3);
        }
    }

    /**
     * Send mail on new vendor creation
     *
     * @access public
     * @return void
     */
    function wcmp_customer_new_account($customer_id, $new_customer_data = array(), $password_generated = false) {
        if (!$customer_id)
            return;
        $user_pass = !empty($new_customer_data['user_pass']) ? $new_customer_data['user_pass'] : '';
        $email = WC()->mailer()->emails['WC_Email_Vendor_New_Account'];
        $email->trigger($customer_id, $user_pass, $password_generated);
        $email_admin = WC()->mailer()->emails['WC_Email_Admin_New_Vendor_Account'];
        $email_admin->trigger($customer_id, $user_pass, $password_generated);
    }

    /**
     * WCMp Order available emails
     *
     * @param array $available_emails
     * @return available_emails
     */
    public function wcmp_order_emails_available($available_emails) {
        $available_emails[] = 'vendor_new_order';

        return $available_emails;
    }

    /**
     * update_vendor_meta
     *
     * @param  $user_id
     */
    public function update_vendor_meta($user_id) {
        update_user_meta($user_id, '_vendor_submit_product', 'Enable');
        update_user_meta($user_id, '_vendor_submit_coupon', 'Enable');

//        update_user_meta($user_id, '_vendor_image', '');
//        update_user_meta($user_id, '_vendor_banner', '');
//        update_user_meta($user_id, '_vendor_address_1', '');
//        update_user_meta($user_id, '_vendor_city', '');
//        update_user_meta($user_id, '_vendor_state', '');
//        update_user_meta($user_id, '_vendor_country', '');
//        update_user_meta($user_id, '_vendor_phone', '');
//        update_user_meta($user_id, '_vendor_postcode', '');
    }

}
?>