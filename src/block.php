<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function generate_letters($attributes, $letters, $currentLetter) {
	$result = '';
	$hashtag = false;
	foreach ($letters as $letterObj ){
		if ($letterObj->letter != '#') {
			if($letterObj->letter == $currentLetter) {
				$result = $result . '<a class="active" style="color: '.$attributes['secondaryColor'].'; border-color: '.$attributes['secondaryColor'].';" href="'.generate_url(array('letter'=>$letterObj->letter)).'">'.$letterObj->letter.'</a>';
			} else {
				$result = $result . '<a href="'.generate_url(array('letter'=>$letterObj->letter)).'">'.$letterObj->letter.'</a>';
			}
		} else {
			$hashtag = true;
		}
	}

	if ($hashtag) {
		if('#' == $currentLetter) {
			$result = $result . '<a class="active" style="color: '.$attributes['secondaryColor'].'; border-color: '.$attributes['secondaryColor'].';" href="'.generate_url(array('letter'=>'hashtag')).'">#</a>';
		} else {
			$result = $result . '<a href="'.generate_url(array('letter'=>'hashtag')).'">#</a>';
		}
	}

	return $result;
}

function generate_entries($attributes, $entries, $currentLetter) {
	$result = '';
	foreach ($entries as $entry ){
		$result = $result . 
			'<article class="entry">' .
			'	<div class="name">' .
			'		<h2 style="color: '.$attributes['secondaryColor'].';">'.$entry->term.'</h2>' .
			' </div>' .
			'	<div class="description">' .
			'		<p>' .
			'			' . nl2br($entry->description) .
			'		</p>' .			
			' </div>' .
			'</article>';
	}

	return $result;
}

function glossary_cgb_block_render($attributes) {
	global $wpdb;
	global $glossary_table_name;

	$handled = false;
	$letters = $wpdb->get_results( "SELECT letter FROM $glossary_table_name GROUP BY letter ORDER BY letter ASC");
	$currentLetter;
	$entries;
	if(isset($_GET['letter'])) {
		if(strlen($_GET['letter']) == 1) {
			$currentLetter = strtoupper(sanitize_text_field( $_GET['letter']));
			$entries = $wpdb->get_results( "SELECT term, description FROM $glossary_table_name WHERE letter = '$currentLetter' ORDER BY term ASC");
			if($wpdb->num_rows > 0) {
				$handled = true;
			}
		} else if ($_GET['letter'] == 'hashtag') {
			$currentLetter = '#';
			$entries = $wpdb->get_results( "SELECT term, description FROM $glossary_table_name WHERE letter = '$currentLetter' ORDER BY term ASC");
			if($wpdb->num_rows > 0) {
				$handled = true;
			}
		}
	}

	if(!$handled) {
		if(count($letters) > 1) {
			if ($letters[0]->letter == '#') {
				$currentLetter = $letters[1]->letter;
			} else {
				$currentLetter = $letters[0]->letter;
			}
		} else {
			$currentLetter = $letters[0]->letter;
		}
		$entries = $wpdb->get_results( "SELECT term, description FROM $glossary_table_name WHERE letter = '$currentLetter' ORDER BY term ASC");
	}

	return '
		<div class="wp-block-cgb-block-glossary">
			<div class="wrapper"> 
				<section class="sidebar">
					<div class="sidebar-header" style="background-color:'.$attributes['primaryColor'].';">
					<div class="letter">
					<h2>'.$currentLetter.'</h2>
					</div>
					</div>
					<div class="sidebar-content">
						<h3 style="color:'.$attributes['secondaryColor'].';">Wählen Sie einen Buchstaben:</h3>
						<div class="letters">
							'.generate_letters($attributes, $letters, $currentLetter).'
						</div>
					</div>
				</section>
				<main class="content">
					'.generate_entries($attributes, $entries, $currentLetter).'
				</main>
			</div>
		</div>
	';
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function glossary_cgb_block_assets() { // phpcs:ignore
	global $wpdb;
	global $glossary_table_name;
	// Register block styles for both frontend + backend.
	wp_register_style(
		'glossary-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'glossary-cgb-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'glossary-cgb-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
	wp_localize_script(
		'glossary-cgb-block-js',
		'cgbGlobal', // Array containing dynamic data for a JS Global.
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			'entries' => $wpdb->get_results( "SELECT * FROM $glossary_table_name"),
			'letters' => $wpdb->get_results( "SELECT letter FROM $glossary_table_name GROUP BY letter ORDER BY letter ASC"),
			// Add more data here that you want to access from `cgbGlobal` object.
		]
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	register_block_type(
		'cgb/block-glossary', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style'         => 'glossary-cgb-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'glossary-cgb-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'glossary-cgb-block-editor-css',
			'attributes' => array(
				'primaryColor' => array(
					'type' => 'string',
					'default' => '#0065AE',
				),
				'secondaryColor' => array(
						'type' => 'string',
						'default' => '#82878c',
				),
			),
			'render_callback' => 'glossary_cgb_block_render',
		)
	);
}

// Hook: Block assets.
add_action( 'init', 'glossary_cgb_block_assets' );