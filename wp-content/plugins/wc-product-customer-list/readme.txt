=== Product Customer List for WooCommerce ===
Contributors: kokomoweb
Tags: woocommerce, customer list, who bought, admin order list, product-specific, export customers to csv, email customers, customer list, customer, list, print
Requires at least: 4.0
Tested up to: 4.8
Stable tag: 2.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a list of customers who bought a specific product at the bottom of the product edit page in WooCommerce and send them e-mails.

== Description ==

A plugin that simply displays a list of customers who bought a specific product at the bottom of the WooCommerce product edit page or as a shortcode. You can also send an email to the list of customers, print the list or export it as a CSV, PDF or Excel file. Requires WooCommerce 2.2+ to be installed and activated. 

Great for sending out e-mails to customers for product recalls or for courses.

= Features: =

* Support for variable products
* Options page to select which info columns to display
* Displays customer name, email, phone number, address, order number, order date and quantity for each product
* Shortcode to display orders in the front-end (beta)
* Button to e-mail all customers for a specific product using your favorite e-mail client (b.c.c.)
* Export the customer list to CSV (great for importing into Mailchimp!)
* Export the customer list to Excel
* Export the customer list to PDF (choose your orientation and page size in the settings)
* Copy the customer list to clipboard
* Print the list of customers
* Search any column in the list
* Sort by any column in the list
* Drag and drop columns to reorder them
* Localized and WPML / Polylang ready (.pot file included)
* Included translations: French, French (France), French (Canada), Spanish, Dutch, Dutch (Netherlands), Dutch (Belgium).
* All functions are pluggable
* Performance oriented
* Responsive
* Multisite compatible
* Support for custom statuses

= Coming soon: =

* Form to e-mail all customers using WooCommerce e-mail formats and templating

