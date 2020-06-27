<?php
/**
 * Admin Page CRUD
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'class-admin-page-entry-form.php';
require_once __DIR__ . '/../models/class-glossary-entry.php';

/**
 * Get entry or redirect
 *
 * Tries to get the entry with the provided id or redirects to the overview page if no single entry could be found.
 *
 * @param mixed $id the id of the entry which should be found.
 *
 * @return Glossary_Entry the entry object if one was found.
 */
function glossary_get_entry_or_redirect( $id ) {
	if ( ! is_numeric( $id ) ) {
		glossary_show_message_on_overview( 'error', __( 'Entry id not valid.', 'arteeo-glossary' ) );
	}

	$id = intval( $id );

	$entry = get_entry_by_id( $id );
	if ( null === $entry ) {
		glossary_show_message_on_overview( 'error', __( 'Entry could not be found.', 'arteeo-glossary' ) );
	}

	return $entry;
}

/**
 * Delete the entry and redirect
 *
 * Deletes the entry with the provided id. After processing the funtcion redirects to the overview page.
 *
 * @param integer $id the id of the entry to be deleted.
 */
function glossary_delete_entry_and_redirect( $id ) {
	// Check if id exists.
	glossary_get_entry_or_redirect( $id );

	$result = delete_entry_by_id( $id );

	if ( false === $result ) {
		glossary_show_message_on_overview( 'error', __( 'Database error.', 'arteeo-glossary' ) );
	} else {
		glossary_show_message_on_overview( 'success', __( 'Entry has been deleted.', 'arteeo-glossary' ) );
	}
}

/**
 * Show message on overview
 *
 * Redirects to the overview page and shows the provided message.
 *
 * @see redirect_to
 * @see generate_url
 *
 * @param string $type the type of the message to be shown.
 * @param string $message the message to be shown.
 */
function glossary_show_message_on_overview( $type, $message ) {
	redirect_to(
		generate_url(
			array(
				'action'       => 'null',
				'id'           => 'null',
				'message_type' => $type,
				'message'      => $message,
			)
		)
	);
}

/**
 * Validate entry form
 *
 * Used for validating the entry form
 *
 * @param object $entry The entry which was submitted by the form.
 *
 * @return string the error message if an error was found else returns null
 */
function glossary_entry_form_check_errors( $entry ) {
	$languages = glossary_get_locales();

	if ( '' === $entry->term ) {
		/* translators: %s is replaced with the fieldname*/
		return sprintf( __( 'Field "%s" has to be filled in.', 'arteeo-glossary' ), __( 'Term', 'arteeo-glossary' ) );
	}
	if ( '' === $entry->description ) {
		return sprintf(
			/* translators: %s is replaced with the fieldname*/
			__( 'Field "%s" has to be filled in.', 'arteeo-glossary' ),
			__( 'Description', 'arteeo-glossary' )
		);
	}

	if ( false === array_search( $entry->locale, $languages, true ) ) {
		return sprintf( __( 'Selected language not supported.', 'arteeo-glossary' ) );
	}

	if ( null !== $entry->id ) {
		glossary_get_entry_or_redirect( $entry->id );
	}
}

/**
 * Handle entry crud
 *
 * Used for CRUD operation on glossary entries
 *
 * @param string  $action the action which has to be done.
 * @param integer $id id of the entry to change. Set to null for new entry.
 */
function glossary_entry_crud( $action, $id ) {
	if ( isset( $_POST['action'] ) && isset( $_POST['term'] ) && isset( $_POST['description'] )
			&& isset( $_POST['locale'] ) && isset( $_POST['_wpnonce'] ) ) {
		$nonce = false;

		switch ( $action ) {
			case 'add':
				$nonce = wp_verify_nonce( $_POST['_wpnonce'], 'add' );
				break;
			case 'edit':
				$nonce = wp_verify_nonce( $_POST['_wpnonce'], 'edit' );
				break;
		}

		if ( false === $nonce ) {
			glossary_show_message_on_overview( 'error', __( 'Form has expired.', 'arteeo-glossary' ) );
		}

		$entry              = new Glossary_Entry();
		$entry->id          = null;
		$entry->term        = sanitize_text_field( $_POST['term'] );
		$entry->description = sanitize_textarea_field( $_POST['description'] );
		$entry->locale      = sanitize_text_field( $_POST['locale'] );

		$error  = glossary_entry_form_check_errors( $entry );
		$action = sanitize_text_field( $_POST['action'] );

		$entry->letter = substr( $entry->term, 0, 1 );
		if ( ! ctype_alpha( $entry->letter ) ) {
			$entry->letter = '#';
		} else {
			$entry->letter = strtoupper( $entry->letter );
		}

		if ( isset( $_POST['id'] ) && 'edit' === $action ) {
			$entry->id = intval( glossary_get_entry_or_redirect( $_POST['id'] )->id );
		}

		if ( null === $error ) {
			glossary_write_to_db( $action, $entry );
		} else {
			$form = new Admin_Page_Entry_Form( $action, $entry, $error );
			$form->render();
		}
	} else {
		glossary_action_switcher( $action, $id );
	}
}

