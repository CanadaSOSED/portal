=== Simple Custom CSS and JS ===
Created: 06/12/2015
Contributors: diana_burduja
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=diana.burduja@gmail.com&lc=AT&item_name=Diana%20Burduja&item_number=WP%2dImage%2dZoooom%2dplugin&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Email: diana@burduja.eu
Tags: CSS, JS, javascript, custom CSS, custom JS, custom style, site css, add style, customize theme, custom code, external css, css3, style, styles, stylesheet, theme, editor, design, admin
Requires at least: 3.0.1
Tested up to: 4.8
Stable tag: 3.3 
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easily add Custom CSS or JS to your website with an awesome editor.

== Description ==

Customize your WordPress site's appearance by easily adding custom CSS and JS code without even having to modify your theme or plugin files. This is perfect for adding custom CSS tweaks to your site.

= Features =
* **Text editor** with syntax highlighting 
* Print the code **inline** or included into an **external file**
* Print the code in the **header** or the **footer**
* Add CSS or JS to the **frontend** or the **admin side**
* Add as many codes as you want
* Keep your changes also when you change the theme

== Installation ==

* From the WP admin panel, click "Plugins" -> "Add new".
* In the browser input box, type "Simple Custom CSS and JS".
* Select the "Simple Custom CSS and JS" plugin and click "Install".
* Activate the plugin.

OR...

* Download the plugin from this page.
* Save the .zip file to a location on your computer.
* Open the WP admin panel, and click "Plugins" -> "Add new".
* Click "upload".. then browse to the .zip file downloaded from this page.
* Click "Install".. and then "Activate plugin".

OR...

* Download the plugin from this page.
* Extract the .zip file to a location on your computer.
* Use either FTP or your hosts cPanel to gain access to your website file directories.
* Browse to the `wp-content/plugins` directory.
* Upload the extracted `custom-css-js` folder to this directory location.
* Open the WP admin panel.. click the "Plugins" page.. and click "Activate" under the newly added "Simple Custom CSS and JS" plugin.

== Frequently Asked Questions ==

= Requirements =
PHP >= 5.3

**Browser requirements**
* Firefox - version 4 and up
* Chrome - any version
* Safari - version 5.2 and up
* Internet Explorer - version 8 and up
* Opera - version 9 and up

= What if I want to add multiple external CSS codes? =
If you write multiple codes of the same type (for example: two external CSS codes), then all of them will be printed one after another

= Will this plugin affect the loading time? =
When you click the `Save` button the codes will be cached in files, so there are no tedious database queries.

= Does the plugin modify the code I write in the editor? =
No, the code is printed exactly as in the editor. It is not modified/checked/validated in any way. You take the full responsability for what is written in there.

= My code doesn't show on the website =
Try one of the following:
1. If you are using any caching plugin (like "W3 Total Cache" or "WP Fastest Cache"), then don't forget to delete the cache before seing the code printed on the website.
2. Make sure the code is in **Published** state (not **Draft** or **in Trash**).
3. Check if the `wp-content/uploads/custom-css-js` folder exists and is writable

= Does it work with a Multisite Network? =
Yes.

= What if I change the theme? =
The CSS and JS are independent of the theme and they will persist through a theme change. This is particularly useful if you apply CSS and JS for modifying a plugin's output. 

