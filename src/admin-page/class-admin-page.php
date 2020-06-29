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

require_once __DIR__ . '/controllers/class-admin-page-table-controller.php';
require_once __DIR__ . '/controllers/class-admin-page-crud-controller.php';
require_once __DIR__ . '/../helper/class-helpers.php';
require_once __DIR__ . '/../models/class-message.php';

global $glossary_page_id;
$glossary_page_id = 'glossary_admin_page';

class Admin_Page {
	const ADD          = 'add';
	const EDIT         = 'edit';
	const DELETE       = 'delete';
	const FORCE_DELETE = 'force-delete';

	private string $page_id;
	private Admin_Page_Table_Controller $table;
	private Admin_Page_CRUD_Controller $crud;
	private Glossary_DB $db;

	public function __construct( Glossary_DB $db ) {
		global $glossary_page_id;

		$this->db      = $db;
		$this->page_id = $glossary_page_id;
		$this->table   = new Admin_Page_Table_Controller( $this->db );
		$this->crud    = new Admin_Page_CRUD_Controller( $this->db );
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
			array( $this, 'run' ),
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
	public function run() {
		$action = null;

		if ( isset( $_GET['action'] ) ) {
			$action = sanitize_text_field( $_GET['action'] );
		}

		switch ( $action ) {
			case self::ADD:
			case self::EDIT:
			case self::DELETE:
			case self::FORCE_DELETE:
				$this->crud->run( $action );
				break;
			default:
				$this->table->run();
		}
	}

	/**
	 * Show message on admin-page
	 *
	 * Redirects to the admin-page and shows the provided message.
	 *
	 * @param Message $message the message to be shown.
	 */
	public static function redirect_and_show_message( Message $message ) {
		Helpers::redirect_to(
			Helpers::generate_url(
				array(
					'action'       => 'null',
					'id'           => 'null',
					'message_type' => $message->type,
					'message'      => $message->content,
				)
			)
		);
	}
}
