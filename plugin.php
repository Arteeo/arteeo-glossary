<?php
/**
 * Plugin Name: Glossary
 * Plugin URI: https://github.com/Vec7or/glossary
 * Description: A simple beautiful glossary
 * Author: vec7or
 * Author URI: https://github.com/Vec7or
 * Version: 1.0.0
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

function arteeo_glossary_activate() {
	do_action( 'arteeo_glossary_activate' );
}
register_activation_hook( __FILE__, 'arteeo\glossary\arteeo_glossary_activate' );

function arteeo_glossary_uninstall() {
	do_action( 'arteeo_glossary_uninstall' );
}
register_uninstall_hook( __FILE__, 'arteeo\glossary\arteeo_glossary_uninstall' );


// Globals.
global $arteo_glossary_root;
$arteo_glossary_root = __DIR__;

/**
 * The loader for the plugin
 */
class Main {
	private Glossary $glossary;
	private Glossary_Db $db;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Db has to be initialized here since it is used within hooks.
		$this->db = new Glossary_Db();
	}

	/**
	 * Run the plugin
	 *
	 * @since 1.0.0
	 */
	public function run() {
		/**
		 * Load Text Domain
		 */
		load_plugin_textdomain( 'arteeo-glossary', false, basename( dirname( __FILE__ ) ) . '/languages/' );

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
		 * Include helpers
		 */
		// require_once 'php/helper/helpers.php';

		/**
		 * Include api
		 */
		// require_once 'php/api/register.php';
		$this->glossary = new Glossary( $this->db );
	}
}

global $wpdb;
global $glossary_table_name;
$glossary_table_name = $wpdb->prefix . 'arteeo_glossary';

global $glossary_plugin_dir;
$glossary_plugin_dir = plugin_dir_path( __FILE__ );

$main = new Main();
$main->run();
