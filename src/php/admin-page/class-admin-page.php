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

/**
 * Handles Admin-Page
 *
 * Contains the logic to render and sort the admin page.
 *
 * @since 1.0.0
 */
class Admin_Page {
	const ADD          = 'add';
	const EDIT         = 'edit';
	const DELETE       = 'delete';
	const FORCE_DELETE = 'force-delete';

	const PAGE_ID = 'arteeo_glossary_admin_page';

	/**
	 * The controller for the admin page table.
	 *
	 * @since 1.0.0
	 * @var Admin_Page_Table_Controller
	 */
	private Admin_Page_Table_Controller $table;

	/**
	 * The controller for the admin page crud operations.
	 *
	 * @since 1.0.0
	 * @var Admin_Page_CRUD_Controller
	 */
	private Admin_Page_CRUD_Controller $crud;

	/**
	 * The database where the glossary-data is kept.
	 *
	 * @since 1.0.0
	 * @var Glossary_DB
	 */
	private Glossary_DB $db;

	/**
	 * The Constructor of the admin page
	 *
	 * Constructs the admin page by initializing the controllers and the database.
	 *
	 * @since 1.0.0
	 * @param Glossary_DB $db @see $db class variable.
	 */
	public function __construct( Glossary_DB $db ) {
		$this->db    = $db;
		$this->table = new Admin_Page_Table_Controller( $this->db );
		$this->crud  = new Admin_Page_CRUD_Controller( $this->db );
	}

	/**
	 * Register admin page
	 *
	 * Registers the admin page within the WordPress backend.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action(
			'admin_menu',
			array( $this, 'register_menu_page' ),
		);
	}

	/**
	 * Register menu page
	 *
	 * Registers the menu page within the WordPress backend.
	 *
	 * @since 1.0.0
	 */
	public function register_menu_page() {
		add_menu_page(
			__( 'Glossary', 'arteeo-glossary' ),
			__( 'Glossary', 'arteeo-glossary' ),
			'manage_options',
			self::PAGE_ID,
			array( $this, 'run' ),
			'dashicons-book-alt',
			null,
		);
	}

	/**
	 * Render the glossary admin page
	 *
	 * Function that is called from the admin_menu hook which will then render
	 * the glossary-admin-page by delegating to the controllers.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
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
