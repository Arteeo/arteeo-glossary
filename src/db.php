<?php
/**
 * Create DB for glossary
 */

global $glossary_db_version;
$glossary_db_version = '1.0';

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function create_glossary_table() {
	global $wpdb;
	global $glossary_db_version;
	
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

	add_option( "glossary_db_version", $glossary_db_version );
}

function drop_glossary_table() {
	global $wpdb;
	global $glossary_db_version;
	$glossary_db_version = '';
	
	$table_name = $wpdb->prefix . "glossary";

    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);

	delete_option( "glossary_db_version", $glossary_db_version );
}