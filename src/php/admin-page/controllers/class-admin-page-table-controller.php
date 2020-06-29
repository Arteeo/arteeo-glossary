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
require_once __DIR__ . '/../views/class-admin-page-table.php';
require_once __DIR__ . '/../../models/class-entry.php';
require_once __DIR__ . '/../../models/class-filter.php';
require_once __DIR__ . '/../../models/class-letters.php';
require_once __DIR__ . '/../../helper/class-helpers.php';

/**
 * Handles Admin-Page-Table
 *
 * Contains the logic to render and sort the admin table correctly based on the given parameters
 *
 * @since 1.0.0
 */
class Admin_Page_Table_Controller {
	/**
	 * The db to be used for the entries.
	 *
	 * @since 1.0.0
	 * @var Glossary_DB
	 */
	private Glossary_DB $db;

	/**
	 * The Constructor of the controller
	 *
	 * Links the controller to the database.
	 *
	 * @since 1.0.0
	 * @param Glossary_DB $db @see $db class variable.
	 */
	public function __construct( Glossary_DB $db ) {
		$this->db = $db;
	}

	public function run() {
		$filter = new Filter();

		$filter->sorting = 'ASC';
		if ( isset( $_GET['glossary_sort'], $_GET['order'] ) ) {
			if ( 'term' === $_GET['glossary_sort'] && 'desc' === $_GET['order'] ) {
				$filter->sorting = 'DESC';
			}
		}

		$languages = Helpers::get_locales();
		if ( isset( $_GET['language_filter'] ) && false !== array_search( $_GET['language_filter'], $languages, true ) ) {
			$filter->locale = sanitize_text_field( $_GET['language_filter'] );
		}

		$entries;
		if ( isset( $_GET['glossary_show'] ) ) {
			$filter->letter = sanitize_text_field( $_GET['glossary_show'] );
		}

		switch ( $filter->letter ) {
			case 'all':
				$filter->letter = null;
				break;
			case ( ( 1 === strlen( $filter->letter) )  && ( ctype_alpha(  $filter->letter ) ) ):
				$filter->letter = strtoupper( $filter->letter );
				break;
			case 'hashtag':
				$filter->letter = '#';
				break;
			default:
				Helpers::redirect_to( Helpers::generate_url( array( 'glossary_show' => 'all' ) ) );
				break;
		}

		$entries = $this->db->get_filtered_entries( $filter );
		$letters = $this->db->get_filtered_letters( $filter );

		$hashtag = null;
		foreach ( $letters as $key => $letter ) {
			if ( '#' === $letter->letter ) {
				$letters->unset( $key );
				$hashtag = $letter;
			}
		}

		if ( null !== $hashtag ) {
			$letters->add( $hashtag );
		}

		if ( 0 === $entries->count() && null !== $filter->letter ) {
			Helpers::redirect_to( Helpers::generate_url( array( 'glossary_show' => 'all' ) ) );
		}

		$message = null;
		if ( isset( $_GET['message'], $_GET['message_type']) ) {
			$message = new Message(
				sanitize_text_field( $_GET['message_type'] ),
				sanitize_text_field( $_GET['message'] ),
			);
		}

		$table = new Admin_Page_Table( $entries, $letters, $message, $filter );
		$table->render();
	}


	public static function get_letter_sort_url( ?Letter $letter ) : string {
		if ( null === $letter ) {
			return Helpers::generate_url( array( 'glossary_show' => 'all' ) );
		}
		if ( '#' === $letter->letter ) {
			return Helpers::generate_url( array( 'glossary_show' => 'hashtag' ) );
		}
		return Helpers::generate_url( array( 'glossary_show' => $letter->letter ) );
	}

	public static function get_entry_delete_url( Entry $entry ) : string {
		return Helpers::generate_url(
			array(
				'action' => 'delete',
				'id'     => $entry->id,
			)
		);
	}

	public static function get_entry_edit_url( Entry $entry ) : string {
		return Helpers::generate_url(
			array(
				'action' => 'edit',
				'id'     => $entry->id,
			)
		);
	}

	public static function get_readable_locale( string $locale ) : string {
		return \Locale::getDisplayName( $locale, get_user_locale() );
	}

	public static function generate_sort_url( string $sortable, string $sorting ) : string {
		return Helpers::generate_url(
			array(
				'glossary_sort' => $sortable,
				'order'         => $sorting,
			)
		);
	}
}
