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
      'rly_base_domain' => 'resize.ly',
      'rly_disable' => false,
      'rly_process_all_images' => false,
      'home_url' => false,
      'is_ssl' => false
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
      /** Add on some of our other options that we'll need */
      $options->home_url = home_url();
      if( is_ssl() ) {
        $options->is_ssl = true;
      }
      /** Assign the options */
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
        /** Add actions that are for both front and backend */
        /** We hook in here so that we can always provide image sizes, even when none exist */
        add_filter( 'wp_get_attachment_metadata', array( $this, 'wp_get_attachment_metadata' ), 10, 100 );
        /** We're intercepting the request for getting image sizes, which allows us to stop thumbnail generation */
        add_filter( 'image_resize_dimensions', array( $this, 'image_resize_dimensions' ), 1, 100 );
        if( is_admin() ){
          /** We're going to intercept the request to send the image to the editor */
          add_filter( 'get_image_tag', array( $this, 'get_image_tag' ), 10, 100 );
          add_filter( 'get_image_tag_class', array( $this, 'get_image_tag_class' ), 10, 100 );
          add_filter( 'image_downsize', array( $this, 'image_downsize' ), 1, 100 );
        }else{
          /** Add our actions for inserting the scripts */
          add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
          add_action( 'wp_footer', array( $this, 'wp_footer' ) );
          /** Go ahead and start output buffering */
          ob_start( array( $this, 'ob_callback' ) );
        }

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

    /**
     * This is our own function to get image sizes and dimensions
     */
    function get_image_sizes(){
      global $_wp_additional_image_sizes;
      $sizes = array();
      foreach( get_intermediate_image_sizes() as $s ){
        /** Skip numeric image sizes */
        if( is_integer( $s ) ){
          continue;
        }
        /** Now, add it to the array */
        $sizes[ $s ] = array(
          'width' => isset( $_wp_additional_image_sizes[ $s ][ 'width' ] ) ? intval( $_wp_additional_image_sizes[ $s ][ 'width' ] ) : get_option( "{$s}_size_w" ),
          'height' => isset( $_wp_additional_image_sizes[ $s ][ 'height' ] ) ? intval( $_wp_additional_image_sizes[ $s ][ 'height' ] ) : get_option( "{$s}_size_h" ),
          'crop' => isset( $_wp_additional_image_sizes[ $s ][ 'crop' ] ) ? intval( $_wp_additional_image_sizes[ $s ][ 'crop' ] ) : get_option( "{$s}_crop" )
        );
      }
      return $sizes;
    }

    /**
     * Hook into the attachment metadata, so we can add the image sizes when required
     */
    function wp_get_attachment_metadata( $data, $id ){
      $sizes = $this->get_image_sizes();
      $mime_type = get_post_mime_type( $id );
      /** If this is not an image, bail */
      if( stripos( $mime_type, 'image/' ) === false ){
        return $data;
      }
      /** Go through our sizes, and replace the data */
      $data[ 'sizes' ] = array();
      foreach( $sizes as $size => $dims ){
        $data[ 'sizes' ][ $size ] = array(
          'file' => wp_basename( $data[ 'file' ] ),
          'width' => $dims[ 'width' ],
          'height' => $dims[ 'height' ]
        );
      }
      return $data;
    }

    /**
     * We intercept this request in order to stop thumbnails from being created, and
     * to send back the URL to resize.ly
     * @author williams@UD
     *
     * ***********************************************************************
     *
     * Scale an image to fit a particular size (such as 'thumb' or 'medium').
     *
     * Array with image url, width, height, and whether is intermediate size, in
     * that order is returned on success is returned. $is_intermediate is true if
     * $url is a resized image, false if it is the original.
     *
     * The URL might be the original image, or it might be a resized version. This
     * function won't create a new resized copy, it will just return an already
     * resized one if it exists.
     *
     * A plugin may use the 'image_downsize' filter to hook into and offer image
     * resizing services for images. The hook must return an array with the same
     * elements that are returned in the function. The first element being the URL
     * to the new image that was resized.
     *
     * @since 2.5.0
     * @uses apply_filters() Calls 'image_downsize' on $id and $size to provide
     *  resize services.
     *
     * @param int $id Attachment ID for image.
     * @param array|string $size Optional, default is 'medium'. Size of image, either array or string.
     * @return bool|array False on failure, array on success.
     */
    function image_downsize( $ret, $id, $size ){
      if( !wp_attachment_is_image( $id ) ){
        return false;
      }
      $img_url = wp_get_attachment_url( $id );
      $meta = wp_get_attachment_metadata( $id );
      $width = $height = 0;
      $is_intermediate = false;
      $img_url_basename = wp_basename( $img_url );
      // try for a new style intermediate size
      if( $intermediate = image_get_intermediate_size( $id, $size ) ){
        $width = $intermediate[ 'width' ];
        $height = $intermediate[ 'height' ];
        $is_intermediate = true;
      }
      if( !$width && !$height && isset( $meta[ 'width' ], $meta[ 'height' ] ) ){
        // any other type: use the real image
        $width = $meta[ 'width' ];
        $height = $meta[ 'height' ];
      }
      if( $img_url ){
        // we have the actual image size, but might need to further constrain it if content_width is narrower
        list( $width, $height ) = image_constrain_size_for_editor( $width, $height, $size );
        // Added by williams@ud to change the path to be one that includes resize.ly
        $original_img_url =  $img_url;
        $img_url = "//{$this->options->rly_base_domain}/{$width}x{$height}/{$img_url}";
        return array( $img_url, $width, $height, $is_intermediate, $original_img_url );
      }
      return false;
    }

    /**
     * This function intercepts a request to get an image size, and will return false so that
     * thumbnails are not generated, but only on certain requests
     * @modified williams@UD
     *
     * ***********************************************************************
     *
     * Retrieve calculated resized dimensions for use in WP_Image_Editor.
     *
     * Calculate dimensions and coordinates for a resized image that fits within a
     * specified width and height. If $crop is true, the largest matching central
     * portion of the image will be cropped out and resized to the required size.
     *
     * @since 2.5.0
     * @uses apply_filters() Calls 'image_resize_dimensions' on $orig_w, $orig_h, $dest_w, $dest_h and
     *  $crop to provide custom resize dimensions.
     *
     * @param int $orig_w Original width.
     * @param int $orig_h Original height.
     * @param int $dest_w New width.
     * @param int $dest_h New height.
     * @param bool $crop Optional, default is false. Whether to crop image or resize.
     * @return bool|array False on failure. Returned array matches parameters for imagecopyresampled() PHP function.
     */
    function image_resize_dimensions( $ret, $orig_w, $orig_h, $dest_w, $dest_h, $crop ){
      if( $orig_w <= 0 || $orig_h <= 0 ){
        return false;
      }
      // at least one of dest_w or dest_h must be specific
      if( $dest_w <= 0 && $dest_h <= 0 ){
        return false;
      }
      // if we're cropping
      if( $crop ){
        // crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
        $aspect_ratio = $orig_w / $orig_h;
        $new_w = min( $dest_w, $orig_w );
        $new_h = min( $dest_h, $orig_h );
        if( !$new_w ){
          $new_w = intval( $new_h * $aspect_ratio );
        }
        if( !$new_h ){
          $new_h = intval( $new_w / $aspect_ratio );
        }
        $size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );
        $crop_w = round( $new_w / $size_ratio );
        $crop_h = round( $new_h / $size_ratio );
        $s_x = floor( ( $orig_w - $crop_w ) / 2 );
        $s_y = floor( ( $orig_h - $crop_h ) / 2 );
      } else{
        // don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
        $crop_w = $orig_w;
        $crop_h = $orig_h;
        $s_x = 0;
        $s_y = 0;
        list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
      }
      // if the resulting image would be the same size or larger we don't want to resize it
      if( $new_w >= $orig_w && $new_h >= $orig_h ){
        return false;
      }
      // if we're uploading an attachment, bail and return false so thumbs are not generated - williams@ud
      if( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'upload-attachment' ){
        return false;
      }
      // the return array matches the parameters to imagecopyresampled()
      // int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
      return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
    }

    /**
     * This function intercepts an image before being sent back to the editor, and we're going to transform the image
     * to what Resize.ly expects
     * Sample Arguments:
     * [0] => <img src="http://wp-resizely.loc/wp-content/uploads/2011/07/dsc20051220_160808_102-1024x682.jpg" alt="Antique Farm Machinery" width="604" height="402" class="alignright size-large wp-image-762" />
     * [1] => 762
     * [2] => Antique Farm Machinery
     * [3] =>
     * [4] => right
     * [5] => large
     */
    function get_image_tag( $html, $id, $alt, $title, $align, $size ){
      list( $img_src, $width, $height, $unknown, $original ) = image_downsize( $id, $size );
      $hwstring = image_hwstring($width, $height);
      $title = $title ? 'title="' . esc_attr( $title ) . '" ' : '';
      $class = 'align' . esc_attr( $align ) . ' size-' . esc_attr( $size ) . ' wp-image-' . $id;
      $class = apply_filters( 'get_image_tag_class', $class, $id, $align, $size, true );
      $html = '<img data-src="' . esc_attr( $original ) . '" src="' . esc_attr( $img_src ) . '" alt="' . esc_attr( $alt ) . '" ' . $hwstring . $title . 'class="' . $class . '" />';
      return $html;
    }

    /**
     * Changes the class on image tags in HTML
     * Sample Arguments:
     * [0] => alignright size-large wp-image-762
     * [1] => 762
     * [2] => right
     * [3] => large
     */
    function get_image_tag_class( $class, $id, $align, $size, $from_self = false ){
      /** We're just going to add the resizely class to it */
      if( !$from_self ){
        return $class;
      }
      return 'wp-resizely ' . $class;
    }

    /**
     * This handles our output buffering for the front end, and replaces image tags as necessary
     */
    function ob_callback( $buffer ){
      /** Lets put our HTML into a dom document */
      $doc = new \DOMDocument();
      if( @$doc->loadHTML( $buffer ) ){
        /** Get our images */
        $imgs = $doc->getElementsByTagName( 'img' );
        foreach( $imgs as $img ){
          /** Get the attributes we need */
          $classes = explode( ' ', $img->getAttribute( 'class' ) );
          $data_src = $img->getAttribute( 'data-src' );
          $src = $img->getAttribute( 'src' );
          /** Now, make sure we have the resize.ly class */
          if( in_array( 'wp-resizely', $classes ) ){
            /** See if we have the data-src attribute */
            if( $data_src ){
              $img->removeAttribute( 'src' );
            }
          }elseif( $this->options->rly_process_all_images ){
            /** Ok, so we're forcing this to occur, change the source to data-source, and add the class */
            if( $src && !$data_src ){
              $img->setAttribute( 'data-src', $src );
            }
            $img->removeAttribute( 'src' );
            if( is_array( $classes ) ){
              $classes[] = 'wp-resizely';
              $img->setAttribute( 'class', implode( ' ', $classes ) );
            }



          }
        }
        /** Ok, replace our buffer */
        $buffer = $doc->saveHTML();
      }
      return $buffer;
    }

    /**
     * Initialize Resize.ly client in footer
     *
     * @author potanin@UD
     * @for WP_Resizely_Core
     * @since 0.1.0
     */
    function wp_footer() { ?>
      <script type="text/javascript" language="javascript">
        if( typeof jQuery == 'function' ){
          jQuery( document ).ready( function( $ ){
            "use strict";
            $( 'img.wp-resizely[ data-src ]' ).resizely( {
              d: '<?php echo addcslashes( $this->options->rly_base_domain, "'" ); ?>'
            } );
          } );
        }
      </script> <?php
    }

    /**
     * Enqueue Resize.ly client-side script(s)
     *
     * @author potanin@UD
     * @for WP_Resizely_Core
     * @since 0.1.0
     * @todo Change the src to point to a minified version
     */
    function wp_enqueue_scripts() {
      /** Enqueue the script */
      wp_enqueue_script( 'wp-resizely', WP_Resizely_URL . '/externals/resizely-client/src/jquery.resizely.js', array( 'jquery' ) );
      /** Localize Resize.ly client options */
      wp_localize_script( 'wp-resizely', 'WP_Resizely', (array) $this->options );
    }
  }
}