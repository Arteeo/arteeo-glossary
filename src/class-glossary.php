<?php
/**
 * Glossary plugin controller
 *
 * The main controller for the glossary plugin.
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

require_once 'admin-page/class-admin-page.php';
require_once 'block/block.php';
require_once 'models/class-entry.php';

/**
 * Glossary plugin controller
 *
 * The main controller for the glossary plugin.
 */
class Glossary {

	const VERSION = '1.0.0';

	/**
	 * Db instance for the glossary.
	 *
	 * @var Glossary_DB
	 */
	private Glossary_DB $db;

	/**
	 * Backend instance for the glossary.
	 *
	 * @var Admin_Page
	 */
	private Admin_Page $admin_page;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Glossary_DB $db The db from which to get the entries.
	 */
	public function __construct( Glossary_DB $db ) {
		$this->db         = $db;
		$this->admin_page = new Admin_Page( $this->db );
		$this->block      = new Glossary_Block();

		$this->admin_page->init();
		$this->block->init();
	}
}
