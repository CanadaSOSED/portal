=== Advanced Orders Export For WooCommerce ===
Contributors: algolplus
Donate link: http://algolplus.com/plugins/
Tags: woocommerce,export,order,xls,csv,xml,woo export lite,export orders,orders export,csv export,xml export,xls export
Requires at least: 4.2.4
Tested up to: 4.8
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Export orders from WooCommerce with ease ( Excel/CSV/XML/Json supported )

== Description ==
This plugin helps you to **easily** export WooCommerce order data. 

Export any custom field assigned to orders/products/coupons is easy and you can select from various formats to export the data in such as CSV, XLS, XML and JSON.

= Features =

* **select** the fields to export
* **rename** labels
* **reorder** columns 
* export WooCommerce **custom fields** or terms for products/orders
* mark your WooCommerce orders and run "Export as..." a **bulk operation**.
* apply **powerful filters** and much more

= Export Includes =

* order data
* summary order details (# of items, discounts, taxes etc…)
* customer details (both shipping and billing)
* product attributes
* coupon details
* CSV, XLS, XML and JSON formats

= Use this plugin to export orders for =

* sending order data to 3rd part drop shippers
* updating your accounting system
* analysing your order data


Have an idea or feature request?
Please create a topic in the "Support" section with any ideas or suggestions for new features.

> Pro Version

> Are you looking to have your Woocommerce products drop shipped from a third party? Our plugin can help you export your orders to CSV/XML/etc and send them to your drop shipper. You can even automate this process with [Pro version](http://algolplus.com/plugins/downloads/woocommerce-order-export/) .



== Installation ==

= Automatic Installation =
Go to Wordpress dashboard, click  Plugins / Add New  , type 'order export lite' and hit Enter.
Install and activate plugin, visit WooCommerce > Export Orders.

= Manual Installation =
[Please, visit the link and follow the instructions](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Frequently Asked Questions ==

Need help ? Create ticket in [helpdesk system](https://algolplus.freshdesk.com). Don't forget to attach your settings or some screenshots. It will significantly reduce reply time :)

Check [some snippets](https://algolplus.com/plugins/snippets-plugins/) for popular plugins or review  [this page](https://algolplus.com/plugins/code-samples/) to study how to extend the plugin.

= I want to add new attribute to export  =
Check screenshot #5! You should open section "Setup Fields", scroll down to field "Products", click button  "Setup Fields", click button "Add Field", select field in the dropdown, type column title and press button "Add".

= I can't filter/export custom attribute for Simple Product =
I'm sorry, but it's impossible. You should add this attribute to Products>Attributes at first and use "Filter by Product Taxonomies".

= Plugin produces unreadable XLS file =
The theme or another plugin outputs some lines. Usually, there are extra empty lines at the end of functions.php(in active theme).

= When exporting .csv containing european special characters , I want to open this csv in Excel without extra actions =
You  should open tab "CSV" and setup ISO-8859-1 as codepage.

= Red text flashes at bottom during page loading = 
It's a normal situation. The plugin hides this warning on successful load. 

= Can I request any new feature ? =
Yes, you can email a request to aprokaev@gmail.com. We intensively develop this plugin.

== Screenshots ==

1. Default view after installation.  Just click 'Express Export' to get results.
2. Filter orders by many parameters, not only by order date or status.
3. Select the fields to export, rename labels, reorder columns.
4. Button Preview works for all formats.
5. Add custom field or taxonomy as new column to export.
6. Select orders to export and use "bulk action".

== Changelog ==

= 1.4.0 - 2017-06-02 =
* Fixed bug for field "Customer order note"
* Fixed bug for filter by product category
* Tested for Wordpress 4.8
* Added new product fields "Description" and "Short Description"
* Added logger for backgound tasks (for Woocommerce 3.0+)
* Added a lot of hooks 
* New tab "Order Change" to export single order immediately (Pro)

= 1.3.1 - 2017-05-12 =
* Optimized for big shops (tested with 10,000+ orders)
* Export refunds
* Export deleted products
* Added new filter "Product custom fields"
* Added new product field "Product Variation"
* Added new coupon fields "Type","Amount", "Discount Amount + Tax"
* Tweaked default settings
* Menu uses capability "view_woocommerce_reports"

= 1.3.0 - 2017-04-11 =
* The plugin is compatible with Woocommerce 3.0
* Display warning message if user interface fails to load
* Update Select2.js to fix some user interface problems
* Fixed fields "Order Tax" and "Subtotal" (uses Woocommerce functions to format it)

= 1.2.7 - 2017-03-17 =
* Portuguese and French translations were added. Thanks to contributors!
* Added new field "Order amount without tax"
* Added new product field "Quantity (- Refund)"
* Added tab "Help"
* Added some UI hooks
* Fixed bug in filter by Taxonomies
* Fixed bug in filter by Shipping Methods (disabled for Woocommerce earlier than  2.6)
* Fixed field "State Full Name" (html entities removed)
* Skip **deleted products** during export
* Removed word "hack" from PHPExcel source

= 1.2.6 - 2017-02-02 =
* Added new filter "Filter by coupons"
* Added new filter "Shipping methods" to section "Filter by shipping"
* Added "refund" fields for items/taxes/shipping
* Simple products can be filtered by attributes using "Product Taxonomies"
* Fixed bug in filtering by products ( it checked first X products only)
* Fixed bug for filename in bulk actions
* Kill extra lines in generated files if the theme or another plugin outputs something at top
* XLS format doesn't require module "php-zip" now

= 1.2.5 - 2016-12-21 =
* Button "Preview" displays estimation (# of orders in exported file)
* User can change number of orders in "Preview"
* Orders can be sorted by "Order Id" in descending/ascending direction
* Added column "Image Url" for products (featured image)
* Fixed bug, **the plugin exported deleted orders!**
* Fixed bug, autocomplete displayed deleted products in filter "Product"
* Fixed bug, filter "category" and filter "Attribute" work together for variations
* Fixed bug, import settings didn't work correcty
* Suppress useless warning if the plugin can't create file in system "/tmp" folder
* New filters/hooks for products/coupons/vendors
* New filters/hooks for XLS format
* Russian/Chinise translations were updated

= 1.2.4 - 2016-11-15 =
* Added new filter "Item Metadata" to section "Filter by product"
* Added Chinese language. I’d like to thank user [7o599](https://wordpress.org/support/users/7o599/) 
* Added new tab "Tools" with section "export/import settings"
* Added button to hide non-selected fields
* XML format supports custom structures (some hooks were added too)
* Fixed bug for taxonomies (we export attribute Name instead of slug now)
* Fixed bug for XLS  without header line
* Fixed bug with pagination after export (bulk action)
* Fixed bug in action "Hide unused" for products
* Fixed bug for shops having huge number of users
* Fixed bug for "&" inside XML 

= 1.2.3 - 2016-10-21 =
* Added usermeta fields to section "Add field"
* "Press ESC to cancel export" added to progressbar 
* Added column "State Name"
* Added columns "Shipping Method", "Payment Method" (abbreviations)
* Format CSV can be exported without quotes around values
* Added checkbox to skip suborders
* Bulk export recoded to be compatible with servers behind a Load Balancer
* Skip root xml if it's empty
* New filters/hooks for CSV/XML formats
* [Code samples](https://algolplus.com/plugins/code-samples/)  added to documentation

= 1.2.2 - 2016-09-28 =
* Added column "Product Shipping Class"
* Added column "Download Url"
* Added column "Item Seller"
* Fixed bug in field "Line w/o tax" (if price doesn't include tax)
* Fixed bug in XML format  (for PHP7)
* A lot of new filters/hooks added

= 1.2.1 - 2016-08-12 =
* New filter by Payment Method
* New filter by Vendor( product creator)
* New field "Order Notes"
* Button "Export w/o progressbar" (added for servers behind a Load Balancer)
* Fixed bug if order was filtered by variable product

= 1.2.0 - 2016-07-11 =
* Support both XLS and XLSX
* Solved problem with filters ("Outdated Select2.js" warning)
* Added date/time format
* Comparison operators for custom fields & product attributes( + LIKE operator)
* Codepage for CSV file
* Preview displays 3 records
* Fixed bug for "Item cost"
* Refreshed language files 
 
= 1.1.13 - 2016-06-18 =
* Possibility to "Delete" fields (except default!)
* Added 'Hide unused' for order/product/coupon fields (dropdowns filtered by matching orders)
* Auto width for Excel format
* Export attributes which are not used in variations
* Support single/double quotes in column name
* Added  MAX # of columns ( if we export products as columns)

= 1.1.12 - 2016-05-25 =
* Added filter by users/roles
* Added filename for downloaded file
* Export refund amount
* Xls supports RTL

= 1.1.11 - 2016-04-27 =
* Added filter by custom fields (for order)
* Coded fallback if the plugin can't create files in folder "/tmp"
* Added new hooks/filters

= 1.1.10 - 2016-03-30 =
* "Filter by product" allows to export only filtered products
* Fixed bug for meta fields with spaces in title
* Fixed bug for XML/Json fields ( unable to rename )
* Added new hooks/filters
* Added extra UI alerts
* Added tab "Profiles" (Pro version)

= 1.1.9 - 2016-03-14 =
* Disable Object Cache during export
* Added fields : Line Subtotal, Order Subtotal, Order Total Tax

= 1.1.8 - 2016-03-07 =
* Added link to PRO version
* Fixed few minor bugs

= 1.1.7 - 2016-02-18 =
* Added options "prepend/append raw XML"
* Added column "Item#" for Products
* Fixed custom fields for Products

= 1.1.6 - 2016-02-04 =
* Added column "Total weight" (to support Royal Mails DMO)
* Display progressbar errors during export

= 1.1.5 - 2016-01-21 =
* Fixed another bug for product custom fields

= 1.1.4 - 2016-01-13 =
* Added custom css to our pages only

= 1.1.3 - 2015-12-18 =
* Ability to export selected orders only
* Fixed bug for product custom fields
* Fixed progressbar freeze

= 1.1.2 - 2015-11-11 =
* Fixed path for temporary files
* Export coupon description

= 1.1.1 - 2015-10-27 =
* Export products taxonomies

= 1.1.0 - 2015-10-06 =
* Order exported records by ID
* Corrected extension for xlsx files
* Fixed bug for "Fields Setup"

= 1.0.6 - 2015-09-28 =
* Attribute filter shows attribute values.
* Shipping filter shows values too.

= 1.0.5 - 2015-09-09  =
* Filter by product taxonomies

= 1.0.4 - 2015-09-04 =
* Export to XLS

= 1.0.3 =
* Partially support outdated Select2 (some plugins still use version 3.5.x)
* Fixed problem with empty file( preview was fine)

= 1.0.2 - 2015-08-25 =
* Added Progress bar
* Added new csv option "Populate other columns if products exported as rows"

= 1.0.1 - 2015-08-11 =
* Added Russian language


= 1.0.0 - 2015-08-10  =
* First release.