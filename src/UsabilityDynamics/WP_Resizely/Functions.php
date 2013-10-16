<?php
/**
 * Functions file for WP_Resizely
 */
namespace UsabilityDynamics\WP_Resizely{
  /** Bring in any classes we'll need */
  use UsabilityDynamics as UD;

  /**
   * Holds our resizely functions
   * @class Functions
   * @author: williams@UD
   */
  class Functions {
    /**
     * Get options object.
     *
     * @for WP_Resizely_Functions
     * @author potanin@UD
     * @return array|mixed
     */
    static function get_options(){
      $options = json_decode( get_option( 'wp-resizely' ) );
      foreach( (array) $options as $key => $option ){
        // Convert booleans
        if( $option === 'true' ){
          $options->{$key} = true;
        }
        // Convert booleans
        if( $option === 'false' ){
          $options->{$key} = false;
        }
      }
      return $options;
    }

    /**
     * Administrative Menu.
     *
     * @author potanin@UD
     * @for WP_Resizely_Functions
     * @since 0.1.0
     */
    static function shortcode( $atts ){
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
      if( $atts[ 'class' ] ){
        $atts[ ] = $atts[ 'class' ];
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
  }
}