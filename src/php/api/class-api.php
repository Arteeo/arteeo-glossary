<?php
/**
 * API controller
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../db/class-glossary-db.php';
require_once __DIR__ . '/../models/class-filter.php';
require_once __DIR__ . '/../models/class-letter.php';

/**
 * API
 *
 * Registers and controlls api-endpoints for the glossary plugin.
 *
 * @package arteeo\glossary
 */
class API {
	/**
	 * The version of the api.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $version;

	/**
	 * The namespace of the api.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $namespace;

	/**
	 * The db from which to get the data.
	 *
	 * @since 1.0.0
	 * @var Glossary_DB
	 */
	private Glossary_DB $db;

	/**
	 * Constructor.
	 *
	 * Initializes the version and namespace of the api.
	 *
	 * @since 1.0.0
	 * @param Glossary_DB $db @see $db class variable.
	 */
	public function __construct( Glossary_DB $db ) {
		$this->version   = '1';
		$this->namespace = 'arteeo/glossary/v' . $this->version;
		$this->db        = $db;
	}

	/**
	 * Register actions
	 *
	 * Registers action to be done on api initialisation.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Register endpoints
	 *
	 * Registers all API-Endpoints with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function register_endpoints() {
		register_rest_route(
			$this->namespace,
			'/entries',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_entries' ),
				'args'     => $this->get_entries_args(),
			)
		);
	}

	/**
	 * Register endpoints
	 *
	 * Registers all API-Endpoints with WordPress.
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request The request which was received by the api.
	 */
	public function get_entries( \WP_REST_Request $request ) {
		$filter = new Filter();
		if ( isset( $request['letter'] ) ) {
			$filter->letter = $request['letter'];
		}
		$entries = $this->db->get_filtered_entries( $filter )->get_array();
		wp_send_json( $entries );
	}

	public function get_entries_args() : array {
		$args           = array();
		$args['letter'] = array(
			'description'       => esc_html__(
				'The letter parameter is used to filter the collection of entries by letter',
				'arteeo-glossary'
			),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_letter_callback' ),
		);
		return $args;
	}

	/**
	 * Validate the letter argument for the get_entries-Endpoint
	 *
	 * @param  mixed           $value   Value of the 'letter' argument.
	 * @param  WP_REST_Request $request The current request object.
	 * @param  string          $param   Key of the parameter. In this case it is 'filter'.
	 * @return WP_Error|boolean
	 */
	public function validate_letter_callback( $value, $request, $param ) {
		if ( ! is_string( $value ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				esc_html__( 'The letter argument must be a string.', 'arteeo-glossary' ),
				array( 'status' => 400 )
			);
		}

		try {
			$letter = new Letter( $value, 1 );
		} catch ( \Exception $e ) {
			return new \WP_Error(
				'rest_invalid_param',
				esc_html__( 'The letter argument must be A-Z or #.', 'arteeo-glossary' ),
				array( 'status' => 400 )
			);
		}
	}
}
