<?php
/**
 * Admin Page CRUD controller
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../class-admin-page.php';
require_once __DIR__ . '/../views/class-admin-page-entry-form.php';
require_once __DIR__ . '/../views/class-admin-page-delete-form.php';
require_once __DIR__ . '/../../models/class-glossary-entry.php';
require_once __DIR__ . '/../../helper/class-helpers.php';

/**
 * Handles all CRUD operations made on entries.
 *
 * Contains the logic to render the different forms and process the form data.
 *
 * @since 1.0.0
 */
class Admin_Page_CRUD_Controller {
	/**
	 * The db to be used for the entries.
	 *
	 * @since 1.0.0
	 * @var Glossary_DB
	 */
	private Glossary_DB $db;

	/**
	 * The Constructor of the entry
	 *
	 * Links the database with the entry.
	 *
	 * @since 1.0.0
	 * @param Glossary_DB $db @see $db class variable.
	 */
	public function __construct( Glossary_DB $db ) {
		$this->db = $db;
	}

	/**
	 * Handle entry crud
	 *
	 * Used for CRUD operation on glossary entries
	 *
	 * @param string $action the action which has to be done.
	 */
	public function run( $action ) {
		if ( isset( $_POST['action'], $_POST['term'], $_POST['description'], $_POST['locale'], $_POST['_wpnonce'] ) ) {
			$action = sanitize_text_field( $_POST['action'] );
			$nonce  = wp_verify_nonce( $_POST['_wpnonce'], $action );

			if ( false === $nonce ) {
				Admin_Page::redirect_and_show_message( 'error', __( 'Form has expired.', 'arteeo-glossary' ) );
			}

			$entry              = new Glossary_Entry( $this->db );
			$entry->id          = null;
			$entry->term        = sanitize_text_field( $_POST['term'] );
			$entry->description = sanitize_textarea_field( $_POST['description'] );
			$entry->locale      = sanitize_text_field( $_POST['locale'] );

			$error = $this->check_entry_for_errors( $entry );

			$entry->letter = substr( $entry->term, 0, 1 );
			if ( ! ctype_alpha( $entry->letter ) ) {
				$entry->letter = '#';
			} else {
				$entry->letter = strtoupper( $entry->letter );
			}

			if ( isset( $_POST['id'] ) && Admin_Page::EDIT === $action ) {
				$entry->id = intval( $this->get_entry_or_redirect( $_POST['id'] )->id );
			}

			if ( null === $error ) {
				$this->save_entry_and_redirect( $entry, ( Admin_Page::ADD === $action ) );
			} else {
				$form = new Admin_Page_Entry_Form( $action, $entry, $error );
				$form->render();
			}
		} else {
			if ( isset( $_GET['id'] ) ) {
				$this->action_switcher( $action, $_GET['id'] );
			} else {
				$this->action_switcher( $action );
			}
		}
	}

	/**
	 * Call forms
	 *
	 * Calls the forms depending on the action.
	 *
	 * @param string $action the action which has to be done.
	 * @param ?int   $id id of the entry to change. Set to null for new entry.
	 */
	private function action_switcher( string $action, ?int $id = null ) {
		switch ( $action ) {
			case Admin_Page::ADD:
				$form = new Admin_Page_Entry_Form( $action, new Glossary_Entry( $this->db ) );
				$form->render();
				break;
			case Admin_Page::EDIT:
				$entry = $this->get_entry_or_redirect( $id );

				$form = new Admin_Page_Entry_Form( $action, $entry );
				$form->render();
				break;
			case Admin_Page::DELETE:
				$entry        = $this->get_entry_or_redirect( $id );
				$deletion_url = Helpers::generate_url(
					array(
						'action' => 'force-delete',
						'id'     => $entry->id,
					)
				);
				$form         = new Admin_Page_Delete_Form( $entry->term, $deletion_url );
				$form->render();
				break;
			case Admin_Page::FORCE_DELETE:
				$entry = $this->get_entry_or_redirect( $id );
				$this->delete_entry_and_redirect( $id );
				break;
		}
	}

	/**
	 * Save entry
	 *
	 * Saves the entry. After processing the function redirects to the admin page.
	 *
	 * @param Glossary_Entry $entry the entry to be saved.
	 * @param bool           $new if it is a new entry to be saved. Default false.
	 */
	private function save_entry_and_redirect( Glossary_Entry $entry, bool $new = false ) {
		$db_result = $entry->save();

		if ( false === $db_result ) {
			Admin_Page::redirect_and_show_message( 'error', __( 'Database error.', 'arteeo-glossary' ) );
		} elseif ( 0 === $db_result ) {
				Admin_Page::redirect_and_show_message(
					'success',
					__( 'No changes have occured.', 'arteeo-glossary' ),
				);
		} else {
			if ( ! $new ) {
				Admin_Page::redirect_and_show_message(
					'success',
					__( 'Entry has been adjusted.', 'arteeo-glossary' )
				);
			} else {
				Admin_Page::redirect_and_show_message(
					'success',
					__( 'Entry has been created.', 'arteeo-glossary' )
				);
			}
		}
	}

	/**
	 * Delete the entry and redirect
	 *
	 * Deletes the entry. After processing the function redirects to the admin page.
	 *
	 * @param Glossary_Entry $entry the id of the entry to be deleted.
	 */
	private function delete_entry_and_redirect( $entry ) {
		$result = $entry->delete();

		if ( false === $result ) {
			Admin_Page::redirect_and_show_message( 'error', __( 'Database error.', 'arteeo-glossary' ) );
		} else {
			Admin_Page::redirect_and_show_message( 'success', __( 'Entry has been deleted.', 'arteeo-glossary' ) );
		}
	}

	/**
	 * Validate entry
	 *
	 * Used for validating the entry received from the entry form
	 *
	 * @param object $entry The entry which was submitted by the form.
	 *
	 * @return string the error message if an error was found else returns null
	 */
	private function check_entry_for_errors( $entry ) {
		$languages = Helpers::get_locales();

		if ( '' === $entry->term ) {
			return sprintf(
				/* translators: %s is replaced with the fieldname*/
				__( 'Field "%s" has to be filled in.', 'arteeo-glossary' ),
				__( 'Term', 'arteeo-glossary' ),
			);
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
			$this->get_entry_or_redirect( $entry->id );
		}
	}

	/**
	 * Get entry or redirect
	 *
	 * Tries to get the entry with the provided id or redirects to the admin page if no single entry could be found.
	 *
	 * @param mixed $id the id of the entry which should be found.
	 *
	 * @return Glossary_Entry the entry object if one was found.
	 */
	private function get_entry_or_redirect( $id ) {
		if ( ! is_numeric( $id ) ) {
			Admin_Page::redirect_and_show_message( 'error', __( 'Entry id not valid.', 'arteeo-glossary' ) );
		}

		$id = intval( $id );

		$entry = $this->db->get_entry_by_id( $id );
		if ( null === $entry ) {
			Admin_Page::redirect_and_show_message( 'error', __( 'Entry could not be found.', 'arteeo-glossary' ) );
		}

		return $entry;
	}
}
