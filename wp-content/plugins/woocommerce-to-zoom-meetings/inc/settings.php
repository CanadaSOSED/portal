<?php

/**
* 
*
*
* Add our new settings tab and settings
*/
class WC_Settings_WooCommerce_To_Zoom_Meetings {
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_zoommeetings', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_zoommeetings', __CLASS__ . '::update_settings' );
    }
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['zoommeetings'] = __( 'Zoom Meetings', 'woocommerce-to-zoom-meetings' );
        return $settings_tabs;
    }

    public static function output_right_sidebar_content(  ) {
        
        //frequently asked questions
        $faq = array(
            __('How do I actually use this plugin?','woocommerce-to-zoom-meetings') => __('Please follow the following steps:<br><ol>
                <li>Enter the order/purchase email at the top of this settings page to receive automatic updates and support.</li>  
                <li>Click the big blue connect button on this page which will connect your website to your Zoom account. It\'s working when you see the status next to the button saying "Connected".</li>
                <li>Create a WooCommerce product or go to an existing WooCommerce product and ensure you set the product as virtual and add any other parameters you want for the product like price etc. and then you will a meeting selection tab. This is where you can select your meeting. Once you are done click save settings.</li>
                <li>That\'s it. Once someone purchases your product they will be automatically registered for the meeting.</li>
            </ol>
            
            ','woocommerce-to-zoom-meetings'),

            __('Users are not being registered for the Zoom meeting?','woocommerce-to-zoom-meetings') => __('There are a couple of reasons this could occur:
                <br></br>
                <ol>
                    <li><strong>Orders aren\'t being marked as complete</strong> - Only completed orders are synced to Zoom. If you\'re using a payment method like PayPal or Stripe in test mode often they won\'t mark the orders as complete, however in live mode they do mark them as complete automatically. So for testing purposes just mark the order as complete from your <a href="'.get_admin_url().'edit.php?post_type=shop_order">orders</a> page. If you are accepting cash or cheque payments or some kind of offline payment and you want to mark orders automatically as complete you can use this code snippet <a target="_blank" href="https://docs.woocommerce.com/document/automatically-complete-orders/">here</a> or you can use this premium <a target="_blank" href="https://woocommerce.com/products/woocommerce-order-status-control/">plugin</a>.</li>
                    <li><strong>You are using an email on your Zoom account during your testing</strong> - If you are testing out the plugin you might have entered in your Zoom account email or the email of a user on your Zoom account during the checkout process. You can not register for your own meeting. So for testing purposes try a different email address and it should work ok.</li>
                    <li><strong>Ensure registration is required</strong> - In Zoom, on the meeting edit page, make sure for "Registration" you have the "Required" checkbox checked.</li>
                    <li><strong>You have only authenticated users can join</strong> - In Zoom, on the meeting edit page, make sure for "Only authenticated users can join" is unchecked.</li>
                </ol>','woocommerce-to-zoom-meetings'),

            __('I just added a new meeting in Zoom but can\'t see it on the product page?','woocommerce-to-zoom-meetings') => __('Meetings are cached and get refreshed every hour so there may be some delay between updates in Zoom vs what is shown on your site. You can clear the cache though by clicking the link just above the save settings button on this settings page.','woocommerce-to-zoom-meetings'),

            __('I can\'t see the Zoom meeting selection tab on the product page?','woocommerce-to-zoom-meetings') => __('Please ensure you set the product as virtual - only once a product is set as virtual will the tab appear. Please check out this image <a target="_blank" href="https://northernbeacheswebsites.com.au/root-nbw/wp-content/uploads/2019/12/Zoom-Webinar-Product-Edit-Page-2048x586.jpg">here</a> showing this. Also try re-connecting again by clicking the "Connect with Zoom" button on this page.','woocommerce-to-zoom-meetings'),

            __('I don\'t want to select an existing meeting, I want to create a meeting in Zoom and add registrants to that new meeting using WooCommerce Bookings!','woocommerce-to-zoom-meetings') => __('We currently support an integration with <a target="_blank" href="https://woocommerce.com/products/woocommerce-bookings/">WooCommerce Bookings</a> so you can do just that. All you need to do is create your product and instead of selecting an existing meeting from the dropdown, just select the first item called "WooCommerce Bookings" and that\'s it!','woocommerce-to-zoom-meetings'),

            __('How do users receive notification of successful registration?','woocommerce-to-zoom-meetings') => __('This is all handled by Zoom and they provide a range of options to customise the emails. Please click <a target="_blank" href="https://support.zoom.us/hc/en-us/articles/203686335-Webinar-Email-Settings">here</a> to learn more about this. There is also a setting on this page called "Enable Completed Order Email Registration Links" which adds to the WooCommerce completed order email (so sent to the purchaser) which contains a list of registrants and their respective registration links.','woocommerce-to-zoom-meetings'),

            __('I only want to see upcoming meetings in the dropdown on the product page?','woocommerce-to-zoom-meetings') => __('You can use the following filter to only show upcoming meetings in the dropdown (note, this code should be placed in your themes functions.php file or a custom plugin):<br><code>add_filter( \'woocommerce_to_zoom_meetings_get_meetings_args\', \'only_upcoming_meetings\',10,1);<br>
            function only_upcoming_meetings($args) {<br>
                return $args.\'&type=upcoming\';<br>
            } </code>','woocommerce-to-zoom-meetings'),

            __('I am having some kind of other issue?','woocommerce-to-zoom-meetings') => __('First make sure you are using the latest version of this plugin; you can check for updates from your main <a href="'.get_admin_url().'plugins.php">plugins page</a>. If this does not solve your issue please contact me <a target="_blank" href="https://northernbeacheswebsites.com.au/support/">here</a>. Please note, I am located in Sydney, Australia so there may be some time difference in my reply however you will most certainly receive a response within 24 hours on weekdays.','woocommerce-to-zoom-meetings'),
        );


        //start output
        $html = '';
        $html .= '<div class="faq-container-meetings">';
            //do heading
            $html .= '<ul class="zoom-faq">';
            $html .= '<h2>'.__('Frequently Asked Questions','woocommerce-to-zoom-meetings').'</h2>';

                foreach($faq as $question => $answer){
                    $html .= '<li>';
                        $html .= '<h2 class="question"><span class="dashicons dashicons-plus"></span> '.$question.'</h2>';
                        $html .= '<span class="answer">'.$answer.'</span>';
                    $html .= '</li>';
                }
                

            $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }



    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {

        woocommerce_admin_fields( self::get_settings() );
        echo self::output_right_sidebar_content();
        
    }
    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }
    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {

        $settings = array(


            //updates
            array(
                'name'     => __( 'Licence Settings', 'woocommerce-to-zoom-meetings' ),
                'type'     => 'title',
            ),
            array(
                'name' => __( 'Order Email', 'woocommerce-to-zoom-meetings' ),
                'type' => 'text',
                'desc_tip' => __( 'The email used to purchase the plugin.', 'woocommerce-to-zoom-meetings' ),
                'id'   => 'wc_settings_zoom_meetings_order_email'
            ),
            array(
                'name' => __( 'Order ID', 'woocommerce-to-zoom-meetings' ),
                'type' => 'text',
                'desc_tip' => __( 'This order id number was sent to your email address upon purchase of the plugin.', 'woocommerce-to-zoom-meetings' ),
                'id'   => 'wc_settings_zoom_meetings_order_id'
            ),
            array(
                'name' => __( 'Sandbox Mode', 'woocommerce-to-zoom-meetings' ),
                'type' => 'checkbox',
                'desc_tip' => __( 'It is advised to keep this unchecked unless directed otherwise.', 'woocommerce-to-zoom-meetings' ),
                'id'   => 'wc_settings_zoom_meetings_sandbox_mode'
            ),

            
            //section end
            array( 'type' => 'sectionend' ),

            array(
                'name'     => __( 'Completed Order Notification', 'woocommerce-to-zoom-meetings' ),
                'type'     => 'title',
            ),
            array(
                'name' => __( 'Enable Completed Order Email Registration Links', 'woocommerce-to-zoom-meetings' ),
                'type' => 'checkbox',
                'desc_tip' => __( 'When checked, we will add a list on your WooCommerce Completed Order email which will contain a list of meeting registrants and their respective registration links. Please ensure you have enabled your completed order email <a href="'.get_admin_url().'admin.php?page=wc-settings&tab=email">here</a>.', 'woocommerce-to-zoom-meetings' ),
                'id'   => 'wc_settings_zoom_meetings_enable_completed_order_email'
            ),


            //section end
            array( 'type' => 'sectionend' ),

            array(
                'name'     => __( 'Past Meeting Products', 'woocommerce-to-zoom' ),
                'type'     => 'title',
            ),
            array(
                'name' => __( 'What do you want to do with old meeting products in WooCommerce?', 'woocommerce-to-zoom-meetings' ),
                'type' => 'select',
                'id'   => 'wc_settings_zoom_meetings_old_meeting_products',
                'options'  => array('nothing'=>__('Do nothing','woocommerce-to-zoom-meetings'),'draft'=>__('Make them a draft','woocommerce-to-zoom-meetings'),'delete'=>__('Delete them','woocommerce-to-zoom-meetings'))
            ),



            //section end
            array( 'type' => 'sectionend' ),

            //connection
            array(
                'name'     => __( 'Connect with Zoom Meetings', 'woocommerce-to-zoom-meetings' ),
                'type'     => 'title',
                'desc'  => woocommerce_to_zoom_meetings_connect_button(),
            ),
            //section end
            array( 'type' => 'sectionend' ),

 

           

        );

        return apply_filters( 'wc_settings_zoom_meetings', $settings );
    }
}
WC_Settings_WooCommerce_To_Zoom_Meetings::init();
/**
* 
*
*
* Output appropriate code for connect button and disconnect link
*/
function woocommerce_to_zoom_meetings_connect_button(){


        //output connect button

        $connectUrl = 'https://zoom.us/oauth/authorize?response_type=code&';
        $connectUrl .= 'client_id='.woocommerce_to_zoom_meetings_get_client_id();
        $connectUrl .= '&';
        $connectUrl .= 'redirect_uri='.woocommerce_to_zoom_meetings_get_redirect_uri();
        $connectUrl .= '&';
        $connectUrl .= 'state='.get_admin_url().'zoommeeting';

        $return_data = '<a target="_self" href="'.$connectUrl.'" id="zoom-connect-button" class="button-secondary"><span class="video-icon dashicons dashicons-video-alt2"></span> '.__('Connect with Zoom','woocommerce-to-zoom-meetings').'</a>';

        //lets also show a connected status
        if(get_option('wc_settings_zoom_refresh_token') && strlen(get_option('wc_settings_zoom_refresh_token'))>0){
            $return_data .= '<span class="connection-container"><span class="status-text">'.__('Status:','woocommerce-to-zoom-meetings').'</span> <span class="connected-text">'.__('Connected','woocommerce-to-zoom-meetings').'</span> <a class="disconnect-from-zoom-meetings" href="#">'.__('Disconnect Now','woocommerce-to-zoom-meetings').'</a></span>';   
        } else {
            $return_data .= '<span class="connection-container"><span class="status-text">'.__('Status:','woocommerce-to-zoom-meetings').'</span> <span class="disconnected-text">'.__('Disconnected','woocommerce-to-zoom-meetings').'</span></span>';   
        }


        //lets also output the ability to clear transients
        $return_data .= '<div class="transient-settings"><a id="clear-zoom-meetings-transients" href="#">'.__('Clear Cache','woocommerce-to-zoom-meetings').'</a> <em>'.__('(If you update your registration form fields in Zoom, the form fields will be cached for an hour so they may not show straight away, so use this button to clear the cache)','woocommerce-to-zoom-meetings').'</em></div>';   

        // $return_data .= '<br></br>Sandbox mode: '.get_option('wc_settings_zoom_meetings_sandbox_mode');
        

        return $return_data;

  
}


?>