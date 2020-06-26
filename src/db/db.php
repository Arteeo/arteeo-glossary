<?php
/**
 * Create DB for glossary
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
function prepare_glossary_table() {
	global $wpdb;
	global $glossary_table_name;
	$exists = $wpdb->get_results( "SHOW TABLES LIKE '$glossary_table_name'");
	// In case a upgrade is necessary this is the place to check since after the upgrade this function is called to.

	if($wpdb->num_rows == 0) {
		create_glossary_table();
		fill_glossary_table();
	}
}

function create_glossary_table() {
	global $wpdb;
	global $glossary_version;
	global $glossary_table_name;

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $glossary_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		letter char NOT NULL,
		term tinytext NOT NULL,
		description text NOT NULL,
		locale text NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( "glossary_version", $glossary_version );
}

function fill_glossary_table() {
	global $wpdb;
	global $glossary_table_name;

	$locale = get_locale();
	
	$term = __('Dog', 'glossary');
	$description = __('Congratulations, you just completed the installation!', 'glossary');
	
	$wpdb->insert( 
		$glossary_table_name, 
		array(
			'letter' => strtoupper(substr($term, 0, 1)),
			'term' => $term, 
			'description' => $description, 
			'locale' => $locale,
		) 
	);

	$term = __('Sheep', 'glossary');
	$description = 'Lorem ipsum dolor sit amet consectetur adipiscing elit nisl ultrices, dapibus ut fermentum luctus tellus magnis fames nunc curae curabitur, velit cubilia cum scelerisque phasellus fusce leo eget. Dis quam fusce vivamus congue felis sociosqu taciti, eget libero dignissim condimentum purus a, sed metus semper auctor torquent imperdiet. Sociis suscipit sociosqu turpis eros feugiat aliquam commodo vel et, non dictum malesuada nam ut netus hendrerit varius, natoque tincidunt magna litora placerat eleifend vehicula tristique.';
	
	$wpdb->insert( 
		$glossary_table_name, 
		array(
			'letter' => strtoupper(substr($term, 0, 1)),
			'term' => $term, 
			'description' => $description,
			'locale' => $locale,
		) 
	);

	$term = __('Serpent', 'glossary');;
	$description = __('Congratulations, you just completed the installation!', 'glossary');
	
	$wpdb->insert( 
		$glossary_table_name, 
		array(
			'letter' => strtoupper(substr($term, 0, 1)),
			'term' => $term, 
			'description' => $description, 
			'locale' => $locale,
		) 
	);

	$term = '.htaccess';
	$description = __('Congratulations, you just completed the installation!', 'glossary');

	$wpdb->insert(
		$glossary_table_name, 
		array(
			'letter' => '#',
			'term' => $term, 
			'description' => $description, 
			'locale' => $locale,
		) 
	);
}


function drop_glossary_table() {
	global $wpdb;
	global $glossary_version;
	$glossary_version = '';
	global $glossary_table_name;
	
    $sql = "DROP TABLE IF EXISTS $glossary_table_name";
	$wpdb->query($sql);
	
	$glossary_table_name = '';

	delete_option( "glossary_version", $glossary_version );
}

function check_for_glossary_table_update() {
	global $glossary_version;
	if ( get_site_option( 'glossary_version' ) != $glossary_version ) {
			create_glossary_table();
	}
}

function get_entry_by_id( $id ) {
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
		return $entries[0];
	}

	return null;
}

function delete_entry_by_id( $id ) {
	global $wpdb;
	global $glossary_table_name;

	$result = $wpdb->delete( $glossary_table_name, array( 'id' => $id ) );

	if ( 1 === $result ) {
		return true;
	}

	return false;
}

function update_entry( $entry ) {
	global $wpdb;
	global $glossary_table_name;

	$result = $wpdb->update(
		$glossary_table_name,
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

function insert_entry( $entry ) {
	global $wpdb;
	global $glossary_table_name;

	$result = $wpdb->insert(
		$glossary_table_name,
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
		)
	);

	return $result;
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
