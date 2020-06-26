<?php
/**
 * Api register
 *
 * Registers the api endpoints
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'glossary/v1',
			'/entry/(?P<id>\d+)',
			array(
				'methods'  => 'GET',
				'callback' => 'endpoint_get_entry',
				'args'     => array(
					'id' => array(
						'validate_callback' => function( $param, $request, $key ) {
							return is_numeric( $param );
						},
					),
				),
			)
		);
	}
);

/**
 * Include endpoints
 */
require_once 'endpoints.php';
