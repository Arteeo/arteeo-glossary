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

require_once __DIR__ . '/../models/class-glossary-entry.php';
require_once __DIR__ . '/../helper/helpers.php';

/**
 * Handles the admin page entry form
 *
 * Contains the preparation and rendering of the entry form.
 *
 * @since 1.0.0
 */
class Admin_Page_Entry_Form {
	const ADD  = 'add';
	const EDIT = 'edit';

	/**
	 * The action to be performed with this form can be 'Add' or 'Edit'.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $action;

	/**
	 * The entry to adjust with this form.
	 *
	 * @since 1.0.0
	 * @var Glossary_Entry
	 */
	private Glossary_Entry $entry;

	/**
	 * The error message to be shown to the user. Default is null.
	 *
	 * @since 1.0.0
	 * @var ?string
	 */
	private ?string $error_message;

	/**
	 * The Construction of the form .
	 *
	 * Constructs the form which can then later be rendered.
	 *
	 * @since 1.0.0
	 * @param string          $action        @see $action class variable.
	 * @param ?Glossary_Entry $entry         The entry to change. @see Glossary_Entry.
	 * @param ?string         $error_message @see $error_message class variable.
	 */
	public function __construct( string $action, ?Glossary_Entry $entry, ?string $error_message = null ) {
		$this->action        = $action;
		$this->entry         = $entry;
		$this->error_message = $error_message;

		// ToDo: Check for errors:
		// Add and id? Edit and no id? Action not known?
		// I think class should be called correctly.
	}


	/**
	 * Renders the form.
	 *
	 * Renders the form with the parameters defined in construction.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		echo '<div class="wrap">';
		$this->render_error_message();
		$this->render_title();
		$this->render_form();
		echo '</div>';
	}

	/**
	 * Renders the error message.
	 *
	 * Renders the error message if it is not null.
	 *
	 * @since 1.0.0
	 */
	private function render_error_message() {
		if ( null !== $this->error_message ) {
			echo '' .
				'	<div id="message" class="error">' .
				'		<p>' . esc_html( $this->error_message ) . '</p>' .
				'	</div>';
		}
	}

	/**
	 * Renders the title
	 *
	 * Renders the title depending on the action.
	 *
	 * @since 1.0.0
	 */
	private function render_title() {
		switch ( $this->action ) {
			case self::ADD:
				echo '' .
					'	<h1 class="add-entry">' . esc_html( __( 'Add glossary entry', 'arteeo-glossary' ) ) . '</h1>' .
					'	<p>' . esc_html( __( 'Add a new entry to the glossary.', 'arteeo-glossary' ) ) . '</p>';
				break;
			case self::EDIT:
				echo '' .
					'	<h1 class="edit-entry">' .
							esc_html( __( 'Edit glossary entry', 'arteeo-glossary' ) ) .
					'	</h1>' .
					'	<p>' . esc_html( __( 'Adjust the entry from the glossary.', 'arteeo-glossary' ) ) . '</p>';
				break;
			default:
				// ToDo: Show error?
				echo 'error';
				break;
		}
	}

	/**
	 * Renders the form
	 *
	 * Renders the form containing the entry.
	 *
	 * @since 1.0.0
	 */
	private function render_form() {
		echo '	<form id="entry_form" class="validate" method="post" name="entry_form" novalidate="novalidate">';
		$this->render_hidden_fields();
		echo '' .
			'		<table class="form-table" role="presentation">' .
			'			<tbody>' .
			'				<tr class="form-field form-required">' .
			'					<th scope="row">' .
			'						<label for="glossary_term">' .
			'							' . esc_html( __( 'Term', 'arteeo-glossary' ) ) .
			'							<span class="description">' .
			'								(' . esc_html( __( 'required', 'arteeo-glossary' ) ) . ')' .
			'							</span>' .
			'						</label>' .
			'					</th>' .
			'					<td>' .
			'						<input id="glossary_term" name="term" type="text" ' .
											'value="' . esc_html( $this->entry->term ) . '" aria-required="true"' .
			'								autocapitalize="none" autocorrect="on" maxlenght="60">' .
			'					</td>' .
			'				</tr>' .
			'				<tr class="form-field form-required">' .
			'					<th scope="row">' .
			'						<label for="glossary_description">' .
			'							' . esc_html( __( 'Description', 'arteeo-glossary' ) ) .
			'							<span class="description">' .
			'								(' . esc_html( __( 'required', 'arteeo-glossary' ) ) . ')' .
			'							</span>' .
			'						</label>' .
			'					</th>' .
			'					<td>' .
			'						<textarea id="glossary_description" name="description" aria-required="true" ' .
											'autocapitalize="none" autocorrect="on">' .
											esc_html( $this->entry->description ) .
									'</textarea>' .
			'					</td>' .
			'				</tr>' .
			'				<tr class="form-field form-required">' .
			'					<th scope="row">' .
			'						<label for="glossary_locale">' .
			'							' . esc_html( __( 'Language', 'arteeo-glossary' ) ) .
			'							<span class="locale">' .
			'								(' . esc_html( __( 'required', 'arteeo-glossary' ) ) . ')' .
			'							</span>' .
			'						</label>' .
			'					</th>' .
			'					<td>';
		Helpers::render_dropdown_languages( 'locale', Helpers::get_locales(), $this->entry->locale );
		echo '' .
			'					</td>' .
			'				</tr>' .
			'			</tbody>' .
			'		</table>';
		$this->render_submit_button();
		echo '	</form>';
	}

	/**
	 * Renders the hidden fields
	 *
	 * Renders the hidden fields inside the form depending on the action.
	 *
	 * @since 1.0.0
	 */
	private function render_hidden_fields() {
		if ( self::EDIT === $this->action ) {
			echo '		<input name="id" type="hidden" value="' . esc_html( $this->entry->id ) . '">';
		}

		echo '		<input name="action" type="hidden" value="' . esc_html( $this->action ) . '">';
		wp_nonce_field( $this->action );
	}

	/**
	 * Renders the submit button
	 *
	 * Renders the submit button based on the action.
	 *
	 * @since 1.0.0
	 */
	private function render_submit_button() {
		echo '		<p class="submit">';
		switch ( $this->action ) {
			case self::ADD:
				echo '' .
					'		<input id="entry_submit" class="button button-primary" type="submit" name="entry_form" ' .
							'value="' . esc_html( __( 'Create new entry', 'arteeo-glossary' ) ) . '">';
				break;
			case self::EDIT:
				echo '' .
					'		<input id="entry_submit" class="button button-primary" type="submit" name="entry_form" ' .
							'value="' . esc_html( __( 'Save entry', 'arteeo-glossary' ) ) . '">';
				break;
			default:
				// ToDo: Show error?
				echo 'error';
				break;
		}
		echo '		</p>';
	}
}
