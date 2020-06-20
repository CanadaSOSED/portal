<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: code_editor
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'SP_WPCF_Field_code_editor' ) ) {
  class SP_WPCF_Field_code_editor extends SP_WPCF_Fields {

    public $version = '5.41.0';
    public $cdn_url = 'https://cdn.jsdelivr.net/npm/codemirror@';

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $default_settings = array(
        'tabSize'       => 2,
        'lineNumbers'   => true,
        'theme'         => 'default',
        'mode'          => 'htmlmixed',
        'cdnURL'        => $this->cdn_url . $this->version,
      );

      $settings = ( ! empty( $this->field['settings'] ) ) ? $this->field['settings'] : array();
      $settings = wp_parse_args( $settings, $default_settings );
      $encoded  = htmlspecialchars( json_encode( $settings ) );

      echo $this->field_before();
      echo '<textarea name="'. $this->field_name() .'"'. $this->field_attributes() .' data-editor="'. $encoded .'">'. $this->value .'</textarea>';
      echo $this->field_after();

    }

    public function enqueue() {

      // Do not loads CodeMirror in revslider page.
      if( in_array( spf_get_var( 'page' ), array( 'revslider' ) ) ) { return; }

      if( ! wp_script_is( 'spf-codemirror' ) ) {
        wp_enqueue_script( 'spf-codemirror', $this->cdn_url . $this->version .'/lib/codemirror.min.js', array( 'spf' ), $this->version, true );
        wp_enqueue_script( 'spf-codemirror-loadmode', $this->cdn_url . $this->version .'/addon/mode/loadmode.min.js', array( 'spf-codemirror' ), $this->version, true );
      }

      if( ! wp_style_is( 'spf-codemirror' ) ) {
        wp_enqueue_style( 'spf-codemirror', $this->cdn_url . $this->version .'/lib/codemirror.min.css', array(), $this->version );
      }

    }

  }
}
