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

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/block.php';

/**
 * Admin Page
 */
require_once plugin_dir_path( __FILE__ ) . 'src/admin-page.php';