/**
 * Write entry to db
 *
 * Writes the entry into the db or generates error if not possible. After processing the function redirects to the
 * overview page.
 *
 * @param string $action the action which has to be done.
 * @param object $entry {
 *     The entry which should be written. The object has the following attributes.
 *
 *     @type integer id          The id of the entry. Null if new entry
 *     @type char    letter      The first letter of the term from the entry.
 *     @type string  term        The term of the entry.
 *     @type string  description The description of the entry.
 *     @type string  locale      The locale of the entry.
 * }
 */
function glossary_write_to_db( $action, $entry ) {
	switch ( $action ) {
		case 'add':
			$result = insert_entry( $entry );

			if ( false === $result ) {
				glossary_show_message_on_overview( 'error', __( 'Database error.', 'arteeo-glossary' ) );
			} else {
				glossary_show_message_on_overview( 'success', __( 'Entry has been created.', 'arteeo-glossary' ) );
			}
			break;
		case 'edit':
			$result = update_entry( $entry );

			if ( false === $result ) {
				glossary_show_message_on_overview( 'error', __( 'Database error.', 'arteeo-glossary' ) );
			} else {
				if ( 0 === $result ) {
					glossary_show_message_on_overview(
						'success',
						__( 'No changes have occured.', 'arteeo-glossary' ),
					);
				} else {
					glossary_show_message_on_overview(
						'success',
						__( 'Entry has been adjusted.', 'arteeo-glossary' )
					);
				}
			}
			break;
	}
}

/**
 * Call forms
 *
 * Calls the forms depending on the action.
 *
 * @param string  $action the action which has to be done.
 * @param integer $id id of the entry to change. Set to null for new entry.
 */
function glossary_action_switcher( $action, $id ) {
	switch ( $action ) {
		case 'add':
			$form = new Admin_Page_Entry_Form( $action, new Glossary_Entry() );
			$form->render();
			break;
		case 'edit':
			$entry = glossary_get_entry_or_redirect( $id );

			$form = new Admin_Page_Entry_Form( $action, $entry );
			$form->render();
			break;
		case 'delete':
			$entry = glossary_get_entry_or_redirect( $id );
			glossary_delete_form( $id, $entry->term );
			break;
		case 'force-delete':
			$entry = glossary_get_entry_or_redirect( $id );
			glossary_delete_entry_and_redirect( $id );
			break;
	}
}

/**
 * Show delete form
 *
 * Shows the form for deleting an entry.
 *
 * @param integer $id the id of the entry to be deleted.
 * @param string  $term the term of the entry to be deleted.
 */
function glossary_delete_form( $id, $term ) {
	?>
	<div class="wrap">
		<h1 class="delete-entry"><?php esc_html_e( 'Delete glossary entry', 'arteeo-glossary' ); ?></h1>
		<p>
		<?php
		/* translators: %s is replaced with the term of the entry */
		echo esc_html( sprintf( __( 'Do you really want to delete glossary entry "%s"?', 'arteeo-glossary' ), $term ) );
		?>
		</p>
		<a id="entry_delete" class="button button-primary"
				href="
				<?php
				echo esc_html(
					generate_url(
						array(
							'action' => 'force-delete',
							'id'     => $id,
						)
					)
				);
				?>
		">
			<?php esc_html_e( 'Delete', 'arteeo-glossary' ); ?>
		</a>
	</div>
	<?php
}
