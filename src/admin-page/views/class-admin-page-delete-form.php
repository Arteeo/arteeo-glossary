<?php
/**
 * Admin Page Entry Form
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the admin page delete form
 *
 * Contains the preparation and rendering of the delete form.
 *
 * @since 1.0.0
 */
class Admin_Page_Delete_Form {

	/**
	 * The id of the event to be deleted.
	 *
	 * @since 1.0.0
	 * @var ?int
	 */
	private ?int $id;

	/**
	 * The term of the event to be deleted.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $term;

	/**
	 * The url to redirect to if the deletion is approved.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $deletion_url;

	/**
	 * The Construction of the form .
	 *
	 * Constructs the form which can then later be rendered.
	 *
	 * @since 1.0.0
	 * @param string $term         @see $term class variable.
	 * @param string $deletion_url @see $deletion_url class variable.
	 */
	public function __construct( string $term, string $deletion_url ) {
		$this->term         = $term;
		$this->deletion_url = $deletion_url;
	}


	/**
	 * Renders the form.
	 *
	 * Renders the form with the parameters defined in construction.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		echo '' .
		'<div class="wrap">' .
		'	<h1 class="delete-entry">' . esc_html( __( 'Delete glossary entry', 'arteeo-glossary' ) ) . '</h1>' .
		'	<p>' .
		'		' .
		esc_html(
			/* translators: %s is replaced with the term of the entry */
			sprintf( __( 'Do you really want to delete glossary entry "%s"?', 'arteeo-glossary' ), $this->term )
		) .
		'	</p>' .
		'	<a id="entry_delete" class="button button-primary" href="' . esc_url( $this->deletion_url ) . '">' .
		'		' . esc_html( __( 'Delete', 'arteeo-glossary' ) ) .
		'	</a>' .
		'</div>';
	}
}
