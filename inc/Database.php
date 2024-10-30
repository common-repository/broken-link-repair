<?php

namespace Hexometer;

class Database {
  // Just setting it here for later usage
  private $table_name = 'hexometer_broken_links';

  public function getTableName() {
    global $wpdb;
    return $tableName = $wpdb->prefix . $this->table_name;;
  }

  public function createTable() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS ".$this->getTableName()." (
      url text NOT NULL,
      hash varchar(50) NOT NULL,
      parentURLs text NOT NULL,
      status int NOT NULL,
      fileType text,
      contentType text,
      PRIMARY KEY  (hash)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  public function cleanTable() {
    global $wpdb;
    $wpdb->query("TRUNCATE TABLE ". $this->getTableName());
  }

  public function fullClean() {
    global $wpdb;
    delete_option('hexometer-property-disable-auto-fix');
    delete_option('hexometer-property-token');
    delete_option('hexometer_last_scan');
    $this->cleanTable();
    $wpdb->query("DROP TABLE IF EXISTS ". $this->getTableName());
  }

  public function getFullScanData() {
    global $wpdb;

    $tableName = $wpdb->prefix . $this->table_name;
    $scanData = get_option('hexometer_last_scan');
    if ($scanData && strlen($scanData) > 0) {
      $scanData = json_decode($scanData, true);
      $scanData['brokenLinks'] = $wpdb->get_results('SELECT * FROM ' . $tableName, ARRAY_A);
    } else {
      $scanData = array();
    }

    return $scanData;
  }

  public function getMatchedHashes($hashes = array()) {
    global $wpdb;
    if (sizeof($hashes) > 0) {
      return  $wpdb->get_results('SELECT url FROM ' . $this->getTableName() . ' WHERE hash IN(' . implode(",", $hashes) . ')', ARRAY_A);
    }

    return array();
  }

  public function ingestScanData($scanData) {
    global $wpdb;

    $token = get_option('hexometer-property-token', '');
    if (strlen($token) == 0) {
      return;
    }

    $tableName = $this->getTableName();
    delete_option('hexometer_last_scan');
    add_option('hexometer_last_scan', json_encode(array(
      '_id' => $scanData['_id'],
      'startedAt' => $scanData['startedAt'],
      'completed' => $scanData['completed'],
    )));

    if (array_key_exists('brokenLinks', $scanData)) {
      $wpdb->query("DELETE FROM $tableName");
      foreach($scanData['brokenLinks'] as $link) {
        $wpdb->insert($tableName, array(
          'url' => $link['url'],
          'hash' => $link['hash'],
          'parentURLs' => implode(',', $link['parentURLs']),
          'status' => $link['status'],
          'fileType' => $link['fileType'],
          'contentType' => $link['contentType']
        ));
      }
    }
  }
}

