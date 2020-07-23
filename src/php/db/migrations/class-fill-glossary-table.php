<?php
/**
 * Migration to fill glossary table
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
 * Fill glossary Table
 *
 * This migration fills the glossary table with sample data.
 *
 * @since 1.0.0
 */
class Fill_Glossary_Table implements Migration {
	/**
	 * The timestamp of the migration
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private static int $timestamp = 1595400203;

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
		// English.
		$db->insert(
			Glossary_DB::$glossary_table_name,
			array(
				'letter'      => 'D',
				'term'        => 'Dog',
				'description' => 'The dog is a medium sized animal.',
				'locale'      => 'en_US',
			)
		);
		$db->insert(
			Glossary_DB::$glossary_table_name,
			array(
				'letter'      => 'S',
				'term'        => 'Serpent',
				'description' => 'The serpent is dangerous.',
				'locale'      => 'en_US',
			)
		);
		$db->insert(
			Glossary_DB::$glossary_table_name,
			array(
				'letter'      => 'S',
				'term'        => 'Sheep',
				'description' => 'Just a sheep.',
				'locale'      => 'en_US',
			)
		);
		$db->insert(
			Glossary_DB::$glossary_table_name,
			array(
				'letter'      => '#',
				'term'        => '@Special',
				'description' => 'Special characters are supported.',
				'locale'      => 'en_US',
			)
		);
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
		$db->query( 'DELETE FROM ' . Glossary_DB::$glossary_table_name );
	}
}
