<?php
/**
 * General Resize.ly Options
 *
 * @class WP_Resizely_Functions
 * @author: potanin@UD
 * @date: 8/17/13
 * @time: 2:06 PM
 * 
 */
class WP_Resizely_Functions {

  /**
   * Get options object.
   *
   * @for WP_Resizely_Functions
   * @author potanin@UD
   * @return array|mixed
   */
  static function options() {
    $options = json_decode( get_option( 'wp-resizely' ) );

    foreach ( (array) $options as $key => $option ) {

      // Convert booleans
      if( $option === 'true' ) {
        $options->{$key} = true;
      }

      // Convert booleans
      if( $option === 'false' ) {
        $options->{$key} = false;
      }

    }

    // die( print_r( $options ) );

    return $options;

  }


  /**
   * Administrative Menu.
   *
   * @author potanin@UD
   * @for WP_Resizely_Functions
   * @since 0.1.0
   */
  static function shortcode( $atts ) {

    // Set default attributes/
    $atts = shortcode_atts( array(
      'id' => null,
      'url' => null,
      'width' => '100%',
      'class' => '',
      'alt' => '',
      'height' => 'auto'
    ), $atts );

    // Default class.
    $class = array( 'resizely' );

    // Add custom classes to be concatenated later.
    if( $atts[ 'class' ]  ) {
      $atts[] = $atts[ 'class' ];
    }

    // Resolve ID to url.
    $atts[ 'src' ] = array_shift( wp_get_attachment_image_src( $atts[ 'id' ], null, null, true ) );

    // @todo Add automatic "alt" lookup based on Media Library post object.

    // Create HTML element tags.
    $tags = array(
      'data-src="' . $atts[ 'src' ] . '"',
      'width="' . $atts[ 'width' ] . '"',
      'height="' . $atts[ 'height' ] . '"',
      'alt="' . $atts[ 'alt' ] . '"',
      'class="' . implode( ' ', $class ) . '"'
    );

    // Combine into HTML element.
    return '<img ' . implode( ' ', $tags ) . ' />';

  }

  /**
   * Activate Plugin.
   *
   * @todo Does not seem to trigger. -potanin@UD
   * @for WP_Resizely_Functions
   * @author potanin@UD
   */
  static function activation() {
    update_option( 'wp-resizely-version', WP_Resizely_Version );
  }

  /**
   * Deactivate Plugin
   *
   * @for WP_Resizely_Functions
   * @author potanin@UD
   */
  static function deactivation() {}

}