<?php
/**
 * -
 *
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
   * Constructor.
   *
   * @for WP_Resizely_Functions
   * @author potanin@UD
   * @since 0.1.0
   */
  function WP_Resizely_Core() {


    // Add Settings Page
    add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ));


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
    add_options_page( __( 'Resize.ly', WP_Resizely_Locale ), __( 'Resize.ly', WP_Resizely_Locale), 'manage_options', 'wp-resizely', function() {
      include( WP_Resizely_Path . '/core/ui/wp-resizely.php' );
    });

  }

  /**
   * Adds "Settings" link to the plugin overview page
   *
   *
   * @author potanin@UD
   * @for WP_Resizely_Core
   * @since 0.1.0
   */
  function plugin_action_links( $links, $file ){

    if ( $file == 'wp-resizely/wp-resizely.php' ){
      array_unshift( $links, '<a href="' . admin_url( 'options-general.php?page=wp-resizely' ) . '">' . __( 'Settings' ) . '</a>' ); // before other links
    }

    return $links;

  }

}