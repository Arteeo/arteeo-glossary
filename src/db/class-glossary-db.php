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
require_once __DIR__ . '/../models/class-glossary-entry.php';

class Glossary_DB {
	private string $table_name;
	private object $wpdb;


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'arteeo_glossary';
	}

	public function register_actions() {
		add_action( 'plugins_loaded', array( $this, 'check_for_glossary_table_update' ) );
		add_action( 'arteeo_glossary_activate', array( $this, 'prepare_glossary_table' ) );
		add_action( 'arteeo_glossary_uninstall', array( $this, 'drop_glossary_table' ) );
	}

	/**
	 * Setup the glossary database.
	 *
	 * This function checks if the database exists if this is not the case it creates the database and fills it with some
	 * sample data.
	 *
	 * @global object $wpdb                The WordPress database instance.
	 * @global string $glossary_table_name The name of the glossary database table.
	 */
	public function prepare_glossary_table() {
		global $wpdb;
		global $glossary_table_name;

		if ( $this->table_name !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $this->table_name ) ) ) {
			self::create_glossary_table();
			self::fill_glossary_table();
		} else {
			// In case a upgrade is necessary this is the place to check since after the upgrade this 
			// function is called to.
		}
	}

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

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'glossary_version', Glossary::VERSION );
	}

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

	public function drop_glossary_table() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . $this->table_name );
		$glossary_table_name = '';
		delete_option( 'glossary_version' );
	}

	public function check_for_glossary_table_update() {
		if ( Glossary::VERSION !== get_site_option( 'glossary_version' ) ) {
			self::create_glossary_table();
		}
	}

	public function insert_entry( Glossary_Entry $entry ) {
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
		return $result;
	}

	public function update_entry( Glossary_Entry $entry ) {
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

		return $result;
	}

	public function delete_entry( Glossary_Entry $entry ) {
		global $wpdb;
		$result = $wpdb->delete( $this->table_name, array( 'id' => $entry->id ) );
		
		return $result;
	}

	public function get_entry_by_id( int $id ) {
		global $wpdb;
		global $glossary_table_name;
	
		$entries = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . $glossary_table_name . ' WHERE' .
				' id = %d',
				$id
			)
		);
		if ( 1 === $wpdb->num_rows ) {
			$entry = $entries[0];
	
			$result              = new Glossary_Entry( $this );
			$result->id          = $entry->id;
			$result->letter      = $entry->letter;
			$result->term        = $entry->term;
			$result->description = $entry->description;
			$result->locale      = $entry->locale;
	
			return $result;
		}
	
		return null;
	}
}

function get_entry_by_id( int $id ) {
	global $wpdb;
	global $glossary_table_name;

	$entries = $wpdb->get_results(
		$wpdb->prepare(
			'SELECT * FROM ' . $glossary_table_name . ' WHERE' .
			' id = %d',
			$id
		)
	);
	if ( 1 === $wpdb->num_rows ) {
		$entry = $entries[0];

		$result              = new Glossary_Entry( $this );
		$result->id          = $entry->id;
		$result->letter      = $entry->letter;
		$result->term        = $entry->term;
		$result->description = $entry->description;
		$result->locale      = $entry->locale;

		return $result;
	}

	return null;
}

function get_filtered_entries( $filters, $sorting ) {
	global $wpdb;
	global $glossary_table_name;

	$entries = array();

	if ( isset( $filters['locale'] ) && isset( $filters['letter'] ) ) {
		$entries = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . $glossary_table_name . ' WHERE locale=%s AND letter=%s ORDER BY term ' . $sorting,
				$filters['locale'],
				$filters['letter']
			)
		);
	} elseif ( isset( $filters['locale'] ) ) {
		$entries = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . $glossary_table_name . ' WHERE locale=%s ORDER BY term ' . $sorting,
				$filters['locale']
			)
		);
	} elseif ( isset( $filters['letter'] ) ) {
		$entries = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . $glossary_table_name . ' WHERE letter=%s ORDER BY term ' . $sorting,
				$filters['letter']
			)
		);
	} else {
		$entries = $wpdb->get_results(
			'SELECT * FROM ' . $glossary_table_name . ' ORDER BY term ' . $sorting
		);
	}

	return $entries;
}

function get_filtered_letters( $filters ) {
	global $wpdb;
	global $glossary_table_name;

	$letters = array();

	if ( isset( $filters['locale'] ) ) {
		$letters = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT letter, count(letter) AS count FROM ' . $glossary_table_name .
					' WHERE locale=%s GROUP BY letter ORDER BY letter ASC',
				$filters['locale']
			)
		);
	} else {
		$letters = $wpdb->get_results(
			'SELECT letter, count(letter) AS count FROM ' . $glossary_table_name .
					' GROUP BY letter ORDER BY letter ASC'
		);
	}

	return $letters;
}
