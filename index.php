<?php

namespace Hexometer;

/**
* Plugin Name: Broken Link Repair
* Plugin URI: https://hexometer.com
* Description: WordPress Broken Links Repair Plugin will get the broken links list from your Hexometer dashboard and will disable (or “unlink”) them before the page will be loaded.
* Version: 1.0.4
* Author: HEXACT, Inc.
* Author URI: https://hexact.io
**/

require_once plugin_dir_path( __FILE__ ) . 'inc/Database.php';
require_once plugin_dir_path( __FILE__ ) . 'hooks/content.php';
require_once plugin_dir_path( __FILE__ ) . 'hooks/admin.php';
require_once plugin_dir_path( __FILE__ ) . 'hooks/ingest.php';

function hexometer_broken_links_activated() {
  $db = new Database();
  $db->createTable();
}

function hexometer_broken_links_deactivated() {
  $db = new Database();
  $db->cleanTable();
}

function hexometer_broken_links_uninstalled() {
  $db = new Database();
  $db->fullClean();
}

register_activation_hook(__FILE__, 'Hexometer\\hexometer_broken_links_activated');
register_deactivation_hook(__FILE__, 'Hexometer\\hexometer_broken_links_deactivated');
register_uninstall_hook(__FILE__, 'Hexometer\\hexometer_broken_links_uninstalled');


