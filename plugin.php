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

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/block.php';


register_activation_hook( __FILE__, 'create_glossary_table' );
register_activation_hook( __FILE__, 'fill_glossary_table' );
register_uninstall_hook( __FILE__, 'drop_glossary_table' );
/**
 * Create DB
 */
require_once plugin_dir_path( __FILE__ ) . 'src/db.php';

/**
 * Admin Page
 */
require_once plugin_dir_path( __FILE__ ) . 'src/admin-page/admin-page.php';