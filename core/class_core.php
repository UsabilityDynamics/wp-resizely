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
   * @for WP_Resizely_Core
   * @author potanin@UD
   * @since 0.1.0
   */
  function WP_Resizely_Core() {

    // $options = WP_Resizely_Functions::options();

    // Add Settings Page
    add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ));

    // wp_enqueue_scripts action hook to link only on the front-end
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ));
    add_action( 'wp_footer', array( __CLASS__, 'wp_footer' ));

  }

  /**
   * Administrative Menu.
   *
   * @author potanin@UD
   * @for WP_Resizely_Core
   * @since 0.1.0
   */
  static function admin_menu() {

    // Plugin Settings link.
    add_filter('plugin_action_links', array( __CLASS__, 'plugin_action_links'), 10, 2 );

    // Settings page.
    add_options_page( __( 'Resize.ly', WP_Resizely_Locale ), __( 'Resize.ly', WP_Resizely_Locale ), 'manage_options', 'wp-resizely', function() {
      include( WP_Resizely_Path . '/core/ui/wp-resizely.php' );
    });

    //** Make Property Featured Via AJAX */
    if( isset( $_REQUEST[ '_wpnonce' ] ) ) {
      if( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'wp-resizely-settings' )) {
        update_option( 'wp-resizely', json_encode( $_REQUEST[ 'options' ] ) );
        wp_redirect( admin_url( 'options-general.php?page=wp-resizely&updated=true' ) );
      }
    }

  }

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

    // Localize Resize.ly client options
    wp_localize_script( 'wp-resizely', 'WP_Resizely', (array) WP_Resizely_Functions::options() );

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