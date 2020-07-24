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
 * Provides helper functions
 *
 * Provides multiple helper functions which are used throughout the plugin.
 *
 * @since 1.0.0
 */
class Helpers {
	/**
	 * Render a language dropdown
	 *
	 * Generates a dropdown based on the input parameters.
	 *
	 * @since 1.0.0
	 * @param string $name        The name and id of the geneareted select element.
	 * @param array  $languages   An array of the language locales which should be geneared.
	 * @param string $selected    The locale which should be selected.
	 * @param bool   $include_all Boolean to signal if an 'all' option should be included. Default set to false.
	 */
	public static function render_dropdown_languages( $name, $languages, $selected, $include_all = false ) {
		echo '<select id="' . esc_html( $name ) . '" name="' . esc_html( $name ) . '">';

		if ( $include_all ) {
			echo '' .
			'	<option value=""' . ( ( '' === $selected ) ? 'selected >' : '>' ) .
					esc_html__( 'All', 'glossary-by-arteeo' ) .
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

	/**
	 * Get all supported locales.
	 *
	 * Returns all locales for which a translation exists
	 *
	 * @since 1.0.0
	 * @return array Array with the supported locales as values.
	 */
	public static function get_locales() : array {
		$languages      = get_available_languages( __DIR__ . '/../../languages' );
		$prefix         = 'glossary-by-arteeo-';
		$language_count = count( $languages );

		for ( $i = 0; $i < $language_count; $i++ ) {
			$languages[ $i ] = substr( $languages[ $i ], strlen( $prefix ) );
		}
		array_push( $languages, 'en_US' );
		return $languages;
	}

	/**
	 * Convert locale to readable
	 *
	 * Takes a locale code and transforms it into a human readable string of the locale.
	 *
	 * @since 1.0.0
	 * @param string $locale The locale code to be converted
	 * @return string Returns the human readable locale string.
	 */
	public static function get_readable_locale( string $locale ) : string {
		return \Locale::getDisplayName( $locale, get_user_locale() );
	}

	/**
	 * Generate url with get parameters.
	 *
	 * Take current url as well as get parameters and adjust them by changing the provided get parameters.
	 * Usage: generate_url( array( 'action' => 'edit' ) )
	 *
	 * @since 1.0.0
	 * @param array $parameters {
	 *     Array with the get-parameters which should be changed as keys and the desired values as values.
	 *
	 *     $parameters['action'] => 'edit' If defined sets the get-parameter 'action' to 'edit'.
	 *     $paremeters['action'] => 'null' If set to 'null' the parameter will be removed from the url.
	 * }
	 *
	 * @return string the resulting url after adjusting the parameters.
	 */
	public static function generate_url( $parameters ) : string {
		$out_parameters  = array();
		$url             = '';
		$first_parameter = true;

		foreach ( $_GET as $key => $value ) {
			if ( isset( $parameters[ $key ] ) ) {
				if ( 'null' !== $parameters[ $key ] ) {
					$out_parameters[ $key ] = $parameters[ $key ];
				}
				$parameters[ $key ] = null;
			} elseif ( 'message' !== $key && 'message_type' !== $key ) {
				$out_parameters[ $key ] = $value;
			}
		}

		foreach ( $parameters as $key => $value ) {
			if ( null !== $value && 'null' !== $value ) {
				$out_parameters[ $key ] = $parameters[ $key ];
			}
		}

		foreach ( $out_parameters as $key => $value ) {
			if ( $first_parameter ) {
				$first_parameter = false;
				$url             = '?' . $key . '=' . $value;
			} else {
				$url = $url . '&' . $key . '=' . $value;
			}
		}

		return esc_url( $url );
	}

	/**
	 * Redirect to
	 *
	 * Redirect to the page the url is refering to.
	 *
	 * @since 1.0.0
	 * @param string $url the url to redirect to.
	 */
	public static function redirect_to( $url ) {
		echo '<meta http-equiv="refresh" content="0; URL=' . esc_url( $url ) . '">';
		exit;
	}
}
