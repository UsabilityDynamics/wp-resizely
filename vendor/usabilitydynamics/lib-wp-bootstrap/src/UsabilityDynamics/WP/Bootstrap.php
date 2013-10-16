<?php
/**
 * WP_Boostrap.
 *
 * @namespace UsabilityDynamics
 *
 * This file can be used to bootstrap any of the UD plugins, it essentially requires that you have
 * a core file which will be called after 'plugins_loaded'. In addition, if the core class has
 * 'activate' and 'deactivate' functions, then those will be called automatically by this class.
 */
namespace UsabilityDynamics {

  /**
   * Bootstrap the plugin in WordPress.
   *
   * @class Bootstrap
   * @author: potanin@UD
   */
  class WP_Bootstrap {

    /**
     * Singleton Instance Reference.
     *
     * @public
     * @static
     * @property $instance
     * @type {Object}
     */
    private static $instance = false;

    /**
     * Core object reference for the plugin
     *
     * @public
     * @static
     * @property $core
     * @type {Object}, boolean, or string (depending on what state we're in)
     */
    public $core = false;

    /**
     * Core constructor.
     *
     * @for WP_Provision_Core
     * @author potanin@UD
     * @since 0.1.0
     *
     * @param string $core_class The core class we're trying to load
     */
    public function __construct( $core_class = false ) {

      /** If we already have a singleton, return it */
      if( self::$instance ) {
        return self::$instance;
      }

      /** Now if we don't have a core class, we need it */
      if( !$core_class ){
        throw new \ErrorException( 'A core class must be specified.' );
      }

      /** Setup our core class */
      $this->core = $core_class;

      /** Register activation hook if the function exists */
      if( method_exists( $core_class, 'activate' ) ){
        /** Setup our debug backtrace */
        if( !isset( $dt ) ){
          $dt = array_shift( debug_backtrace( false ) );
        }
        register_activation_hook( $dt[ 'file' ], array( __CLASS__, 'activate' ) );
      }

      /** Register deactivation hook if the function exists */
      if( method_exists( $core_class, 'deactivate' ) ){
        /** Setup our debug backtrace */
        if( !isset( $dt ) ){
          $dt = array_shift( debug_backtrace( false ) );
        }
        register_deactivation_hook( $dt[ 'file' ], array( __CLASS__, 'deactivate' ) );
      }

      /** Add the action that inits our core after the plugins are loaded */
      add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

      /** Setup the instance */
      self::$instance =& $this;

    }

    /**
     * Initiate the plugin.
     *
     * @private
     * @for Bootstrap
     * @method plugins_loaded
     */
    public function plugins_loaded() {
      /** Create the core object */
      $this->core = new $this->core;
    }

    /**
     * Activation actions.
     *
     * @public
     * @author potanin@UD
     * @for Bootstrap
     * @method activation
     */
    public static function activate() {
      /** All we're going to do is call the core's function */
      $instance = self::get_instance();
      call_user_func( array( $instance->core, 'activate' ) );
    }

    /**
     * Deactivation actions.
     *
     * @private
     * @author potanin@UD
     * @for Bootstrap
     * @method deactivation
     */
    public function deactivate() {
      /** All we're going to do is call the core's function */
      $instance = self::get_instance();
      call_user_func( array( $instance->core, 'deactivate' ) );
    }

    /**
     * Get the WP-Provision Singleton
     *
     * Concept based on the CodeIgniter get_instance() concept.
     *
     * @example
     *
     *      var settings = WP_Provision::get_instance()->Settings;
     *      var api = WP_Provision::$instance()->API;
     *
     * @static
     * @return object
     *
     * @author potanin@UD
     * @method get_instance
     * @for WP_Provision
     */
    public static function &get_instance() {
      return self::$instance;
    }
  }
}