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
require_once __DIR__ . '/../helper/class-helpers.php';
require_once __DIR__ . '/../models/class-filter.php';
require_once __DIR__ . '/../models/class-letter.php';

/**
 * API
 *
 * Registers and controlls api-endpoints for the glossary plugin.
 *
 * @since 1.0.0
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

		register_rest_route(
			$this->namespace,
			'/letters',
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_letters' ),
				'args'     => $this->get_letters_args(),
			)
		);
	}

	/**
	 * Entries endpoint
	 *
	 * Get the entries based on the filters defined in the request. Returns a json of all entries matching with the
	 * filter.
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request The request which was received by the api.
	 */
	public function get_entries( \WP_REST_Request $request ) {
		$filter = new Filter();
		if ( isset( $request['letter'] ) ) {
			$filter->letter = $request['letter'];
		}

		if ( isset( $request['locale'] ) ) {
			$filter->locale = $request['locale'];
		}

		$entries = $this->db->get_filtered_entries( $filter );
		$output  = array();
		foreach ( $entries as $entry ) {
			$simple_entry              = new \stdClass();
			$simple_entry->term        = $entry->term;
			$simple_entry->description = $entry->description;

			array_push( $output, $simple_entry );
		}
		wp_send_json( $output );
	}

	/**
	 * Letters endpoint
	 *
	 * Get the letters based on the filters defined in the request. Returns a json of all letters matching with the
	 * filter.
	 *
	 * @since 1.0.0
	 * @param \WP_REST_Request $request The request which was received by the api.
	 */
	public function get_letters( \WP_REST_Request $request ) {
		$filter = new Filter();

		if ( isset( $request['locale'] ) ) {
			$filter->locale = $request['locale'];
		}

		$letters = $this->db->get_filtered_letters( $filter );
		$output  = array();
		foreach ( $letters as $letter ) {
			$simple_letter = $letter->letter;

			array_push( $output, $simple_letter );
		}
		wp_send_json( $output );
	}

	/**
	 * Arguments for get_entries endpoint
	 *
	 * This function defines the supported arguments for the get_entries endpoint.
	 *
	 * @since 1.0.0
	 * @return array Returns the supported arguments.
	 */
	public function get_entries_args() : array {
		$args           = array();
		$args['letter'] = array(
			'description'       => esc_html__(
				'The letter parameter is used to filter the collection of entries by letter.',
				'arteeo-glossary'
			),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_letter_callback' ),
		);
		$args           = $this->add_locale_arg( $args );
		return $args;
	}

	/**
	 * Arguments for get_letters endpoint
	 *
	 * This function defines the supported arguments for the get_letters endpoint.
	 *
	 * @since 1.0.0
	 * @return array Returns the supported arguments.
	 */
	public function get_letters_args() : array {
		$args = array();
		$args = $this->add_locale_arg( $args );
		return $args;
	}

	/**
	 * Add locale argument
	 *
	 * Adds the locale argument to the input args array.
	 *
	 * @since 1.0.0
	 * @param array $args The args array that needs the locale argument.
	 * @return array Returns the args array with the argument added.
	 */
	private function add_locale_arg( array $args ) : array {
		$args['locale'] = array(
			'description'       => esc_html__(
				'The locale parameter is used to filter the collection by locale.',
				'arteeo-glossary'
			),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_locale_callback' ),
			'enum'              => Helpers::get_locales(),
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
				sprintf(
					/* translators: %s is replaced with the parameter name*/
					__( 'The %s parameter must be a string.', 'arteeo-glossary' ),
					$param
				),
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

	/**
	 * Validate the locale argument.
	 *
	 * @param  mixed           $value   Value of the 'locale' argument.
	 * @param  WP_REST_Request $request The current request object.
	 * @param  string          $param   Key of the parameter. In this case it is 'locale'.
	 * @return WP_Error|boolean
	 */
	public function validate_locale_callback( $value, $request, $param ) {
		if ( ! is_string( $value ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: %s is replaced with the parameter name*/
					__( 'The %s parameter must be a string.', 'arteeo-glossary' ),
					$param
				),
				array( 'status' => 400 )
			);
		}

		$attributes = $request->get_attributes();
		$args       = $attributes['args'][ $param ];

		if ( ! in_array( $value, $args['enum'], true ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: %1$s is replaced with the locale value and %2$s with the list of possible locales*/
					__( '%1$s is not supported, must be one of: [ %2$s ]' ),
					$value,
					implode( ', ', $args['enum'] ),
				),
				array( 'status' => 400 )
			);
		}
	}
}
