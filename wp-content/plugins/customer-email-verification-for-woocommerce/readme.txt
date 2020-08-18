=== Customer verification for WooCommerce   ===
Contributors: zorem
Tags: woocommerce, email address verification, email validation, woocommerce registration, customer account, customer verification, registration verification, woocommerce signup spam
Requires at least: 4.0
Requires PHP: 5.2.4
Tested up to: 5.4
Stable tag: 5.3
License: GPLv2 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This Customer verification will reduce WooCommerce registration spam in your store and will require them to verify their email address by sending a verification link to the email address which they registered to their account. You can allow the customer to log-in to their account when they first register and to require them to verify the email on the next login or you can restrict access to their account until they verify their email.

== Key Features==

* Require email verification for new user registrations
* Option to add the verification link in the WooCommerce new account email or to send the verification in a separate email
* Option to resend the verification link on login form in cases that the customer lost the first verification email.
* Customize the separate email verification header/subject/message
* Option to customize the verification message
* Option to allow the customer to enter his account after the first time he registered without email verification.
* Skip email verification for selected user roles
* Customize the frontend messages
* Redirect users to any page on your website after successful validation
* Option for manual email verification from admin
* Email verification status will display for each user on admin
* Option to bulk actions resend verification email and verify user email address
 
== Installation ==

1. Upload the folder `customer-email-verification-for-woocommerce` to the `/wp-content/plugins/` folder
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.1 =
* Enhancement - merged the verification status fields in WordPress users admin and added actions icons.
* Enhancement - added option to  bulk actions resend verification email and verify user email address
* Fix - auto refresh the permalink

= 1.0.9 =
* Enhancement - Added a filter in Users list page for Verified and Non verified users
* Enhancement - Added a option for "Allow first login after registration without email verification"
* Fix - Fixed warnings Undefined offset: 0 in /includes/class-wc-customer-email-verification-admin.php on line 439

= 1.0.8 =
* Enhancement - change Navigation label
* Fix - Fixed settings page design issue
* Fix - Fixed user page layout issue
* Fix - Fixed warnings in Undefined index 0

= 1.0.7 =
* Dev - added functionality for skip email verification for already registered user

= 1.0.6 =
* Enhancement - Change design of enter your pin input box in email verification form
* Enhancement - Updated design of email verification form
* Dev - Change default value for email heading and email content

= 1.0.5 =
* Fix - Fixed Separate email subject issue
* Fix - Fixed WooCommerce email verification endpoint not found issue

= 1.0.4 =
* Enhancement - Added Enable/Disable option for customer email verification
* Enhancement - Added spinner and settings save message in settings page
* Fix - Fixed warnings in front end page

= 1.0.3 =
* Dev - Updated email verification process and added pin verification functionality
* Dev - After login block all my account page until user verify his email

= 1.0.2 =
* Dev - Tested with WordPress 5.4 and WooCommerce 4.0

= 1.0.1 =
* Fix - Fixed issue with WooCommerce email
* Fix - Fixed skip email verification for selected roles option save issue
* Fix - Fixed warnings from users list page

= 1.0 =
* Initial version.
