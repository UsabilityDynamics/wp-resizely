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