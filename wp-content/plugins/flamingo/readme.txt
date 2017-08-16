=== Flamingo ===
Contributors: takayukister, megumithemes
Tags: bird, contact, mail, crm
Requires at least: 4.7
Tested up to: 4.8
Stable tag: 1.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A trustworthy message storage plugin for Contact Form 7.

== Description ==

Flamingo is a message storage plugin originally created for [Contact Form 7](https://wordpress.org/plugins/contact-form-7/), which doesn't store submitted messages.

After activation of the plugin, you'll find *Flamingo* on the WordPress admin screen menu. All messages through contact forms are listed there and are searchable. With Flamingo, you are no longer need to worry about losing important messages due to mail server issues or misconfiguration in mail setup.

For more detailed information, please refer to the [Contact Form 7 documentation page](https://contactform7.com/save-submitted-messages-with-flamingo/).

== Installation ==

1. Upload the entire `flamingo` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.6 =

* Tested up to WordPress 4.8.
* Requires WordPress 4.7 or higher.
* Added RTL stylesheet.
* Strengthened capability checking.
* Removed inappropriate content from h1 headings.
* Changed the default format of the export CSV.
* Introduced the flamingo_csv_value_separator and flamingo_csv_quotation filter hooks to enable customizing CSV output.

= 1.5 =

* Tested up to WordPress 4.7.
* Requires WordPress 4.5 or higher.
* count() method added to Flamingo_Inbound_Message class.
* All language files in the languages folder were removed. Translations have moved to translate.wordpress.org.
