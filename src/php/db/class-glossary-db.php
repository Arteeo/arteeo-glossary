<?php
/**
 * DB for glossary
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../class-glossary.php';
require_once __DIR__ . '/../models/class-entry.php';
require_once __DIR__ . '/../models/class-entries.php';
require_once __DIR__ . '/../models/class-letters.php';
require_once __DIR__ . '/../models/class-migrations.php';

// Migrations.
require_once __DIR__ . '/migrations/class-create-glossary-table.php';
require_once __DIR__ . '/migrations/class-fill-glossary-table.php';

/**
 * Glossary Database
 *
 * Contains all code which interacts with the WordPress $wpdb. Defines and maintains the glossary database table.
 *
 * @since 1.0.0
 */
class Glossary_DB {
	/**
	 * The name of the database table.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static string $glossary_table_name = '';

	/**
	 * Static constructor
	 *
	 * Is called first and initialises static variables
	 *
	 * @since 1.0.0
	 */
	public static function constructor_static() {
		global $wpdb;
		self::$glossary_table_name = $wpdb->prefix . 'arteeo_glossary';
	}

	/**
	 * List of migrations to be done.
	 *
	 * @since 1.0.0
	 * @var Migrations
	 */
	private Migrations $migrations;

	/**
	 * Constructor.
	 *
	 * Defines the table name for the database.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->migrations = new Migrations();
		$this->migrations->add( new Create_Glossary_Table() );
		$this->migrations->add( new Fill_Glossary_Table() );
	}

	/**
	 * Register actions
	 *
	 * Registers the action which will be executed on plugin activation and deinstallation as well as plugin upgrades.
	 *
	 *   @since 1.0.0
	 */
	public function register_actions() {
		add_action( 'plugins_loaded', array( $this, 'do_migrations' ) );
		add_action( 'arteeo_glossary_uninstall', array( $this, 'rollback_migrations' ) );
	}

	/**
	 * Apply migrations
	 *
	 * Applies all migrations not yet written to the Database. Prepares the database for use with the plugin.
	 *
	 * @since 1.0.0
	 * @global object $wpdb The WordPress database instance.
	 */
	public function do_migrations() {
		global $wpdb;

		$current_migration = get_option( 'arteeo_glossary_current_migration' );

		if ( false === $current_migration ) {
			add_option( 'arteeo_glossary_current_migration', 0 );
			$current_migration = 0;
		}

		if ( $this->migrations->get_latest_timestamp() <= $current_migration ) {
			// No new migrations.
			return;
		}

		foreach ( $this->migrations as $migration ) {
			if ( $current_migration < $migration->get_timestamp() ) {
				$migration->up( $wpdb );
				$current_migration = $migration->get_timestamp();
			}
		}

		update_option( 'arteeo_glossary_current_migration', $current_migration );
	}

	/**
	 * Rollback migrations
	 *
	 * Does a rollback on all migrations effectively removing all elements of this plugin from within the Database.
	 *
	 * @since 1.0.0
	 * @global object $wpdb The WordPress database instance.
	 */
	public function rollback_migrations() {
		global $wpdb;

		$current_migration = get_option( 'arteeo_glossary_current_migration' );
		if ( false !== $current_migration && 0 < $current_migration ) {
			foreach ( $this->migrations->get_reverse_iterator() as $migration ) {
				if ( $current_migration >= $migration->get_timestamp() ) {
					$migration->down( $wpdb );
					$current_migration = $migration->get_timestamp();
				}
			}
		}
		delete_option( 'arteeo_glossary_current_migration' );
	}

