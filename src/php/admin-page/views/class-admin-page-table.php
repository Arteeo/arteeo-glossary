<?php
/**
 * Admin Page Table
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../class-glossary.php';
require_once __DIR__ . '/../../helper/class-helpers.php';

require_once __DIR__ . '/../../models/class-message.php';
require_once __DIR__ . '/../../models/class-entries.php';
require_once __DIR__ . '/../../models/class-letters.php';
require_once __DIR__ . '/../../models/class-filter.php';


/**
 * Handles the admin page table
 *
 * Contains the rendering of the overview table.
 *
 * @since 1.0.0
 */
class Admin_Page_Table {
	/**
	 * The entries to be rendered.
	 *
	 * @since 1.0.0
	 * @var Entries
	 */
	private Entries $entries;

	/**
	 * The letters to be rendered.
	 *
	 * @since 1.0.0
	 * @var Letters
	 */
	private Letters $letters;

	/**
	 * The message to be shown. If set to null no message will be shown.
	 *
	 * @since 1.0.0
	 * @var ?Message
	 */
	private ?Message $message;

	/**
	 * Defines the filters which are currently active on the table.
	 *
	 * @since 1.0.0
	 * @var Filter
	 */
	private Filter $filter;

	/**
	 * The Constructor of the table
	 *
	 * Constructs the table which can then later be rendered.
	 *
	 * @since 1.0.0
	 * @param Entries  $entries @see $entries class variable.
	 * @param Letters  $letters @see $letters class variable.
	 * @param ?Message $message @see $message class variable.
	 * @param Filter   $filter  @see $filter class variable.
	 */
	public function __construct( Entries $entries, Letters $letters, ?Message $message, Filter $filter ) {
		$this->entries = $entries;
		$this->letters = $letters;
		$this->message = $message;
		$this->filter  = $filter;
	}

	/**
	 * Render the table
	 *
	 * Renders the table based on the parameters defined in the constructor.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		echo '<div class="wrap">';
		$this->render_header();
		$this->render_message();
		$this->render_letters();
		$this->render_language_filter_form();
		$this->render_table();
		echo '</div>';
	}

	/**
	 * Render the header
	 *
	 * Renders the header of the table.
	 *
	 * @since 1.0.0
	 */
	private function render_header() {
		echo '' .
			'	<h1 class="wp-heading-inline">' . esc_html__( 'Glossary', 'glossary-by-arteeo' ) . '</h1>' .
			'	<span>v' . esc_html( Glossary::VERSION ) . '</span>' .
			'	<a class="page-title-action aria-button-if-js" role="button" aria-expanded="false" ' .
						'href="' . esc_url( Helpers::generate_url( array( 'action' => 'add' ) ) ) . '"> ' .
			'		' . esc_html__( 'Add entry', 'glossary-by-arteeo' ) .
			'	</a>' .
			'	<hr class="wp-header-end">';
	}

	/**
	 * Render the message
	 *
	 * Renders the error or success message if one is defined.
	 *
	 * @since 1.0.0
	 */
	private function render_message() {
		if ( null !== $this->message ) {
			if ( Message::SUCCESS === $this->message->type ) {
				echo '' .
					'	<div id="message" class="updated notice is-dismissible">' .
					'		<p>' . esc_html( $this->message->content ) . '</p>' .
					'		<button class="notice-dismiss" type="button">' .
					'			<span class="screen-reader-text">' .
					'				' . esc_html__( 'Hide this message', 'glossary-by-arteeo' ) .
					'			</span>' .
					'		</button>' .
					'	</div>';
			} elseif ( Message::ERROR === $this->message->type ) {
				echo '' .
					'	<div id="message" class="error">' .
					'		<p>' . esc_html( $this->message->content ) . '</p>' .
					'	</div>';
			}
		}
	}

