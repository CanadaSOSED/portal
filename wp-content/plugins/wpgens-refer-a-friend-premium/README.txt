=== Refer A Friend for WooCommerce by WPGens PREMIUM ===
Contributors: wpgens
Tags: refer a friend, refer, referral, woocommerce, ecommerce, affiliate, referral marketing,reward, sponsors, sponsorship
Requires at least: 4.4
Tested up to: 5.4
Stable tag: 2.3.11
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Referral System for WooCommerce. Each customer has referral link that rewards them with a coupon after someone makes a purchase through their link

== Description ==

### WordPress Refer A Friend Plugin ###

This plugin will enable you to create a simple but powerful referral system on your website. Instead of giving money to your referrals, you are rewarding them with coupons that they can use to get discounts when buying on your website, or even free products, depending on a coupon settings.

The plugin will create a unique URL for each of your WooCommerce members, link that is visible on their account page. In premium version you can show it with shortcode as well. Members can use this referral link to invite people to your site, and every time someone comes to your site through their link and makes a purchase, you will reward them with a coupon.

= Coupons Are Connected With WooCoupons. This Allows You To: =

* set the value of each coupon;
* define the type of the coupon;
* define the minimum order;
* determine whether the coupon is product specific or not; (PREMIUM only)
* set a coupon expiry date (PREMIUM only)

After order is marked as complete, coupon is sent to a person who referred you a customers. Every time referral receives a coupon, it will be shown on his account page and only he can use it.

As with our other plugins, refer a friend is coded with best practice, it is super light and will not slow down your site.

== Installation ==

1. Upload the Refer a Friend plugin to your site, Activate it.
2. Go to WooCommerce -> Settings -> Refer a friend tab to set it up.
3. Start earning more money! :)

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 2.3.11 =
* Better WPML support
* WhatsApp guest share fix
* Buyer email subject fix
* Minor coupon issue fixed
* Fix empty referrals applied on subscription renewals.
* Added button to remove email field
* Fix for number of referrals per screen
* Fix events timezone.

= 2.3.10 =
* Support for Aelia currency switcher for minimum order amount.
* Missing raf link bug fix.

= 2.3.9 =
* Security update
* Change in woo subscription, ability to disable shipping costs when applying on automatic subscription.

= 2.3.8 =
* Guests can no longer keep using same referral code to get a discount on the same email address. 

= 2.3.7 =
* Exclude sales item for coupon codes
* Japanese translation

= 2.3.6 =
* Fixed issue with referrals not showing even if checkbox to hide the ones with zero order is not checked.
* Notice error removed.

= 2.3.5 =
* Added option to disable referral codes for users without orders.
* Prevent the use of false emails to get guest discounts.

= 2.3.4 =
* Fixed minimum amount required when applying coupon manually.

= 2.3.3 =
* WhatsApp share link fix.

= 2.3.2 =
* Disable sending of emails via option under emails tab.
* Urgent fix for manually added referral codes.

= 2.3.1 =
* Manually add referral code for past orders
* Updated pot file with new translatable strings
* Few bugs fixed regarding the new event system

= 2.3.0 =
* New Raf Events system.
* Added more social share options

= 2.2.5 =
* New filter for points rewards plugin and a filter to disable showing of referral code per user role.

= 2.2.4 =
* Fix send email button for guest users

= 2.2.3 =
* Fix notice for Contact Form 7 shortcode

= 2.2.1 - 2.2.2 =
* Couple of small errors fixed after 2.2 update.

= 2.2.0 =
* FEATURE: Guests referral. A user does not need to be registered user to refer anymore, guests can refer now as well.
* FEATURE: Make sure email is valid when inviting via email.
* FEATURE: Export all referrals as CSV under Woocommerce -> Refer a friend data screen.
* FEATURE: WooCommerce Points & Rewards plugin is now fully supported, you can choose how many points user will earn when he is referring a friend. 
* Critical fix: User could use both referral link and referral code in some cases.
* Support for latest Contact form 7 version.
* Optimization for checkout email field
* Templates have been updated to 2.2.0., please update them.

= 2.1.1 =
* Fix for referral codes, exclude bug fix
* Added tab title filter.
* Added new classes for custom styling for referral codes vs referral links.
* Fix for referral codes applied on cart/checkout.
* Fix header notice on checkout, 
* Fix issue of guest coupons when using referral codes instead of links, he was able to use code multiple times even tho he was registered user.


= 2.1.0 =
* Option to hide share via email.
* Better UX on share via email, inputs are reappearing after sending emails.
* Added a free shipping option for a coupon
* Added a new filter to enable coupon types created by other plugins
* Translation updates.
* Fall back to "Your Friend", when user does not have first and last name and invites friends. 
* Filter to easly change coupon generation to other order status than completed.
* Added action that runs after sending referral email.
* Subscription - option to spend all coupons at once.
* Added support for popup maker
* Contact Form 7 - bug fix.
* Small bug fixes
* Update to templates.

= 2.0.9 =
* Subscription renewal, fix a renewal bug.
* Click to copy is now available. Update your templates. Clear site cache.

= 2.0.8 =
* Minimum order bug fixed
* Added filter for order status
* Fixed the issue with woocommerce email look taking over headings from custom email template.

= 2.0.7 =
* Generate missing referral links tool - bug fixed.

= 2.0.6 =
* Plugin updater bug fixed.

= 2.0.5 =
* Email template fix for older outlook email clients. 
* Fixed translation bug where sometimes it did not work.

= 2.0.4 =
* Couple of bug fixes, return of the depricated {{code}} tag in emails. Couple of new actions and ability to remove filters if needed.

= 2.0.0 =
* Completely rewritten from scratch for better code organizing.
* New Email Templates
* New Templates System for the whole plugin. Copy everything from templates folder to theme/wpgens-raf folder and change HTML and styling.
* New Settings Page
* Support for WooCommerce Subscription
* Ability to generate coupon codes on every nth referral.
* New referral codes as coupon codes.
* Plenty of new filters & actions added
* Added names for email invites
* Plenty of other small changes.

= 1.2.7 =
* Fixed plugin folder name, back to old one - this was mistake.
* Updated Refer a friend Data page to speed up loading time.
* Added brand new statistics under Reports.
* Added currency on my account page and better date format which is using WordPress options date format.

= 1.2.6 =
* Add {{name}} tag in share emails body and subject.
* Add order ID to coupon description.
* Coupon value as percentage of order
* Exclude products by ID
* NEW Order Box that shows all referral info.
* Added multiple security checks to prevent abuse.
* Change in a way referral is generated. Now all referals are generated but coupons are automatically generated only if it passes security features.
* Translation updated.

= 1.2.5 =
* Add ability to offer "From" email to be sender's email. Couple of other fixes.

= 1.2.4 =
* Whatsup button fix, issue with plugin updater not working.

= 1.2.3 =
* Fatal error fix on auto applied coupon on cart, for older woo versions.

= 1.2.2 =
* Whatsup button fix.

= 1.2.1 =
* Small bug fixes after big 1.2 update.

= 1.2.0 =
* Complete change of the front end look. Added email share and rewrote part of the code.

= 1.1.2 =
* Support for older version of Woocommerce. In woocommerce -> system added new button to reset data, to be used in dev environment.

= 1.1.1 =
* Woocommerce 3.0 support. Removed coupon % product discount that woocommerce removed.

= 1.1.0 =
* Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 1.0.0 =
* Initial version

== Upgrade Notice ==

= 2.0.0 =
2.0 is a BIG update! Check your email settings and also check new filters if you have been using them to alter RAF.