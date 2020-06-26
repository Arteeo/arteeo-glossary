<?php
/**
 * Admin Page
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require 'admin-page-table.php';
require 'admin-page-crud.php';

global $glossary_page_id;
$glossary_page_id = 'glossary_admin_page';

class Admin_Page {
	private string $page_id;
	private Admin_Page_Table $table;
	private Admin_Page_Crud $crud;

	public function __construct() {
		global $glossary_page_id;

		$this->page_id = $glossary_page_id;
		$this->table   = new Admin_Page_Table();
		//$this->crud  = new Admin_Page_Crud();
	}

	public function init() {
		add_action(
			'admin_menu',
			array( $this, 'register_menu_page' ),
		);
	}

	public function register_menu_page() {
		add_menu_page(
			__( 'Glossary', 'arteeo-glossary' ),
			__( 'Glossary', 'arteeo-glossary' ),
			'manage_options',
			$this->page_id,
			array( $this, 'render' ),
			'dashicons-book-alt',
			null,
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
	 * @global object $wpdb                The WordPress database instance.
	 * @global string $glossary_table_name The name of the glossary database table.
	 */
	public function render() {
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
			$this->table->render();
		}
	}
}
