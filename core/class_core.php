<?php
/**
 * -
 *
 * @class WP_Resizely_Core
 * @author: potanin@UD
 * @date: 8/17/13
 * @time: 2:06 PM
 * 
 */

class WP_Resizely_Core {

  /**
   * Core constructor.
   *

  /**
   * Initialize Resize.ly client in footer
   *
   * @author potanin@UD
   * @for WP_Resizely_Core
   * @since 0.1.0
   */
  static function wp_footer() {
    echo "<script>jQuery( document ).ready( function ( $ ) {  $( 'img[data-src]' ).resizely( WP_Resizely ); });</script>";
  }

  /**
   * Enqueue Resize.ly client-side script(s)
   *
   * @author potanin@UD
   * @for WP_Resizely_Core
   * @since 0.1.0
   */
  static function wp_enqueue_scripts() {

    // Enqueue Resize.ly client-side library
    wp_enqueue_script( 'wp-resizely', WP_Resizely_URL . '/vendor/resizely-client/build/jquery.resizely.min.js', array( 'jquery' ));

    $options = (array) WP_Resizely_Functions::options();

    $options[ 'home_url' ] = home_url();

    if( is_ssl() ) {
      $options[ 'is_ssl' ] = true;
    }

    // Localize Resize.ly client options
    wp_localize_script( 'wp-resizely', 'WP_Resizely', $options );

  }

  /**
   * Adds "Settings" link to the plugin overview page
   *
   *
   * @author potanin@UD
   * @for WP_Resizely_Core
   * @since 0.1.0
   */
  static function plugin_action_links( $links, $file ) {

    if ( $file == 'wp-resizely/wp-resizely.php' ){
      array_unshift( $links, '<a href="' . admin_url( 'options-general.php?page=wp-resizely' ) . '">' . __( 'Settings' ) . '</a>' ); // before other links
    }

    return $links;

  }

}

