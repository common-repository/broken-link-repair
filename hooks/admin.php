<?php

namespace Hexometer;

require_once plugin_dir_path( __DIR__ ) . 'inc/Database.php';
require_once plugin_dir_path( __DIR__ ) . 'inc/LinksTable.php';

function hexometer_helper_get_hostname() {
  $base_url = get_site_url();
  $parsed_url = parse_url($base_url); // PHP_URL_HOST == 2
  if ($parsed_url !== false) {
    return $parsed_url['host'];
  }

  return $base_url;
}

// Setup Admin Menu
add_action('admin_menu', 'Hexometer\\hexometer_plugin_setup_menu');
function hexometer_plugin_setup_menu() {
  add_menu_page( 'Broken Link Repair', 'Broken Link Repair', 'manage_options', 'hexometer-broken-links', 'Hexometer\\hexometer_broken_links_admin_page_init', file_get_contents(plugin_dir_path( __DIR__ ) . 'static/img/logo.png.base64') );
}

// Add settings link in Plugins list page
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'Hexometer\\hexometer_add_plugin_page_settings_link');
function hexometer_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .admin_url( 'admin.php?page=hexometer-broken-links' ) .'">' . __('Settings') . '</a>';
	return $links;
}


// Base Admin page Init
function hexometer_broken_links_admin_page_init() {
  $db = new Database();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['hexometer_activation_nonce']) || ! wp_verify_nonce( $_POST['hexometer_activation_nonce'], 'hexometer_activation' )) {
      echo 'Seems your request is not verified!';
      exit;
    }

    if ($_POST['hexometer-activation']) {
      add_option('hexometer-property-token', sanitize_text_field($_POST['activationCode']), '', 'yes');
    }

    if ($_POST['hexometer-disable-auto-fix'] == 1) {
      add_option('hexometer-property-disable-auto-fix', 1, '', 'yes');
    } else if ($_POST['hexometer-disable-auto-fix'] == 0) {
      delete_option('hexometer-property-disable-auto-fix');
    }
  }

  $token = get_option('hexometer-property-token', '');
  if (!$token || strlen($token) == 0) {
    include_once( plugin_dir_path( __DIR__ ) . 'views/LoginForm.php' );
  } else {
    $linksTable = new LinksTable($db->getFullScanData());
    $linksTable->renderList();
    echo '<input id="property_token" type="hidden" name="property" value="'.$token.'" />';
  }
}


// Adding Asset files for admin panel page
function hexometer_broken_links_admin_adding_scripts() {
  wp_enqueue_style('hexometer-main', plugins_url('static/css/main.css', __DIR__ ), array(), "" . round(microtime(true) * 1000));
  wp_enqueue_script('readmore', plugins_url('static/js/shortentext.js', __DIR__ ), array('jquery'), round(microtime(true) * 1000));
  wp_enqueue_script('hexometer-main', plugins_url('static/js/main.js', __DIR__ ), array('jquery'), round(microtime(true) * 1000));
}

add_action( 'admin_enqueue_scripts', 'Hexometer\\hexometer_broken_links_admin_adding_scripts', 999 );
