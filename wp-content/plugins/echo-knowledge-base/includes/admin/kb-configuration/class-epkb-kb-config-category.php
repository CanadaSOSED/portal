<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle Category Icon upload features.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Category {
	
	public $kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;

	public function __construct() {

		$this->kb_id = EPKB_KB_Handler::get_current_kb_id();

		// handle Category icon on KB Category screen
		$taxonomy = EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_id );
		add_action( "{$taxonomy}_edit_form_fields", array( $this, 'display_category_fields' ), 99 );
		add_action( "{$taxonomy}_add_form_fields", array( $this, 'display_category_fields' ), 99 );
		add_action( "edit_{$taxonomy}", array( $this, 'save_category_fields' ), 10, 2 );
		add_action( "created_{$taxonomy}", array( $this, 'save_category_fields' ), 10, 2 );
	}

	/**
	 * On Category edit page display icon upload feature
	 * @param $category
	 */
	public function display_category_fields( $category ) {
		
		$main_page_layout = epkb_get_instance()->kb_config_obj->get_value( 'kb_main_page_layout', $this->kb_id );

		if ( $main_page_layout == 'Sidebar' ) { ?>
			<div class="epkb-term-options-message">
				<p><i class="epkbfa epkbfa-info-circle" aria-hidden="true"></i> <?php _e( 'Sidebar Layout does not use icons for categories.', 'echo-knowledge-base'); ?></p>
			</div><?php
			return;
		}

		$location = $main_page_layout == 'Grid' ? $this->get_grid_icon_location( $this->kb_id ) : epkb_get_instance()->kb_config_obj->get_value( 'section_head_category_icon_location', $this->kb_id );
		$is_new_category = ! is_object($category);
		
		// if icons disabled just show turn on/off link
		if ( $location == 'no_icons' ) {
		    if ( $is_new_category ) {
			    $this->category_icon_message( 'epkb-icons-are-disabled', 'Category Icons are <strong>disabled.</strong>' , $this->get_on_off_icons_link( $main_page_layout ), 'Turn Category Icons ON' );
			} else {    ?>
				<tr class="form-field epkb-term-options-wrap">
				<th scope="row">
					<label><?php _e( 'Category Icon', 'echo-knowledge-base' ); ?></label>
				</th>
				<td><?php $this->category_icon_message( 'epkb-icons-are-disabled', 'Category Icons are <strong>disabled.</strong>' , $this->get_on_off_icons_link( $main_page_layout ), 'Turn Category Icons ON' ); ?></td>
			    </tr><?php
			}
			return;
		}

		// not all categories have icons
		$category_level = $this->get_level( $category );
		$hide_block = false;
		
		$hide_reason = __('The icon will NOT show on the front-end KB pages for this category. ', 'echo-knowledge-base');
		
		if ( $main_page_layout == 'Tabs' ) {
			if ( $category_level !== 2 ) {
				$hide_block = true;
				$hide_reason .= __('Only sub-categories in Tabs Layout have icons.', 'echo-knowledge-base');
			}
		} elseif ( $main_page_layout == 'Sidebar' ) {
			$hide_block = true;
			$hide_reason .= __('Sidebar Layout does not use icons for categories.', 'echo-knowledge-base');
		} else { 
			// all else layouts 
			if ( $category_level > 1 ) {
				$hide_block = true;
				$hide_reason .= __('Only top-level categories have icons visible.', 'echo-knowledge-base');
			}
		}
		
		$categories_icons = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Icons::CATEGORIES_ICONS, array(), true );

		if ( ! $is_new_category  && ! empty( $categories_icons[$category->term_id] ) ) {
			$active_icon_name =empty( $categories_icons[$category->term_id]['name'] ) ? EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME : $categories_icons[$category->term_id]['name'];
			$active_icon_type = empty( $categories_icons[$category->term_id]['type'] ) ? EPKB_Icons::DEFAULT_CATEGORY_TYPE : $categories_icons[$category->term_id]['type'];
			$active_image_id = empty( $categories_icons[$category->term_id]['image_id'] ) ? EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID : $categories_icons[$category->term_id]['image_id'];
			$active_image_size = empty( $categories_icons[$category->term_id]['image_size'] ) ? EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE : $categories_icons[$category->term_id]['image_size'];
		} else {
			$active_icon_name = EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME;
			$active_icon_type = EPKB_Icons::DEFAULT_CATEGORY_TYPE;
			$active_image_id = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID;
			$active_image_size = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE;
		}		?>

		<<?php echo $is_new_category ? 'div' : 'tr'; ?> class="form-field epkb-term-options-wrap" >
			<<?php echo $is_new_category ? 'div' : 'th'; ?> scope="row">
				<label><?php _e( 'Category Icon', 'echo-knowledge-base' ); ?></label>
			</<?php echo $is_new_category ? 'div' : 'th'; ?>>
			<<?php echo $is_new_category ? 'div' : 'td'; ?>>
				<div class="epkb-categories-icons" <?php if ($hide_block) echo 'style="display:none;"'; ?>>
					<div class="epkb-categories-icons__tabs-header">
						<div class="epkb-categories-icons__button <?php echo ( $active_icon_type == 'font' ) ? 'epkb-categories-icons__button--active' : ''; ?>" id="epkb_font_icon" data-type="font">
							<?php _e( 'Font Icon', 'echo-knowledge-base' ); ?>
						</div>
						<div class="epkb-categories-icons__button <?php echo ( $active_icon_type == 'image' ) ? 'epkb-categories-icons__button--active' : ''; ?>" id="epkb_image_icon" data-type="image">
							<?php _e( 'Image Icon', 'echo-knowledge-base' ); ?>
						</div>
					</div>  <?php
					$this->category_icon_message( 'epkb-icons-are-enabled','Category Icons are <strong>enabled.</strong>', $this->get_on_off_icons_link( $main_page_layout ) , 'Turn Category Icons OFF'); ?>
					<div class="epkb-categories-icons__tab-body epkb-categories-icons__tab-body--font  <?php echo ( $active_icon_type == 'font' ) ? 'epkb-categories-icons__tab-body--active' : ''; ?>"><?php EPKB_Icons::get_icons_pack_html( true, $active_icon_name ); ?></div>
					<div class="epkb-categories-icons__tab-body epkb-categories-icons__tab-body--image <?php echo ( $active_icon_type == 'image' ) ? 'epkb-categories-icons__tab-body--active' : ''; ?>"><?php $this->display_image_block( $active_image_id, $active_image_size ); ?></div>
					
					<input type="hidden" name="epkb_head_category_icon_type" id="epkb_head_category_icon_type" value="<?php echo $active_icon_type; ?>">
					<input type="hidden" name="epkb_head_category_icon_name" id="epkb_head_category_icon_name" value="<?php echo $active_icon_name; ?>">
					<input type="hidden" name="epkb_head_category_icon_image" id="epkb_head_category_icon_image" value="<?php echo $active_image_id; ?>">
					<input type="hidden" name="epkb_head_category_level" id="epkb_head_category_level" value="<?php echo $category_level; ?>">
					<input type="hidden" name="epkb_head_category_template" id="epkb_head_category_template" value="<?php echo $main_page_layout; ?>">
				</div>
				<div class="epkb-term-options-message" <?php if ( ! $hide_block) echo 'style="display:none;"'; ?>>
					<p><i class="epkbfa epkbfa-info-circle" aria-hidden="true"></i> <?php echo $hide_reason; ?></p>
				</div>
			</<?php echo $is_new_category ? 'div' : 'td'; ?>>
		</<?php echo $is_new_category ? 'div' : 'tr'; ?>> <?php 
	}

	function category_icon_message( $class, $message , $url , $urlText ) { ?>

		<div class="epkb-term-options-message <?php echo $class; ?>">
			<p>
				<i class="epkbfa epkbfa-info-circle" aria-hidden="true"></i>				<?php
				_e( $message, 'echo-knowledge-base');

				if ( ! empty($url) ) {   ?>
					<a href="<?php echo $url; ?>"><?php _e( $urlText, 'echo-knowledge-base' ); ?></a>				<?php
				}   ?>

			</p>
		</div>	<?php
	}
	
	/**
	 * On Category Edit screen display image selected (if any)
	 * @param string $image_id
	 * @param string $image_size
	 */
	private function display_image_block( $image_id = '', $image_size = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE ) {
		$image_url = wp_get_attachment_image_url( $image_id, $image_size ); ?>

		<div class="epkb-category-image">
			<div class="epkb-category-image__dropdown">
				<label>
					<span><?php _e( 'Image Size:', 'echo-knowledge-base' ); ?></span>
					<select id="epkb_head_category_thumbnail_size" name="epkb_head_category_thumbnail_size">
						<option value="full" <?php selected( 'full', $image_size); ?>><?php _e('Full', 'echo-knowledge-base' ); ?></option><?php 
						if ( $sizes = $this->get_image_sizes() ) {
							foreach ( $sizes as $key => $val ) { 
								$width = empty($val['width']) ? '' : $val['width'];
								$height = empty($val['height']) ? '' : $val['height'];
								
								if ( $width && $height ) {
									$dimension = $width . 'x' . $height;
								} else {
									$dimension = $width . $height;
								}
								echo '<option value="' . $key . '" ' . selected($key, $image_size) . '>' . ucwords( __( $key, 'echo-knowledge-base' ) ) . ' (' . $dimension . 'px)</option>';
							}
						} ?>
					</select>
				</label>
			</div>
			<div class="epkb-category-image__button <?php echo $image_url ? 'epkb-category-image__button--have-image' : 'epkb-category-image__button--no-image'; ?>" style="<?php echo $image_url ? 'background-image: url('.$image_url.');' : ''; ?>" data-title="<?php _e('Choose Category Icon', 'echo-knowledge-base'); ?>">
				<i class="epkbfa ep_font_icon_plus"></i>
				<i class="epkbfa epkbfa-pencil"></i>
			</div>
			<div class="epkb-category-image__text">
				<ul>
					<li><?php _e( 'The size of all image icons on the front-end is controlled in Features Wizard -> Main Page - Categories.', 'echo-knowledge-base' ); ?></li>
					<li><?php _e( 'This image should match the given setting for image icon size. If you choose a larger image, the image will be compressed to the icon size. This may cause unnecessary ' .
					              'load time on the front-end.', 'echo-knowledge-base' ); ?></li>
					<li><?php _e( 'For example, if you set the icon size to 50px, an image size of 50x50 will work the best.', 'echo-knowledge-base' ); ?></li>
				</ul>
			</div>
		</div>		<?php
	}

	/**
	 * Called by front-end layout code, get icon data or default from icons data array in the right format
	 *
	 * @param $term_id
	 * @param $categories_icons
	 * @return array
	 */
	public static function get_category_icon( $term_id, $categories_icons ) {
		$result = array(
			'type' => EPKB_Icons::DEFAULT_CATEGORY_TYPE, 
			'name' => EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME,
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG,
			'color' => '#000000'
		);
		
		if ( ! empty( $categories_icons[$term_id] ) ) {
			$result = array_merge( $result, $categories_icons[$term_id] );
		}
		
		if (strpos( $result['name'], 'epkbfa' ) === false) {
			$result['name'] = str_replace( 'fa-', 'epkbfa-', $result['name'] );
		}

		// image might have been updated so get the latest version if it is not demo data
		if ( ! empty($result['image_id']) && empty($_POST['epkb-wizard-demo-data']) ) {
			$image_url = wp_get_attachment_image_url( $result['image_id'], $result['image_size'] );
			$result['image_thumbnail_url'] = empty($image_url) ? '' : $image_url;
		} 
		
		return $result;
	}

	/**
	 * Save Taxonomy Icon
	 *
	 * @param $term_id
	 * @param $tt_id
	 */
	public function save_category_fields( $term_id, $tt_id ) {

		$icon_type = EPKB_Utilities::get('epkb_head_category_icon_type');
		$icon_name = EPKB_Utilities::get('epkb_head_category_icon_name');
		$icon_image_id = EPKB_Utilities::get('epkb_head_category_icon_image');
		$icon_image_size = EPKB_Utilities::get('epkb_head_category_thumbnail_size');
		
		$image_url = '';

		if ( empty($icon_type) || ! isset($icon_name) || ! isset($icon_image_id) || empty($icon_image_size) || ! in_array($icon_type, array('image', 'font')) ) {
			return;
		}

		// icon type = image, font
		$icon_type = ( $icon_type == 'image' ) && empty($icon_image_id) ? 'font' : $icon_type;

		if ( $icon_type == 'image' ) {
			$icon_name = EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME;
			$image_url = wp_get_attachment_image_url( $icon_image_id, $icon_image_size );
		} else {
			$icon_image_id = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID;
		} 

		$categories_icons = EPKB_Utilities::get_kb_option( $this->kb_id, EPKB_Icons::CATEGORIES_ICONS, array(), true );

		$categories_icons[$term_id] = array(
			'type' => $icon_type,
			'name' => $icon_name,
			'image_id' => $icon_image_id,
			'image_size' => $icon_image_size,
			'image_thumbnail_url' => empty($image_url) ? '' : $image_url,
			'color' => '#000000'    // FUTURE
		);
			
		EPKB_Utilities::save_kb_option( $this->kb_id, EPKB_Icons::CATEGORIES_ICONS, $categories_icons, true );
	}

	/**
	 * What image sizes the user can choose from.
	 * @param bool|true $unset_disabled
	 * @return array
	 */
	private function get_image_sizes( $unset_disabled = true ) {
		$wais = & $GLOBALS['_wp_additional_image_sizes'];

		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			//if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			if ( in_array( $_size, array('thumbnail', 'medium') ) ) {
				$sizes[ $_size ] = array(
					'width'  => get_option( "{$_size}_size_w" ),
					'height' => get_option( "{$_size}_size_h" ),
					'crop'   => (bool) get_option( "{$_size}_crop" ),
				);
			} else {
				continue;
			}

		/*	elseif ( isset( $wais[$_size] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $wais[ $_size ]['width'],
					'height' => $wais[ $_size ]['height'],
					'crop'   => $wais[ $_size ]['crop'],
				);
			} */

			// size registered, but has 0 width and height
			if ( $unset_disabled && ($sizes[ $_size ]['width'] == 0) && ($sizes[ $_size ]['height'] == 0) ) {
				unset( $sizes[$_size] );
			}
		}

		return $sizes;
	}

	/**
	 * Find level of currently edited category.
	 *
	 * @param $category
	 * @param int $level
	 * @return int
	 */
	private function get_level($category, $level = 1) {
		
		if ( $level > 2 ) {
			return 3;
		}
		
		if ( empty($category->parent) ) {
			return $level;
		} else {

			$level++;
			$category = EPKB_Utilities::get_kb_category_unfiltered( $this->kb_id, $category->parent );
			if ( empty($category) ) {
				return 3;
			}
			
			return $this->get_level($category, $level);
		}
	}

	private function get_grid_icon_location( $kb_id ) {
		if ( function_exists('elay_get_instance' ) && isset(elay_get_instance()->kb_config_obj) ) {
			return elay_get_instance()->kb_config_obj->get_value( $kb_id, 'grid_category_icon_location');
		}
		return '';
	}

	// open Wizard for user to switch on/off icons
	private function get_on_off_icons_link( $main_page_layout ) {
		return admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_id ) . '&page=epkb-kb-configuration&wizard-features&preselect=' .
		                  ( $main_page_layout == 'Grid' ? 'grid_category_icon_location' : 'section_head_category_icon_location' ) );
	}
}