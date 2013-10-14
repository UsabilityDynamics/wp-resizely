<?php
/**
 * Core file for WP_Resizely
 */
namespace UsabilityDynamics\WP_Resizely\UI{
  /** Bring in any classes we'll need */
  use UsabilityDynamics as UD;
  use UsabilityDynamics\WP_Resizely\Functions as F;

  /**
   * The settings page
   *
   * @class Settings
   * @author: potanin@UD
   */
  class Settings {
    /** Our constructor is our template file */
    function __construct(){
      /** Get our options */
      $instance = UD\WP_Bootstrap::get_instance();
      $options = $instance->core->options;
      prq( $options );
?>

<div class="wrap">
  <?php screen_icon(); ?>
  <h2><?php _e( 'Resize.ly Settings', WP_Resizely_Locale ); ?></h2>

  <form method="post" action="" />
  <?php wp_nonce_field( 'wp-resizely-settings' ); ?>

  <table class="form-table">

    <?php /*<tr valign="top">
        <th scope="row"><label for="blogname"><?php _e( 'Site Title' ) ?></label></th>
        <td><input name="options[blogname]" type="text" id="blogname" value="<?php echo $options->blogname; ?>" class="regular-text" /></td>
      </tr>*/ ?>

    <tr valign="top">
      <th scope="row"><?php _e( 'Options' ) ?></th>
      <td>

        <ul>
          <li><label><input name="options[disable_resizely]" type="checkbox" value="true" <?php checked( true, $options->disable_resizely  ); ?> /> <?php _e( 'Disable Resize.ly', WP_Resizely_Locale ); ?></label></li>
        </ul>

      </td>
    </tr>

  </table>

  <?php submit_button(); ?>

  </form>

</div> <?php



    }
  }
}