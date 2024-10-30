<?php require_once plugin_dir_path( __FILE__ ) . 'layout/header.php'; ?>
  <div id="nds-wp-list-table-demo">
    <div id="nds-post-body">
    <?php if (!$this->lastScanData['completed'] && $this->lastScanData['startedAt']) { ?>
      <div class="error notice">
        <p>Last Started on <?php echo date("F d Y, H:m", strtotime($this->lastScanData['startedAt'])); ?>, Hexometer will automatically update list when scan will be completed.</p>
      </div>
    <?php } else if ($this->lastScanData['startedAt']) { ?>
      <div class="updated notice">
        <p>Last Scan completed on <?php echo date("F d Y, H:m", strtotime($this->lastScanData['startedAt'])); ?>, please visit <a href='https://dash.hexometer.com/?source=plugin-wordpress&ref=<?php echo Hexometer\hexometer_helper_get_hostname(); ?>' target='_blank'>dash.hexometer.com</a> to see full results.</p>
      </div>
    <?php } else { ?>
      <div class="updated notice">
        <p>There is no data available yet. Hexometer will automatically update broken links list when available.</p>
      </div>
    <?php } ?>
      <div id='hexometer_dynamic_errors'>
      </div>
      <p>
        <form id='disable-autofix-form' class='form' method='post'>
          <?php wp_nonce_field( 'hexometer_activation', 'hexometer_activation_nonce' ); ?>
          <input id='disable-autofix-input' type='hidden' name='hexometer-disable-auto-fix' value='0' />
          <div class='form-group'>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="disable-autofix-checkbox" <?= get_option('hexometer-property-disable-auto-fix', NULL) != 1 ? 'checked' : '' ?>  />
              <label class="custom-control-label" for="disable-autofix-checkbox">Automatically disable broken links from content</label>
            </div>
          </div>
          <div>&nbsp;</div>
          <div class='form-group'>
            <button type="submit" class="button button-primary">Save</button>
          </div>
        </form>
      </p>
      <hr/>
      <p>&nbsp;</p>
      <form id="broken_links_list" method="post">
        <?php $this->display(); ?>
      </form>
    </div>
  </div>
<?php require_once plugin_dir_path( __FILE__ ) . 'layout/footer.php'; ?>
