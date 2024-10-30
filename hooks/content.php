<?php

namespace Hexometer;
require_once plugin_dir_path( __DIR__ ) . 'inc/Database.php';

use DOMDocument;

function hexometer_hide_broken_links_href( $post_content ) {
  if (!isset($post_content) || strlen($post_content) === 0) {
    return;
  }

  $db = new Database();

  $should_disable = get_option('hexometer-property-disable-auto-fix', NULL);
  if ($should_disable) {
    return $post_content;
  }

  $doc = new DOMDocument();
  libxml_use_internal_errors(true);
  $doc->loadHTML($post_content);
  libxml_clear_errors();
  $hashes = array();
  foreach($doc->getElementsByTagName('a') as $linkItem) {
    $hashes[] = "'". md5($linkItem->getAttribute('href')) ."'";
  }

  $matchedLinks = $db->getMatchedHashes($hashes);
  foreach($matchedLinks as $link) {
    $post_content = str_replace($link['url'], '#hexometer-broken-link-repair-'.$link['url'], $post_content);
  }

  return $post_content;
}
add_action('the_content', 'Hexometer\\hexometer_hide_broken_links_href');
