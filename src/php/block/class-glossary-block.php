<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Glossary_Block {

	const BLOCK_NAME = 'arteeo/glossary-block';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	public function init() {
		$this->register();
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_frontend_js' ) );
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
	private function register() {
		// Register block styles for both frontend + backend.
		wp_register_style(
			'arteeo-glossary-style-css', // Handle.
			plugins_url( 'css/block/block.css', plugin_dir_path( __DIR__ ) ), // Block style CSS.
			is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
			filemtime( __DIR__ . '/../../css/block/block.css' ) // Version: File modification time.
		);

		// Register block editor script for backend.
		wp_register_script(
			'arteeo-glossary-block-js', // Handle.
			plugins_url( 'js/block/block.js', plugin_dir_path( __DIR__ ) ), // Block.build.js: We register the block here. Built with Webpack.
			array( 'wp-api-fetch', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
			filemtime( __DIR__ . '/../../js/block/block.js' ), // Version: filemtime — Gets file modification time.
			true // Enqueue the script in the footer.
		);

		wp_register_script(
			'arteeo-glossary-frontend-js', // Handle.
			plugins_url( 'js/block/arteeo-glossary.js', plugin_dir_path( __DIR__ ) ), // Block.build.js: We register the block here. Built with Webpack.
			array( 'wp-api-fetch', 'wp-polyfill' ), // Dependencies, defined above.
			filemtime( __DIR__ . '/../../js/block/arteeo-glossary.js' ), // Version: filemtime — Gets file modification time.
			true // Enqueue the script in the footer.
		);

		// WP Localized globals. Use dynamic PHP stuff in JavaScript via `arteeoGlossaryGlobal` object.
		wp_localize_script(
			'arteeo-glossary-block-js',
			'arteeoGlossaryGlobal', // Array containing dynamic data for a JS Global.
			$this->get_script_globals()
		);

		wp_localize_script(
			'arteeo-glossary-frontend-js',
			'arteeoGlossaryGlobal', // Array containing dynamic data for a JS Global.
			$this->get_script_globals()
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
		 *
		 * @see generate_letters for explanation of attributes array.
		 */
		register_block_type(
			self::BLOCK_NAME,
			array(
				// Enqueue blocks.style.build.css on both frontend & backend.
				'style'           => 'arteeo-glossary-style-css',
				// 'script'          => 'arteeo-glossary-js',
				// Enqueue blocks.build.js in the editor only.
				'editor_script'   => 'arteeo-glossary-block-js',
			)
		);
	}

	public function enqueue_frontend_js() {
		if ( has_block( self::BLOCK_NAME ) ) {
			wp_enqueue_script( 'arteeo-glossary-frontend-js' );
		}
	}

	private function get_script_globals() : array {
		$letters_endpoint = get_rest_url( null, '/arteeo/glossary/v1/letters' );
		if ( strpos( $letters_endpoint, '?' ) !== false ) {
			$letters_endpoint = $letters_endpoint . '&locale=' . get_locale();
		} else {
			$letters_endpoint = $letters_endpoint . '?locale=' . get_locale();
		}

		$entries_endpoint = get_rest_url( null, '/arteeo/glossary/v1/entries' );
		if ( strpos( $entries_endpoint, '?' ) !== false ) {
			$entries_endpoint = $entries_endpoint . '&locale=' . get_locale();
		} else {
			$entries_endpoint = $entries_endpoint . '?locale=' . get_locale();
		}

		return array(
			'translations' => array(
				'Glossary'            => __( 'Glossary', 'arteeo-glossary' ),
				'glossary'            => __( 'glossary', 'arteeo-glossary' ),
				'glossaryDescription' => __( 'glossary', 'arteeo-glossary' ) . ' - ' .
					__( 'Glossary block for the Gutenberg editor.', 'arteeo-glossary' ),
				'primaryColor'        => __( 'Primary color', 'arteeo-glossary' ),
				'accentColor'         => __( 'Accent color', 'arteeo-glossary' ),
				'selectLetter'        => __( 'Select a letter:', 'arteeo-glossary' ),
				'noEntry'             => __(
					'Unfortunately no entries in your language could be found in this glossary.',
					'arteeo-glossary'
				),
				'apiError'            => __(
					'Sorry, we were unable to retrieve entries from the server.',
					'arteeo-glossary'
				),
			),
			'locale'       => get_locale(),
			'endpoints'    => array(
				'letters' => $letters_endpoint,
				'entries' => $entries_endpoint,
			),
			// Add more data here that you want to access from `arteeoGlossaryGlobal` object.
		);
	}
}
