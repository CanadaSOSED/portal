=== Breeze - WordPress Cache Plugin ===
Contributors: Cloudways
Tags: cache, caching, performance, wp-cache, cdn, combine, compress, speed plugin, database cache,gzip, http compression, js cache, minify, optimize, page cache, performance, speed, expire headers
Requires at least: 4.5
Tested up to: 4.8
Stable tag: 1.0.3-beta
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Breeze is a WordPress Caching Plugin developed by Cloudways. Breeze uses advance caching systems to improve WordPress loading times exponentially.

== Description ==

Breeze is a free, simple (yet powerful) and user-friendly WordPress Caching Plugin developed by the Cloudways team. It offers various options to optimize WordPress performance at various levels. It works equally great with WordPress, WordPress with WooCommerce and WordPress Multisite.

Breeze excels in the following areas:

* **Performance:** Breeze improves website speed and resource optimization. Other features include file level cache system, database cleanup, minification, support for Varnish cache and simplified CDN integration options.

* **Convenience:** Breeze is easy to install and configure directly from WordPress. Configuring Breeze is easy and most of the default options work well right out of the box. The recommended settings should work on all your WordPress websites seamlessly.

* **Simplicity:** Breeze is designed to be simple for all users. Just install and activate the plugin and you'll see the results instantaneously.

What makes Breeze WordPress Cache Plugin awesome is that it comes with builtin support for Varnish. If Varnish is not installed on your servers, Breeze will utilize its internal cache mechanism to boost up your WordPress site performance.

**Support:** We love to provide support! Post your questions on the WordPress.org support forums, or if you are a Cloudways Customer you may ask questions on the <a href="https://community.cloudways.com/">Cloudways Community Forum</a>. 

**Note:** This plugin is still in Beta phase and this plugin is tested on Cloudways WordPress hosting servers.

**Special Thanks:** We would like to give special mention to WP Speed Of Light for being an inspiration for Breeze.

== Installation ==

= To install the plugin via WordPress Dashboard: =
* In the WordPress admin panel, navigate to Plugin > Add new
* Search for Breeze
* Click install and wait for the installation to finish. Next, click the activate link

= To install the plugin manually: =
* Download and unzip the plugin package - breeze.1.0.0.zip
* Upload the breeze to /wp-content/plugins/
* Activate the plugin through the 'Plugins' menu in WordPress
* Access Breeze from WordPress Admin > Settings > Breeze

== Frequently Asked Questions ==

= Installation Instructions

To install the plugin via WordPress Dashboard
1. In the WordPress admin panel, Menu > Plugin > Add new
2. Search for Breeze
3. Click on install and wait for the installation to finish. Next, then click on the activate link

To install the plugin manually
1. Download and unzip the plugin package - breeze.1.0.0.zip
2. Upload the /breeze to /wp-content/plugins/
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Access Breeze from WordPress Admin > Settings > Breeze

= Does Breeze support Varnish and to what extent? =

Breeze, by default, supports Varnish. It has been tested to be fully compatible with Cloudways Servers that come with Varnish pre-installed. If you are using hosting providers other than Cloudways, we suggest you confirm Varnish support with your hosting provider

= Does Breeze support WooCommerce? =

Breeze is fully compatible with WooCommerce, out of the box. It does not require any special configurations. 

= Does Breeze support WordPress Multisite? =

Breeze is fully compatible with WordPress Multisite without the need for any extra configuration. 

= Is Breeze compatible with other WordPress Cache plugins? =

We DO NOT recommend using two WordPress cache plugins at the same time on any WordPress website. 
We strongly recommend that you use Breeze as the only cache plugin for your website. If there are any other cache plugins installed, please ensure that you have disabled them prior to proceeding with the Breeze installation.


= Is Breeze compatible with HTTPS? =

Breeze does not require any special configuration to work with HTTP or HTTPS pages.

= Does Breeze have compatibility issues with other known plugins? =

Breeze has been tested with popular plugins available on WordPress.org. Please feel free to report any incompatibilities on the WordPress Support Forums or on <a href="https://community.cloudways.com/">Cloudways Community Forum</a>.

= Does Breeze support CDN? =

Breeze supports CDN integration. It allows all static assets (such as images, CSS and JS files) to be served via CDN. 

= What does Breeze's Database Optimization feature do? =

WordPress databases are notorious for storing information like post revisions, spam comments and much more. Over time, databases l become bloated and it is a good practice to clear out unwanted information to reduce database size and improve optimization. 

Breeze's database optimization cleans out unwanted information in a single click. 

= Will comments and other dynamic parts of my blog appear immediately? =

Comments will appear upon moderation as per the comment system (or policy) set in place by the blog owner. Other dynamic changes such as any modifications in files will require a full cache purge.

= Can I exclude URLs of individual files and pages from cache? =

You can exclude a file by mentioning its URL or file type (by mentioning file extension) in the exclude fields (available in the Breeze settings). Exclude will not let the cache impact that URL or file type. 

If Varnish is active, you will need to exclude URLs and file type(s) in the Varnish configuration. If you are hosting WordPress websites on Cloudways servers, follow <a href="https://support.cloudways.com/how-to-exclude-url-from-varnish/">this KB to exclude URLs from the Varnish cache</a>.


= Does it work with all hosting providers? =

Breeze is in Beta phase and has not yet been tested on hosting providers other than Cloudways. 
However, major Breeze options such as Gzip, browser cache, minification, grouping, database optimization. CDN integration will work as expected on other hosting providers. 


= Where can I get support for Breeze? =

You can get your questions answered on the WordPress support forums. If you are a Cloudways customer, please feel free to start a discussion at <a href="https://community.cloudways.com/">Cloudways Community Forum</a>.

== Changelog ==

= 1.0.3-beta =
* Fix : Disabled browser cache for WooCommerce cart, shop and account pages
* Fix : Removal of htaccess when disabling browser cache and gzip compression options
* Fix : CDN issues of not serving all the configured contents from CDN service

= 1.0.2-beta =
* Fix : Compatibility issues of WooCommerce

= 1.0.1-beta =
* Fix : Purging issue to allow only admin users to Purge
* Add : Feedback link

= 1.0.0 =
* Add : First Beta release


== Upgrade Notice ==

Update Breeze through WordPress Admin > Dashboard >Updates. The settings will remain intact after the update.

== Screenshots ==


== Requirements ==

PHP 5.3+, PHP7 or 7.1 recommended for better performance, WordPress 4.5+
