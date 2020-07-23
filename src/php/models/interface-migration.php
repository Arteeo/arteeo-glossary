<?php
/**
 * Abstract class for DB-migrations
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DB Migration
 *
 * Provides the interface with needed functions for DB-Migrations
 *
 * @since 1.0.0
 */
interface Migration {
	/**
	 * Get timestamp
	 *
	 * The timestamp is the identifier for a DB-Migration it defines in which order the migrations are done.
	 *
	 * @since 1.0.0
	 * @return int Returns the timestamp of the migration.
	 */
	public function get_timestamp() : int;

	/**
	 * Apply migration
	 *
	 * Applies the defined migration to the Database.
	 *
	 * @since 1.0.0
	 * @param Object $db The wpdb-instance the migration should be applied to.
	 */
	public function up( object $db );

	/**
	 * Undo migration
	 *
	 * Reverts the defined migration inside the Database.
	 *
	 * @since 1.0.0
	 * @param Object $db The wpdb-instance the migration should be applied to.
	 */
	public function down( object $db );
}