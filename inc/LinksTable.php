<?php

namespace Hexometer;

if(!class_exists('WP_List_Table')){
  require_once( ABSPATH . 'wp-admin/includes/screen.php' );
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

use \WP_List_Table;

class LinksTable extends WP_List_Table {
  public $success_message = "";
  public $items = array();
  public $lastScanData = array();

  public function __construct($scanData = array()) {
    parent::__construct();
    $this->lastScanData = array(
      '_id' => $scanData['_id'],
      'completed' => $scanData['completed'],
      'startedAt' => $scanData['startedAt']
    );

    $this->items = !$scanData['brokenLinks'] ? array() : $scanData['brokenLinks'];
    $this->prepare_items();
  }
  // just the barebone implementation.
  public function get_columns() {
    return array(
      // "cb"       => '<input type="checkbox" />',
      "url" => __( 'Broken URL', $this->plugin_text_domain ),
      "parentURLs" => __( 'Detected on', $this->plugin_text_domain ),
      "status" => __( 'Status', $this->plugin_text_domain ),
    );
  }

  public function get_sortable_columns() {
      // return array('url' => array('URL', false));
    return array();
  }

  function prepare_items() {
    $this->process_bulk_action();

    $this->_column_headers = array(
      $this->get_columns(),		// columns
      array(),			// hidden
      $this->get_sortable_columns(),	// sortable
    );

    $currentPage = $this->get_pagenum();
    $limit = 20;

    $this->set_pagination_args( array(
      'total_items' => is_array($this->items) ? sizeof($this->items) : 0,
      'per_page'    => $limit,
    ) );

    $this->items = is_array($this->items) ?
      array_slice($this->items, ($currentPage - 1) * $limit, $limit)
      :
      array();
  }

  public function column_default( $item, $column_name ) {
    switch($column_name) {
      case 'url':
        return '<a href="'.$item[ $column_name ].'" class="item-content" target="_blank">'.$item[ $column_name ].'</a>';
      case 'parentURLs':
        $parentURLs = explode(',', $item[$column_name]);
        $content = '<p class="parents-content">';
        foreach($parentURLs as $parentURL) {
          $content .= '<a href="'.$parentURL.'" target="_blank">'.$parentURL.'</a><br/>';
        }
        return $content . '</p><a href="#" class="parent-morelink" style="display: block;color: #adadad;margin-top: 5px;">More</a>';
    }
    if (array_key_exists($column_name, $this->get_columns())) {
      return $item[ $column_name ];
    }
  }

  public function extra_tablenav($which) {
    if ($which === 'top') {
      ?>
        <div id="hexometer_links_reload">
          <button type="button" class="button button-primary">Sync Links With Hexometer</button>
          <div class="custom-spinner lds-ring" style="display: none;"><div></div><div></div><div></div><div></div></div>
        </div>
      <?php
    }
  }

  protected function process_bulk_action() {
		if ( 'fix' === $this->current_action() ) {
      $this->success_message = '500 Items Fixes in Content';
    }
	}

  public function renderList() {
    // render the List Table
	  include_once( plugin_dir_path( __DIR__ ) . 'views/LinksTableContent.php' );
  }
}