Feel free to [contact me](http://www.kokomoweb.com/contact/) for any feature requests.

= Contributors: =
* Support for variable products: [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/)
* Dutch translation: [pieterclaesen](https://wordpress.org/support/profile/pieterclaesen)
* Portuguese (Brazil) translation: [Marcello Ruoppolo](https://profiles.wordpress.org/mragenciadigital)

== Installation ==

1. Upload the plugin files to the "/wp-content/plugins/wc-product-customer-list" directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Edit any WooCommerce product to view the list of customers that bought it.
4. Make sure that the 'Product Customer List for WooCommerce’ checkbox is ticked in your screen options.
5. Access the settings page in WooCommerce / Settings / Products / Product Customer List


== Frequently Asked Questions ==

= How do I use the shortcode? =

To display the list in the front end, simply use the following shortcode: [customer_list product=PRODUCT_ID quantity=TRUE/FALSE]
Replace PRODUCT_ID with the ID of the product for which you want to display the customers. Use TRUE or FALSE to display the quantity. If you do not use any attributes, it will display the customers of the current product (on a product page), and it will not display the quantity. 

= Why doesn't the customer list appear when I edit a product? =

Make sure that the 'Product Customer List for WooCommerce’ checkbox is ticked in your screen options.

= Where can I select which columns to display =

You can access the settings page in WooCommerce / Settings / Products / Product Customer List

= How can I reorder the columns? = 

You can reorder the columns by dragging them and dropping them in the order you want. The browser will remember your selection.

= What are the available hooks? = 

There is currently only one hook, that enables you to add content after the “email all customers” button. To use it: add_action( 'wpcl_after_email_button' , ‘your_function_here’, 10 , 1 );

== Screenshots ==

1. The customer list in the product edit page.
2. The settings page.

== Changelog ==

= 2.5.1 =
* Added hook “wpcl_after_email_button” to display content after the email button.
* Fixed variation display.

= 2.5.0 =
* Fixed issue where the email list would be incomplete.

= 2.4.9 =
* Added support for custom statuses

= 2.4.8 =
* Fixed deprecation notices and bugs in variable products

= 2.4.7 =
* Script optimizations

= 2.4.6 =
* Fixed settings text mismatch

= 2.4.5 =
* Fixed bug where current date would be show instead of the order date
* Added plugin action links
* Added order total column
* Added translations for order statuses

= 2.4.4 =
* WooCommerce 3.0+ compatibility
* Script optimizations (thanks to [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/))
* Code optimization
* Improved multisite compatibility
* Updated .pot file

= 2.4.3 =
* Added Customer ID column
* Fixed wpdb notice (thanks to [Michal Bluma](https://profiles.wordpress.org/michalbluma))

= 2.4.2 =
* Fixed multisite compatibility

= 2.4.1 =
* Fixed compatibility issue with plugin “WooCommerce Amazon S3 storage”

= 2.4.0 =
* Added multisite compatibility

= 2.3.9 =
* Added the option for city in the settings

= 2.3.8 =
* Fixed bug where quantity would not show up in shortcode

= 2.3.7 =
* Added compatibility with WPML

= 2.3.6 =
* Fixed PDF orientation and size.
* Added payment method column and option.

= 2.3.5 =
* Added settings for PDF orientation and size.

= 2.3.4 =
* Fixed bug where refunds would appear in the list.
* Removed old unused code.

= 2.3.3 =
* Fixed trailing slash in scripts and stylesheet urls which could prevent them to load on certain servers.

= 2.3.2 =
* Fixed bug where featured image uploader wouldn’t work when activated.
* Updated PDFMake script to latest version (local)

= 2.3.1 =
* Added column reordering and state save
* Fixed javascript localization handling (wp_localize_script)

= 2.3.0 =
* Changed print and export system to reflect filters and order
* Added export to excel
* Added export to PDF
* Added copy to clipboard

= 2.2.9 =
* Added all missing order statuses in settings

= 2.2.8 =
* Fixed bug where shipping postal code wouldn’t be displayed in CSV export

= 2.2.7 =
* Fixed bug where two extra columns would appear while printing
* Fixed bug where there would be an error if you delete a variation after it is purchased

= 2.2.6 =
* Added Portuguese (Brazil) translation (thanks to [Marcello Ruoppolo](https://profiles.wordpress.org/mragenciadigital))
* Fixed alignment shortcode bug and added default product as current product

= 2.2.5 =
* Added support for variable products (thanks to [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/))
* Bug fixes & optimisation

= 2.2.4 =
* Fixed Urls for wordpress subdirectory installs

= 2.2.3 =
* Fixed issue where columns would shift when printing

= 2.2.2 =
* Added front-end shortcode
* Fixed default order type in settings

= 2.2.1 =
* Added date column
* Added compatibility with Wordpress 4.5
* Fixed some bugs

= 2.2.0 =
* Added settings tab section
* Added support for horizontal scrolling
* Loaded datatables CSS and JS via CDN

= 2.1.2 =
* Fixed undefined object error when there are no customers
* Fixed text domain to match plugin slug
* Added Dutch (Belgium) translation

= 2.1.1 =
* Fixed issue where the plugin would prevent WooCommerce from displaying or saving product attributes (price & stock)

= 2.1.0 =
* Added pagination
* Added search
* Added sortable columns
* Added Dutch (Netherlands) translation (thanks to [pieterclaesen](https://wordpress.org/support/profile/pieterclaesen))
* Added row actions
* Fixed empty table notice
* Cleaned code

= 2.0.4 =
* Fixed other “cannot send session cache limiter” warning 

= 2.0.3 =
* Fixed bug where variations wouldn’t be added to the quantity column sum

= 2.0.2 =
* Fixed “session_start(): Cannot send session cookie” warning
* Fixed “session_start(): Cannot send session cache limiter” warning

= 2.0.1 =
* Fixed quantity bug

= 2.0.0 =
* Added “export to CSV” button
* Added print button

= 1.11 =
* Improved table styling
* Added Spanish translation
* Optimized code: now even lighter files!

= 1.1 =
* Added quantity column
* Fixed and optimized WooCommerce plugin check
* Improved code readability
* Updated translations

= 1.02 =
* Fixed email button

= 1.01 =
* Updated deprecated WooCommerce order statuses
* Added pluggable functions
* Optimized code

= 1.0 =
* First stable version