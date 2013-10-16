<?php
/**
 * User Interface
 *
 * Ported over from WPP 2.0 class_ui.php
 *
 * @author potanin@UD
 * @author peshkov@UD
 * @author korotkov@UD
 *
 * @version 0.1.2
 * @module UsabilityDynamics
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\UI' ) ) {

    /**
     * Class UI
     *
     * @submodule UI
     * @class UI
     */
    class UI extends Utility {

      /**
       * SaaS Class version.
       *
       * @public
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.1.2';

      /**
       *
       */
      static function create_dashboard_page() {
      }

      /**
       * Removes 'quick edit' link on property type objects
       *
       * Called in via page_row_actions filter
       *
       * @since 0.5
       */
      static function property_row_actions( $actions, $post ) {

        if( $post->post_type != WPP_Object ) {
          return $actions;
        }

        unset( $actions[ 'inline' ] );

        return $actions;
      }

      /**
       * Inserts content into the "Publish" metabox on property pages
       *
       * @updated 2.0 - Migrated from WPP_Core
       * @since 1.04
       */
      static function post_submitbox_misc_actions() {
        global $post, $action;

        if( $post->post_type == WPP_Object ) {

          ?>
          <div class="misc-pub-section">

      <ul>
        <li><?php _e( 'Menu Sort Order:', 'wpp' ) ?> <?php echo WPP_F::input( "name=menu_order&special=size=4", $post->menu_order ); ?></li>

        <?php if( current_user_can( 'manage_options' ) && $wp_properties[ 'configuration' ][ 'do_not_use' ][ 'featured' ] != 'true' ) { ?>
          <li><?php echo WPP_F::checkbox( "name=wpp_data[meta][featured]&label=" . __( 'Display in featured listings.', 'wpp' ), get_post_meta( $post->ID, 'featured', true ) ); ?></li>
        <?php } ?>

        <?php do_action( 'wpp_publish_box_options', $post ); ?>
      </ul>

      </div>
        <?php
        }

        return;

      }

      /**
       * Loads admin UI view
       *
       * @since 2.0
       * @author potanin@UD
       * @author peshkov@UD
       */
      static function get_interface( $_atts = false ) {
        global $wp_properties;

        $atts = shortcode_atts( array(
          'interface'  => is_string( $_atts ) ? $_atts : '',
          'expiration' => ( 60 * 60 * 12 ),
          'version'    => 'default',
          'feature'    => '',
        ), $_atts );

        if( empty( $atts[ 'interface' ] ) ) {
          return new WP_Error( __METHOD__, __( 'Interface name not understood.', 'wpp' ) );
        }

        //** Parse interface name */
        if( strpos( $atts[ 'interface' ], '/' ) === 0 ) {
          $atts[ 'name' ] = substr( $atts[ 'name' ], 1 );
        }

        preg_match( $wp_properties[ '_regex' ][ 'get_script_url' ], $atts[ 'interface' ], $matches );

        if( empty( $matches[ 2 ] ) ) {
          return new WP_Error( __METHOD__, __( 'Interface name not understood.', 'wpp' ) );
        }

        //** If interface belongs to premium feature we should get it from CDN */
        if( !empty( $atts[ 'feature' ] ) ) {

          //** Set unique transient to be sure that if interface params will be updated ( e.g. version ) we will not use the old transient */
          $transient = 'wpp_interface::';
          $transient .= !empty( $atts[ 'feature' ] ) ? $atts[ 'feature' ] . '::' : '';
          $transient .= !empty( $matches[ 1 ] ) ? untrailingslashit( $matches[ 1 ] ) . '::' : '';
          $transient .= $matches[ 2 ] . '::' . $atts[ 'version' ];

          //* Try to load from transient */
          if( $_interface = get_transient( $transient ) ) {
            return $_interface;
          }

          //** Set CDN url */
          $url = trailingslashit( !empty( $atts[ 'cdn_url' ] ) ? $atts[ 'cdn_url' ] : ( defined( 'UD_CDN_URL' ) ? UD_CDN_URL : ( is_ssl() ? 'https' : 'http' ) . '://ud-cdn.com' ) );

          if( !empty( $matches[ 1 ] ) ) {
            $resource = "{$matches[1]}{$atts['version']}/views/{$matches[2]}" . ".interface";
          } else {
            $resource = "{$matches[2]}/{$atts['version']}/views/{$matches[2]}" . ".interface";
          }

          //** Set params */
          $customer_key = get_option( 'ud::customer_key' );
          $site_uid     = get_option( 'ud::site_uid' );
          if( !$site_uid ) $site_uid = '';
          $key    = ( !empty( $customer_key ) ) ? base64_encode( "{$customer_key}:{$site_uid}" ) : false;
          $params = array_filter( array( 'key' => $key ) );

          $_response = WPP_F::get_service( "wpp/{$atts['feature']}", $resource, $params, array( 'source' => $url ) );

          if( !is_wp_error( $_response ) ) {
            set_transient( $transient, $_response->html, $atts[ 'expiration' ] );
          }

          return $_response;

        } //* Try to find interface locally */
        else if( file_exists( $_path = UI . '/interfaces/' . $matches[ 1 ] . $matches[ 2 ] . '.interface' ) ) {
          return file_get_contents( $_path );
        }

        return new WP_Error( __METHOD__, __( 'Interface was not found.', 'wpp' ) );

      }

      /**
       * Returns url of script.
       * Checks script locally and if it doesn't exist, uses CDN.
       *
       * @param mixed $_atts Set of params or name of file without extension
       *
       * @return string url
       * @author peshkov@UD
       */
      static function get_script_url( $_atts = false ) {
        global $wp_properties;

        $atts = shortcode_atts( array(
          'name'        => is_string( $_atts ) ? $_atts : '',
          'version'     => 'latest',
          'feature'     => false,
          'debug'       => ( !defined( 'SCRIPT_DEBUG' ) || !SCRIPT_DEBUG ) ? false : true,
          'cdn_url'     => false,
          'development' => ( !defined( 'UD_SCRIPT_LATEST' ) || !UD_JS_LATEST ) ? false : true,
        ), $_atts );

        /** Set CDN url */
        $url = trailingslashit( !empty( $atts[ 'cdn_url' ] ) ? $atts[ 'cdn_url' ] : ( defined( 'UD_CDN_URL' ) ? UD_CDN_URL : ( is_ssl() ? 'https' : 'http' ) . '://ud-cdn.com' ) );

        if( empty( $atts[ 'name' ] ) ) {
          return false;
        }

        //** Parse script name */
        if( strpos( $atts[ 'name' ], '/' ) === 0 ) {
          $atts[ 'name' ] = substr( $atts[ 'name' ], 1 );
        }

        preg_match( $wp_properties[ '_regex' ][ 'get_script_url' ], $atts[ 'name' ], $matches );

        if( empty( $matches[ 2 ] ) ) {
          return false;
        }

        //** If script belongs to premium feature we should get it from  */
        if( $atts[ 'feature' ] ) {

          if( !empty( $matches[ 1 ] ) ) {
            $url .= "wpp/{$atts['feature']}/{$matches[1]}{$atts['version']}/{$matches[2]}" . ( !$atts[ 'debug' ] ? '.min' : '' ) . ".js";
          } else {
            $url .= "wpp/{$atts['feature']}/{$matches[2]}/{$atts['version']}/{$matches[2]}" . ( !$atts[ 'debug' ] ? '.min' : '' ) . ".js";
          }

          /** Set params */
          $customer_key = get_option( 'ud::customer_key' );
          $site_uid     = get_option( 'ud::site_uid' );
          if( !$site_uid ) $site_uid = '';
          $key    = ( !empty( $customer_key ) ) ? base64_encode( "{$customer_key}:{$site_uid}" ) : false;
          $params = array_filter( array( 'key' => $key ) );

          if( !empty( $params ) ) {
            $url .= "?" . build_query( $params );
          }

        } /* Try to find script locally */
        else if( file_exists( $path = WPP_Path . 'js/' . $matches[ 1 ] . $matches[ 2 ] . ( !$atts[ 'debug' ] ? '.min' : '' ) . '.js' ) ) {
          return str_replace( WPP_Path, WPP_URL, $path );
        } /* Try to get script from CDN */
        else {
          $atts[ 'version' ] = $atts[ 'development' ] ? 'latest' : $atts[ 'version' ];
          $url .= "js/{$matches[2]}/{$atts['version']}/" . $matches[ 2 ] . ( !$atts[ 'debug' ] ? '.min' : '' ) . ".js";
        }

        return $url;
      }

      /**
       * Returns a UI element for AJAX Response
       *
       * @author korotkov@UD
       * @since 2.0
       */
      static function get_ui( $args = false ) {

        $defaults = array(
          'scope'   => 'core',
          'ui'      => false,
          'version' => false
        );

        if( is_string( $args ) ) {
          $d = explode( '.', $args );
          if( empty( $d[ 0 ] ) || empty( $d[ 1 ] ) ) {
            return array( 'success' => false, 'message' => __( 'Passed params are wrong', 'wpp' ) );
          }
          $args = array(
            'scope' => array_shift( $d ),
            'ui'    => implode( '.', $d )
          );
        }

        if( !is_array( $args ) ) {
          return array( 'success' => false, 'message' => __( 'Passed params are wrong', 'wpp' ) );
        }

        $args = wp_parse_args( $args, $defaults );

        if( !$args[ 'version' ] && $args[ 'scope' ] !== 'core' && is_callable( array( $args[ 'scope' ], 'get_interface_version' ) ) ) {
          $args[ 'version' ] = call_user_func( array( $args[ 'scope' ], 'get_interface_version' ) );
        }

        $html = self::get_interface( array_filter( array(
          'interface' => $args[ 'ui' ],
          'feature'   => $args[ 'scope' ] !== 'core' ? $args[ 'scope' ] : false,
          'version'   => $args[ 'version' ]
        ) ) );

        if( is_wp_error( $html ) ) {

          //** Check template file for existance */
          if( file_exists( UI . '/templates/' . $args[ 'ui' ] . '.php' ) ) {

            //** Buffer page content */
            ob_start();
            include_once UI . '/templates/' . $args[ 'ui' ] . '.php';
            $html = ob_get_clean();

          } else {

            return array(
              'success' => false,
              'message' => sprintf( __( 'Could not find the file "%s" for UI section.', 'wpp' ), '/settings/' . $args[ 'ui' ] . '.php' )
            );
          }

        }

        //** Return what we got */
        return array(
          'success' => true,
          'ui'      => $html,
          'id'      => $args[ 'scope' ] . '.' . $args[ 'ui' ],
        );

      }

      /**
       * Returns an array of UI elements for AJAX Response
       *
       * @author odokienko@UD
       * @since 2.0.0
       */
      static function get_uis( $argies = false ) {

        $results = array();

        //** Prepare args */
        foreach( (array) $argies as $ui ) {
          if( empty( $ui ) ) continue;
          //** Collect what we got */
          $results[ ] = self::get_ui( $ui );
        }

        return array(
          'success' => true,
          'uis'     => $results
        );

      }

      /**
       * Data Model for Upgrade / Install splash screen.
       *
       * @since 2.0
       * @author potanin@UD
       */
      static function model_wpp_upgrade( $model ) {
        global $wp_properties;

        $strings = array(
          'page_title'      => __( 'Welcome to WP-Property', 'wpp' ),
          'welcome_message' => __( 'Blah blah, Welcome to WP-Property', 'wpp' ),
        );

        $model = array(
          '_static' => array(
            'strings' => $strings,
          ),
          'global'  => array(
            '_observable' => array(),
          ),
        );

        return $model;

      }

      /**
       * Returns MVVM Model for settings page
       *
       * @author peshkov@UD
       */
      static function model_wpp_settings( $model ) {
        global $wp_properties;

        /**
         * stripslashes_deep() added due to task #204
         *
         * @author korotkov@ud 01.21.2013
         */
        $_data_structure = stripslashes_deep( (array) $wp_properties[ '_data_structure' ] );
        $attributes      = array_values( (array) $_data_structure[ 'attributes' ] );
        $groups          = array_values( (array) $_data_structure[ 'groups' ] );
        $types           = array_values( (array) $_data_structure[ 'types' ] );
        $format          = (array) $wp_properties[ '_attribute_classifications' ];

        $image_sizes     = WPP_F::all_image_sizes();
        $wpp_image_sizes = array_keys( (array) $wp_properties[ 'image_sizes' ] );
        foreach( $image_sizes as $k => $v ) {
          $image_sizes[ $k ][ 'built_in' ] = in_array( $k, $wpp_image_sizes ) ? false : true;
        }

        $logs = WPP_F::get_log( array( 'limit' => 300, 'sort_type' => 'DESC' ) );
        $logs = !empty( $logs[ 'success' ] ) && $logs[ 'success' ] === true ? $logs[ 'log' ] : array();

        $model = array(
          '_static' => array(
            'using_custom_css' => ( file_exists( STYLESHEETPATH . '/wp_properties.css' ) || file_exists( TEMPLATEPATH . '/wp_properties.css' ) ? true : false )
          ),
          'global'  => array(
            '_observable' => array(
              'attributes'               => $attributes,
              'groups'                   => $groups,
              'property_types'           => $types,
              'attribute_classification' => $format,
              'image_sizes'              => $image_sizes,
              'activity_logs'            => $logs,
            ),
          ),
          '_action' => array(
            'wpp_property_type_settings' => apply_filters( 'wpp_property_type_settings', array() )
          ),
        );

        return $model;
      }

      /**
       * Was goign to put cloud synchornization status here but didn't look right
       *
       * @author potanin@UD
       * @since 2.0
       */
      static function admin_footer_text( $default ) {
        return $default;
      }

      /**
       * Show a Welcome! / Wizard UI on updates and new installs
       *
       * @screen_id property_page_wpp_upgrade
       * @author potanin@UD
       * @version 2.0
       */
      static function upgrade_splash() {

        $interface = false;

        switch( true ) {

          /* Render New Installation and Setup Wizard */
          case ( get_option( 'wpp::splash::new_installation' ) ):
            $interface = UI::get_interface( 'upgrade_splash' );
            //$interface = UI::get_interface( 'new_installation_splash' );
            break;

          /* Render Upgrade Splash */
          case ( get_option( 'wpp::splash::upgrade' ) ):
            $interface = UI::get_interface( 'upgrade_splash' );
            break;

        }

        echo !$interface || is_wp_error( $interface ) ? __( 'Could not load Setup Assistant interface. Please contact support.', 'wpp' ) : $interface;

      }

      /**
       * Displays the primary metabox on property editing page.
       *
       *
       * @version 1.14.2
       * @author Usability Dynamics <info@usabilitydynamics.com>
       * @package WP-Property
       *
       */
      static function page_attributes_meta_box( $post ) {

        $post_type_object = get_post_type_object( $post->post_type );
        if( $post_type_object->hierarchical ) {
          $pages = wp_dropdown_pages( array( 'post_type' => $post->post_type, 'exclude_tree' => $post->ID, 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __( '(no parent)', 'wpp' ), 'sort_column' => 'menu_order, post_title', 'echo' => 0 ) );
          if( !empty( $pages ) ) {
            ?>

            <strong><?php _e( 'Parent', 'wpp' ) ?></strong>
            <label class="screen-reader-text" for="parent_id"><?php _e( 'Parent', 'wpp' ) ?></label>
            <?php echo $pages; ?>
          <?php
          } // end empty pages check
        } // end hierarchical check.
        if( 'page' == $post->post_type && 0 != count( get_page_templates() ) ) {
          $template = !empty( $post->page_template ) ? $post->page_template : false;
          ?>
          <strong><?php _e( 'Template', 'wpp' ) ?></strong>
          <label class="screen-reader-text" for="page_template"><?php _e( 'Page Template', 'wpp' ) ?></label>
          <select name="page_template" id="page_template">
        <option value='default'><?php _e( 'Default Template', 'wpp' ); ?></option>
            <?php page_template_dropdown( $template ); ?>
      </select>
        <?php } ?>
        <strong><?php _e( 'Order', 'wpp' ) ?></strong>
        <p><label class="screen-reader-text" for="menu_order"><?php _e( 'Order', 'wpp' ) ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr( $post->menu_order ) ?>"/></p>
        <p><?php if( 'page' == $post->post_type )
            _e( 'Need help? Use the Help tab in the upper right of your screen.', 'wpp' ); ?></p>
      <?php
      }

      /**
       * Prints Property Atrributes Metabox
       * on Property Edit Page
       *
       * @param object $object. Property
       * @param array  $attrs. Metabox attributes
       */
      static function metabox_meta( $object, $attrs ) {
        global $wp_properties, $wpdb;
        static $loaded = false;

        $property            = WPP_F::get_property( $object->ID );
        $instance            = $attrs[ 'id' ];
        $stats_group         = ( !empty( $attrs[ 'args' ][ 'group' ] ) ? $attrs[ 'args' ][ 'group' ] : false );
        $disabled_attributes = (array) $wp_properties[ '_geo_attributes' ];
        $property_stats      = (array) $wp_properties[ 'property_stats' ];
        $stat_keys           = array_keys( $property_stats );
        $this_property_type  = $property[ 'property_type' ];

        //* Set default property type */
        if( empty( $this_property_type ) && empty( $property[ 'post_name' ] ) ) {
          $this_property_type = WPP_F::get_most_common_property_type();
        }

        //** Check for current property type if it is deleted */
        if( is_array( $wp_properties[ 'property_types' ] ) && isset( $property[ 'property_type' ] ) && !in_array( $property[ 'property_type' ], array_keys( $wp_properties[ 'property_types' ] ) ) ) {
          $wp_properties[ 'property_types' ][ $property[ 'property_type' ] ] = WPP_F::de_slug( $property[ 'property_type' ] );
          $wp_properties[ 'descriptions' ][ 'property_type' ]                = '<span class="attention">' . sprintf( __( '<strong>Warning!</strong> The %1$s %2$s type has been deleted.', 'wpp' ), $wp_properties[ 'property_types' ][ $property[ 'property_type' ] ], WPP_F::property_label( 'singular' ) ) . '</span>';
        }

        ?>

        <?php if( !$loaded ) : ?>
          <style type="text/css">
      <?php if ($wp_properties['configuration']['completely_hide_hidden_attributes_in_admin_ui'] == 'true'): ?>
      .disabled_row {
        display: none;
      }

      <?php endif; ?>
    </style>
          <?php $loaded = true; ?>
        <?php endif; ?>

        <table class="widefat property_meta">

      <?php //* 'Falls Under' field should be shown only in 'General Information' metabox */ ?>
          <?php if( $instance == 'wpp_main' ) : ?>
            <?php //** Do not do page dropdown when there are a lot of properties */ ?>
            <?php
            $sql            = apply_filters( 'wpp_falls_under_count_sql', "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = 'property' AND post_status = 'publish'" );
            $property_count = $wpdb->get_var( $sql );
            ?>
            <?php if( $property_count < 200 ) : ?>
              <?php
              $pages = wp_dropdown_pages( apply_filters( 'wpp_falls_under_dropdown', array(
                'post_type'        => 'property',
                'exclude_tree'     => $object->ID,
                'selected'         => $object->post_parent,
                'name'             => 'parent_id',
                'show_option_none' => __( '(no parent)', 'wpp' ),
                'sort_column'      => 'menu_order, post_title',
                'echo'             => 0
              ), $object ) );
              ?>
              <?php if( !empty( $pages ) ) : ?>
                <tr class="wpp_attribute_row_parent wpp_attribute_row <?php if( is_array( $wp_properties[ 'hidden_attributes' ][ $property[ 'property_type' ] ] ) && in_array( 'parent', $wp_properties[ 'hidden_attributes' ][ $property[ 'property_type' ] ] ) ) {
                  echo 'disabled_row;';
                } ?>">
            <th><?php _e( 'Falls Under', 'wpp' ); ?>
              <span class="description"><?php printf( __( 'Parent\'s %s. If selected, some values can be inherited.', 'wpp' ), WPP_F::property_label( 'singular' ) ); ?></span></th>
            <td><?php echo $pages; ?></td>
          </tr>
              <?php endif; ?>
            <?php else : ?>
              <tr class="wpp_attribute_row_parent wpp_attribute_row <?php if( is_array( $wp_properties[ 'hidden_attributes' ][ $property[ 'property_type' ] ] ) && in_array( 'parent', $wp_properties[ 'hidden_attributes' ][ $property[ 'property_type' ] ] ) ) {
                echo 'disabled_row;';
              } ?>">
            <th><?php _e( 'Falls Under', 'wpp' ); ?>
              <span class="description"><?php printf( __( 'Parent\'s %s. If selected, some values can be inherited.', 'wpp' ), WPP_F::property_label( 'singular' ) ); ?></span></th>
            <td>
              <input type="text" name="parent_id" value="<?php echo $property[ 'parent_id' ]; ?>" class="text-input wpp_numeric" size="5"/>
              <span class="description"><?php printf( __( 'ID of parent %1$s', 'wpp' ), WPP_F::property_label( 'singular' ) ); ?></span>
            </td>
          </tr>
            <?php endif; ?>
          <?php endif; ?>
          <?php

          //** Detect attributes that were taken from a range of child properties. */
          $upwards_inherited_attributes = is_array( $property[ 'system' ][ 'upwards_inherited_attributes' ] ) ? $property[ 'system' ][ 'upwards_inherited_attributes' ] : array();

          foreach( (array) $property_stats as $slug => $label ) {

            $attribute_data = WPP_Config::get_attribute_data( $slug );

            //* Be sure that attribute belongs to classification which is editable. peshkov@UD */
            if( !$wp_properties[ '_attribute_classifications' ][ $attribute_data[ 'classification' ] ][ 'settings' ][ 'editable' ] ) {
              continue;
            }

            //* Determine if attribute belongs to the current metabox ( group ) */
            if( $attribute_data[ 'group_key' ] != $instance ) {
              continue;
            }

            $attribute_description = ( !empty( $wp_properties[ 'property_stats_descriptions' ][ $slug ] ) ? $wp_properties[ 'property_stats_descriptions' ][ $slug ] : '' );

            //* Setup row classes */
            $row_classes = array( "wpp_attribute_row", "wpp_attribute_row_{$slug}" );

            if( is_array( $wp_properties[ 'hidden_attributes' ][ $property[ 'property_type' ] ] ) && in_array( 'parent', $wp_properties[ 'hidden_attributes' ][ $property[ 'property_type' ] ] ) ) {
              $row_classes[ ] = 'disabled_row';
            }
            if( !empty( $wp_properties[ 'attribute_classification' ][ $slug ] ) ) {
              $row_classes[ ] = 'wpp_classification_' . $wp_properties[ 'attribute_classification' ][ $slug ];
            }

            $clsf           = !empty( $wp_properties[ 'attribute_classification' ][ $slug ] ) ? $wp_properties[ 'attribute_classification' ][ $slug ] : WPP_Default_Classification;
            $row_classes[ ] = 'wpp_classification_' . $clsf;

            //** Make note of attributes that consist of ranges upwards inherited from child properties */
            if( in_array( $slug, $upwards_inherited_attributes ) ) {
              $row_classes[ ]         = 'wpp_upwards_inherited_attributes';
              $disabled_attributes[ ] = $slug;
              $attribute_notice       = sprintf( __( 'Values aggregated from child %1$s.', 'wpp' ), WPP_F::property_label( 'plural' ) );
            }

            if( $wp_properties[ 'configuration' ][ 'allow_multiple_attribute_values' ] == 'true' && !in_array( $slug, apply_filters( 'wpp_single_value_attributes', array( 'property_type' ) ) ) ) {
              $row_classes[ ] = 'wpp_allow_multiple';
            }

            $predefined_values = isset( $wp_properties[ 'predefined_values' ][ $slug ] ) ? $wp_properties[ 'predefined_values' ][ $slug ] : array();
            $predefined_values = apply_filters( "wpp::predefined_values", $predefined_values, $slug );

            //** Check input type */
            $input_type = isset( $wp_properties[ 'admin_attr_fields' ][ $slug ] ) ? $wp_properties[ 'admin_attr_fields' ][ $slug ] : 'input';

            ?>
            <tr class="<?php echo implode( ' ', $row_classes ); ?>">

          <th>
            <label for="wpp_meta_<?php echo $slug; ?>"><?php echo $label; ?></label>
            <span class="description"><?php echo $attribute_description; ?></span>
          </th>

          <td class="wpp_attribute_cell">
            <div class="wpp_attribute_inner_wrapper">

            <span class="disabled_message"><?php echo sprintf( __( 'Editing %s is disabled, it may be inherited.', 'wpp' ), $label ); ?></span>

            <ul class="wpp_single_attribute_entry_list">

            <?php
            $values = isset( $property[ $slug ] ) ? $property[ $slug ] : array( '' );
            $values = !is_array( $values ) ? array( $values ) : $values;

            foreach( $values as $value_count => $value ) {

              $value = apply_filters( "wpp::classification::edit::{$attribute_data['classification']}", $value, array( 'slug' => $slug, 'property' => $property ) );

              if( in_array( $slug, (array) $disabled_attributes ) ) {

                $html_input = "<input type='text' row_count='{$value_count}' id='wpp_meta_{$slug}' name='wpp_data[meta][{$slug}][{$value_count}]' class='text-input wpp_field_disabled {$attribute_data['ui_class']}' value='{$value}' disabled='disabled' />";

              } else {

                switch( $input_type ) {

                  case 'checkbox':
                    $html_input = "<input type='hidden' row_count='{$value_count}' name='wpp_data[meta][{$slug}][{$value_count}]' value='false' /><input " . checked( $value, 'true', false ) . "type='checkbox' id='wpp_meta_{$slug}' name='wpp_data[meta][{$slug}]' value='true' {$disabled} /> <label for='wpp_meta_{$slug}'>" . __( 'Enable.', 'wpp' ) . "</label>";
                    break;

                  case 'dropdown':
                    $predefined_options = array();
                    foreach( (array) $predefined_values as $key_opt => $option ) {
                      $predefined_options[ ] = "<option " . selected( esc_attr( trim( $value ) ), esc_attr( trim( str_replace( '-', '&ndash;', $key_opt ) ) ), false ) . " value='" . esc_attr( $key_opt ) . "'>" . apply_filters( "wpp::attribute::{$slug}", $option, array( 'property' => $property ) ) . "</option>";
                    }
                    $html_input = "<select row_count='{$value_count}' id='wpp_meta_{$slug}' name='wpp_data[meta][{$slug}][{$value_count}]' class='{$attribute_data['ui_class']}'><option value=''> - </option>" . implode( '', $predefined_options ) . "</select>";
                    break;

                  case 'textarea':
                    $html_input = "<textarea class=\"text-input {$attribute_data['ui_class']}\" row_count=\"{$value_count}\" id=\"wpp_meta_{$slug}\" name=\"wpp_data[meta][{$slug}][{$value_count}]\">{$value}</textarea>";
                    break;

                  default:
                    $html_input = "<input type='text' row_count='{$value_count}' id='wpp_meta_{$slug}' name='wpp_data[meta][{$slug}][{$value_count}]' class='text-input {$attribute_data['ui_class']}' value=\"{$value}\" />";
                    break;

                }

              }

              echo '<li class="wpp_field_wrapper">' . apply_filters( "wpp::metabox::input::{$slug}", $html_input, $property ) . '</li>';

            }
            ?>
            </ul>

            <span class="description">
              <?php echo apply_filters( "wpp::attribute_description::$slug", $attribute_notice, $property ); ?>
            </span>

              <?php do_action( 'wpp_ui_after_attribute_' . $slug, $object->ID ); ?>

            </div>
          </td>
        </tr>
          <?php } ?>
    </table>
      <?php
      }

    }
  }

}