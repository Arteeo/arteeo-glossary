<?php
/**
 * Endpoints
 *
 * The endpoints of the glossary plugin
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

/**
 * Get Entry
 *
 * Returns the json data of one entry based on the id .
 *
 * @param WP_REST_Request $request The WordPress rest request object.
 */
function endpoint_get_entry( WP_REST_Request $request ) {
	$entry = get_entry_by_id( $request['id'] );

	if ( is_null( $entry ) ) {
		return new WP_Error( 'no_entry', 'Invalid author', array( 'status' => 404 ) );
	}

	return entry;
}
