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
	private string $table_name;

	/**
	 * Constructor.
	 *
	 * Defines the table name for the database.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'arteeo_glossary';
	}

	/**
	 * Register actions
	 *
	 * Registers the action which will be executed on plugin activation and deinstallation as well as plugin upgrades.
	 *
	 * @since 1.0.0
	 */
	public function register_actions() {
		add_action( 'plugins_loaded', array( $this, 'check_for_glossary_table_update' ) );
		add_action( 'arteeo_glossary_activate', array( $this, 'prepare_glossary_table' ) );
		add_action( 'arteeo_glossary_uninstall', array( $this, 'drop_glossary_table' ) );
	}

	/**
	 * Setup the glossary database.
	 *
	 * This function checks if the database exists if this is not the case it creates the database and fills it with
	 * sample data.
	 *
	 * @since 1.0.0
	 * @global object $wpdb The WordPress database instance.
	 */
	public function prepare_glossary_table() {
		global $wpdb;

		if ( $this->table_name !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $this->table_name ) ) ) {
			self::create_glossary_table();
			self::fill_glossary_table();
		}
	}

	/**
	 * Create table
	 *
	 * Creates the glossary database table. And saves the current glossary version inside a WordPress option.
	 *
	 * @since 1.0.0
	 * @global object $wpdb The WordPress database instance.
	 */
	private function create_glossary_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $this->table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			letter char NOT NULL,
			term tinytext NOT NULL,
			description text NOT NULL,
			locale text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( 'glossary_version', Glossary::VERSION );
	}

	/**
	 * Fill table
	 *
	 * Fills the created table with some sample data.
	 *
	 * @since 1.0.0
	 * @global object $wpdb The WordPress database instance.
	 */
	private function fill_glossary_table() {
		global $wpdb;

		$locale = get_locale();

		$term        = __( 'Dog', 'arteeo-glossary' );
		$description = __( 'Congratulations, you just completed the installation!', 'arteeo-glossary' );

		$wpdb->insert(
			$this->table_name,
			array(
				'letter'      => strtoupper( substr( $term, 0, 1 ) ),
				'term'        => $term,
				'description' => $description,
				'locale'      => $locale,
			)
		);

		$term        = __( 'Sheep', 'arteeo-glossary' );
		$description = 'Lorem ipsum dolor sit amet consectetur adipiscing elit nisl ultrices, dapibus ut fermentum' .
						'luctus tellus magnis fames nunc curae curabitur, velit cubilia cum scelerisque phasellus' .
						'fusce leo eget. Dis quam fusce vivamus congue felis sociosqu taciti, eget libero dignissim' .
						'condimentum purus a, sed metus semper auctor torquent imperdiet. Sociis suscipit sociosqu' .
						'turpis eros feugiat aliquam commodo vel et, non dictum malesuada nam ut netus hendrerit' .
						'varius, natoque tincidunt magna litora placerat eleifend vehicula tristique.';

		$wpdb->insert(
			$this->table_name,
			array(
				'letter'      => strtoupper( substr( $term, 0, 1 ) ),
				'term'        => $term,
				'description' => $description,
				'locale'      => $locale,
			)
		);

		$term        = __( 'Serpent', 'arteeo-glossary' );
		$description = __( 'Congratulations, you just completed the installation!', 'arteeo-glossary' );

		$wpdb->insert(
			$this->table_name,
			array(
				'letter'      => strtoupper( substr( $term, 0, 1 ) ),
				'term'        => $term,
				'description' => $description,
				'locale'      => $locale,
			)
		);

		$term        = '.htaccess';
		$description = __( 'Congratulations, you just completed the installation!', 'arteeo-glossary' );

		$wpdb->insert(
			$this->table_name,
			array(
				'letter'      => '#',
				'term'        => $term,
				'description' => $description,
				'locale'      => $locale,
			)
		);
	}

	/**
	 * Uninstall table
	 *
	 * Drops the glossary table and removes the version option as to completely remove the plugin from the system.
	 *
	 * @since 1.0.0
	 * @global object $wpdb The WordPress database instance.
	 */
	public function drop_glossary_table() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . $this->table_name );
		delete_option( 'glossary_version' );
	}

	/**
	 * Check for update
	 *
	 * On plugin update this function checks if the glossary version has changed and updates the table if necessary.
	 *
	 * @since 1.0.0
	 * @global object $wpdb The WordPress database instance.
	 */
	public function check_for_glossary_table_update() {
		if ( Glossary::VERSION !== get_site_option( 'glossary_version' ) ) {
			function (){};
		}
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
			$this->table_name,
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
			$this->table_name,
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
		$result = $wpdb->delete( $this->table_name, array( 'id' => $entry->id ) );

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
				'SELECT * FROM ' . $this->table_name . ' WHERE' .
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
					'SELECT * FROM ' . $this->table_name .
						' WHERE locale=%s AND letter=%s ORDER BY term ' . $filter->sorting,
					$filter->locale,
					$filter->letter
				)
			);
		} elseif ( isset( $filter->locale ) ) {
			$entries = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . $this->table_name . ' WHERE locale=%s ORDER BY term ' . $filter->sorting,
					$filter->locale
				)
			);
		} elseif ( isset( $filter->letter ) ) {
			$entries = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . $this->table_name . ' WHERE letter=%s ORDER BY term ' . $filter->sorting,
					$filter->letter
				)
			);
		} else {
			$entries = $wpdb->get_results(
				'SELECT * FROM ' . $this->table_name . ' ORDER BY term ' . $filter->sorting
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
					'SELECT letter, count(letter) AS count FROM ' . $this->table_name .
						' WHERE locale=%s GROUP BY letter ORDER BY letter ASC',
					$filter->locale
				)
			);
		} else {
			$letters = $wpdb->get_results(
				'SELECT letter, count(letter) AS count FROM ' . $this->table_name .
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
