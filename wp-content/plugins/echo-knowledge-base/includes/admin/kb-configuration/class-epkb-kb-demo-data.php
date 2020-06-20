<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Holds demo data for KB
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Demo_Data {

	public static function get_category_demo_data( $layout, $kb_config ) {

		$articles_sequence_new_value = isset($kb_config['articles_display_sequence']) ? $kb_config['articles_display_sequence'] : '';
		$categories_sequence_new_value = isset($kb_config['categories_display_sequence']) ? $kb_config['categories_display_sequence'] : '';

		if ( $categories_sequence_new_value == 'alphabetical-title' ) {
			$category_seq = array(
				'11' => array(), //'111' => array('1111' => array())),
				'22' => array( '222' => array( '2222' => array() ) ),
				'33' => array(), //'333' => array('3333' => array())),
				'66' => array(),
				'55' => array(), //'555' => array('5555' => array())),
				'44' => array( '444' => array( '4444' => array() ) )
			);
		} else {
			$category_seq = array(
				'11' => array(), //'111' => array('1111' => array())),
				'22' => array( '222' => array( '2222' => array() ) ),
				'33' => array(), //'333' => array('3333' => array())),
				'44' => array( '444' => array( '4444' => array() ) ),
				'55' => array(), //'555' => array('5555' => array())),
				'66' => array()
			);
		}

		if ( $layout == EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME ) {
			if ( $categories_sequence_new_value == 'alphabetical-title' ) {
				$category_seq['11'] += array( '112' => array() );
				$category_seq['11'] += array( '115' => array() );
				$category_seq['11'] += array( '113' => array() );
				$category_seq['11'] += array( '116' => array() );
				$category_seq['11'] += array( '114' => array() );
				$category_seq['11'] += array( '111' => array( '1111' => array() ) );
			} else {
				$category_seq['11'] += array( '111' => array( '1111' => array() ) );
				$category_seq['11'] += array( '112' => array() );
				$category_seq['11'] += array( '113' => array() );
				$category_seq['11'] += array( '114' => array() );
				$category_seq['11'] += array( '115' => array() );
				$category_seq['11'] += array( '116' => array() );
			}
		}
		
		$cat_11_descr = __( 'Your Account Settings ', 'echo-knowledge-base' );
		$cat_22_descr = __( 'Search & Browse for Items', 'echo-knowledge-base' );
		$cat_33_descr = __( 'Payment methods, gift cards and fees', 'echo-knowledge-base' );
		$cat_44_descr = __( 'Rates, Rules , Tracking', 'echo-knowledge-base' );
		$cat_55_descr = __( 'Refund conditions and rules', 'echo-knowledge-base' );
		$cat_66_descr = __( 'Promotions and special pricing', 'echo-knowledge-base' );

		if ( $articles_sequence_new_value == 'alphabetical-title' ) {
			$article_seq = array(
				'11' => array(0 => __( 'Account', 'echo-knowledge-base' ), 1 => $cat_11_descr, 2 => __( 'Create Account', 'echo-knowledge-base' ), 3 => __( 'Order History', 'echo-knowledge-base' ), 4 => __( 'Login Issues', 'echo-knowledge-base' )),
				'22' => array(0 => __( 'Ordering', 'echo-knowledge-base' ), 1 => $cat_22_descr, 2 => __( 'Editing an Order', 'echo-knowledge-base' ), 3 => __( 'Placing an Order', 'echo-knowledge-base' ), 4 => __( 'Pre/back-orders', 'echo-knowledge-base' )),
				'222' => array(0 => __( 'Order Processing', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Canceling an Order', 'echo-knowledge-base' ), 3 => __( 'Order verification', 'echo-knowledge-base' ), 4 => __( 'Un-canceling an Order', 'echo-knowledge-base' )),
				'2222' => array(0 => __( 'Order Processing Time', 'echo-knowledge-base' ), 1 => '', 2 => __( 'How Will I Know If You Have Received My Order?', 'echo-knowledge-base' ), 3 => __( 'When is My Order Processed?', 'echo-knowledge-base' ), 4 => __( 'When Will I Receive My Order?', 'echo-knowledge-base' )),
				'33' => array(0 => __( 'Payment', 'echo-knowledge-base' ), 1 => $cat_33_descr, 2 => __( 'Gift Cards', 'echo-knowledge-base' ), 3 => __( 'Payment methods', 'echo-knowledge-base' ), 4 => __( 'Taxes and Fees', 'echo-knowledge-base' )),
				'44' => array(0 => __( 'Shipping', 'echo-knowledge-base' ), 1 => $cat_44_descr, 2 => __( 'Shipping Issues', 'echo-knowledge-base' ), 3 => __( 'Shipping options', 'echo-knowledge-base' ), 4 => __( 'Tracking an Order', 'echo-knowledge-base' )),
				'444' => array(0 => __( 'Shipping Options', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Calculating the Shipping Cost', 'echo-knowledge-base' ), 3 => __( 'Free shipping', 'echo-knowledge-base' ), 4 => __( 'PO Boxes', 'echo-knowledge-base' )),
				'4444' => array(0 => __( 'Shipping Rates', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Australia', 'echo-knowledge-base' ), 3 => __( 'Canada', 'echo-knowledge-base' ), 4 => __( 'France', 'echo-knowledge-base' ), 5 => __( 'Germany', 'echo-knowledge-base' ), 6 => __( 'Spain', 'echo-knowledge-base' ), 7 => __( 'Russia', 'echo-knowledge-base' ), 8 => __( 'China', 'echo-knowledge-base' ), 9 => __( 'US', 'echo-knowledge-base' )),
				'55' => array(0 => __( 'Returns & Refunds', 'echo-knowledge-base' ), 1 => $cat_55_descr, 2 => __( 'Creating a Return', 'echo-knowledge-base' ), 3 => __( 'Return Guidelines', 'echo-knowledge-base' ), 4 => __( 'Return Shipping', 'echo-knowledge-base' )),
				'66' => array(0 => __( 'Pricing/Promos', 'echo-knowledge-base' ), 1 => $cat_66_descr, 2 => __( 'Newsletter', 'echo-knowledge-base' ), 3 => __( 'Pricing Guidelines', 'echo-knowledge-base' ), 4 => __( 'Promotions', 'echo-knowledge-base' )),
			);
		} else {
			$article_seq = array(
				'11' => array(0 => __( 'Account', 'echo-knowledge-base' ), 1 => $cat_11_descr, 2 => __( 'Create Account', 'echo-knowledge-base' ), 3 => __( 'Login Issues', 'echo-knowledge-base' ), 4 => __( 'Order History', 'echo-knowledge-base' )),
				'22' => array(0 => __( 'Ordering', 'echo-knowledge-base' ), 1 => $cat_22_descr, 2 => __( 'Placing an Order', 'echo-knowledge-base' ), 3 => __( 'Editing an Order', 'echo-knowledge-base' ), 4 => __( 'Pre/back-orders', 'echo-knowledge-base' )),
				'222' => array(0 => __( 'Order Processing', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Order verification', 'echo-knowledge-base' ), 3 => __( 'Canceling an Order', 'echo-knowledge-base' ), 4 => __( 'Un-canceling an Order', 'echo-knowledge-base' )),
				'2222' => array(0 => __( 'Order Processing Time', 'echo-knowledge-base' ), 1 => '', 2 => __( 'When is My Order Processed?', 'echo-knowledge-base' ), 3 => __( 'How Will I Know If You Have Received My Order?', 'echo-knowledge-base' ), 4 => __( 'When Will I Receive My Order?', 'echo-knowledge-base' )),
				'33' => array(0 => __( 'Payment', 'echo-knowledge-base' ), 1 => $cat_33_descr, 2 => __( 'Payment methods', 'echo-knowledge-base' ), 3 => __( 'Taxes and Fees', 'echo-knowledge-base' ), 4 => __( 'Gift Cards', 'echo-knowledge-base' )),
				'44' => array(0 => __( 'Shipping', 'echo-knowledge-base' ), 1 => $cat_44_descr, 2 => __( 'Shipping Options', 'echo-knowledge-base' ), 3 => __( 'Tracking an Order', 'echo-knowledge-base' ), 4 => __( 'Shipping Issues', 'echo-knowledge-base' )),
				'444' => array(0 => __( 'Shipping Options', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Calculating the Shipping Cost', 'echo-knowledge-base' ), 3 => __( 'Free Shipping', 'echo-knowledge-base' ), 4 => __( 'PO Boxes', 'echo-knowledge-base' )),
				'4444' => array(0 => __( 'Shipping Rates', 'echo-knowledge-base' ), 1 => '', 2 => __( 'US', 'echo-knowledge-base' ), 3 => __( 'Canada', 'echo-knowledge-base' ), 4 => __( 'Germany', 'echo-knowledge-base' ), 5 => __( 'France', 'echo-knowledge-base' ), 6 => __( 'Spain', 'echo-knowledge-base' ), 7 => __( 'Russia', 'echo-knowledge-base' ), 8 => __( 'Hong Kong', 'echo-knowledge-base' ), 9 => __( 'Australia', 'echo-knowledge-base' )),
				'55' => array(0 => __( 'Returns & Refunds', 'echo-knowledge-base' ), 1 => $cat_55_descr, 2 => __( 'Creating a Return', 'echo-knowledge-base' ), 3 => __( 'Return Guidelines', 'echo-knowledge-base' ), 4 => __( 'Return Shipping', 'echo-knowledge-base' )),
				'66' => array(0 => __( 'Pricing/Promos', 'echo-knowledge-base' ), 1 => $cat_66_descr, 2 => __( 'Pricing Guidelines', 'echo-knowledge-base' ), 3 => __( 'Promotions', 'echo-knowledge-base' ), 4 => __( 'Newsletter', 'echo-knowledge-base' )),
			);
		}

		if ( $layout == EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME ) {
			
			$article_seq['111'] = array( 0 => __( 'Login Issues', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Problems Logging in', 'echo-knowledge-base' ), 3 => __( 'Too Many Login Attempts', 'echo-knowledge-base' ) );
			$article_seq['1111'] = array(0 => __( 'Premium Support', 'echo-knowledge-base' ), 1 => '', 2 => __( '24/7 Phone Line', 'echo-knowledge-base' ), 3 => __( 'Immediate Assistance', 'echo-knowledge-base' ));
			$article_seq['112'] = array( 0 => __( 'Account Settings', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Changing Your Password', 'echo-knowledge-base' ), 3 => __( 'Recover Password', 'echo-knowledge-base' ) );
			$article_seq['113'] = array( 0 => __( 'History', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Download Account History', 'echo-knowledge-base' ), 3 => __( 'Your Browsing History', 'echo-knowledge-base' ) );
			$article_seq['114'] = array( 0 => __( 'Premium Account', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Different Types', 'echo-knowledge-base' ), 3 => __( 'How Do I Upgrade to a Premium Account', 'echo-knowledge-base' ), 4 => __( 'How do I Manage my Premium Account', 'echo-knowledge-base' ) );
			$article_seq['115'] = array( 0 => __( 'Additional Services', 'echo-knowledge-base' ), 1 => '', 2 => __( 'What is Open Profile', 'echo-knowledge-base' ), 3 => __( 'What Currencies are Supported', 'echo-knowledge-base' ) );
			$article_seq['116'] = array( 0 => __( 'Other', 'echo-knowledge-base' ), 1 => '', 2 => __( 'Account Overview', 'echo-knowledge-base' ), 3 => __( 'Create Account', 'echo-knowledge-base' ) );
		}
		
		
		$category_icons = EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME == $layout ?
							// Tab Layout
							array(
								111 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'ep_font_icon_person',   
									), // Login Issues
								112 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'ep_font_icon_gears',       
									), // Account Settings
								113 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'ep_font_icon_data_report',       
									),  // History
								114 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'epkbfa-cubes',                     
									),// Premium Account
								115 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'epkbfa-bookmark-o',              
									),  // Additional Services
								116 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'ep_font_icon_pencil'            
									),   // Other
							) :
							// Basic Layout
							array(
								11 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'ep_font_icon_person',          
									),     // Account
								22 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'ep_font_icon_shopping_cart',        
									),// Ordering
								33 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'ep_font_icon_credit_card',         
									), // Payment
								44 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'epkbfa-truck',               
									),       // Shipping
								55 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'epkbfa-bookmark',              
									),     // Returns & Refunds
								66 => array( 
										'type' => 'font',
										'image_id' => 0,
										'image_thumbnail_url' => '',
										'name' => 'ep_font_icon_money'           
									),      // Pricing / Promos
							);

		// if current KB has at least one image then show demo page with images
		$image_found = false;
		if ( empty($kb_config['theme_name']) ) {
			$categories_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, array(), true );
			foreach( $categories_icons as $categories_icon ) {
				if ( $categories_icon['type'] == 'image' ) {
					$image_found = true;
					break;
				}
			}
		}

		// show image icons only on themes with icons
		if ( EPKB_Icons::is_theme_with_image_icons( $kb_config ) || $image_found ) { // || EPKB_KB_Wizard::get_demo_icons_type($kb_config['id']) == 'image' ) {

			$category_icons = array(
				11 => array(
					'type' => 'image',
					'image_id' => 0,
					'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/women-icon.png',
					'name' => 'ep_font_icon_person',
				),     // Account
				22 => array(
					'type' => 'image',
					'image_id' => 0,
					'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/e-commerce-icon.png',
					'name' => 'ep_font_icon_shopping_cart',
				),// Ordering
				33 => array(
					'type' => 'image',
					'image_id' => 0,
					'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/payment-icon.png',
					'name' => 'ep_font_icon_credit_card',
				), // Payment
				44 => array(
					'type' => 'image',
					'image_id' => 0,
					'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/earth-icon.png',
					'name' => 'epkbfa-truck',
				),       // Shipping
				55 => array(
					'type' => 'image',
					'image_id' => 0,
					'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/info-icon.png',
					'name' => 'epkbfa-bookmark',
				),     // Returns & Refunds
				66 => array(
					'type' => 'image',
					'image_id' => 0,
					'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/price-tag-icon.png',

					'name' => 'ep_font_icon_money'
				),      // Pricing / Promos
			);
			
		}
			
		return array( 'category_seq' => $category_seq, 'article_seq' => $article_seq, 'category_icons' => $category_icons );
	}

	public static function get_demo_article() {
		return "
			<h1>Header Level 1</h1>
			<strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante.
			 Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em>
			<h2>Header Level 2</h2>
			<ol>
	            <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
	            <li>Aliquam tincidunt mauris eu risus.</li>
			</ol>
			<h3>Header Level 3</h3>
			<ul>
	            <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
	            <li>Aliquam tincidunt mauris eu risus.</li>
			</ul>
			";
	}
	
	// Demo categories for sidebar 
	public static function get_demo_categories_list() {
		return array(
		/*	array(
				'name' => 'FAQs',
				'active' => true,
				'count' => 1
			),
			array(
				'name' => 'Introduction',
				'active' => false,
				'count' => 5
			),
			array(
				'name' => 'Other',
				'active' => false,
				'count' => 0
			), */

			array(
				'name' => __( 'Account', 'echo-knowledge-base' ),
				'category_id' => 11,
				'active' => true,
				'count' => 3
			),
			array(
				'name' => __( 'Order Processing', 'echo-knowledge-base' ),
				'category_id' => 222,
				'active' => false,
				'count' => 1
			),
			array(
				'name' => __( 'Ordering', 'echo-knowledge-base' ),
				'category_id' => 22,
				'active' => false,
				'count' => 4
			),

			array(
				'name' => __( 'Payment', 'echo-knowledge-base' ),
				'category_id' => 33,
				'active' => false,
				'count' => 3
			),
			array(
				'name' => __( 'Shipping Options', 'echo-knowledge-base' ),
				'category_id' => 444,
				'active' => false,
				'count' => 2
			),
			array(
				'name' => __( 'Shipping', 'echo-knowledge-base' ),
				'category_id' => 44,
				'active' => false,
				'count' => 5
			),
			array(
				'name' => __( 'Returns & Refunds', 'echo-knowledge-base' ),
				'category_id' => 55,
				'active' => false,
				'count' => 3
			),
			array(
				'name' => __( 'Pricing/Promos', 'echo-knowledge-base' ),
				'category_id' => 66,
				'active' => false,
				'count' => 3
			),
		);
	}
}
