<?php
/**
 * Helpers
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Take current url and convert it by changing the provided get parameter
 *
 * @param string $parameters array of values
 *                           $key
 *                              The parameter which should be adjusted
 *                           $parameters[$key]
 *                              the value which should be set for the
 *                              parameter if set to string 'null' 
 * 															the parameter will be removed
 * @return string the resulting url after adjusting the parameters
 */
function generate_url( $parameters ) {
	global $glossary_page_id;

	$url = '?page=' . $glossary_page_id;

	foreach ( $_GET as $key => $value ) {
		if ( isset( $parameters[ $key ] ) ) {
			if ( 'null' !== $parameters[ $key ] ) {
				$url = $url . '&' . $key . '=' . $parameters[ $key ];
			}
			$parameters[ $key ] = null;
		} elseif ( 'page' !== $key && 'message' !== $key && 'message_type' !== $key ) {
			$url = $url . '&' . $key . '=' . $value;
		}
	}

	foreach ( $parameters as $key => $parameter ) {
		if ( null !== $parameter && 'null' !== $parameter ) {
			$url = $url . '&' . $key . '=' . $parameter;
		}
	}

	return esc_url( $url );
}

/**
 * Redirect to
 *
 * Redirect to the page the url is refering to.
 *
 * @param string $url the url to redirect to.
 */
function redirect_to( $url ) {
	echo '<meta http-equiv="refresh" content="0; URL=' . esc_url( $url ) . '">';
	exit;
}


function glossary_get_locales() {
	global $glossary_plugin_dir;

	$languages      = get_available_languages( $glossary_plugin_dir . 'languages' );
	$prefix         = 'glossary-';
	$language_count = count( $languages );

	for ( $i = 0; $i < $language_count; $i++ ) {
		$languages[ $i ] = substr( $languages[ $i ], strlen( $prefix ) );
	}

	array_push( $languages, 'en_US' );

	return $languages;
}