	/**
	 * Render the letters
	 *
	 * Renders the letters which are available for selecting.
	 *
	 * @since 1.0.0
	 */
	private function render_letters() {
		echo '	<ul class="subsubsub">';
		if ( 0 < $this->letters->count() ) {
			echo '' .
				'		<li class="all">' .
				'			<a class="' . ( ( null === $this->filter->letter ) ? 'current ' : '' ) . '" ' .
								'href="' . esc_url( self::generate_letter_sort_url( null ) ) . '">' .
								esc_html__( 'All', 'glossary-by-arteeo' ) .
				'				<span class="count">(' . esc_html( $this->letters->count() ) . ')</span>' .
				'			</a>' .
				'			|' .
				'		</li>';
		}

		foreach ( $this->letters as $key => $letter ) {
			echo '' .
			'		<li class="' . esc_html( $letter->letter ) . '">' .
			'			<a class="' . ( ( $letter->letter === $this->filter->letter ) ? 'current ' : '' ) . '" ' .
			'					href="' . esc_url( self::generate_letter_sort_url( $letter ) ) .
								'">' . esc_html( $letter->letter ) .
			'				<span class="count">(' . esc_html( $letter->count ) . ')</span>' .
			'			</a>' .
			'			' . ( ( $this->letters->is_last( $key ) ) ? '' : '|' ) .
			'		</li>';
		}
		echo '	</ul>';
	}

	/**
	 * Render language picker
	 *
	 * Renders the form for the language picker component. To not lose any get-parameters this function puts all get-
	 * parameters defined into hidden fields.
	 *
	 * @since 1.0.0
	 */
	private function render_language_filter_form() {
		echo '' .
			'	<div class="tablenav top">' .
			'		<div class="alignleft actions">' .
			'			<form method="get">' .
			'				<label class="screen-reader-text" for="language_filter">' .
			'					' . esc_html__( 'Filter by language', 'glossary-by-arteeo' ) .
			'				</label>';
		foreach ( self::get_hidden_parameters() as $name => $value ) {
			echo '' .
				'<input type="hidden" name="' . esc_html( $name ) . '" value="' . esc_html( $value ) . '">';
		}

		$languages = Helpers::get_locales();
		Helpers::render_dropdown_languages( 'language_filter', $languages, $this->filter->locale, true );
		echo '' .
			'				<input id="post-query-submit" class="button" type="submit" ' .
								'value="' . esc_html__( 'Filter by language', 'glossary-by-arteeo' ) . '">' .
			'			</form>' .
			'		</div>' .
			'	</div>';
	}

	/**
	 * Render the table
	 *
	 * Renders the main table.
	 *
	 * @since 1.0.0
	 */
	private function render_table() {
		echo '' .
			'	<h2 class="screen-reader-text">' . esc_html__( 'Glossary entries', 'glossary-by-arteeo' ) . '</h2>' .
			'	<table class="wp-list-table widefat fixed striped">' .
			'		<thead>' .
			'			<th id="term" class="manage-column column-term column-primary sorted ' .
								esc_html( strtolower( $this->filter->sorting ) ) . '" scope="col">' .
			'				<a href="' .
									esc_url(
										self::generate_sort_url(
											'term',
											( ( 'DESC' === $this->filter->sorting ) ? 'asc' : 'desc' ),
										)
									) .
			'					">' .
			'					<span>' . esc_html__( 'Term', 'glossary-by-arteeo' ) . '</span>' .
			'					<span class="sorting-indicator"></span>' .
			'				</a>' .
			'			</th>' .
			'			<th id="description" class="manage-column column-description" scope="col">' .
			'				' . esc_html__( 'Description', 'glossary-by-arteeo' ) .
			'			</th>' .
			'			<th id="locale" class="manage-column column-locale" scope="col">' .
			'				' . esc_html__( 'Language', 'glossary-by-arteeo' ) .
			'			</th>' .
			'		</thead>' .
			'		<tbody id="the-list">';
		$this->render_entries();
		echo '' .
			'		</tbody>' .
			'	</table>';
	}

