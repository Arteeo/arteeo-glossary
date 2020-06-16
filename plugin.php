<?php
/**
 * Plugin Name: Glossary
 * Plugin URI: https://example.com
 * Description: A simple beautiful glossary
 * Author: vec7or
 * Author URI: https://github.com/Vec7or
 * Version: 1.0.0
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: glossary
 * Domain Path: /languages
 * 
 * @package CGB
 */
global $glossary_version;
$glossary_version = '1.0';
global $wpdb;
global $glossary_table_name;
$glossary_table_name = $wpdb->prefix . "glossary";

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function load_glossary_textdomain() {
	load_plugin_textdomain( 'glossary', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_glossary_textdomain' );

// Include helpers
require_once plugin_dir_path( __FILE__ ) . 'php/helper/helpers.php';

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'php/block/block.php';

/**
 * Create DB
 */
require_once plugin_dir_path( __FILE__ ) . 'php/db/db.php';

register_activation_hook( __FILE__, 'prepare_glossary_table' );
register_uninstall_hook( __FILE__, 'drop_glossary_table' );
add_action( 'plugins_loaded', 'check_for_glossary_table_update' );


/**
 * Admin Page
 */
require_once plugin_dir_path( __FILE__ ) . 'php/admin-page/admin-page.php';
