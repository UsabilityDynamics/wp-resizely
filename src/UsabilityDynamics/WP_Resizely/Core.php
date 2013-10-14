<?php
/**
 * Core file for WP_Resizely
 */
namespace UsabilityDynamics\WP_Resizely{
  /** Bring in any classes we'll need */
  use UsabilityDynamics as UD;
  use UsabilityDynamics\WP_Resizely\Functions as F;

  /**
   * Our core WP_Resizely class
   *
   * @class Core
   * @author: potanin@UD
   */
  class Core {

    /**
     * Version of class
     *
     * @public
     * @property version
     * @var string
     */
    public static $version = '0.0.1';

    /**
     * Textdomain String
     *
     * @public
     * @property text_domain
     * @var string
     */
    public static $text_domain = 'WP_Resizely';

    /**
     * Plugin Path
     *
     * @public
     * @property path
     * @var string
     */
    public static $path = null;

    /**
     * Plugin URL
     *
     * @public
     * @property url
     * @var string
     */
    public static $url = null;

    /**
     * This object holds our options, and will hold some values by default
     */
    private $options = array(
      'rly_base_domain' => 'https://resize.ly',
      'rly_disable' => false
    );

    /**
     * Core constructor.
     *
     * @for WP_Provision_Core
     * @author potanin@UD
     * @since 0.1.0
     */
    public function __construct() {

      /** Set our path variables */
      self::$path = untrailingslashit( plugin_dir_path( __FILE__ ) );
      self::$url  = untrailingslashit( plugin_dir_url( __FILE__ ) );

      /** Setup our defines */
      define( 'WP_Resizely_Version', self::$version );
      define( 'WP_Resizely_Path', untrailingslashit( str_ireplace( 'src' . DIRECTORY_SEPARATOR . 'UsabilityDynamics' . DIRECTORY_SEPARATOR . 'WP_Resizely', '', plugin_dir_path( __FILE__ ) ) ) );
      define( 'WP_Resizely_Directory', basename( WP_Resizely_Path ) );
      define( 'WP_Resizely_URL', untrailingslashit( plugins_url( WP_Resizely_Directory ) ) );
      define( 'WP_Resizely_Locale', WP_Resizely_Directory );

      /** First, turn the options into an object */
      $options = new \stdClass();
      foreach( $this->options as $key => $value ){
        $options->{$key} = $value;
      }
      /** Now, try to get our options from the DB */
      if( $t = F::get_options() ){
        /** Make sure our value is good */
        foreach( $this->options as $key => $value ){
          if( isset( $t->{$key} ) ){
            $options->{$key} = $t->{$key};
          }
        }
      }
      $this->options = $options;

      /** Do the rest of our actions */
      if( is_admin() ){
        /** Admin Menu */
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        /** Admin scripts */
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
      }


      /** Enable resize.ly unless we're disabled via options */
      if( $this->options->rly_disable !== true ) {
        //add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ));
        //add_action( 'wp_footer', array( $this, 'wp_footer' ));
      }

    }

    /**
     * A generic getter
     */
    function get( $var ){
      if( isset( $this->{$var} ) ){
        return $this->{$var};
      }
      return false;
    }

    /**
     * This is our activation hook that gets called from WP Bootstrap
     */
    function activate(){
      /** Just set the version number in the options table */
      update_option( 'wp-resizely-version', self::$version );
    }

    /**
     * Handles admin scripts
     */
    function admin_enqueue_scripts( $hook ){
      if( $hook != 'settings_page_wp-resizely' ){
        return;
      }
      wp_enqueue_script( 'wp_resizely_settings', WP_Resizely_URL . '/ux/scripts/settings.js', array( 'jquery' ) );
    }

    /**
     * Administrative Menu.
     *
     * @author potanin@UD
     * @for WP_Resizely\Core
     */
    function admin_menu() {

      /** Plugin's page link */
      add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

      /** Add in our settings page */
      add_options_page( __( 'Resize.ly', WP_Resizely_Locale ), __( 'Resize.ly', WP_Resizely_Locale ), 'manage_options', 'wp-resizely', function() {
        /** Get our vars */
        $instance = UD\WP_Bootstrap::get_instance();
        $options = $instance->core->get( 'options' );
        require_once( WP_Resizely_Path . '/ux/views/settings.php' );
      } );

      /** Make Property Featured Via AJAX */
      if( isset( $_REQUEST[ '_wpnonce' ] ) ) {
        if( wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'wp-resizely-settings' )) {
          /** Simply update the option */
          update_option( 'wp-resizely', json_encode( $_REQUEST[ 'options' ] ) );
          /** Then redirect */
          wp_redirect( admin_url( 'options-general.php?page=wp-resizely&updated=true' ) );
        }
      }

    }

    /**
     * Adds "Settings" link to the plugin overview page
     *
     * @author potanin@UD
     * @for WP_Resizely_Core
     */
    static function plugin_action_links( $links, $file ) {
      if ( $file == 'wp-resizely/wp-resizely.php' ){
        array_unshift( $links, '<a href="' . admin_url( 'options-general.php?page=wp-resizely' ) . '">' . __( 'Settings' ) . '</a>' );
      }
      return $links;
    }

  }
}