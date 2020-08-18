=== No CAPTCHA reCAPTCHA for WooCommerce ===
Contributors: Collizo4sky
Donate link: https://w3guy.com/about/
Tags: woocommerce, captcha, recaptcha, form, security, login, registration, comments, spam, spammers, bots, anti-spam, anti spam
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 1.2.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Protect WooCommerce login, registration and password reset form against spam using Google's No CAPTCHA reCAPTCHA.

== Description ==

A simple plugin for adding the new No CAPTCHA reCAPTCHA to WooCommerce login, registration and password reset form to protect against spam.

### Features
*   Option to activate CAPTCHA in WooCommerce login, registration and password reset form page.
*   Choose a theme for the CAPTCHA.
*   Auto-detects the user's language

**Note:** Multiple instance of reCAPTCHA can not appear in the same page i.e only a single reCAPTCHA can exist per web page.
As a result, if you activate the CAPTCHA in both login and registration form, in WooCommerce checkout and My Account page, the CAPTCHA will appear only
in the login form.

### Plugins you will like
* **[ProfilePress](https://wordpress.org/plugins/ppress/)**: A shortcode based WordPress form builder that makes building custom login, registration and password reset forms stupidly simple. [More info here](https://profilepress.net)
* **[MailOptin](https://mailoptin.io/)** - The best WordPress email optin forms, email automation & newsletters plugin in the market.


== Installation ==

Installing No CAPTCHA reCAPTCHA is just like any other WordPress plugin.
Navigate to your WordPress “Plugins” page, inside of your WordPress dashboard, and follow these instructions:

1. In the search field enter **No Captcha Recaptcha for WooCommerce**. Click "Search Plugins", or hit Enter.
1. Select **No Captcha Recaptcha for WooCommerce** and click either "Details" or "Install Now".
1. Once installed, click "Activate".

== Frequently Asked Questions ==

= Why isn't CAPTCHA showing in both WooCommerce login and registration form? =

Multiple instance of reCAPTCHA can not appear in the same page i.e only a single reCAPTCHA can exist per web page. That's how reCAPTCHA was programmed.

If you activate the CAPTCHA in both login and registration form, in WooCommerce checkout and My Account page, the CAPTCHA will appear only
in the login form.

Any question? post it in the support forum.

== Screenshots ==

1. Add your reCAPTCHA keys.
2. Select where to activate.
3. Plugin general settings.
4. CAPTCHA in WooCommerce registration form
5. CAPTCHA in WooCommerce login form

== Changelog ==

= 1.2.6 =
* Tuned down admin notices.

= 1.2.5 =
* Improve compatibility with WordPress 5.0

= 1.2.4 =
* Make admin notice dismiss button more obvious.

= 1.2.3 =
* Small fix and improvement.

= 1.2.2 =
* Fixed bug where recaptcha on lost password page was broken.
* Fixed bug where this plugin was causing wp core lost password from working.

= 1.2.1 =
* Changed default error message to "Please confirm you are not a robot"
* Fix undefined index PHP notices
* Remove recaptcha check on checkout page

= 1.2 =
* Fix compatibility with "No CAPTCHA reCAPTCHA plugin" (https://wordpress.org/plugins/no-captcha-recaptcha/)

= 1.1 =
* Added captcha to WooCommerce password reset form.


= 1.0.4 =
* Fix error where Captcha could be bypassed by disabling Javascript

= 1.0.3 =
* Fixes call to undefine method
* fixes header already sent error when adding recaptcha keys

= 1.0.2 =
* Bug fixes and tweaks

= 1.0.1 =
* Removed output buffering
* No XSS security vulnerability. plugin safe

= 1.0 =
* stable version

= 0.9 =
* Initial commit