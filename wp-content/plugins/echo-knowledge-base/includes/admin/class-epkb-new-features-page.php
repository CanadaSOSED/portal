<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display New Features page
 *
 * @copyright   Copyright (C) 2019, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_New_Features_Page {

	/**
	 * Each plugin will have a filter to add count of the new features
	 * look eckb_count_of_new_features filter
	 */
	public static function get_menu_item_title() {

		$counter = '';
		$new_features_count = apply_filters( 'eckb_count_of_new_features', 0 );
		if ( ! empty($new_features_count) && EPKB_Utilities::is_positive_int($new_features_count) ) {
			$counter = '<span class="update-plugins"><span class="plugin-count">' . $new_features_count . '</span></span>';
		}

		return '<span style="color:#5cb85c;">' . __( 'New Features', 'echo-knowledge-base' ) . '<span class="dashicons dashicons-star-filled" style="font-size: 13px;line-height: 20px;"></span></span>' . $counter;
	}

	/**
	 * Display the New Features page
	 */
	public function display_new_features_page() {

		// update last seen version of KB and add-ons to current version
		do_action('eckb_update_last_seen_version');  // clears menu count of versions not seen

		ob_start(); ?>

		<!-- This is to catch WP JS garbage -->
		<div class="wrap">
			<h1></h1>
		</div>
		<div class="">		</div>
		<div id="ekb-admin-page-wrap" class="epkb-features-container">

			<!-- Top Banner -->
			<div class="epkb-features__top-banner">
				<div class="epkb-features__top-banner__inner">
					<h1><?php esc_html_e( 'New Features for Knowledge Base and its Add-ons', 'echo-knowledge-base' ); ?></h1>
					<p><?php printf ( __( 'Here are the latest features that we\'ve released! You can also submit a feature request %s here%s.', 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/feature-request/" target="_blank">', '</a>' ); ?></p>
				</div>
				<svg class="epkb-features__top-banner__background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none">
					<polygon class="svg--sm" fill="rgba(253, 254, 255, .2)" points="0,0 30,100 65,21 90,100 100,75 100,100 0,100"/>
					<polygon class="svg--lg" fill="rgba(253, 254, 255, .2)" points="0,0 15,100 33,21 45,100 50,75 55,100 72,20 85,100 95,50 100,80 100,100 0,100" />
				</svg>
			</div>

			<!-- Tab Navigation -->
			<div class="epkb-features__nav-container">
				<ul>
					<li id="features" class="epkb-fnc--active-tab"><?php _e( 'New Features', 'echo-knowledge-base' ); ?></li>
					<!-- <li id="history">History</li>-->
				</ul>
			</div>

			<!-- Tab Panels -->
			<div class="epkb-features__panel-container">
				<div id="features-panel" class="epkb-features__panel epkb-features__panel--active">
					<?php self::display_new_features_details();  ?>
				</div>
				<div id="history-panel" class="epkb-features__panel"></div>
			</div>

		</div>      <?php

		echo ob_get_clean();
	}

	/**
	 * Display all new features
	 * add-ons
	 * for epkb_new_features look EPKB_KB_Config_Overview::display_overview_box function
	 * $history = array('2019.1') = array([history_item],[history_item]...)
	 */
	private static function display_new_features_details() {

		// get new features in last release
		$features = apply_filters('eckb_new_features_list', array());
		$features = empty($features) || ! is_array($features) ? array() : $features;		?>
		<div class="epkb-grid-row-5-col">
			<?php
			foreach ( $features as $date => $feature ) {
				self::new_feature( $date, $feature );
			}        ?>
		</div>		  <?php
	}

	/**
	 * Display feature information with image.
	 * @param $date
	 * @param array $values
	 */
	private static function new_feature( $date, $values = array () ) {
		global $wp_locale; 
		
		$season = explode('.', $date);
		if ( ! empty($season[0]) && ! empty($season[1]) ) {
			$monthName = ucfirst($wp_locale->get_month_abbrev($wp_locale->get_month($season[1])));
			$date = $monthName . ' ' . $season[0];
		}

		$pluginType = '';
		switch ($values['plugin-type']) {
			case 'add-on':
				$pluginType = '<div class="epkb-fnf__meta__addon">' . __( 'Add-on', 'echo-knowledge-base') . '</div>';
				break;
			case 'core':
				$pluginType = '<div class="epkb-fnf__meta__core">' . __( 'Core', 'echo-knowledge-base') . '</div>';
				break;
		}
		$type = '';
		switch ($values['type']) {
			case 'new-addon':
				$type = '<span class="epkb-fnf__header__new-add-on"> <i class="epkbfa epkbfa-plug" aria-hidden="true"></i> ' . __( 'New Add-on', 'echo-knowledge-base') . '</span>';
				break;
			case 'new-feature':
				$type = '<span class="epkb-fnf__header__new-feature"> <i class="epkbfa epkbfa-star" aria-hidden="true"></i>' . __( 'New Feature', 'echo-knowledge-base') . '</span>';
				break;
		}		?>

		<div class="epkb-features__new-feature" class="add_on_product">

			<div class="epkb-fnf__header">
				<?php echo $type; ?>
				<h3 class="epkb-fnf__header__title"><?php esc_html_e($values['title']); ?></h3>

			</div>

			<div class="featured_img epkb-fnf__img">
				<img src="<?php echo empty($values['image']) ? '' : $values['image']; ?>">
			</div>

			<div class="epkb-fnf__meta">
				<?php echo $pluginType; ?>
				<div class="epkb-fnf__meta__plugin"><?php esc_html_e($values['plugin']); ?></div>
				<div class="epkb-fnf__meta__date"><?php echo $date ?></div>
			</div>

			<div class="epkb-fnf__body">
				<p>
					<?php echo wp_kses_post($values['description']); ?>
				</p>
			</div>			<?php
			if ( ! empty($values['learn_more_url']) ) {         ?>
				<div class="epkb-fnf__button-container">
					<a class="button primary-btn" href="<?php echo $values['learn_more_url']; ?>" target="_blank"><?php _e( 'Learn More', 'echo-knowledge-base' ); ?></a>
				</div>			<?php
			}       ?>

		</div>    <?php
	}
}

