<?php
/**
 * The help page for the WP Carousel
 *
 * @package WP Carousel
 * @subpackage wp-carousel-free/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access.

/**
 * The help class for the WP Carousel
 */
class WP_Carousel_Free_Upgrade {

	/**
	 * Wp Carousel Pro single instance of the class
	 *
	 * @var null
	 * @since 2.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WP_Carousel_Free_Help Instance
	 *
	 * @since 2.0.0
	 * @static
	 * @see sp_wpcp_help()
	 * @return self Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add admin menu.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function upgrade_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=sp_wp_carousel', __( 'Premium', 'wp-carousel-free' ), __( 'Premium', 'wp-carousel-free' ), 'manage_options', 'wpcf_upgrade', array(
				$this,
				'upgrade_page_callback',
			)
		);
	}

	/**
	 * The WP Carousel Upgrade Callback.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function upgrade_page_callback() {
			?> <div class="wrap about-wrap sp-wpcf-upgrade">
			<h1><?php _e( 'Upgrade to <span>WordPress Carousel Pro</span>', 'wp-carousel-free' ); ?></h1>
			<p class="about-text">
			<?php
			esc_html_e(
				'Get more Advanced Functionality & Flexibility with the Premium version.', 'wp-carousel-free'
			);
			?>
			</p>
			<div class="wp-badge"></div>
			<ul>
				<li class="wpcf-upgrade-btn"><a href="https://shapedplugin.com/plugin/wordpress-carousel-pro/" target="_blank">Buy WP Carousel Pro <i class="fa fa-caret-right"></i></a></li>
				<li class="wpcf-upgrade-btn"><a href="https://wordpresscarousel.com" target="_blank">Live Demo & All Features <i class="fa fa-angle-double-right"></i></a></li>
			</ul>

			<hr>

			<div class="sp-wpc-pro-features">
				<h2 class="sp-wpc-text-center">Premium Features Built for Future</h2>
				<p class="sp-wpc-text-center sp-wpc-pro-subtitle">We've added 100+ extra features in our Premium Version of this plugin. Let’s see some amazing features.</p>
				<div class="feature-section three-col">
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Responsive & SEO Friendly</h3>
							<p>Design carousels for desktop, tablet & mobile. Your carousels will adjust for any device. WordPress Carousel Pro follows the best SEO practices & performs speedily on all sites.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Slide Anything</h3>
							<p>WordPress Carousel Pro allows you to create a carousel slider where the content for each slide can be anything you want – image, post, product, content, video, text, HTML, Shortcodes etc.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Compatible with any Theme</h3>
							<p>Guaranteed to work with your any WordPress site including Genesis, Divi, WooThemes, ThemeForest or any theme, in any WordPress single site and multisite network.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Advanced Shortcode Generator</h3>
							<p>WordPress Carousel Pro comes with a built-in easy to use Shortcode Generator that helps you save, edit, copy and paste shortcode where you want!</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>No Coding Required</h3>
							<p>The plugin is built with unlimited stunning styling options like color, font family, size, alignment etc. to stylize your own way without any limitation. No Coding Skills Needed!</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>840+ Google Fonts</h3>
							<p>WordPress Carousel Pro supports the list of Google font family with a huge collection of 840+ fonts. Customize the font family, size, transform, spacing, color, line-height etc.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Image Carousel</h3>
							<p>WordPress Carousel Pro allows you to create beautiful image carousels for your site in minutes! Upload images via WordPress regular gallery, create a gallery to make carousel.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Post Carousel</h3>
							<p>Display posts from multiple Categories, Tags, Formats, or Types: Latest, Taxonomies, Specific etc. Show the post contents: title, image, excerpt, read more, category, date, author, tags, comments etc.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>WooCommerce Product Carousel</h3>
							<p>Filter by different product types. (e.g. latest, categories, specific products etc.). Show/hide the product name, image, price, excerpt, read more, rating, add to cart button etc.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Content Carousel</h3>
							<p>Slide anything you want based on your WordPress site. (e.g. images, text, HTML, shortcodes, any custom contents etc.) You can sort slide content by drag and drop easily.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Video Carousel</h3>
							<p>Show videos from multiple sources: YouTube, Vimeo, Dailymotion, mp4, WebM, and even self-hosted video with Lightbox. A customizable video icon will place over the video thumb.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Drag & Drop Builder</h3>
							<p>Drag & Drop carousel content ordering is one of the amazing features of WordPress Carousel Pro. You can order your content easily from WordPress default gallery settings.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Image Internal & External Links</h3>
							<p>You can link to each carousel image easily. You can add a link to each carousel in WordPress gallery settings. You can set URLs to them, they can open in the same or new tab.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Lightbox Functionality for Image</h3>
							<p>Lightbox is one of the impressive premium features of WordPress Carousel Pro. You can also make lightbox image group. To have the images in lightbox, just turn on the lightbox.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>One Page Carousel Slider</h3>
							<p>You are able to build one column carousel slider. You can add slider caption & description to the respective images. You can change slider colors with your desired color!</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Carousel Settings</h3>
							<p>Carousel controls e.g. 6 Navigation arrows & 9 Positions, Pagination dots, AutoPlay & speed, Stop on hover, looping, Touch Swipe, scroll, key navigation, Mouse Draggable etc.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Carousel Mode</h3>
							<p>WordPress Carousel Pro has two carousel mode: Standard and Ticker (Smooth looping, with no pause). You can change the carousel mode based on your choice or demand.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Sliding and Hover Effects</h3>
							<p>You can set hover and sliding effects for images like, gray-scale, overlay opacity, fade in or out etc. that are both edgy and appealing. Try them all. Use the one you like best.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Custom Image Re-sizing</h3>
							<p>You’ll find in the image settings all cropping sizes available. You can change the default size of your images in the settings. The width is dynamic with fixed height through CSS.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Fully Internationalized</h3>
							<p>WordPress Carousel Pro is fully multilingual ready with WPML, Polylang, qTranslate-x, GTranslate, Google Language Translator, WPGlobus etc. popular translation plugins.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Multi-site Supported</h3>
							<p>One of the important features of WordPress Carousel Pro is Multi-site ready. The Premium version works great in the multi-site network. You’ll find details here.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Widget Ready</h3>
							<p>To include logo carousel or grid inside a widget area is as simple as including any other widget! The plugin is widget ready. Create a shortcode first and use it simply in the widget.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Advanced Settings</h3>
							<p>The plugin is completely customizable and also added a custom CSS field option to override styles. You can also enqueue or dequeue scripts/CSS to avoid conflicts and loading issue.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Right to Left Support</h3>
							<p>WordPress Carousel Pro is Right-to-Left supported. For Arabic, Hebrew, Persian, etc. languages, you can select the right-to-left option for carousel direction, without writing any CSS.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Lifetime Auto Update & Support</h3>
							<p>WordPress Carousel Pro is integrated with automatic updates which allows you to update the plugin through the WordPress dashboard without downloading them manually.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Fast and Friendly Support</h3>
							<p>If you need any assistance, we’re here to help you with the instant support forum. A professional support team is always ready whenever you face with any issues to use the plugin.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Page Builders Compatibility</h3>
							<p>WordPress Carousel Pro works nicely with the popular Page Builders plugins: Gutenberg, WPBakery, Elementor, Divi builder, BeaverBuilder, SiteOrgin, Themify Builder etc.</p>
						</div>
					</div>

				</div>
			</div>
			<hr>					
			<h2 class="text-center sp-wpcp-promo-video-title">Watch How <b>WordPress Carousel Pro</b> Works</h2>
				<div class="headline-feature feature-video">

				<iframe width="1050" height="590" src="https://www.youtube.com/embed/XMYYgFD7ZIA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
				<hr>
				<div class="sp-wpcp-join-community text-center">
					<h2>Join the <b>20000+</b> Happy Users Worldwide!</h2>
					<a class="wpcf-upgrade-btn" target="_blank" href="https://shapedplugin.com/plugin/wordpress-carousel-pro/">Get a license instantly</a>
					<p>Every purchase comes with <b>7-day</b> money back guarantee and access to our incredibly Top-notch Support with lightening-fast response time and 100% satisfaction rate. One-Time payment, lifetime automatic update.</p>
				</div>
				<br>
				<br>

				<hr>
				<div class="sp-wpc-upgrade-sticky-footer sp-wpc-text-center">
					<p><a href="https://wordpresscarousel.com" target="_blank" class="button
					button-primary">Live Demo</a> <a href="https://shapedplugin.com/plugin/wordpress-carousel-pro/" target="_blank" class="button button-primary">Upgrade Now</a></p>
				</div>

			</div>
<?php	}

}
