<?php
/**
 * Create DB for glossary
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( "glossary_version", $glossary_version );
}

function fill_glossary_table() {
	global $wpdb;
	global $glossary_table_name;
	
	$term = 'Mr. WordPress';
	$description = 'Congratulations, you just completed the installation!';
	
	$wpdb->insert( 
		$glossary_table_name, 
		array(
			'letter' => 'M',
			'term' => $term, 
			'description' => $description, 
		) 
	);

	$term = 'Auxiliary';
	$description = 'Lorem ipsum dolor sit amet consectetur adipiscing elit nisl ultrices, dapibus ut fermentum luctus tellus magnis fames nunc curae curabitur, velit cubilia cum scelerisque phasellus fusce leo eget. Dis quam fusce vivamus congue felis sociosqu taciti, eget libero dignissim condimentum purus a, sed metus semper auctor torquent imperdiet. Sociis suscipit sociosqu turpis eros feugiat aliquam commodo vel et, non dictum malesuada nam ut netus hendrerit varius, natoque tincidunt magna litora placerat eleifend vehicula tristique.';
	
	$wpdb->insert( 
		$glossary_table_name, 
		array(
			'letter' => 'A',
			'term' => $term, 
			'description' => $description, 
		) 
	);

	$term = 'All';
	$description = 'Congratulations, you just completed the installation!';
	
	$wpdb->insert( 
		$glossary_table_name, 
		array(
			'letter' => 'A',
			'term' => $term, 
			'description' => $description, 
		) 
	);

	$term = 'Crucial';
	$description = 'Congratulations, you just completed the installation!';

	$wpdb->insert( 
		$glossary_table_name, 
		array(
			'letter' => 'C',
			'term' => $term, 
			'description' => $description, 
		) 
	);

	$term = '.htaccess';
	$description = 'Congratulations, you just completed the installation!';

	$wpdb->insert(
		$glossary_table_name, 
		array(
			'letter' => '#',
			'term' => $term, 
			'description' => $description, 
		) 
	);

	$term = '.gitignore';
	$description = 'Congratulations, you just completed the installation!';

	$wpdb->insert(
		$glossary_table_name, 
		array(
			'letter' => '#',
			'term' => $term, 
			'description' => $description, 
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