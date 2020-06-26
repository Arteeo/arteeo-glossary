<?php
/**
 * Glossary plugin controller
 *
 * The main controller for the glossary plugin.
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

require 'admin-page/admin-page.php';
require 'helper/helpers.php';
require 'block/block.php';
require 'models/class-glossary-entry.php';

/**
 * Glossary plugin controller
 *
 * The main controller for the glossary plugin.
 *
 * @var Glossary_Db $db hello.
 */
class Glossary {

	const VERSION = '1.0.0';

	/**
	 * Db instance for the glossary.
	 *
	 * @var Glossary_Db
	 */
	private Glossary_Db $db;

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
	 */
	public function __construct( Glossary_Db $db) {
		$this->db = $db;
		$this->admin_page = new Admin_Page();
		$this->admin_page->init();
		$this->block = new Glossary_Block();
		$this->block->init();
	}
}
