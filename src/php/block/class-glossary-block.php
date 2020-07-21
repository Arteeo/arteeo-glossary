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

/**
 * Glossary Block
 *
 * Registers and controlls the Gutenberg-Block of the Plugin.
 *
 * @since 1.0.0
 */
class Glossary_Block {
	const BLOCK_NAME = 'arteeo/glossary-block';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	/**
	 * Initialise the block and add required actions
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->register();
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_frontend_assets' ) );
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
			'arteeo-glossary-style-css',
			plugins_url( 'css/block/arteeo-glossary.css', plugin_dir_path( __DIR__ ) ),
			is_admin() ? array( 'wp-editor' ) : null,
			filemtime( __DIR__ . '/../../css/block/arteeo-glossary.css' )
		);

		// Register block editor script for backend.
		wp_register_script(
			'arteeo-glossary-block-backend-js',
			plugins_url( 'js/block/arteeo-glossary-block-backend.js', plugin_dir_path( __DIR__ ) ),
			array( 'wp-api-fetch', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			filemtime( __DIR__ . '/../../js/block/arteeo-glossary-block-backend.js' ),
			true // Enqueue the script in the footer.
		);

		// Register script for frontend use.
		wp_register_script(
			'arteeo-glossary-block-frontend-js',
			plugins_url( 'js/block/arteeo-glossary-block-frontend.js', plugin_dir_path( __DIR__ ) ),
			array( 'wp-api-fetch', 'wp-polyfill' ),
			filemtime( __DIR__ . '/../../js/block/arteeo-glossary-block-frontend.js' ),
			true // Enqueue the script in the footer.
		);

		// Register script for frontend resizing.
		wp_register_script(
			'arteeo-glossary-block-resize-js',
			plugins_url( 'js/block/arteeo-glossary-block-resize.js', plugin_dir_path( __DIR__ ) ),
			array( 'wp-polyfill' ),
			filemtime( __DIR__ . '/../../js/block/arteeo-glossary-block-resize.js' ),
			true // Enqueue the script in the footer.
		);

		// WP Localized globals. Use dynamic PHP stuff in JavaScript via `arteeoGlossaryGlobal` object.
		wp_localize_script(
			'arteeo-glossary-block-backend-js',
			'arteeoGlossaryGlobal', // Array containing dynamic data for a JS Global.
			$this->get_script_globals()
		);

		// WP Localized globals. Use dynamic PHP stuff in JavaScript via `arteeoGlossaryGlobal` object.
		wp_localize_script(
			'arteeo-glossary-block-frontend-js',
			'arteeoGlossaryGlobal',
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
		 * @since 1.0.0
		 */
		register_block_type(
			self::BLOCK_NAME,
			array(
				// Enqueue style for both frontend & backend.
				'style'         => 'arteeo-glossary-style-css',
				// Enqueue react-wrapper in the editor only.
				'editor_script' => 'arteeo-glossary-block-backend-js',
			)
		);
	}

	/**
	 * Activate frontend script
	 *
	 * Callback which enqueues the glossary-frontend-assets if the block is present.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_frontend_assets() {
		if ( has_block( self::BLOCK_NAME ) ) {
			wp_enqueue_script( 'arteeo-glossary-block-frontend-js' );
			wp_enqueue_script( 'arteeo-glossary-block-resize-js' );
		}
	}

	/**
	 * Get Globals
	 *
	 * Returns the needed translations and php-values to be used by the js-components.
	 *
	 * @since 1.0.0
	 * @return array Array with the needed values.
	 */
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
		);
	}
}
