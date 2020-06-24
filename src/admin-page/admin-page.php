<?php
/**
 * Admin Page
 *
 * @package glossary
 */

global $glossary_page_id;
$glossary_page_id = 'glossary_admin_page';

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include table view.
require_once plugin_dir_path( __FILE__ ) . 'admin-page-table.php';
// Include CRUD views.
require_once plugin_dir_path( __FILE__ ) . 'admin-page-crud.php';

add_action( 'admin_menu', 'glossary_admin_page' );

/**
 * Register admin page
 *
 * Registers the glossary admin page within the WordPress backend.
 *
 * @since 1.0.0
 *
 * @global string $glossary_page_id The slug of the glossary admin page.
 */
function glossary_admin_page() {
	global $glossary_page_id;
	add_menu_page(
		__( 'Glossary', 'glossary' ),
		__( 'Glossary', 'glossary' ),
		'manage_options',
		$glossary_page_id,
		'create_glossary_admin_page',
		'dashicons-book-alt',
		null
	);
}

/**
 * Render the glossary admin page
 *
 * Function that is called from the admin_menu hook which will then render
 * the glossary-admin-page.
 *
 * @since 1.0.0
 *
 * @global string $glossary_page_id    The slug of the glossary admin page.
 * @global string $glossary_version    The current version of the glossary plugin.
 * @global object $wpdb                The WordPress database instance.
 * @global string $glossary_table_name The name of the glossary database table.
 */
function create_glossary_admin_page() {
	global $glossary_page_id;
	global $glossary_version;
	global $wpdb;
	global $glossary_table_name;

	$handled = false;

	if ( isset( $_GET['action'] ) ) {
		$action = $_GET['action'];
		$handled = true;

		switch ( $action ) {
			case 'add':
				glossary_entry_crud( $action, null );
				break;
			case 'edit':
			case 'delete':
			case 'force-delete':
				glossary_entry_crud( $action, $_GET['id'] );
				break;
			default:
				$handled = false;
		}
	}

	if ( ! $handled ) {
		create_glossary_admin_table();
	}
}
