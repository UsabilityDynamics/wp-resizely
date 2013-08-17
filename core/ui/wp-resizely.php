<?php
/**
 * -
 *
 * -
 *
 * Created by JetBrains PhpStorm.
 * @user: potanin@UD
 * @date: 8/17/13
 * @time: 4:27 PM
 *
 */

$options = WP_Resizely_Functions::options();

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

</div>