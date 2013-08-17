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

    foreach ( $options as $key => $option ) {

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