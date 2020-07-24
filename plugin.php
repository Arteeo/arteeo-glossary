<?php
/**
 * Plugin Name: Glossary by Arteeo
 * Plugin URI: https://github.com/Arteeo/arteeo-glossary/
 * Description: Glossary block for the Gutenberg editor.
 * Version: 1.0.0
 * Author: Arteeo
 * Author URI: https://github.com/Arteeo
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: arteeo-glossary
 * Domain Path: /languages
 * Requires PHP: 7.4
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'php/class-glossary.php';
require_once 'php/db/class-glossary-db.php';

/**
 * Trigger uninstall
 *
 * On plugin deinstallation this function is called which does then trigger a custom action so that the plugin can be
 * correctly removed.
 *
 * @since 1.0.0
 */
function arteeo_glossary_uninstall() {
	do_action( 'arteeo_glossary_uninstall' );
}
register_uninstall_hook( __FILE__, 'arteeo\glossary\arteeo_glossary_uninstall' );

/**
 * The loader for the plugin
 *
 * @since 1.0.0
 */
class Main {
	/**
	 * The glossary.
	 *
	 * @var Glossary
	 */
	private Glossary $glossary;

	/**
	 * Db instance for the glossary.
	 *
	 * @var Glossary_DB
	 */
	private Glossary_DB $db;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Db has to be initialized here since it is used within hooks.
		$this->db = new Glossary_DB();
	}

	/**
	 * Run the plugin
	 *
	 * @since 1.0.0
	 */
	public function run() {
		/**
		 * Actions and Hooks
		 */
		$this->db->register_actions();
		add_action( 'init', array( $this, 'includes' ) );
	}

	/**
	 * Include other classes
	 *
	 * Includes the other components of the plugin
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		/**
		 * Load Text Domain
		 */
		load_plugin_textdomain( 'arteeo-glossary', false, basename( dirname( __FILE__ ) ) . '/languages/' );

		$this->glossary = new Glossary( $this->db );
	}
}

$main = new Main();
$main->run();
