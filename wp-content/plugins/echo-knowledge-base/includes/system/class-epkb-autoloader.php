<?php  if ( ! defined( 'ABSPATH' ) ) exit;

spl_autoload_register(array('EPKB_Autoloader', 'autoload'), false);

/**
 * A class which contains the autoload function, that the spl_autoload_register
 * will use to autoload PHP classes.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Autoloader {

	public static function autoload( $class ) {
		static $classes = null;

		if ( $classes === null ) {
			$classes = array(

				// CORE
				'epkb_utilities'                    =>  'includes/class-epkb-utilities.php',
				'epkb_html_elements'                =>  'includes/class-epkb-html-elements.php',
				'epkb_icons'                        =>  'includes/class-epkb-icons.php',
				'epkb_input_filter'                 =>  'includes/class-epkb-input-filter.php',

				// SYSTEM
				'epkb_logging'                      =>  'includes/system/class-epkb-logging.php',
				'epkb_help_pointers'                =>  'includes/system/class-epkb-help-pointers.php',
				'epkb_help_upgrades'                =>  'includes/system/class-epkb-help-upgrades.php',
				'epkb_templates'                    =>  'includes/system/class-epkb-templates.php',
				'epkb_upgrades'                     =>  'includes/system/class-epkb-upgrades.php',
				'epkb_wpml'                         =>  'includes/system/class-epkb-wpml.php',

				// ADMIN CORE
				'epkb_admin_notices'                =>  'includes/admin/class-epkb-admin-notices.php',
				'epkb_welcome_screen'               =>  'includes/admin/class-epkb-welcome-screen.php',

				// ADMIN PLUGIN MENU PAGES
				'epkb_settings_controller'          =>  'includes/admin/settings/class-epkb-settings-controller.php',
				'epkb_settings_specs'               =>  'includes/admin/settings/class-epkb-settings-specs.php',
				'epkb_settings_db'                  =>  'includes/admin/settings/class-epkb-settings-db.php',
				'epkb_settings_page'                =>  'includes/admin/settings/class-epkb-settings-page.php',
				'epkb_analytics_page'               =>  'includes/admin/settings/class-epkb-analytics-page.php',

				// KB Configuration
				'epkb_kb_config_controller'         =>  'includes/admin/kb-configuration/class-epkb-kb-config-controller.php',
				'epkb_kb_config_specs'              =>  'includes/admin/kb-configuration/class-epkb-kb-config-specs.php',
				'epkb_kb_config_db'                 =>  'includes/admin/kb-configuration/class-epkb-kb-config-db.php',
				'epkb_kb_config_layouts'            =>  'includes/admin/kb-configuration/class-epkb-kb-config-layouts.php',
				'epkb_kb_config_layout_basic'       =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-basic.php',
				'epkb_kb_config_layout_tabs'        =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-tabs.php',
				'epkb_kb_config_page'               =>  'includes/admin/kb-configuration/class-epkb-kb-config-page.php',
				'epkb_kb_config_overview'           =>  'includes/admin/kb-configuration/class-epkb-kb-config-overview.php',
				'epkb_kb_config_sequence'           =>  'includes/admin/kb-configuration/class-epkb-kb-config-sequence.php',
				'epkb_kb_config_elements'           =>  'includes/admin/kb-configuration/class-epkb-kb-config-elements.php',
				'epkb_kb_demo_data'                 =>  'includes/admin/kb-configuration/class-epkb-kb-demo-data.php',
				'epkb_kb_menu_configuration'        =>  'includes/admin/kb-configuration/class-epkb-kb-menu-configuration.php',

				'epkb_add_ons_page'                 =>  'includes/admin/add-ons/class-epkb-add-ons-page.php',

				// FEATURES - LAYOUT
				'epkb_layout'                       =>  'includes/features/layouts/class-epkb-layout.php',
				'epkb_layout_basic'                 =>  'includes/features/layouts/class-epkb-layout-basic.php',
				'epkb_layout_tabs'                  =>  'includes/features/layouts/class-epkb-layout-tabs.php',
				'epkb_layouts_setup'                =>  'includes/features/layouts/class-epkb-layouts-setup.php',

				// FEATURES - KB
				'epkb_kb_handler'                   =>  'includes/features/kbs/class-epkb-kb-handler.php',
				'epkb_kb_search'                    =>  'includes/features/kbs/class-epkb-kb-search.php',

				// FEATURES - CATEGORIES
				'epkb_categories_db'                =>  'includes/features/categories/class-epkb-categories-db.php',
				'epkb_categories_admin'             =>  'includes/features/categories/class-epkb-categories-admin.php',
				'epkb_categories_array'             =>  'includes/features/categories/class-epkb-categories-array.php',

				// FEATURES - ARTICLES
				'epkb_articles_cpt_setup'           =>  'includes/features/articles/class-epkb-articles-cpt-setup.php',
				'epkb_articles_db'                  =>  'includes/features/articles/class-epkb-articles-db.php',
				'epkb_articles_admin'               =>  'includes/features/articles/class-epkb-articles-admin.php',
				'epkb_articles_array'               =>  'includes/features/articles/class-epkb-articles-array.php',
				'epkb_articles_setup'               =>  'includes/features/articles/class-epkb-articles-setup.php',

				// TEMPLATES
				'epkb_templates_various'            =>  'templates/helpers/class-epkb-templates-various.php'
			);
		}

		$cn = strtolower( $class );
		if ( isset( $classes[ $cn ] ) ) {
			/** @noinspection PhpIncludeInspection */
			include_once( plugin_dir_path( Echo_Knowledge_Base::$plugin_file ) . $classes[ $cn ] );
		}
	}
}
