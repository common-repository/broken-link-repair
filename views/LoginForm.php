<?php
  require_once plugin_dir_path( __FILE__ ) . 'layout/header.php';
?>
  <div id="hexometer_wp_list_wrap">
    <div id="hexometer_wp_list_body">
    <div class="error notice">
      <p>For using Hexometer Live broken link cleaner plugin you have to activate it using Activation Code <a href='https://dash.hexometer.com/?source=plugin-wordpress-login&ref=<?php echo Hexometer\hexometer_helper_get_hostname(); ?>' target='_blank'>dash.hexometer.com</a></p>
    </div>
    <div id='hexometer_dynamic_errors'>
    </div>
    <div class='login-form-wrap'>
      <form class='form' method='post' id='activation_form'>
        <?php wp_nonce_field( 'hexometer_activation', 'hexometer_activation_nonce' ); ?>
        <input id='' type='hidden' name='hexometer-activation' value='1' />
        <div class="form-field form-required term-name-wrap">
          <h4>Verification Code</h4>
          <input name="activationCode" type="text" value="" aria-required="true" required placeholder='paste verification code from dash.hexometer.com settings page' />
          <p>For getting the verification code visit <a href='https://dash.hexometer.com/?source=plugin-wordpress-login&ref=<?php echo Hexometer\hexometer_helper_get_hostname(); ?>' target='_blank'>dash.hexometer.com</a> -> Select your website -> Go to Settings page -> Copy Verification code</p>
        </div>
        <div class='form-group'>
          <button type="submit" class="button button-primary">Activate Plugin</button>
          <div class="custom-spinner lds-ring" style="display: none;"><div></div><div></div><div></div><div></div></div>
        </div>
      </form>
    </div>

    <?php if (isset($login_error_message) && $login_error_message) {?>
      <div class="error notice">
        <p><?= $login_error_message ?> </p>
      </div>
    <?php } ?>
  </div>
<?php require_once plugin_dir_path( __FILE__ ) . 'layout/footer.php'; ?>
