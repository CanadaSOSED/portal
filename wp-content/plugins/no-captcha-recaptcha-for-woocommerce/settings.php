<?php
ob_start();

class WC_Ncr_Settings_Page
{

    public static function initialize()
    {
        add_action('admin_menu', array(__CLASS__, 'register_menu_page'));
    }


    public static function register_menu_page()
    {

        add_submenu_page(
            'woocommerce',
            __('No CAPTCHA reCAPTCHA for WooCommerce', 'woocommerce'),
            __('No CAPTCHA', 'woocommerce'),
            'manage_options',
            'wc-ncr',
            array(
                __CLASS__,
                'settings_page',
            )
        );

    }

    public static function settings_page()
    {

        $wc_ncr_options = get_option('wc_ncr_options');
        $site_key = isset($wc_ncr_options['site_key']) ? $wc_ncr_options['site_key'] : '';
        $secrete_key = isset($wc_ncr_options['secrete_key']) ? $wc_ncr_options['secrete_key'] : '';

        $captcha_wc_login = isset($wc_ncr_options['captcha_wc_login']) ? $wc_ncr_options['captcha_wc_login'] : '';
        $captcha_wc_registration = isset($wc_ncr_options['captcha_wc_registration']) ? $wc_ncr_options['captcha_wc_registration'] : '';
        $captcha_wc_lost_password = isset($wc_ncr_options['captcha_wc_lost_password']) ? $wc_ncr_options['captcha_wc_lost_password'] : '';

        $theme = isset($wc_ncr_options['theme']) ? $wc_ncr_options['theme'] : '';
        $language = isset($wc_ncr_options['language']) ? $wc_ncr_options['language'] : '';
        $error_message = isset($wc_ncr_options['error_message']) ? $wc_ncr_options['error_message'] : '';

        // call to save the setting options
        self::save_options();
        ?>
        <style>
            input[type='text'], textarea, select {
                width: 600px;
            }
        </style>
        <div class="wrap">

            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('No CAPTCHA reCAPTCHA for WooCommerce', 'wc-no-captcha'); ?></h2>

            <p><?php _e('Protect WooCommerce login registration and lost password form against spam using Google\'s No CAPTCHA reCAPTCHA.', 'wc-no-captcha'); ?></p>

            <?php
            if (isset($_GET['settings-updated']) && ($_GET['settings-updated'])) {
                echo '<div id="message" class="updated"><p><strong>' . __('Settings saved.', 'wc-no-captcha') . '</strong></p></div>';
            }
            ?>
            <div id="poststuff">

                <div id="post-body" class="metabox-holder columns-2">

                    <!-- main content -->
                    <div id="post-body-content">

                        <div class="meta-box-sortables ui-sortable">

                            <form method="post">

                                <div class="postbox">

                                    <div title="Click to toggle" class="handlediv"><br></div>
                                    <h3 class="hndle"><span><?php _e('reCAPTCHA Keys', 'wc-no-captcha'); ?></span>
                                    </h3>

                                    <div class="inside">
                                        <table class="form-table">
                                            <tr>
                                                <th scope="row"><label
                                                            for="site-key"><?php _e('Site key', 'wc-no-captcha'); ?></label>
                                                </th>
                                                <td>
                                                    <input id="site-key" type="text" name="wc_ncr_options[site_key]"
                                                           value="<?php echo $site_key; ?>">

                                                    <p class="description">
                                                        <?php _e('Used for displaying the CAPTCHA. Grab it <a href="https://www.google.com/recaptcha/admin" target="_blank">Here</a>', 'wc-no-captcha'); ?>

                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label
                                                            for="secrete-key"><?php _e('Secret key', 'wc-no-captcha'); ?></label>
                                                </th>
                                                <td>
                                                    <input id="secrete-key" type="text" name="wc_ncr_options[secrete_key]"
                                                           value="<?php echo $secrete_key; ?>">

                                                    <p class="description">
                                                        <?php _e('Used for communication between your site and Google. Grab it <a href="https://www.google.com/recaptcha/admin" target="_blank">Here</a>', 'wc-no-captcha'); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <p>
                                            <?php wp_nonce_field('wc_ncr_settings_nonce'); ?>
                                            <input class="button-primary" type="submit" name="settings_submit"
                                                   value="Save All Changes">
                                        </p>
                                    </div>
                                </div>

                                <div class="postbox">

                                    <div title="Click to toggle" class="handlediv"><br></div>
                                    <h3 class="hndle"><span><?php _e('Display Settings', 'wc-no-captcha'); ?></span>
                                    </h3>

                                    <div class="inside">
                                        <table class="form-table">
                                            <tr>
                                                <th scope="row">
                                                    <label for="login"><?php _e('Login Form', 'wc-no-captcha'); ?></label>
                                                </th>
                                                <td>
                                                    <input id="login" type="checkbox" name="wc_ncr_options[captcha_wc_login]"
                                                           value="yes" <?php checked($captcha_wc_login, 'yes') ?>>

                                                    <p class="description">
                                                        <?php _e('Check to enable CAPTCHA in WooCommerce login form', 'wc-no-captcha'); ?>

                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label
                                                            for="registration"><?php _e('Registration Form', 'wc-no-captcha'); ?></label>
                                                </th>
                                                <td>
                                                    <input id="registration" type="checkbox" name="wc_ncr_options[captcha_wc_registration]"
                                                           value="yes" <?php checked($captcha_wc_registration, 'yes') ?>>

                                                    <p class="description">
                                                        <?php _e('Check to enable CAPTCHA in WooCommerce registration form', 'wc-no-captcha'); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label
                                                            for="lost_password"><?php _e('Lost Password Form', 'wc-no-captcha'); ?></label>
                                                </th>
                                                <td>
                                                    <input id="lost_password" type="checkbox" name="wc_ncr_options[captcha_wc_lost_password]"
                                                           value="yes" <?php checked($captcha_wc_lost_password, 'yes') ?>>

                                                    <p class="description">
                                                        <?php _e('Check to enable CAPTCHA in WooCommerce lost password form', 'wc-no-captcha'); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <p>
                                            <?php wp_nonce_field('wc_ncr_settings_nonce'); ?>
                                            <input class="button-primary" type="submit" name="settings_submit"
                                                   value="Save All Changes">
                                        </p>
                                    </div>
                                </div>


                                <div class="postbox">

                                    <div class="handlediv"><br></div>
                                    <h3 class="hndle"><span><?php _e('General Settings', 'wc-no-captcha'); ?></span>
                                    </h3>

                                    <div class="inside">
                                        <table class="form-table">
                                            <tr>
                                                <th scope="row"><label
                                                            for="theme"><?php _e('Theme', 'wc-no-captcha'); ?></label>
                                                </th>
                                                <td>
                                                    <select id="theme" name="wc_ncr_options[theme]">
                                                        <option value="light" <?php selected('light', $theme); ?>>Light</option>
                                                        <option value="dark" <?php selected('dark', $theme); ?>>Dark</option>
                                                    </select>

                                                    <p class="description">
                                                        <?php _e('The theme colour of the widget.', 'wc-no-captcha'); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <table class="form-table">
                                            <tr>
                                                <th scope="row"><label
                                                            for="theme"><?php _e('Language', 'wc-no-captcha'); ?></label>
                                                </th>
                                                <td>
                                                    <select id="theme" name="wc_ncr_options[language]">
                                                        <?php
                                                        $languages = array(
                                                            __('Auto Detect', 'wc-no-captcha') => '',
                                                            __('English', 'wc-no-captcha') => 'en',
                                                            __('Arabic', 'wc-no-captcha') => 'ar',
                                                            __('Bulgarian', 'wc-no-captcha') => 'bg',
                                                            __('Catalan Valencian', 'wc-no-captcha') => 'ca',
                                                            __('Czech', 'wc-no-captcha') => 'cs',
                                                            __('Danish', 'wc-no-captcha') => 'da',
                                                            __('German', 'wc-no-captcha') => 'de',
                                                            __('Greek', 'wc-no-captcha') => 'el',
                                                            __('British English', 'wc-no-captcha') => 'en_gb',
                                                            __('Spanish', 'wc-no-captcha') => 'es',
                                                            __('Persian', 'wc-no-captcha') => 'fa',
                                                            __('French', 'wc-no-captcha') => 'fr',
                                                            __('Canadian French', 'wc-no-captcha') => 'fr_ca',
                                                            __('Hindi', 'wc-no-captcha') => 'hi',
                                                            __('Croatian', 'wc-no-captcha') => 'hr',
                                                            __('Hungarian', 'wc-no-captcha') => 'hu',
                                                            __('Indonesian', 'wc-no-captcha') => 'id',
                                                            __('Italian', 'wc-no-captcha') => 'it',
                                                            __('Hebrew', 'wc-no-captcha') => 'iw',
                                                            __('Jananese', 'wc-no-captcha') => 'ja',
                                                            __('Korean', 'wc-no-captcha') => 'ko',
                                                            __('Lithuanian', 'wc-no-captcha') => 'lt',
                                                            __('Latvian', 'wc-no-captcha') => 'lv',
                                                            __('Dutch', 'wc-no-captcha') => 'nl',
                                                            __('Norwegian', 'wc-no-captcha') => 'no',
                                                            __('Polish', 'wc-no-captcha') => 'pl',
                                                            __('Portuguese', 'wc-no-captcha') => 'pt',
                                                            __('Romanian', 'wc-no-captcha') => 'ro',
                                                            __('Russian', 'wc-no-captcha') => 'ru',
                                                            __('Slovak', 'wc-no-captcha') => 'sk',
                                                            __('Slovene', 'wc-no-captcha') => 'sl',
                                                            __('Serbian', 'wc-no-captcha') => 'sr',
                                                            __('Swedish', 'wc-no-captcha') => 'sv',
                                                            __('Thai', 'wc-no-captcha') => 'th',
                                                            __('Turkish', 'wc-no-captcha') => 'tr',
                                                            __('Ukrainian', 'wc-no-captcha') => 'uk',
                                                            __('Vietnamese', 'wc-no-captcha') => 'vi',
                                                            __('Simplified Chinese', 'wc-no-captcha') => 'zh_cn',
                                                            __('Traditional Chinese', 'wc-no-captcha') => 'zh_tw'
                                                        );

                                                        foreach ($languages as $key => $value) {
                                                            echo "<option value='$value'" . selected($value, $language, true) . ">$key</option>";
                                                        }
                                                        ?>
                                                    </select>

                                                    <p class="description">
                                                        <?php _e('Forces the widget to render in a specific language', 'wc-no-captcha'); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="form-table">
                                            <tr>
                                                <th scope="row"><label
                                                            for="message"><?php _e('Error Message', 'wc-no-captcha'); ?></label>
                                                </th>
                                                <td>
                                                    <input id="message" type="text" name="wc_ncr_options[error_message]"
                                                           value="<?php echo $error_message; ?>">

                                                    <p class="description">
                                                        <?php _e('Message or text to display when CAPTCHA is ignored or the test is failed.', 'wc-no-captcha'); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <p>
                                            <?php wp_nonce_field('settings_nonce'); ?>
                                            <input class="button-primary" type="submit" name="settings_submit"
                                                   value="Save All Changes">
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">

                        <div class="meta-box-sortables">

                            <div class="postbox">
                                <div class="handlediv"><br></div>
                                <h3 class="hndle" style="text-align: center;">
                                    <span><?php _e('Developer', 'ncr-captcha'); ?></span>
                                </h3>

                                <div class="inside">
                                    <div style="text-align: center; margin: auto"><?php _e('Made with lots of love by', 'ncr-captcha'); ?>
                                        <br>
                                        <?php /* translators: plugin author name */ ?>
                                        <a target="_blank" href="https://mailoptin.io?utm_source=woocommerce_no_captcha_recaptcha&utm_medium=wp_dashboard&utm_campaign=sidebar-banner"><strong><?php _e('MailOptin Team', 'ncr-captcha'); ?></strong></a>
                                    </div>
                                </div>
                            </div>

                            <div class="postbox" style="text-align: center">
                                <div class="handlediv"><br></div>
                                <h3 class="hndle ui-sortable-handle"><span>ProfilePress Plugin</span></h3>

                                <div class="inside">
                                    <p>A shortcode based WordPress form builder that makes building custom login, registration and password reset forms stupidly simple.</p>
                                    <strong>Features</strong>
                                    <ul>
                                        <li>Unlimited front-end login forms</li>
                                        <li>Unlimited front-end registration forms</li>
                                        <li>Unlimited password reset forms.</li>
                                        <li>Automatic login after registration.</li>
                                        <li>Social Logins.</li>
                                        <li>Custom user redirect users after login & logout</li>
                                        <li>One-click widget creator.</li>
                                        <li>And lots more.</li>
                                        <li></li>
                                    </ul>
                                    <div><a href="https://wordpress.org/plugins/ppress/" target="_blank">
                                            <button class="button-primary" type="button">Download for Free</button>
                                        </a></div>
                                </div>
                            </div>

                            <div class="postbox" style="text-align: center">
                                <div class="handlediv"><br></div>
                                <h3 class="hndle ui-sortable-handle"><span>MailOptin WordPress Plugin</span></h3>

                                <div class="inside">
                                    <p>Convert your website visitors into email subscribers, nurture & engage them with beautifully designed automated newsletters. All in WordPress.</p>
                                    <div style="margin:10px 0">
                                        <a href="https://mailoptin.io/pricing/?discount=10PERCENTOFF&utm_source=woocommerce_no_captcha_recaptcha&utm_medium=wp_dashboard&utm_campaign=sidebar-banner" target="_blank"><img width="250" src="https://i0.wp.com/mailoptin.io/wp-content/uploads/2016/01/mailoptin10off.jpg">
                                        </a></div>
                                </div>
                            </div>

                            <div class="postbox">
                                <div class="handlediv"><br></div>
                                <h3 class="hndle" style="text-align: center;">
                                    <span><?php _e('Support Plugin', 'wc-no-captcha'); ?></span>
                                </h3>

                                <div class="inside">
                                    <div style="text-align: center; margin: auto">
                                        <ul>
                                            <li>Leave a positive review on the plugin's
                                                <a href="https://wordpress.org/support/view/plugin-reviews/no-captcha-recaptcha-for-woocommerce">WordPress listing</a>
                                            </li>
                                            <li>
                                                <a href="http://twitter.com/home?status=I%20love%20this%20WordPress%20plugin!%20http://wordpress.org/plugins/no-captcha-recaptcha-for-woocommerce/" target="_blank">Share your thoughts on Twitter</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }


    public static function save_options()
    {
        if (isset($_POST['settings_submit']) && check_admin_referer('settings_nonce', '_wpnonce')) {

            $saved_options = $_POST['wc_ncr_options'];

            update_option('wc_ncr_options', $saved_options);

            wp_redirect('?page=wc-ncr&settings-updated=true');
            exit;
        }
    }
}

ob_clean();
