<?php

namespace Hexometer;

require_once plugin_dir_path( __DIR__ ) . 'inc/Database.php';

function hexometer_handle_http_post_scan_data() {
  $db = new Database();
  if (array_key_exists('hexometerPropertyToken', $_POST)) {
    // Getting token available in database, which should be encrypted JWT
    $token = get_option('hexometer-property-token');

    if (strlen($token) < 3) {
      return;
    }

    $db->ingestScanData(json_decode(base64_decode($_POST['hexometerPropertyScanData']), true));
  }
}

add_action('init', 'Hexometer\\hexometer_handle_http_post_scan_data');