= Can I use a CSS preprocesor like LESS or Sass? =
For the moment only plain CSS is supported, but you can check out the [Pro version](https://www.silkypress.com/simple-custom-css-js-pro/?utm_source=wordpress&utm_campaign=ccj_free&utm_medium=banner) in case you need a CSS preprocessor.

= Can I upload images for use with my CSS? =
Yes. You can upload an image to your Media Library, then refer to it by its direct URL from within the CSS stylesheet. For example:
`div#content {
    background-image: url('http://example.com/wp-content/uploads/2015/12/image.jpg');
}`

= Can I use CSS rules like @import and @font-face? =
Yes.

= Who can publish/edit/delete Custom Codes? =
By default only the Administrator will be able to publish/edit/delete Custom Codes. Check out [this article](https://www.silkypress.com/simple_custom_css_js/user-capabilities-simple-custom-css-js-plugin/) for more details on roles and user capabilities customizations.

= Compatibility with qTranslate X plugin =
* If the [qTranslate X](https://wordpress.org/plugins/qtranslate-x/) plugin is adding some `[:]` or `[:en]` characters to your code, then you need to remove the `custom-css-js` post type from the qTranslate settings. Check out [this screenshot](https://www.silkypress.com/wp-content/uploads/2016/08/ccj_qtranslate_compatibility.png) on how to do that.



== Screenshots ==

1. Manage Custom Codes

2. Add/Edit Javascript

3. Add/Edit CSS

$. Add/Edit HTML 

== Changelog ==

= 3.3 =
* 06/23/2017
* Feature: option for adding Codes to the Login Page 

= 3.2 =
* 06/13/2017
* Fix: compatibility issue with the HTML Editor Syntax Highlighter plugin

= 3.1 =
* 05/14/2017
* Feature: prepare the plugin for translation

= 3.0 =
* 04/12/2017
* Feature: create the Web Designer role
* Feature: allow Custom Codes to be managed only by users with the right capabilities

= 2.10 =
* 02/05/2017
* Feature: circumvent external file caching by adding a GET parameter
* Add special offer for Simple Custom CSS and JS pro

= 2.9 =
* 12/05/2016
* Compatibility with WP4.7. The "custom HTML code" was not showing up anymore

= 2.8 =
* 10/09/2016
* Feature: add search within the editor accessible with Ctrl+F
* Feature: make the inactive rows opaque

= 2.7 =
* 09/04/2016
* Fix: there was a space in the htmlmixed.%20js url
* Feature: make the editor resizable

= 2.6 =
* 08/31/2016
* Feature: add HTML code
* Fix: add htmlentities when showing them in the editor
* Feature: when adding a code, show more explanations as comments

= 2.5 =
* 08/25/2016
* Fix: compatibility with other plugins that interfere with the CodeMirror editor

= 2.4 =
* 08/01/2016
* Add the "Add CSS Code" and "Add JS Code" buttons next to the page title
* Compatibility with WP 4.6: the "Modified" column in the Codes listing was empty

= 2.3 =
* 06/22/2016
* Add the includes/admin-notices.php and includes/admin-addons.php
* Feature: change the editor's scrollbar so it can be caught with the mouse

= 2.2 =
* 06/22/2016
* Check compatibility WordPress 4.5.3
* Add special offer for Simple Custom CSS and JS pro

= 2.1 =
* 04/24/2016
* Fix: on multisite installs have to create the custom-css-js folder in the upload dir for each site
* Fix: the `deactivate code` star wasn't working when first time clicked
* Fix: In the add/edit Code page filter which meta boxes are allowed
* Fix: If the custom-css-js folder is not created of is not writable, issue an admin notice.

= 2.0 =
* 04/11/2016
* Feature: enable/disable codes
* Feature: add a GET parameter at the end of external files in order to over circumvent caching
* Fix: don't add the "<script>" tag from the code if already present. 

= 1.6 =
* 03/26/2016
* Fix: the number of codes were limited because query_posts is automatically inserting a limit 

= 1.5 =
* 10/03/2016
* Fix: solved a conflict with the `shortcoder` plugin.

= 1.4 =
* 04/01/2016
* Tweak: Do not enqueue scripts unless we are editing the a custom-css-js type post.
* Fix: The register_activation_hook was throwing a notice
* Fix: add window.onload when initializing the CodeMirror editor
* Tweak: Differentiated the option names for "Where on page" and "Where in site"
* Fix: set the correct language modes to CodeMirror object
* Tweak: remove the `slug` metabox
* Tweak: use the compressed version of CodeMirror

= 1.3 =
* 27/12/2015
* Tweak: changed the submenus to "Add Custom CSS" and "Add Custom JS" instead of "New Custom Code"
* Tweak: Use `admin_head` instead of `admin_enqueue_scripts` for external files in order to add priority to the code
* Fix: The javascript code was not shown
* Fix: For longer code the last line in the editor was hidding because of the CodeMirrorBefore div.

= 1.2 =
* 14/12/2015
* Fix: when a code was sent into Trash it still wasn't shown on the website

= 1.1 =
* 10/12/2015
* Tweak: for external files use wp_head and wp_footer instead of wp_enqueue_style. Otherwise the CSS and JS is inserted before all the other scripts and are overwritten.
* Tweak: Save all the codes in files in order to save on database queries
* Tweak: Rewrite the readme.txt in the form of FAQ for better explanations

= 1.0 =
* 06/12/2015
* Initial commit

== Upgrade Notice ==

Nothing at the moment