	/**
	 * Render the entries
	 *
	 * Renders all entries available with the current filters. Is used by @see render_table.
	 *
	 * @since 1.0.0
	 */
	private function render_entries() {
		if ( 0 === $this->entries->count() ) {
			echo '' .
				'			<tr id="entry-?">' .
				'				<td class="term column-term has-row-actions column-primary" ' .
									'data-colname="' . esc_html__( 'Term', 'glossary-by-arteeo' ) . '">' .
				'				<strong class="row-Term">' .
				'					?' .
				'				</stong>' .
				'			</td>' .
				'			<td class="description column-description" ' .
									'data-colname="' . esc_html__( 'Description', 'glossary-by-arteeo' ) . '">' .
				'				' . esc_html__( 'No entries found.', 'glossary-by-arteeo' ) .
				'			</td>' .
				'			<td class="locale column-locale" ' .
									'data-colname="' . esc_html__( 'Language', 'glossary-by-arteeo' ) . '">' .
				'				-' .
				'			</td>' .
				'		</tr>';
		}

		foreach ( $this->entries as $entry ) {
			echo '' .
				'			<tr id="entry-' . esc_html( $entry->id ) . '">' .
				'				<td class="term column-term has-row-actions column-primary" ' .
										'data-colname="' . esc_html__( 'Term', 'glossary-by-arteeo' ) . '">' .
				'					<strong class="row-Term">' .
				'						' . esc_html( $entry->term ) .
				'					</stong>' .
				'					<div class="row-actions">' .
				'						<span class="edit">' .
				'							<a href="' .
													esc_url( self::generate_entry_edit_url( $entry ) ) .
													'" aria-label="\'' . esc_html( $entry->term ) . '\' ' .
														'(' . esc_html__( 'Edit', 'glossary-by-arteeo' ) . ')">' .
					'								' . esc_html__( 'Edit', 'glossary-by-arteeo' ) .
				'							</a>' .
				'							|' .
				'						</span>' .
				'						<span class="delete">' .
				'							<a class="delete" href="' .
													esc_url( self::generate_entry_delete_url( $entry ) ) .
													'" aria-label="\'' . esc_html( $entry->term ) . '\' ' .
													'(' . esc_html__( 'Delete', 'glossary-by-arteeo' ) . ')">' .
				'								' . esc_html__( 'Delete', 'glossary-by-arteeo' ) .
				'							</a>' .
				'						</span>' .
				'					</div>' .
				'				</td>' .
				'				<td class="description column-description" ' .
										'data-colname="' . esc_html__( 'Description', 'glossary-by-arteeo' ) . '">' .
				'				' . nl2br( esc_html( $entry->description ) ) .
				'				</td>' .
				'				<td class="locale column-locale" ' .
										'data-colname="' . esc_html__( 'Language', 'glossary-by-arteeo' ) . '">' .
				'					' . esc_html( Helpers::get_readable_locale( $entry->locale ) ) .
				'				</td>' .
				'			</tr>';
		}
	}


	/**
	 * Generate letter sort url
	 *
	 * Generates the url which can be used to sort the table by the provided letter.
	 *
	 * @since 1.0.0
	 * @param Letter $letter The letter for which the url should be created.
	 * @return string The url which was generated.
	 */
	private static function generate_letter_sort_url( ?Letter $letter ) : string {
		if ( null === $letter ) {
			return Helpers::generate_url( array( 'glossary_show' => 'all' ) );
		}
		if ( '#' === $letter->letter ) {
			return Helpers::generate_url( array( 'glossary_show' => 'hashtag' ) );
		}
		return Helpers::generate_url( array( 'glossary_show' => $letter->letter ) );
	}

	/**
	 * Generate entry delete url
	 *
	 * Generates the url which can be used to show the delete form for the given entry.
	 *
	 * @since 1.0.0
	 * @param Entry $entry The entry for which the url should be created.
	 * @return string The url which was generated.
	 */
	private static function generate_entry_delete_url( Entry $entry ) : string {
		return Helpers::generate_url(
			array(
				'action' => 'delete',
				'id'     => $entry->id,
			)
		);
	}

	/**
	 * Generate entry edit url
	 *
	 * Generates the url which can be used to show the edit form for the given entry.
	 *
	 * @since 1.0.0
	 * @param Entry $entry The entry for which the url should be created.
	 * @return string The url which was generated.
	 */
	private static function generate_entry_edit_url( Entry $entry ) : string {
		return Helpers::generate_url(
			array(
				'action' => 'edit',
				'id'     => $entry->id,
			)
		);
	}

	/**
	 * Generate sorting url
	 *
	 * Generates the url which can be used to sort by the specified column.
	 *
	 * @since 1.0.0
	 * @param string $sortable The column to sort by. Must be sortable.
	 * @param string $sorting  The sorting asc or desc.
	 * @return string The url which was generated.
	 */
	private static function generate_sort_url( string $sortable, string $sorting ) : string {
		return Helpers::generate_url(
			array(
				'glossary_sort' => $sortable,
				'order'         => $sorting,
			)
		);
	}

	/**
	 * Get parameters for language picker
	 *
	 * Gets the parameters which are needed for the language picker to maintain the current url with its get-parameters.
	 *
	 * @since 1.0.0.
	 * @return array The parameters which have to be added.
	 */
	private static function get_hidden_parameters() : array {
		$result = $_GET;
		if ( isset( $result['language_filter'] ) ) {
			unset( $result['language_filter'] );
		}
		return $result;
	}
}
