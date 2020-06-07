<?php
/**
 * Admin Page
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'glossary_settings_page' );

function glossary_settings_page() {
	//add_options_page( 'Settings API Page', 'Settings API Page', 'manage_options', 'settings-api-page', 'settings_api_page' );
	add_menu_page( 'Glossary', 'Glossary', 'manage_options', 'glossary_entries_page', 'settings-api-page', 'dashicons-book-alt', null );
	//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
}

register_activation_hook( __FILE__, 'create_glossary_table' );

function create_glossary_table() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . "glossary";

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		letter char NOT NULL,
		name tinytext NOT NULL,
		description text NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( "glossary_db_version", "1.0" );
}