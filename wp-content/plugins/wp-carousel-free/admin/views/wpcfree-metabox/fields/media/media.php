<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: media
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'SP_WPCF_Field_media' ) ) {
  class SP_WPCF_Field_media extends SP_WPCF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'url'          => true,
        'preview'      => true,
        'library'      => array(),
        'button_title' => esc_html__( 'Upload', 'wp-carousel-free' ),
        'remove_title' => esc_html__( 'Remove', 'wp-carousel-free' ),
        'preview_size' => 'thumbnail',
      ) );

      $default_values = array(
        'url'         => '',
        'id'          => '',
        'width'       => '',
        'height'      => '',
        'thumbnail'   => '',
        'alt'         => '',
        'title'       => '',
        'description' => ''
      );

      // fallback
      if( is_numeric( $this->value ) ) {
        $this->value  = array(
        'id'        => $this->value,
        'url'       => wp_get_attachment_url( $this->value ),
        'thumbnail' => wp_get_attachment_image_src( $this->value, 'thumbnail', true )[0],
      );
      }
      $this->value = wp_parse_args( $this->value, $default_values );

      $library     = ( is_array( $args['library'] ) ) ? $args['library'] : array_filter( (array) $args['library'] );
      $library     = ( ! empty( $library ) ) ? implode(',', $library ) : '';
      $preview_src = ( $args['preview_size'] !== 'thumbnail' ) ? $this->value['url'] : $this->value['thumbnail'];
      $hidden_url  = ( empty( $args['url'] ) ) ? ' hidden' : '';
      $hidden_auto = ( empty( $this->value['url'] ) ) ? ' hidden' : '';
      $placeholder = ( empty( $this->field['placeholder'] ) ) ? ' placeholder="'.  esc_html__( 'No media selected', 'wp-carousel-free' ) .'"' : '';

      echo $this->field_before();

      if( ! empty( $args['preview'] ) ) {
        echo '<div class="spf--preview'. $hidden_auto .'">';
        echo '<div class="spf-image-preview"><a href="#" class="spf--remove fa fa-times"></a><img src="'. $preview_src .'" class="spf--src" alt=""/></div>';
        echo '</div>';
      }

      echo '<div class="spf--placeholder">';
      echo '<input type="text" name="'. $this->field_name('[url]') .'" value="'. $this->value['url'] .'" class="spf--url'. $hidden_url .'" readonly="readonly"'. $this->field_attributes() . $placeholder .' />';
      echo '<a href="#" class="button button-primary spf--button" data-library="'. esc_attr( $library ) .'" data-preview-size="'. esc_attr( $args['preview_size'] ) .'">'. $args['button_title'] .'</a>';
      echo ( empty( $args['preview'] ) ) ? '<a href="#" class="button button-secondary spf-warning-primary spf--remove'. $hidden_auto .'">'. $args['remove_title'] .'</a>' : '';
      echo '</div>';

      echo '<input type="hidden" name="'. $this->field_name('[id]') .'" value="'. $this->value['id'] .'" class="spf--id"/>';
      echo '<input type="hidden" name="'. $this->field_name('[width]') .'" value="'. $this->value['width'] .'" class="spf--width"/>';
      echo '<input type="hidden" name="'. $this->field_name('[height]') .'" value="'. $this->value['height'] .'" class="spf--height"/>';
      echo '<input type="hidden" name="'. $this->field_name('[thumbnail]') .'" value="'. $this->value['thumbnail'] .'" class="spf--thumbnail"/>';
      echo '<input type="hidden" name="'. $this->field_name('[alt]') .'" value="'. $this->value['alt'] .'" class="spf--alt"/>';
      echo '<input type="hidden" name="'. $this->field_name('[title]') .'" value="'. $this->value['title'] .'" class="spf--title"/>';
      echo '<input type="hidden" name="'. $this->field_name('[description]') .'" value="'. $this->value['description'] .'" class="spf--description"/>';

      echo $this->field_after();

    }

  }
}
