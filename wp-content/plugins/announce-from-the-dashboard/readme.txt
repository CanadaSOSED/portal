=== Announce from the Dashboard ===
Contributors: gqevu6bsiz
Donate link: http://gqevu6bsiz.chicappa.jp/please-donation/?utm_source=wporg&utm_medium=donate&utm_content=afd&utm_campaign=1_5_1
Tags: admin, dashboard, news, announce, role, user
Requires at least: 3.8
Tested up to: 4.3
Stable tag: 1.5.1
License: GPL2

Announcement to users on the Dashboard.

== Description ==

This plugin to show announce for per user roles.

And, if you want to change plugin capability, please refer to this code.

For example add filter:
`
function afd_custom_change_capability( $capability ) {
	// plugin minimum capability
	$capability = 'edit_posts';
	return $capability;
}
add_filter( 'afd_capability_manager' , 'afd_custom_change_capability' );
`

And, if you want to add filter, please refer to this code.

For example add filter:

`
function afd_custom_filter( $announces ) {
	// filter
	return $announces;
}

add_filter( 'afd_before_announce' , 'afd_custom_filter' );
`



== Installation ==

1. Upload the full directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to `WP-Admin -> Settings -> Announcement settings for Dashboard` to configure the plugin.

== Frequently Asked Questions ==

= A question that someone might have =

= What about foo bar? =

== Screenshots ==

1. Settings Interface
2. Configuration Example
3. Display for Example
4. Metabox for Example

== Changelog ==

= 1.5.1 =
* Fixed: Html miss.

= 1.5 =
* Added: Support to do_shortcode.
* Added: Add to some actions/filters.
* Added: Add class for metabox.
* Fixed: How to date range check at main blog tymezone.
* Updated: Improve useful to settings interface.
* Changed: Specification Change.

= 1.4.4 =
* Fixed: Get the current user role.

= 1.4.3 =
* Fixed: Referrer check of Ajax.
* Fixed: Small bug fixes.

= 1.4.2 =
* Fixed: Changed the action priority.
* Updated: Change the priority of the metabox.

= 1.4.1 =
* Added: Settings data import of Child blog in that case of Multisite.

= 1.4 =
* Updated: Clear the style with Non style.
* Updated: Improve useful to settings interface.
* Added: Support to site per announce on Multisite.
* Added: Change user role of this plugin working.

= 1.3.1 =
* Added: Change the announce order.

= 1.3 =
* Added: Data range feature.

= 1.2.4.2 =
* Fixed: Data update way.

= 1.2.4.1 =
* Updated: Screen shots.

= 1.2.4 =
* Updated: Compatible to 3.8-RC1.
* Added: Show confirmation to before bulk delete.
* Changed: Update save way.
* Bug fixed: Submit empty data when data is lose.

= 1.2.3.1 =
* Update to Screenshots.

= 1.2.3 =
* Compatibility Check for 3.6.
* Support for mp6.
* Support for SSL.

= 1.2.2.2 =
* Added a confirmation of Nonce field.
* Changed notice for donate.
* Checked compatibility with 3.6 RC1.

= 1.2.2.1 =
* Link mistake.

= 1.2.2 =
* Fixed bug : Get of user role.

= 1.2.1 =
* Fixed bug : Problems that can not be individually removed.
* Fixed bug : Error when using the Jetpack.

= 1.2 =
* Added a notation of donation.
* Bulk Delete is possible.
* Added a Non style attribute.
* Changed a little ease of use.

= 1.1.2 =
Bug fix : first metabox announce didn't appear.

= 1.1.1 =
Translations for German have been updated.

= 1.1 =
view to metabox.

= 1.0.1 =
I've changed the readme.txt.

= 1.0 =
This is the initial release.

== Upgrade Notice ==

= 1.0 =

== 日本語でのご説明 ==

このプラグインは、ダッシュボードにお知らせを表示するプラグインです。
ユーザーの権限グループ別に、編集者のみへの表示、
投稿者と寄稿者と購読者のみに表示する設定もできます。