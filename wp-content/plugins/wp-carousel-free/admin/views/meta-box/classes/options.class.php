<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.
/**
 *
 * Options Class
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class SP_WPCP_Framework_Options extends SP_WPCP_Framework_Abstract {

	/**
	 * The option class constructor.
	 *
	 * @param array  $field All the options fields.
	 * @param string $value Value of the options.
	 * @param string $unique Unique id of the options.
	 */
	public function __construct( $field = array(), $value = '', $unique = '' ) {
		$this->field     = $field;
		$this->value     = $value;
		$this->org_value = $value;
		$this->unique    = $unique;
		$this->multilang = $this->element_multilang();
	}

	/**
	 * Element value.
	 *
	 * @param string $value Value of each element.
	 * @return mix
	 */
	public function element_value( $value = '' ) {

		$value = $this->value;

		if ( is_array( $this->multilang ) && is_array( $value ) ) {

			$current = $this->multilang['current'];

			if ( isset( $value[ $current ] ) ) {
				$value = $value[ $current ];
			} elseif ( $this->multilang['current'] == $this->multilang['default'] ) {
				$value = $this->value;
			} else {
				$value = '';
			}
		} elseif ( ! is_array( $this->multilang ) && isset( $this->value['multilang'] ) && is_array( $this->value ) ) {

			$value = array_values( $this->value );
			$value = $value[0];

		} elseif ( is_array( $this->multilang ) && ! is_array( $value ) && ( $this->multilang['current'] != $this->multilang['default'] ) ) {

			$value = '';

		}

		return $value;

	}

	/**
	 * Element name.
	 *
	 * @param string  $extra_name The extra name.
	 * @param boolean $multilang Multi language.
	 * @return statement
	 */
	public function element_name( $extra_name = '', $multilang = false ) {

		$element_id      = ( isset( $this->field['id'] ) ) ? $this->field['id'] : '';
		$extra_multilang = ( ! $multilang && is_array( $this->multilang ) ) ? '[' . $this->multilang['current'] . ']' : '';
		return ( isset( $this->field['name'] ) ) ? $this->field['name'] . $extra_name : $this->unique . '[' . $element_id . ']' . $extra_multilang . $extra_name;

	}

	/**
	 * Element type
	 *
	 * @return statement
	 */
	public function element_type() {
		$type = ( isset( $this->field['attributes']['type'] ) ) ? $this->field['attributes']['type'] : $this->field['type'];
		return $type;
	}

	/**
	 * Element class.
	 *
	 * @param string $el_class The element class.
	 * @return statement
	 */
	public function element_class( $el_class = '' ) {

		$field_class = ( isset( $this->field['class'] ) ) ? ' ' . $this->field['class'] : '';
		return ( $field_class || $el_class ) ? ' class="' . $el_class . $field_class . '"' : '';
	}

	/**
	 * Element Only for Pro.
	 *
	 * @param string $pro_only The element class.
	 * @return statement
	 */
	public function element_pro_only( $pro_only = '' ) {
		$pro_only = ( isset( $this->field['pro_only'] ) ) ? ' disabled' : '';
		return $pro_only;

	}

	/**
	 * Element Attributes.
	 *
	 * @param array $el_attributes Attributes of the element.
	 * @return statement
	 */
	public function element_attributes( $el_attributes = array() ) {

		$attributes = ( isset( $this->field['attributes'] ) ) ? $this->field['attributes'] : array();
		$element_id = ( isset( $this->field['id'] ) ) ? $this->field['id'] : '';

		if ( false !== $el_attributes ) {
			$sub_elemenet  = ( isset( $this->field['sub'] ) ) ? 'sub-' : '';
			$el_attributes = ( is_string( $el_attributes ) || is_numeric( $el_attributes ) ) ? array( 'data-' . $sub_elemenet . 'depend-id' => $element_id . '_' . $el_attributes ) : $el_attributes;
			$el_attributes = ( empty( $el_attributes ) && isset( $element_id ) ) ? array( 'data-' . $sub_elemenet . 'depend-id' => $element_id ) : $el_attributes;
		}

		$attributes = wp_parse_args( $attributes, $el_attributes );

		$atts = '';

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $key => $value ) {
				if ( 'only-key' === $value ) {
					$atts .= ' ' . $key;
				} else {
					$atts .= ' ' . $key . '="' . $value . '"';
				}
			}
		}

		return $atts;

	}

	/**
	 * Element before.
	 *
	 * @return string
	 */
	public function element_before() {
		return ( isset( $this->field['before'] ) ) ? $this->field['before'] : '';
	}

	/**
	 * Element after.
	 *
	 * @return statement
	 */
	public function element_after() {

		$out  = ( isset( $this->field['info'] ) ) ? '<p class="sp-text-desc">' . $this->field['info'] . '</p>' : '';
		$out .= ( isset( $this->field['after'] ) ) ? '<span class="sp-after-text">' . $this->field['after'] . '</span>' : '';
		$out .= $this->element_after_multilang();
		$out .= $this->element_get_error();
		$out .= $this->element_help();
		$out .= $this->element_debug();
		return $out;

	}

	/**
	 * Element debug.
	 *
	 * @return statement.
	 */
	public function element_debug() {

		$out = '';

		if ( ( isset( $this->field['debug'] ) && $this->field['debug'] === true ) || ( defined( 'SP_OPTIONS_DEBUG' ) && SP_OPTIONS_DEBUG ) ) {

			$value = $this->element_value();

			$out .= '<pre>';
			$out .= '<strong>' . __( 'CONFIG', 'wp-carousel-free' ) . ':</strong>';
			$out .= "\n";
			ob_start();
			var_export( $this->field );
			$out .= htmlspecialchars( ob_get_clean() );
			$out .= "\n\n";
			$out .= '<strong>' . __( 'USAGE', 'wp-carousel-free' ) . ':</strong>';
			$out .= "\n";
			$out .= ( isset( $this->field['id'] ) ) ? "sp_get_option( '" . $this->field['id'] . "' );" : '';

			if ( ! empty( $value ) ) {
				$out .= "\n\n";
				$out .= '<strong>' . __( 'VALUE', 'wp-carousel-free' ) . ':</strong>';
				$out .= "\n";
				ob_start();
				var_export( $value );
				$out .= htmlspecialchars( ob_get_clean() );
			}

			$out .= '</pre>';

		}

		if ( ( isset( $this->field['debug_light'] ) && $this->field['debug_light'] === true ) || ( defined( 'SP_OPTIONS_DEBUG_LIGHT' ) && SP_OPTIONS_DEBUG_LIGHT ) ) {

			$out .= '<pre>';
			$out .= '<strong>' . __( 'USAGE', 'wp-carousel-free' ) . ':</strong>';
			$out .= "\n";
			$out .= ( isset( $this->field['id'] ) ) ? "sp_get_option( '" . $this->field['id'] . "' );" : '';
			$out .= "\n";
			$out .= '<strong>' . __( 'ID', 'wp-carousel-free' ) . ':</strong>';
			$out .= "\n";
			$out .= ( isset( $this->field['id'] ) ) ? $this->field['id'] : '';
			$out .= '</pre>';

		}

		return $out;

	}

	/**
	 * Element get error.
	 *
	 * @return statement
	 */
	public function element_get_error() {

		global $sp_errors;

		$out = '';

		if ( ! empty( $sp_errors ) ) {
			foreach ( $sp_errors as $key => $value ) {
				if ( isset( $this->field['id'] ) && $value['code'] == $this->field['id'] ) {
					$out .= '<p class="sp-text-warning">' . $value['message'] . '</p>';
				}
			}
		}

		return $out;

	}

	/**
	 * Element help.
	 *
	 * @return statement
	 */
	public function element_help() {
		return ( isset( $this->field['help'] ) ) ? '<span class="sp-help" data-title="' . $this->field['help'] . '"><span class="fa fa-question-circle"></span></span>' : '';
	}

	/**
	 * Element after multi-language
	 *
	 * @return statement
	 */
	public function element_after_multilang() {

		$out = '';

		if ( is_array( $this->multilang ) ) {

			$out .= '<fieldset class="hidden">';

			foreach ( $this->multilang['languages'] as $key => $val ) {

				// ignore current language for hidden element.
				if ( $key != $this->multilang['current'] ) {

					// set default value.
					if ( isset( $this->org_value[ $key ] ) ) {
						$value = $this->org_value[ $key ];
					} elseif ( ! isset( $this->org_value[ $key ] ) && ( $key == $this->multilang['default'] ) ) {
						$value = $this->org_value;
					} else {
						$value = '';
					}

					$cache_field = $this->field;

					unset( $cache_field['multilang'] );
					$cache_field['name'] = $this->element_name( '[' . $key . ']', true );

					$class   = 'SP_WPCP_Framework_Option_' . $this->field['type'];
					$element = new $class( $cache_field, $value, $this->unique );

					ob_start();
					$element->output();
					$out .= ob_get_clean();

				}
			}

			$out .= '<input type="hidden" name="' . $this->element_name( '[multilang]', true ) . '" value="true" />';
			$out .= '</fieldset>';
			$out .= '<p class="sp-text-desc">' . sprintf( __( 'You are editing language: ( <strong>%s</strong> )', 'wp-carousel-free' ), $this->multilang['current'] ) . '</p>';

		}

		return $out;
	}

	/**
	 * Element data.
	 *
	 * @param string $type Element data type.
	 * @return statement
	 */
	public function element_data( $type = '' ) {

		$options    = array();
		$query_args = ( isset( $this->field['query_args'] ) ) ? $this->field['query_args'] : array();

		switch ( $type ) {
			case 'pages':
			case 'page':
				$pages = get_pages( $query_args );

				if ( ! is_wp_error( $pages ) && ! empty( $pages ) ) {
					foreach ( $pages as $page ) {
						$options[ $page->ID ] = $page->post_title;
					}
				}

				break;

			case 'posts':
			case 'post':
				$posts = get_posts( $query_args );

				if ( ! is_wp_error( $posts ) && ! empty( $posts ) ) {
					foreach ( $posts as $post ) {
						$options[ $post->ID ] = $post->post_title;
					}
				}

				break;

			case 'post_types':
			case 'post_type':
				$post_types = get_post_types( array( 'public' => true ) );
				if ( ! is_wp_error( $post_types ) && ! empty( $post_types ) ) {
					foreach ( $post_types as $post_type => $label ) {
						$options[ $post_type ] = $label;
					}
				}

				break;

			case 'all_posts':
			case 'all_post':
				global $post, $wpdb;
				$saved_meta = get_post_meta( $post->ID, 'sp_wpcp_upload_options', true );
				if ( isset( $saved_meta['post_type'] ) && '' != $saved_meta['post_type'] ) {

					$all_posts = $wpdb->get_results( "SELECT ID,post_title FROM `" . $wpdb->prefix . "posts` where post_type='" . $saved_meta['post_type'] . "' and post_status = 'publish' ORDER BY post_date DESC" );

					if ( ! is_wp_error( $all_posts ) && ! empty( $all_posts ) ) {
						foreach ( $all_posts as $post_obj ) {
							$options[ $post_obj->ID ] = $post_obj->post_title;
						}
					}
				} else {
					$post_types       = get_post_types( array( 'public' => true ) );
					$post_type_list   = array();
					$post_type_number = 1;
					foreach ( $post_types as $post_type => $label ) {
							$post_type_list[ $post_type_number++ ] = $label;
					}

					$all_posts = $wpdb->get_results( "SELECT ID,post_title FROM `" . $wpdb->prefix . "posts` where post_type='" . $post_type_list[1] . "' and post_status = 'publish' ORDER BY post_date DESC" );

					if ( ! is_wp_error( $all_posts ) && ! empty( $all_posts ) ) {
						foreach ( $all_posts as $post_obj ) {
							$options[ $post_obj->ID ] = $post_obj->post_title;
						}
					}
				}

				break;

			case 'taxonomies':
			case 'taxonomy':
				global $post;
				$saved_meta = get_post_meta( $post->ID, 'sp_wpcp_upload_options', true );
				if ( isset( $saved_meta['post_type'] ) && '' != $saved_meta['post_type'] ) {
					$taxonomy_names = get_object_taxonomies( $saved_meta['post_type'], 'names' );
					if ( ! is_wp_error( $taxonomy_names ) && ! empty( $taxonomy_names ) ) {
						foreach ( $taxonomy_names as $taxonomy => $label ) {
							$options[ $label ] = $label;
						}
					}
				} else {
					$post_types       = get_post_types( array( 'public' => true ) );
					$post_type_list   = array();
					$post_type_number = 1;
					foreach ( $post_types as $post_type => $label ) {
							$post_type_list[ $post_type_number++ ] = $label;
					}
					$taxonomy_names = get_object_taxonomies( $post_type_list['1'], 'names' );
					foreach ( $taxonomy_names as $taxonomy => $label ) {
						$options[ $label ] = $label;
					}
				}

				break;

			case 'terms':
			case 'term':
				global $post;
				$saved_meta = get_post_meta( $post->ID, 'sp_wpcp_upload_options', true );
				if ( isset( $saved_meta['post_taxonomy'] ) && $saved_meta['post_taxonomy'] != '' ) {
					$terms = get_terms( $saved_meta['post_taxonomy'] );
					foreach ( $terms as $key => $value ) {
						$options[ $value->term_id ] = $value->name;
					}
				} else {
					$post_types       = get_post_types( array( 'public' => true ) );
					$post_type_list   = array();
					$post_type_number = 1;
					foreach ( $post_types as $post_type => $label ) {
						$post_type_list[ $post_type_number++ ] = $label;
					}
					$taxonomy_names  = get_object_taxonomies( $post_type_list['1'], 'names' );
					$taxonomy_number = 1;
					foreach ( $taxonomy_names as $taxonomy => $label ) {
						$taxonomy_terms[ $taxonomy_number++ ] = $label;
					}
					$terms = get_terms( $taxonomy_terms['1'] );
					foreach ( $terms as $key => $value ) {
						$options[ $value->term_id ] = $value->name;
					}
				}

				break;

			case 'categories':
			case 'category':
				$categories = get_categories( $query_args );

				if ( ! is_wp_error( $categories ) && ! empty( $categories ) && ! isset( $categories['errors'] ) ) {
					foreach ( $categories as $category ) {
						$options[ $category->term_id ] = $category->name;
					}
				}

				break;

			case 'tags':
			case 'tag':
				$taxonomies = ( isset( $query_args['taxonomies'] ) ) ? $query_args['taxonomies'] : 'post_tag';
				$tags       = get_terms( $taxonomies, $query_args );

				if ( ! is_wp_error( $tags ) && ! empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$options[ $tag->term_id ] = $tag->name;
					}
				}

				break;

			case 'custom':
			case 'callback':
				if ( is_callable( $query_args['function'] ) ) {
					$options = call_user_func( $query_args['function'], $query_args['args'] );
				}

				break;

		}

		return $options;
	}

	/**
	 * If the the option is checked.
	 *
	 * @param string  $helper Helper option check.
	 * @param string  $current Current option check.
	 * @param string  $type Type option check.
	 * @param boolean $echo Echo opiton check.
	 * @return statement
	 */
	public function checked( $helper = '', $current = '', $type = 'checked', $echo = false ) {

		if ( is_array( $helper ) && in_array( $current, $helper ) ) {
			$result = ' ' . $type . '="' . $type . '"';
		} elseif ( $helper == $current ) {
			$result = ' ' . $type . '="' . $type . '"';
		} else {
			$result = '';
		}

		if ( $echo ) {
			echo $result;
		}

		return $result;

	}

	/**
	 * Multi-language element.
	 *
	 * @return statement
	 */
	public function element_multilang() {
		return ( isset( $this->field['multilang'] ) ) ? sp_language_defaults() : false;
	}

}

// Load all of fields.
sp_wpcp_load_option_fields();
