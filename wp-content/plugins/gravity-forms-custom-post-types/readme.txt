=== Gravity Forms + Custom Post Types ===
Contributors: spivurno,bradvin,wpsmith
Donate link: http://gravitywiz.com/donate/
Tags: form,forms,gravity,gravity form,gravity forms,CPT,custom post types,custom post type,taxonomy,taxonomies
Requires at least: 3.0.1
Tested up to: 4.8
Stable tag: 3.1.8

Map your Gravity-Forms-generated posts to a custom post type and/or custom taxonomies.

== Description ==

> This plugin is an add-on for [Gravity Forms](http://gravityforms.com). Make sure you visit [GravityWiz.com](http://gravitywiz.com/blog/) for more **free** Gravity Forms resources. And if you're looking to the largest collection of Gravity Forms plugins, check out [Gravity Perks](http://gravityperks.com).

Gravity Forms allows you to create posts from a form submission using special Post Fields. By default, the submitted form will be created as a standard WordPress post. This plugin allows you to change the post type of the generated post. **No code required!** This plugin also provides the ability to assign the generated post to a custom taxonomy.

= Features =

- Map posts to a custom post type
- Map posts to a custom taxonomy (via Drop Down, Multi-select, Radio Button or Checkbox field)
- Map posts to multiple taxonomies
- Visual hierarchy support for hierarchical taxonomies (Drop Down field only)
- Populate a Drop Down with posts
- Assign parent post for generated post (Drop Down field only)
- Single Line Text field support for taxonomies (enter as a comma-delimited list: term a, term b, term c)
- Enhanced UI support for Single Line Text fields (see screenshots)

= How to map a form to a custom post type =

1. Add a Post Title field to your form and click on it to open the field settings.
2. Below the "Description" field setting, you will find the "Post Type" setting.
3. Select the desired post type from the drop down (default is "Posts").

= How to link a field to a custom taxonomy =

1. Add the desired field to which the custom taxonomy should be mapped. Drop Down, Multi Select, Radio Buttons and Checkboxes fields are current supported.
2. Open the field settings by clicking on the field and click on the "Advanced" tab.
3. Check the "Populate with a Taxonomy" checkbox.
4. Select the desired taxonomy from the drop down that appears.

= How to link the saved post to taxonomies using a single line text field =

Single Line Text fields are a great way to allow users to select existing taxonomy terms and to also add new terms.

1. Add a Single Line Text field to your form.
2. Open the field settings by clicking on the field and click on the "Advanced" tab.
3. Check the "Save to Taxonomy" checkbox.
4. Select the desired taxonomy from the drop down that appears.
5. (optional) Check the "Enable Enhanced UI" checkbox to enable an awesome tag-input style UI (see screenshots).

Note: If the user inputs exising term names, the generated post will be assigned these terms. If the user inputs term names that do not exist, these terms will be added to the selected taxonomy and the generated post will also be assigned these terms.

= How to set a parent post with the drop down field =

When populating a Drop Down field with a post type, you may wish to set the selected post as the parent post for the generated post.

1. Add A Drop Down field to your form.
2. Click on the field to open the field settings. Then click on the "Advanced" tab.
3. Check the "Populate with Post Type" checkbox.
4. Select the desired post type from the drop down that appears. Be sure to select the **same post type** for which the post is being generated.

== Installation ==

1. Upload the plugin folder 'gravity-forms-custom-post-types' to your `/wp-content/plugins/` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure you also have Gravity Forms activated.

== Screenshots ==

1. Frontend: Example of mapping to a custom post type and multiple taxonomies
2. Frontend: Example of hierarchical taxonomy support
3. Setting: Mapping to a custom post type
4. Setting: Mapping a field to a custom taxonomy
5. Setting: Assigning generated post to selected parent post
6. Frontend: Example of Single Line Text field with Enhanced UI enabled
7. Setting: Mapping a Single Line Text field to a taxonomy (and enabling Enhanced UI)

== Changelog ==

= 3.1.8

* Fixed issue mapping taxonomies for Single Line Text fiela and Multi Select fields. Thanks, Cameron!

= 3.1.7 =

* Added support for displaying term label on Entry List and Export views (rather than term ID; does not work with Checkbox and Multi-select on Entry List).

= 3.1.6 =

* Added support for displaying term label on Entry Detail view (rather than term ID)

= 3.1.5 =

* Fixed issue with populating Drop Down & Multi Selects
* Fixed issue when saving Multi Select taxonomies 

= 3.1.4 =

* Updated plugin header information.

= 3.1.3 =

* Fixed compatibility issue with Gravity Forms: Post Updates plugin; custom taxonomies were not being saved.

= 3.1.2 =

* Fixed issue where setting first option was not possible due to typo in property name
* Updated how GFCPTAddon::get_base_path() method retrieves the base path

= 3.1.1 =
* Updated all calls to get_post_types() to use plugin-specific version which applies the 'gfcpt_post_type_args' filter. Props: mgratch
* Updated all calls to get_taxonomies() to use plugin-specific version which applies the 'gfcpt_tax_args' filter. Props: mgratch
* Fixed issue where missing script dependency caused tag-style entry of terms was not working. Props: mgratch
* Fixed issue where plugin's registration of GF preview styles was overwriting all other preview styles
* Fixed issue where taxonomies were not saved for delayed payment entries
* Updated Tag init JS to be bound to the 'gform_post_render' JS event which better supports other plugin integrations
* Updated Tag init JS file to be loaded in the footer
* Added 'gfcpt_get_posts_args' filter to allow modifying the posts that are populated into a field

= 3.1 =
* Added "gfcpt_taxonomy_args" filter to allow modifying the arguments used to retrieve taxonomy terms
* Added "gfcpt_post_type_args" filter to allow modifying the arguments used to retrive post types for selection in field settings
* Updated verbiage throughout plugin and readme.txt file
* Updated minimum required version of Gravity Forms to 1.9.3
* Updated "Save As Post Type" to be "Post Type" and moved location to standard settings tab
* Updated jQuery UI enqueue to use version from WP core
* Updated GFCPTAddonBase::load_taxonomy_hierarchical() method to support only displaying children of a parent term (requires "gfcpt_taxonomy_args" filter)
* Fixed styling issues with Enhanced-UI-enabled Single Line Text fields
* Fixed notice where get_base_path() was called statically by changing function to be static
* Fixed issue where taxonomy select on Drop Down field settings would not populate the selected taxonomy correctly
* Fixed issue with GF 1.9 where indirect modfiication of $field['inputs'] property had no effect; resolves issues using custom taxonomies with Checkbox fields

= 3.0.1 =
* Fixed minor bug causing a PHP warning (_FILE_)
* removed the restriction of not including scripts when a call is ajax

= 3.0 =
* Removed support for Gravity Forms v1.4.5. Now supports v1.5 and up (including 1.6)
* Added support for single line text fields
* Added ability to populate a dropdown with posts
* Added ability to set a parent post when saving a post form
* Multiselect control now supports "populate with taxonomy" too
* "first value" default overriden when populating with a taxonomy
* Shows taxonomy selections when designing the form
* Fixed support for conditional logic
* Previews now load taxonomy terms
* Previews can show enhanced UI (only in V1.6 and above)

= 2.0 =
* Added support for both Gravity Forms v1.5 beta and v.1.4.5
* Now supports linking taxonomies to Drop Downs, Multiple Choice or Checkboxes
* Integrated with GF v1.5 hooks for easier configuration (thanks to Alex and Carl from RocketGenius)
* Support linking more than 1 taxonomy to a form
* To keep in line with the GF standards, mapping a form to a CPT in GF v1.4.5 can now be done via the 'post title' field

= 1.0 =
* Initial Relase. First version.

== Frequently Asked Questions ==

= Does this plugin rely on anything? =
Yes, you need to install the [Gravity Forms plugin](ttp://bit.ly/gwizgravityforms) for this plugin to work.

= How do I map a form to a custom post type? =

1. Add a Post Title field to your form and click on it to open the field settings.
2. Below the "Description" field setting, you will find the "Post Type" setting.
3. Select the desired post type from the drop down (default is "Posts").

= How do I link a field to a custom taxonomy? =

1. Add the desired field to which the custom taxonomy should be mapped. Drop Down, Multi Select, Radio Buttons and Checkboxes fields are current supported.
2. Open the field settings by clicking on the field and click on the "Advanced" tab.
3. Check the "Populate with a Taxonomy" checkbox.
4. Select the desired taxonomy from the drop down that appears.

= How do I link the saved post to taxonomies using a single line text field? =

Single Line Text fields are a great way to allow users to select existing taxonomy terms and to also add new terms.

1. Add a Single Line Text field to your form.
2. Open the field settings by clicking on the field and click on the "Advanced" tab.
3. Check the "Save to Taxonomy" checkbox.
4. Select the desired taxonomy from the drop down that appears.
5. (optional) Check the "Enable Enhanced UI" checkbox to enable an awesome tag-input style UI (see screenshots).

Note: If the user inputs exising term names, the generated post will be assigned these terms. If the user inputs term names that do not exist, these terms will be added to the selected taxonomy and the generated post will also be assigned these terms.

= How do I set a parent post with the drop down field? =

When populating a Drop Down field with a post type, you may wish to set the selected post as the parent post for the generated post.

1. Add A Drop Down field to your form.
2. Click on the field to open the field settings. Then click on the "Advanced" tab.
3. Check the "Populate with Post Type" checkbox.
4. Select the desired post type from the drop down that appears. Be sure to select the **same post type** for which the post is being generated.

== Upgrade Notice ==

Please note, Gravity Forms 1.9.3 is now required.
