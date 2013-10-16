<div class="wrap">
  <?php screen_icon(); ?>
  <h2><?php _e( 'Resize.ly Settings', WP_Resizely_Locale ); ?></h2>

  <form method="post" action="">
    <?php wp_nonce_field( 'wp-resizely-settings' ); ?>

    <h3 class="title"><?php _e( 'General' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <?php _e( 'Disable Resize.ly?', WP_Resizely_Locale ); ?>
          </th>
          <td>
            <label for="rly_disable_resizely">
              <input id="rly_disable_resizely" name="options[rly_disable]" type="checkbox" value="true" <?php checked( true, $options->rly_disable  ); ?> />
              <?php _e( 'Yes, globally disable Resize.ly', WP_Resizely_Locale ); ?>
            </label>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <?php _e( 'Disable Thumbnails', WP_Resizely_Locale ); ?>
          </th>
          <td>
            <label for="rly_disable_thumbnails">
              <input id="rly_disable_thumbnails" name="options[rly_disable_thumbnails]" type="checkbox" value="true" <?php checked( true, $options->rly_disable_thumbnails  ); ?> />
              <?php _e( 'Yes, disable WordPress native thumbnail generation', WP_Resizely_Locale ); ?>
            </label>
            <p class="description">
              <?php _e( 'Since Resize.ly handles thumbnail generation, you no longer need to have them stored on your server.', WP_Resizely_Locale ); ?>
            </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <?php _e( 'Use Resize.ly for all images', WP_Resizely_Locale ); ?>
          </th>
          <td>
            <label for="rly_process_all_images">
              <input id="rly_process_all_images" name="options[rly_process_all_images]" type="checkbox" value="true" <?php checked( true, $options->rly_process_all_images  ); ?> />
              <?php _e( 'Yes, process all site images', WP_Resizely_Locale ); ?>
            </label>
            <p class="description">
              <?php _e( 'This option will cause WP-Resizely to resize all images on a page, not just the ones set via the Media Library and in post content.', WP_Resizely_Locale ); ?>
            </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <?php _e( 'Force Render', WP_Resizely_Locale ); ?>
          </th>
          <td>
            <label for="rly_rerender">
              <input id="rly_rerender" name="options[rly_rerender]" type="checkbox" value="true" <?php checked( true, $options->rly_rerender  ); ?> />
              <?php _e( 'Yes, render the images on each request (don\'t use caching)', WP_Resizely_Locale ); ?>
            </label>
            <p class="description">
              <?php _e( 'Forcing Resize.ly to re-render the images on each request ensures that you will always have the latest version of the image.', WP_Resizely_Locale ); ?>
              <strong>
                <?php _e( 'This is not recommended for a production system!', WP_Resizely_Locale ); ?>
              </strong>
            </p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <?php _e( 'Debug', WP_Resizely_Locale ); ?>
          </th>
          <td>
            <label for="rly_debug">
              <input id="rly_debug" name="options[rly_debug]" type="checkbox" value="true" <?php checked( true, $options->rly_debug  ); ?> />
              <?php _e( 'Yes, put Resize.ly in debug mode', WP_Resizely_Locale ); ?>
            </label>
            <p class="description">
              <?php _e( 'Debug mode shows quite a bit more information in the console on the front end.', WP_Resizely_Locale ); ?>
            </p>
          </td>
        </tr>
      </tbody>
    </table>

    <h3 class="title"><?php _e( 'Advanced Options', WP_Resizely_Locale ); ?></h3>
    <a id="rly_advanced_options_link" href="#">
      <?php _e( 'Show Advanced Options', WP_Resizely_Locale ); ?>
    </a>
    <table id="rly_advanced_options" class="form-table" style="display:none;">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="rly_base_domain">
              <?php _e( 'Base Domain', WP_Resizely_Locale ); ?>
            </label>
          </th>
          <td>
            <input id="rly_base_domain" class="regular-text" name="options[rly_base_domain]" type="text" value="<?php echo htmlentities( $options->rly_base_domain ); ?>" >
            <p class="description">
              <?php _e( 'If you need to work with an alternate Resize.ly domain, enter it here. For the most part, this should not be changed.', WP_Resizely_Locale ); ?>
            </p>
          </td>
        </tr>
      </tbody>
    </table>

    <?php submit_button(); ?>
  </form>

</div>