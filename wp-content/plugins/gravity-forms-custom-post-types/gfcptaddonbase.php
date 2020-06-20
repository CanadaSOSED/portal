<?php

if (!class_exists('GFCPTAddonBase')) {

	/*
	 * Base class for the GFCPT Addon. All common code is in here and differences per version are overrided
	 */
	class GFCPTAddonBase {

	  protected $_has_tag_inputs = false;
	  protected $_included_js;
	  protected $_tag_inputs = array();
	  protected $_tag_map = array();
	  protected $_tag_terms = array();


		/*
		 * Main initilize method for wiring up all the hooks
		 */
		public function init() {
			//alter the way forms are rendered by inserting taxomony dropdowns,radios and checkboxes
			add_filter('gform_pre_render' , array(&$this, 'setup_form') );

			//alter the way forms are rendered by the admin too!
			add_filter('gform_admin_pre_render' , array(&$this, 'setup_form') );

			//alter the form for submission - this is mainly for checkboxes
			add_filter('gform_pre_submission_filter', array(&$this, 'setup_form') );

			add_filter( 'gform_form_post_get_meta', array( $this, 'setup_form_on_export' ) );

			//set the post type when saving a post
			add_filter("gform_post_data", array(&$this, 'set_post_values'), 10, 2);

			if( class_exists( 'gform_update_post' ) ) {
				remove_filter( 'gform_after_submission', array( 'gform_update_post', 'delete_custom_taxonomy_save' ), 1, 2 );
			}

			//intercept the form save and save any taxonomy links if needed
			add_action( 'gform_after_create_post', array( $this, 'save_taxonomies'), 10, 3 );

			//enqueue scripts to the page
			add_action( 'gform_enqueue_scripts',       array( $this, 'enqueue_form_scripts' ), 10, 2 );
			add_action( 'gform_register_init_scripts', array( $this, 'register_init_scripts' ), 10, 2 );

			add_filter("gform_preview_styles", array(&$this, 'preview_print_styles'), 10, 2);

			add_filter( 'gform_entry_field_value',   array( $this, 'display_term_name_on_entry_detail' ), 10, 4 );
			add_filter( 'gform_entries_field_value', array( $this, 'display_term_name_on_entry_list' ), 10, 4 );
			add_filter( 'gform_export_field_value',  array( $this, 'display_term_name_on_export' ), 10, 4 );

			add_filter( 'gform_entry_field_value',   array( $this, 'display_post_title_on_entry_detail' ), 10, 4 );
			add_filter( 'gform_entries_field_value', array( $this, 'display_post_title_on_entry_list' ), 10, 4 );
			add_filter( 'gform_export_field_value',  array( $this, 'display_post_title_on_export' ), 10, 4 );


		}

		/*
		 * Setup the form with any taxonomies etc
		 */
		function setup_form( $form ) {

		  //loop thru all fields
		  foreach($form['fields'] as &$field) {

			//see if the field is using a taxonomy
			$taxonomy = $this->get_field_taxonomy( $field );

			if($taxonomy) {
			  $this->setup_taxonomy_field( $field, $taxonomy );
			  continue;
			}

			//if its a select then check if we have set a post type
			if ($field['type'] == 'select') {

			  $post_type = $this->get_field_post_type( $field );

			  if ($post_type) {
				$this->setup_post_type_field( $field, $post_type );
				continue;
			  }

			}
		  }

		  return $form;
		}

		function setup_form_on_export( $form ) {

			if( in_array( rgpost( 'action' ), array( 'rg_select_export_form', 'gf_process_export' ) ) ) {
				$form = $this->setup_form( $form );
			}

			return $form;
		}

		function register_init_scripts( $form ) {

			$inputs = array();
			$taxonomies = array();

			foreach( $form['fields'] as $field ) {

				if( ! $this->has_tax_enhanced_ui( $field ) ) {
					continue;
				}

				$inputs[] = array( 'input' => sprintf( '#input_%d_%d', $form['id'], $field->id ), 'taxonomy' => $field->saveToTaxonomy );
				if( ! array_key_exists( $field->saveToTaxonomy, $taxonomies ) ) {
					$taxonomies[ $field->saveToTaxonomy ] = get_terms( $field->saveToTaxonomy, 'orderby=name&hide_empty=0&fields=names' );
				}

			}

			if( empty( $inputs ) ) {
				return;
			}

			$script = '
				if( typeof gfcpt_tag_inputs == "undefined" ) { window.gfcpt_tag_inputs = { "tag_inputs" : [] }; }
				if( typeof gfcpt_tag_taxonomies == "undefined" ) { window.gfcpt_tag_taxonomies = {}; }
				var gfcptCurrentInputs = ' . json_encode( $inputs ) . ';
				for ( input in gfcptCurrentInputs ) {
				    if( gfcptCurrentInputs.hasOwnProperty( input ) ) {
						gfcpt_tag_inputs.tag_inputs.push( gfcptCurrentInputs[ input ] );        
				    }
				}
				var gfcptCurrentTaxonomies = ' . json_encode( $taxonomies ) . ';
				for ( taxonomy in gfcptCurrentTaxonomies ) {
				    if( gfcptCurrentTaxonomies.hasOwnProperty( taxonomy ) ) {
						gfcpt_tag_taxonomies[ taxonomy ] = gfcptCurrentTaxonomies[ taxonomy ];        
				    }
				}';


			GFFormDisplay::add_init_script( $form['id'], 'gfcpt_tagit', GFFormDisplay::ON_PAGE_RENDER, $script );

		}

		function enqueue_form_scripts( $form ) {

			if( ! $this->has_tax_enhanced_ui( $form ) ) {
				return;
			}

			wp_register_style( 'gfcpt_jquery_ui_theme', plugins_url( 'css/custom/jquery-ui-1.8.16.custom.css' , __FILE__ ) );
			wp_enqueue_style( 'gfcpt_jquery_ui_theme' );

			wp_register_style( 'gfcpt_tagit_css', plugins_url( 'css/jquery.tagit.css' , __FILE__ ) );
			wp_enqueue_style( 'gfcpt_tagit_css' );

			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-widget' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			wp_register_script( 'gfcpt_tagit_js', plugins_url( 'js/tag-it.js' , __FILE__ ), array( 'jquery-ui-widget' ) );
			wp_enqueue_script( 'gfcpt_tagit_js' );

			wp_register_script( 'gfcpt_tagit_init_js', plugins_url( 'js/tag-it.init.js' , __FILE__ ), array('gfcpt_tagit_js' ), false, true );
			wp_enqueue_script( 'gfcpt_tagit_init_js' );

		}

		function has_tax_enhanced_ui( $form_or_field ) {

			if( is_a( $form_or_field, 'GF_Field' ) ) {
				if( $form_or_field->get_input_type() == 'text' && $form_or_field->saveToTaxonomy && $form_or_field->taxonomyEnhanced ) {
					return true;
				}
			} else {
				foreach( $form_or_field['fields'] as $field ) {
					if( $this->has_tax_enhanced_ui( $field ) ) {
						return true;
					}
				}
			}

			return false;
		}


		function preview_print_styles( $styles, $form ) {
			return array_merge( $styles, array( 'gfcpt_jquery_ui_theme', 'gfcpt_tagit_css' ) );
		}

		/*
		 * Set the post values (if neccessary)
		 */
		function set_post_values( $post_data, $form ) {

			//check if the form saves a post
			if ( $this->is_form_a_post_form($form) ) {
				$target_post_type = $this->get_form_post_type( $form );

				if ($target_post_type)
					$post_data["post_type"] = $target_post_type;

				//then check if we have set a parent
				$parent_post_id = $this->get_form_parent_post_id( $form );

				if ($parent_post_id > 0) {
				  $post_data["post_parent"] = $parent_post_id;
				}
			}
			return $post_data;

		}

		/*
		 * Checks if a form includes a 'post field'
		 */
		function is_form_a_post_form( $form ) {
			foreach ($form["fields"] as $field) {
				if(in_array($field["type"],
						array("post_category","post_title","post_content",
							"post_excerpt","post_tags","post_custom_fields","post_image")))
					return true;
			}
			return false;
		}

		/*
		 * override this to get the post type for a form
		 */
		function get_form_post_type( $form ) {
			return null;
		}

		/*
		 * override this to get the taxonomy for a field
		 */
		function get_field_taxonomy( $field ) {
			return null;
		}

		/*
		 * override this to get the post type for a field
		 */
		function get_field_post_type( $field ) {
			return null;
		}

		/*
		 * override this to get the parent Id for a form
		 */
		function get_form_parent_post_id( $form ) {
			return 0;
		}

		function get_taxonomies( $form_id, $args = array() ) {

			$args = wp_parse_args( $args, array(
				'public'   => true,
				'_builtin' => false
			) );

			$args       = gf_apply_filters( 'gfcpt_tax_args', array( $form_id ), $args, $form_id );
			$taxonomies = get_taxonomies( $args, 'objects' );

			return $taxonomies;
		}

		function get_post_types( $form_id, $args = array() ) {

			$args = wp_parse_args( $args, array(
				'public' => true
			) );

			$args       = gf_apply_filters( 'gfcpt_post_type_args', array( $form_id ), $args, $form_id );
			$post_types = get_post_types( $args, 'objects' );

			return $post_types;
		}

		/*
		 * setup a field if it is linked to a post type
		 */
		function setup_post_type_field( &$field, $post_type ) {
			$first_choice = $field['choices'][0]['text'];
			$field['choices'] = $this->load_post_type_choices( $post_type, $first_choice, $field );
			$field->enableChoiceValue = true;
		}

		function load_post_type_choices($post_type, $first_choice = '', $field ) {
			$posts = $this->load_posts_hierarchical( $post_type, $field->formId, $field->id );
			if ($first_choice === '' || $first_choice === 'First Choice'){
				// if no default option is specified, dynamically create based on post type name
				$post_type_obj = get_post_type_object($post_type);
				$choices[] = array('text' => "-- select a {$post_type_obj->labels->singular_name} --", 'value' => '');
			} else {
				$choices[] = array('text' => $first_choice, 'value' => '');
			}

			foreach($posts as $post) {
				$choices[] = array('value' => $post->ID, 'text' => $post->post_title);
			}

			return $choices;
		}

		/*
		 * Get a hierarchical list of posts
		 */
		function load_posts_hierarchical( $post_type, $form_id, $field_id ) {
			$args = gf_apply_filters( 'gfcpt_get_posts_args', array( $form_id, $field_id ), array(
				'post_type'   => $post_type,
				'numberposts' => -1,
				'orderby'     => $post_type == 'page' ? 'menu_order' : 'title',
				'order'       => $post_type == 'page' ? null : 'ASC',
				'post_status' => 'publish'
			) );
			$posts = get_posts( $args );
			return $this->walk_posts( $posts );
		}

		/*
		 * Helper function to recursively 'walk' the posts
		 */
		function walk_posts( $input_array, $parent_id=0, &$out_array=array(), $level=0 ){
			foreach ( $input_array as $item ) {
				if ( $item->post_parent == $parent_id ) {
						$item->post_title = str_repeat('--', $level) . $item->post_title;
						$out_array[] = $item;
						$this->walk_posts( $input_array, $item->ID, $out_array, $level+1 );
				}
			}
			return $out_array;
		}

		/*
		 * setup a field if it is linked to a taxonomy
		 */
		function setup_taxonomy_field( &$field, $taxonomy ) {

			$first_choice = rgars( $field, 'choices/0/text' );
			$field['choices'] = $this->load_taxonomy_choices( $taxonomy, $field['type'], $first_choice, $field );
			$field->enableChoiceValue = true;

			//now check if we are dealing with a checkbox list and do some extra magic
			if ( $field['type'] == 'checkbox' ) {

				$inputs = array();

				$counter = 0;
				//recreate the inputs so they are captured correctly on form submission
				foreach( $field['choices'] as $choice ) {

					//thanks to Peter Schuster for the help on this fix
					$counter++;
					if ( $counter % 10 == 0 ) {
						$counter++;
					}

					$id = $field['id'] . '.' . $counter;
					$inputs[] = array( 'label' => $choice['text'], 'id' => $id );
				}

				$field['inputs'] = $inputs;

			}
		}

		/*
		 * Load any taxonomy terms
		 */
		function load_taxonomy_choices($taxonomy, $type, $first_choice = '', $field ) {
			$choices = array();

			if ( in_array( $field->get_input_type(), gf_apply_filters( array( 'gfcpt_hierarchical_display', $field->formId, $field->fieldId ), array( 'select', 'multiselect' ) ) ) ) {

				$terms = $this->load_taxonomy_hierarchical( $taxonomy, $field );

				if( $field->get_input_type() == 'select' ) {
					if ( $first_choice !== '' && $first_choice !== 'First Choice' && empty( $field->placeholder ) ) {
						// if no default option is specified, dynamically create based on taxonomy name
						$taxonomy = get_taxonomy($taxonomy);
						$choices[] = array('text' => "-- select a {$taxonomy->labels->singular_name} --", 'value' => '');
					}
				}

			} else {
				$terms = get_terms($taxonomy, 'orderby=name&hide_empty=0');
			}

			if ( !array_key_exists("errors",$terms) ) {
			  foreach($terms as $term) {
				  $choices[] = array('value' => $term->term_id, 'text' => $term->name);
			  }
			}

			return $choices;
		}

		/*
		 * Get a hierarchical list of taxonomies
		 */
		function load_taxonomy_hierarchical( $taxonomy, $field ) {

			$args = gf_apply_filters( 'gfcpt_taxonomy_args', array( $field->formId, $field->id ), array(
				'taxonomy'      => $taxonomy,
				'orderby'       => 'name',
				'hierarchical'  => 1,
				'hide_empty'    => 0
			), $field );

			$terms  = get_categories( $args );

			if ( array_key_exists("errors",$terms) ) {
				return $terms;
			} else {
				$parent = isset( $args['parent'] ) ? $args['parent'] : 0;
				return $this->walk_terms( $terms, $parent );
			}

		}

		/*
		 * Helper function to recursively 'walk' the taxonomy terms
		 */
		function walk_terms( $input_array, $parent_id=0, &$out_array=array(), $level=0 ){
			foreach ( $input_array as $item ) {
				if ( $item->parent == $parent_id ) {
						$item->name = str_repeat('--', $level) . $item->name;
						$out_array[] = $item;
						$this->walk_terms( $input_array, $item->term_id, $out_array, $level+1 );
				}
			}
			return $out_array;
		}

		/*
		 * Loop through all fields and save any linked taxonomies
		 */
		function save_taxonomies( $post_id, $entry, $form ) {

			// Check if the submission contains a WordPress post
			if ( isset ( $entry['post_id'] ) ) {

				$this->delete_custom_taxonomies( $entry, $form );

				foreach( $form['fields'] as &$field ) {

					$taxonomy = $this->get_field_taxonomy( $field );

					if ( !$taxonomy ) continue;

					$this->save_taxonomy_field( $field, $entry, $taxonomy );
				}
			}
		}

		/**
		 * Remove Custom Taxonomies
		 *
		 * @author  ekaj
		 * @return	void
		 */
		public function delete_custom_taxonomies( $entry, $form ) {
			// Check if the submission contains a WordPress post
			if (! empty($entry['post_id']) )
			{
				foreach( $form['fields'] as &$field )
				{
					$taxonomy = false;
					if ( array_key_exists('populateTaxonomy', $field) ) {
						$taxonomy = $field['populateTaxonomy'];
					}

					if ( $taxonomy ) {
						wp_set_object_terms( $entry['post_id'], NULL, $taxonomy );
					}
				}
			}
		}

		/*
		 * Save linked taxonomies for a sinle field
		 */
		function save_taxonomy_field( &$field, $entry, $taxonomy ) {

			$terms = array();

			switch( $field->get_input_type() ) {
				case 'multiselect':

					$value = $entry[ $field->id ];
					$terms = json_decode( $value );

					if( ! is_array( $terms ) ) {
						$terms = explode( ',', $value );
					}

					$terms = array_map( 'intval', $terms );

					break;

				case 'checkbox':

					foreach ( $field['inputs'] as $input ) {
						$term = (int) $entry[ (string) $input['id'] ];
						if ( $term ) {
							$terms[] = $term;
						}
					}

					break;
				case 'text':
					$terms = array_filter( explode( ',', $entry[ $field->id ] ) );
					break;
				default:
					$terms = (int) $entry[ $field->id ];
			}

			if ( ! empty ( $terms ) ) {
				$append_terms = (bool) apply_filters( 'gfcpt_append_terms', true, $field, $entry, $taxonomy );
				$append_terms = (bool) apply_filters( sprintf( 'gfcpt_append_terms_%d', $field->formId ), $append_terms, $field, $entry, $taxonomy );
				$append_terms = (bool) apply_filters( sprintf( 'gfcpt_append_terms_%d_%d', $field->formId, $field->id ), $append_terms, $field, $entry, $taxonomy );
				wp_set_object_terms( $entry['post_id'], $terms, $taxonomy, $append_terms );
			}

		}

		function get_term_name( $term_id, $field ) {

			$return = $term_id;

			if( $field->populateTaxonomy && ! empty( $term_id ) ) {
				$term = get_term( (int) $term_id, $field->populateTaxonomy );
				if( ! is_wp_error( $term ) ) {
					$return = $term->name;
				}
			}

			return $return;
		}

		function display_term_name_on_entry_detail( $value, $field, $entry, $form ) {
			return $this->get_term_name( $value, $field );
		}

		function display_term_name_on_entry_list( $value, $form_id, $field_id ) {

			if( is_numeric( $field_id ) ) {
				$field = GFFormsModel::get_field( GFAPI::get_form( $form_id ), $field_id );
				$value = $this->get_term_name( $value, $field );
			}

			return $value;
		}

		function display_term_name_on_export( $value, $form_id, $field_id ) {
			return $this->display_term_name_on_entry_list( $value, $form_id, $field_id );
		}

		function get_post_title( $post_id, $field ) {

			if( $field->populatePostType && ! empty( $post_id ) ) {
				$post = get_post( $post_id );
				return $post ? $post->post_title : $post_id;
			}

			return $post_id;
		}

		function display_post_title_on_entry_detail( $value, $field, $entry, $form ) {
			return $this->get_post_title( $value, $field );
		}

		function display_post_title_on_entry_list( $value, $form_id, $field_id ) {

			if( is_numeric( $field_id ) ) {
				$field = GFFormsModel::get_field( GFAPI::get_form( $form_id ), $field_id );
				$value = $this->get_post_title( $value, $field );
			}

			return $value;
		}

		function display_post_title_on_export( $value, $form_id, $field_id ) {
			return $this->display_post_title_on_entry_list( $value, $form_id, $field_id );
		}

	}
}