	/**
	 * Insert entry into database
	 *
	 * Takes a new entry and inserts it into the database table.
	 *
	 * @since 1.0.0
	 * @global object $wpdb  The WordPress database instance.
	 * @param Entry $entry The entry to be inserted.
	 * @return int Returns the number of rows updated or -1 if an error occured.
	 */
	public function insert_entry( Entry $entry ) : int {
		global $wpdb;

		$result = $wpdb->insert(
			self::$glossary_table_name,
			array(
				'letter'      => $entry->letter,
				'term'        => $entry->term,
				'description' => $entry->description,
				'locale'      => $entry->locale,
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		if ( false === $result ) {
			return -1;
		}

		return $result;
	}

	/**
	 * Update entry from database
	 *
	 * Takes an existing entry and updates its values inside the database.
	 *
	 * @since 1.0.0
	 * @global object $wpdb  The WordPress database instance.
	 * @param Entry $entry The entry to be updated.
	 * @return int Returns the number of rows updated or -1 if an error occured.
	 */
	public function update_entry( Entry $entry ) : int {
		global $wpdb;

		$result = $wpdb->update(
			self::$glossary_table_name,
			array(
				'letter'      => $entry->letter,
				'term'        => $entry->term,
				'description' => $entry->description,
				'locale'      => $entry->locale,
			),
			array( 'id' => $entry->id ),
			array(
				'%s',
				'%s',
				'%s',
			),
			array( '%d' )
		);

		if ( false === $result ) {
			return -1;
		}

		return $result;
	}

	/**
	 * Delete entry from database
	 *
	 * Takes an existing entry and removes it from the database.
	 *
	 * @since 1.0.0
	 * @global object $wpdb  The WordPress database instance.
	 * @param Entry $entry The entry to be updated.
	 * @return int Returns the number of rows updated or -1 if an error occured.
	 */
	public function delete_entry( Entry $entry ) : int {
		global $wpdb;
		$result = $wpdb->delete( self::$glossary_table_name, array( 'id' => $entry->id ) );

		if ( false === $result ) {
			return -1;
		}

		return $result;
	}

	/**
	 * Get entry from database
	 *
	 * Takes an entry id and returns the entry if it exists
	 *
	 * @since 1.0.0
	 * @global object $wpdb  The WordPress database instance.
	 * @param int $id The id of the entry.
	 * @return ?Entry Returns entry or null if it couldn't be found.
	 */
	public function get_entry_by_id( int $id ) : ?Entry {
		global $wpdb;

		$entries = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . self::$glossary_table_name . ' WHERE' .
				' id = %d',
				$id
			)
		);

		if ( 1 === $wpdb->num_rows ) {
			$result = Entry::from_object( $entries[0], $this );

			return $result;
		}

		return null;
	}

	/**
	 * Get entries with filter
	 *
	 * Takes a filter object and returns all entries filtered by the parameters defined in that object.
	 *
	 * @since 1.0.0
	 * @global object $wpdb  The WordPress database instance.
	 * @param Filter $filter Contains the filters to be used.
	 * @return Entries Returns a entries object containing all entries matching the parameters.
	 */
	public function get_filtered_entries( Filter $filter ) : Entries {
		global $wpdb;

		$entries = array();

		if ( isset( $filter->locale ) && isset( $filter->letter ) ) {
			$entries = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . self::$glossary_table_name .
						' WHERE locale=%s AND letter=%s ORDER BY term ' . $filter->sorting,
					$filter->locale,
					$filter->letter
				)
			);
		} elseif ( isset( $filter->locale ) ) {
			$entries = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . self::$glossary_table_name . ' WHERE locale=%s ORDER BY term ' . $filter->sorting,
					$filter->locale
				)
			);
		} elseif ( isset( $filter->letter ) ) {
			$entries = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . self::$glossary_table_name . ' WHERE letter=%s ORDER BY term ' . $filter->sorting,
					$filter->letter
				)
			);
		} else {
			$entries = $wpdb->get_results(
				'SELECT * FROM ' . self::$glossary_table_name . ' ORDER BY term ' . $filter->sorting
			);
		}

		$result = new Entries();

		foreach ( $entries as $key => $entry ) {
			$result->add( Entry::from_object( $entry, $this ) );
		}

		return $result;
	}

	/**
	 * Get letters with filter
	 *
	 * Takes a filter object and returns all letters filtered by the parameters defined in that object.
	 *
	 * @since 1.0.0
	 * @global object $wpdb  The WordPress database instance.
	 * @param Filter $filter Contains the filters to be used.
	 * @return Letters Returns a letters object containing all letters matching the parameters.
	 */
	public function get_filtered_letters( Filter $filter ) : Letters {
		global $wpdb;

		$letters = array();

		if ( isset( $filter->locale ) ) {
			$letters = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT letter, count(letter) AS count FROM ' . self::$glossary_table_name .
						' WHERE locale=%s GROUP BY letter ORDER BY letter ASC',
					$filter->locale
				)
			);
		} else {
			$letters = $wpdb->get_results(
				'SELECT letter, count(letter) AS count FROM ' . self::$glossary_table_name .
						' GROUP BY letter ORDER BY letter ASC'
			);
		}

		$result = new Letters();
		foreach ( $letters as $key => $letter ) {
			$result->add( Letter::from_object( $letter ) );
		}

		return $result;
	}
}

Glossary_DB::constructor_static();
