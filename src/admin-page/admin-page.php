<?php
/**
 * Admin Page
 */
global $glossary_page_id;
$glossary_page_id = 'glossary_admin_page';

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include table view
require_once plugin_dir_path( __FILE__ ) . 'admin-page-table.php';
// Include CRUD views
require_once plugin_dir_path( __FILE__ ) . 'admin-page-crud.php';

add_action( 'admin_menu', 'glossary_admin_page' );

function glossary_admin_page() {
	global $glossary_page_id;
	//add_options_page( 'Settings API Page', 'Settings API Page', 'manage_options', 'settings-api-page', 'settings_api_page' );
	add_menu_page( 'Glossary', 'Glossary', 'manage_options', $glossary_page_id, 'create_glossary_admin_page', 'dashicons-book-alt', null );
	//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
}

function create_glossary_admin_page() {
	global $glossary_page_id;
	global $glossary_version;
	global $wpdb;
  global $glossary_table_name;

  $handled = false;
  
  if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
      glossary_entry_crud('add', null);
      $handled = true;
    } else if ($_GET['action'] == 'edit'){
      glossary_entry_crud('edit', $_GET['id']);
      $handled = true;
    } else if ($_GET['action'] == 'delete'){
      glossary_entry_crud('delete', $_GET['id']);
      $handled = true;
    } else if ($_GET['action'] == 'force-delete'){
      glossary_entry_crud('force-delete', $_GET['id']);
      $handled = true;
    }
  }

  if (!$handled) {
    create_glossary_admin_table();
  }
}