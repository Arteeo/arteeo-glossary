<?php
/**
 * Migration to create glossary table
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../class-glossary-db.php';
require_once __DIR__ . '/../../models/interface-migration.php';

/**
 * Create glossary Table
 *
 * This migration creates the basic glossary table.
 *
 * @since 1.0.0
 */
class Create_Glossary_Table implements Migration {
	/**
	 * The timestamp of the migration
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private static int $timestamp = 1595398244;

	/**
	 * Get timestamp
	 *
	 * The timestamp is the identifier for a DB-Migration it defines in which order the migrations are done.
	 *
	 * @since 1.0.0
	 * @return int Returns the timestamp of the migration.
	 */
	public function get_timestamp() : int {
		return self::$timestamp;
	}

	/**
	 * Apply migration
	 *
	 * Applies the defined migration to the Database.
	 *
	 * @since 1.0.0
	 * @param Object $db The wpdb-instance the migration should be applied to.
	 * @throws \Exception If the table already exists.
	 */
	public function up( object $db ) {
		$table_name = Glossary_DB::$glossary_table_name;

		if ( $table_name !== $db->get_var( $db->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) ) {
			$charset_collate = $db->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				letter char NOT NULL,
				term tinytext NOT NULL,
				description text NOT NULL,
				locale text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		} else {
			throw new \Exception( 'Table:' . Glossary_DB::$glossary_table_name . ' already exists!' );
		}
	}

	/**
	 * Undo migration
	 *
	 * Reverts the defined migration inside the Database.
	 *
	 * @since 1.0.0
	 * @param Object $db The wpdb-instance the migration should be applied to.
	 */
	public function down( object $db ) {
		$db->query( 'DROP TABLE IF EXISTS ' . Glossary_DB::$glossary_table_name );
	}
}
