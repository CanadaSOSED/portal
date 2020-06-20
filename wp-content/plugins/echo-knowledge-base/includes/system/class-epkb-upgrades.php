<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if plugin upgrade to a new version requires any actions like database upgrade
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Upgrades {

	public function __construct() {
        // will run after plugin is updated but not always like front-end rendering
		add_action( 'admin_init', array( 'EPKB_Upgrades', 'update_plugin_version' ) );
        add_filter( 'eckb_plugin_upgrade_message', array( 'EPKB_Upgrades', 'display_upgrade_message' ) );
        add_action( 'eckb_remove_upgrade_message', array( 'EPKB_Upgrades', 'remove_upgrade_message' ) );

		// New Features
		add_filter( 'eckb_count_of_new_features', array( 'EPKB_Upgrades', 'get_new_features_count' ) );
		add_action( 'eckb_update_last_seen_version', array( 'EPKB_Upgrades', 'update_last_seen_version' ) );
		add_filter( 'eckb_new_features_list', array( 'EPKB_Upgrades', 'features_list' ) );

		// ignore if plugin not activated
		if ( ! get_transient( '_epkb_plugin_installed' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_epkb_plugin_installed' );

		// return if activating from network or doing bulk activation
		if ( is_network_admin() || isset($_GET['activate-multi']) ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'forward_to_wizard_page' ), 20 );
	}

	/**
	 * Trigger display of wizard screen on plugin first activation or upgrade; does NOT work if multiple plugins installed at the smae time
	 */
	public function forward_to_wizard_page() {
		wp_safe_redirect( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( 1 ) . '&page=epkb-kb-configuration&wizard-on') );
		exit;
	}

    /**
     * If necessary run plugin database updates
     */
    public static function update_plugin_version() {

        $last_version = EPKB_Utilities::get_wp_option( 'epkb_version', null );

        // if plugin is up-to-date then return
        if ( empty($last_version) || version_compare( $last_version, Echo_Knowledge_Base::$version, '>=' ) ) {
            return;
        }

		// since we need to upgrade this plugin, on the Overview Page show an upgrade message
	    EPKB_Utilities::save_wp_option( 'epkb_show_upgrade_message', true, true );

        // upgrade the plugin
        self::invoke_upgrades( $last_version );

        // update the plugin version
        $result = EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version, true );
        if ( is_wp_error( $result ) ) {
	        EPKB_Logging::add_log( 'Could not update plugin version', $result );
            return;
        }
    }

    /**
     * Invoke each database update as necessary.
     *
     * @param $last_version
     */
    private static function invoke_upgrades( $last_version ) {

        // update all KBs
	    $update_config = false;
        $all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
        foreach ( $all_kb_configs as $kb_config ) {

            if ( version_compare( $last_version, '3.0.0', '<' ) ) {
                self::upgrade_to_v210( $kb_config );
	            $update_config = true;
            }

            if ( version_compare( $last_version, '3.0.0', '<' ) ) {
                self::upgrade_to_v220( $kb_config );
	            $update_config = true;
            }

	        if ( version_compare( $last_version, '3.1.0', '<' ) ) {
		        self::upgrade_to_v310( $kb_config );
		        $update_config = true;
	        }

	        if ( version_compare( $last_version, '3.1.1', '<' ) ) {
		        self::upgrade_to_v311( $kb_config );
		        $update_config = true;
	        }

	        if ( version_compare( $last_version, '4.4.2', '<' ) ) {
		        self::upgrade_to_v442( $kb_config );
		        $update_config = true;
	        }
			
			if ( version_compare( $last_version, '6.1.0', '<' ) ) {
		        self::upgrade_to_v610( $kb_config );
		        $update_config = true;
	        }

	        if ( version_compare( $last_version, '6.1.2', '<' ) ) {
		        self::upgrade_to_v612( $kb_config );
		        $update_config = true;
	        }
			
			if ( version_compare( $last_version, '6.4.0', '<' ) ) {
		        self::upgrade_to_v640( $kb_config );
		        $update_config = true;
	        }
			
	        // store the updated KB data
	        if ( $update_config ) {
		        epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_config['id'], $kb_config );
	        }
        }
    }

	private static function upgrade_to_v640( &$kb_config ) {
		
		if ( isset($kb_config['article-left-sidebar-width-v2']) ) {
			$kb_config['article-left-sidebar-desktop-width-v2'] = $kb_config['article-left-sidebar-width-v2'];
		}
		
		if ( isset($kb_config['article-content-width-v2']) ) {
			$kb_config['article-content-desktop-width-v2'] = $kb_config['article-content-width-v2'];
		}
		
		if ( isset($kb_config['article-right-sidebar-width-v2']) ) {
			$kb_config['article-right-sidebar-desktop-width-v2'] = $kb_config['article-right-sidebar-width-v2'];
		}
		
		if ( isset($kb_config['article-container-width-v2']) ) {
			$kb_config['article-container-desktop-width-v2'] = $kb_config['article-container-width-v2'];
		}
		
		if ( isset($kb_config['article-container-width-units-v2']) ) {
			$kb_config['article-container-desktop-width-units-v2'] = $kb_config['article-container-width-units-v2'];
		}
	}
	
	private static function upgrade_to_v612( &$kb_config ) {

		// if KB Main Page is Grid then use Elegant icons
		if ( $kb_config['kb_main_page_layout'] != 'Grid' ) {
			return;
		}

		$old_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], 'elay_categories_icons', array(), true );

		// update the existing icons to new format
		$new_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, array(), true );
		foreach ( $old_icons as $term_id => $icon_name ) {
			$new_icons[$term_id] = array(
				'type' => 'font',
				'name' => $icon_name,
				'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
				'image_size' => '',
				'image_thumbnail_url' => '',
				'color' => '#000000'
			);
		}

		EPKB_Utilities::save_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, $new_icons, true );

		// update category link from old to new config
		$kb_config['section_hyperlink_text_on'] = ( $kb_config['kb_main_page_category_link'] == 'default' ) ? 'off' : 'on';
	}

	private static function upgrade_to_v610( &$kb_config ) {

		// get old categories icons
		$old_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], 'epkb_categories_icons', array(), true );

		// if KB Main Page is Grid then use Elegant Layouts icons
		if ( $kb_config['kb_main_page_layout'] == 'Grid' ) {
			$old_grid_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], 'elay_categories_icons', array(), true );
			if ( ! empty($old_grid_icons) ) {
				$old_icons = $old_grid_icons;
			}
		}

		// update the existing icons to new format
		$new_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, array(), true );
		foreach ( $old_icons as $term_id => $icon_name ) {
			$new_icons[$term_id] = array(
				'type' => 'font',
				'name' => $icon_name,
				'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
				'image_size' => '',
				'image_thumbnail_url' => '',
				'color' => '#000000'
			);
		}
		
		EPKB_Utilities::save_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, $new_icons, true );
		
		// delete_option( 'epkb_categories_icons_' . $kb_config['id'] );

		// update category link from old to new config
		$kb_config['section_hyperlink_text_on'] = ( $kb_config['kb_main_page_category_link'] == 'default' ) ? 'off' : 'on';
	}
	
	private static function upgrade_to_v442( &$kb_config ) {
		$wpml_enabled = EPKB_Utilities::get_wp_option( 'epkb_wpml_enabled', false );
		$kb_config['wpml_is_enabled'] = $wpml_enabled === 'true';
	}

	private static function upgrade_to_v311( &$kb_config ) {
		$kb_config['breadcrumb_icon_separator'] = str_replace( 'ep_icon', 'ep_font_icon', $kb_config['breadcrumb_icon_separator'] );
		$kb_config['expand_articles_icon'] = str_replace( 'ep_icon', 'ep_font_icon', $kb_config['expand_articles_icon'] );
	}

	private static function upgrade_to_v310( &$kb_config ) {
		if ( empty($kb_config['css_version']) ) {
			$kb_config['css_version'] = 'css-legacy';
		}
	}

	private static function upgrade_to_v220( &$kb_config ) {
		if ( empty($kb_config['templates_for_kb']) ) {
			$kb_config['templates_for_kb'] = 'current_theme_templates';
		}

		if ( $kb_config['kb_main_page_layout'] == 'Sidebar' ) {
			$kb_config['kb_article_page_layout'] = 'Sidebar';
		}
	}

	private static function upgrade_to_v210( &$kb_config ) {
		if ( isset($kb_config['expand_articles_icon']) && substr($kb_config['expand_articles_icon'], 0, strlen('ep_' )) !== 'ep_' ) {
			$kb_config['expand_articles_icon'] = str_replace( 'icon_plus-box', 'ep_font_icon_plus_box', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'icon_plus', 'ep_font_icon_plus', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'arrow_triangle-right', 'ep_font_icon_right_arrow', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'arrow_carrot-right_alt2', 'ep_font_icon_arrow_carrot_right_circle', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'arrow_carrot-right', 'ep_font_icon_arrow_carrot_right', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'icon_folder-add_alt', 'ep_font_icon_folder_add', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'ep_ep_', 'ep_', $kb_config['expand_articles_icon'] );
		}
		if ( $kb_config['expand_articles_icon'] == 'ep_font_icon_arrow_carrot_right_alt2' ) {
			$kb_config['expand_articles_icon'] = 'ep_font_icon_arrow_carrot_right';
		}
	}

    /**
     * Show upgrade message on Overview Page.
     *
     * @param $output
     * @return string
     */
	public static function display_upgrade_message( $output ) {

		if ( EPKB_Utilities::get_wp_option( 'epkb_show_upgrade_message', false ) ) {
			
			$plugin_name = '<strong>' . __('Knowledge Base', 'echo-knowledge-base') . '</strong>';
			$output .= '<p>' . $plugin_name . ' ' . sprintf( esc_html( _x( 'plugin was updated to version %s.',
									' version number, link to what is new page', 'echo-knowledge-base' ) ),
									Echo_Knowledge_Base::$version ) . '</p>';
		}

		return $output;
	}
    
    public static function remove_upgrade_message() {
        delete_option('epkb_show_upgrade_message');
    }

	/**
	 * Count new features to be used in New Features menu item title
	 * @param $count
	 * @return int
	 */
	public static function get_new_features_count( $count ) {

		// if user did't see last new features
		$last_seen_version = EPKB_Utilities::get_wp_option( 'epkb_last_seen_version', '' );
		$features_list = self::features_list();
		
		foreach ( $features_list as $key => $val ) {
			if ( version_compare( $last_seen_version, $key ) < 0 ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Call when the user saw new features
	 */
	public static function update_last_seen_version() {
		
		$features_list = self::features_list();
		$last_feature_date = '';
		
		foreach ( $features_list as $key => $val ) {
			$last_feature_date = $key;
			break;
		}
	
		
		$result = EPKB_Utilities::save_wp_option( 'epkb_last_seen_version', $last_feature_date, true );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not update last seen features', $result );
			return false;
		}

		return true;
	}

	/**
	 * Filter last features array to add latest
	 * @param $features
	 * @return array
	 */
	public static function features_list( $features=array() ) {

		$features['2020.06.01'] = array(
			'plugin'            => __( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => __( 'Sub Category Filter', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'New sub-category filter option to narrow down your search.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/06/new-feature-sub-category-filter.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/demo-11/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.04.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Article Sidebars', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'New article sidebars with the ability to add your own Widgets, TOC and custom code.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/04/new-feature-wizards-sidebars.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2020.03.01'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Wizards', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Use Knowledge Base Wizard for an easy way to set up your KB and to choose from predefined Templates and colors.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/02/new-feature-wizards.jpg',
			'learn_more_url'    => 'https://www.youtube.com/watch?v=5uI9q2ipZxU&utm_medium=newfeatures&utm_content=home&utm_campaign=wizards',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.02.18'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Image Icons for Themes', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'Add Image icons to top categories in your theme. You can upload images or custom icons.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/02/image-icons-for-themes.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=image-icons',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.01.ac'] = array(
			'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
			'title'             => __( 'Categories Focused Layout', 'echo-knowledge-base'),
			'description'       => '<p>' . __( 'New layout that focuses on showing categories in a sidebar on both Category Archive and Article pages.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/01/category-focused-layout.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

	 /*	$features['2020.01.df'] = array(
			'plugin'            => 'KB Core',
			'title'             => 'New Option for Date Formats',
			'description'       => '<p>On Article pages, choose the format for the Last Updated and Created On dates.</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-features-article-category-sequence.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		); */

		$features['2019.12.ac'] = array(
				'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
				'title'             =>__(  'New Option to Show Articles Above Categories', 'echo-knowledge-base'),
				'description'       => '<p>' . __( 'On the Main Page (or Sidebar if you have the Elegant Layout add-on) the article can now be configured to appear above their peer categories and sub-categories.', 'echo-knowledge-base') . '</p>',
				'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/03/new-features-article-category-sequence-2.jpg',
				'learn_more_url'    => '',
				'plugin-type'       => 'core',
				'type'              => 'new-feature'
		);

		$features['2019.12.lv'] = array(
				'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
				'title'             => __( 'Three Additional Levels of Categories', 'echo-knowledge-base'),
				'description'       => '<p>' . __( 'You can now organize your categories and articles up to six levels deep, allowing you to have more complex documentation hierarchy.', 'echo-knowledge-base') . '</p>',
				'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-three-new-levels-3.jpg',
				'learn_more_url'    => '',
				'plugin-type'       => 'core',
				'type'              => 'new-feature'
		);

		$features['2019.12.oo'] = array(
				'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
				'title'             => __( 'Table of Content on Article Pages', 'echo-knowledge-base'),
				'description'       => '<p>' . __( 'Articles can now display table of content (TOC) on either side. The TOC has a list of headings and subheading. Users can easily see the article structure and can navigate to any section of the article.', 'echo-knowledge-base') . '</p>',
				'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-TOC-1.jpg',
				'learn_more_url'    => '',
				'plugin-type'       => 'core',
				'type'              => 'new-feature'
		);


		$features['2019.11.au'] = array(
				'plugin'            => __( 'KB Core', 'echo-knowledge-base'),
				'title'             => __( 'Articles Can Now Display Author and Creation Date', 'echo-knowledge-base'),
				'description'       => '<p>' . __( 'Configure article to display author and create date.', 'echo-knowledge-base') . '</p>',
				'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-core-new-meta-1.jpg',
				'learn_more_url'    => '',
				'plugin-type'       => 'core',
				'type'              => 'new-feature'
		);

		$features['2019.11.rf'] = array(
				'plugin'            => __( 'Article Rating and Feedback', 'echo-knowledge-base'),
				'title'             => __( 'User Can Rate Articles and Submit Feedback', 'echo-knowledge-base'),
				'description'       => '<p>' . __( 'This new add-on allows users to rate articles. They can aslso opt to fill out a form to submit details about their vote. The admin can access the analytics to see the most and least rated articles.', 'echo-knowledge-base') . '</p>',
				'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/EP'.'RF-featured-image.jpg',
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=new-plugin',
				'plugin-type'       => 'add-on',
				'type'              => 'new-addon'
		);

		$features['2019.11.hc'] = array(
				'plugin'            => __( 'Advanced Search', 'echo-knowledge-base'),
				'title'             => __( 'Search Results Include Category for Each Article', 'echo-knowledge-base'),
				'description'       => '<p>' . __( 'Search category filter now shows category hierarchy each found article is in.', 'echo-knowledge-base') . '</p>',
				'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/AS'.'EA-feature-results-category.jpg',
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=category-hierarchy',
				'plugin-type'       => 'add-on',
				'type'              => 'new-feature'
		);

		$features['2019.10.am'] = array(
				'plugin'            => __( 'KB Groups for Access Manager', 'echo-knowledge-base'),
				'title'             => __( 'Search Easily for Users to Add to KB Groups', 'echo-knowledge-base'),
				'description'       => '<p>' . __( 'The KB Groups add-on allows sorting of users into different groups and roles. The new search bar lets the administrator quickly find a specific user to make changes.', 'echo-knowledge-base') . '</p>',
				'image'             => 'https://www.echoknowledgebase.com/search-users-1/',
				'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/2-3-wp-users/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=user-search',
				'plugin-type'       => 'add-on',
				'type'              => 'new-feature'
		);

		return $features;
	}

}
