<?php
/**
 * Helpers
 *
 * @package arteeo\glossary;
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate url with get parameters.
 *
 * Take current url and adust it by changing the provided get parameters.
 * Usage: generate_url( array( 'action' => 'edit' ) )
 *
 * @param array $parameters {
 *     Array with the get-parameters which should be changed as keys and the desired values as values.
 *
 *     $parameters['action'] => 'edit' If defined sets the get-parameter 'action' to 'edit'.
 *     $paremeters['action'] => 'null' If set to 'null' the parameter will be removed from the url.
 * }
 *
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
	$prefix         = 'arteeo-glossary-';
	$language_count = count( $languages );

	for ( $i = 0; $i < $language_count; $i++ ) {
		$languages[ $i ] = substr( $languages[ $i ], strlen( $prefix ) );
	}

	array_push( $languages, 'en_US' );

	return $languages;
}

/**
 * Render a language dropdown
 *
 * Generates a dropdown based on the input parameters.
 *
 * @param string $name        The name and id of the geneareted select element.
 * @param array  $languages   An array of the language locales which should be geneared.
 * @param string $selected    The locale which should be selected.
 * @param bool   $include_all Boolean to signal if an 'all' option should be included. Default set to false.
 */
function glossary_dropdown_languages( $name, $languages, $selected, $include_all = false ) {
	echo '<select id="' . esc_html( $name ) . '" name="' . esc_html( $name ) . '">';

	if ( $include_all ) {
		echo '' .
		'	<option value=""' . ( ( '' === $selected ) ? 'selected >' : '>' ) .
				esc_html( __( 'All', 'arteeo-glossary' ) ) .
		'	</option>';
	}

	$sorted_languages = array();

	$language_count = count( $languages );
	for ( $i = 0; $i < $language_count; $i++ ) {
		$sorted_languages[ $languages[ $i ] ] = \Locale::getDisplayName( $languages[ $i ], get_user_locale() );
	}

	asort( $sorted_languages );

	foreach ( $sorted_languages as $locale => $language ) {
		echo '' .
			'	<option value="' . esc_html( $locale ) . '" ' .
					( ( $locale === $selected ) ? 'selected >' : '>' ) .
				esc_html( $language ) .
			'	</option>';
	}
	echo '</select>';
}