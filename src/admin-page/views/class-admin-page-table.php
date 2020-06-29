<?php
/**
 * Admin Page table
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../class-glossary.php';
require_once __DIR__ . '/../../models/class-message.php';
require_once __DIR__ . '/../../models/class-entries.php';
require_once __DIR__ . '/../../models/class-letters.php';
require_once __DIR__ . '/../../models/class-filter.php';
require_once __DIR__ . '/../../helper/class-helpers.php';

class Admin_Page_Table {

	private Entries $entries;
	private Letters $letters;
	private ?Message $message;
	private Filter $filter;

	public function __construct( Entries $entries, Letters $letters, ?Message $message, Filter $filter ) {
		$this->entries = $entries;
		$this->letters = $letters;
		$this->message = $message;
		$this->filter  = $filter;
	}

	/**
	 * Show overview screen
	 *
	 * Show current entries of the glossary with several filter options.
	 */
	public function render() {
		echo '<div class="wrap">';
		$this->render_header();
		$this->render_message();
		$this->render_letters();
		$this->render_language_filter();
		$this->render_table();
		echo '</div>';
	}

	private function render_header() {
		echo '' .
			'	<h1 class="wp-heading-inline">' . esc_html( __( 'Glossary', 'arteeo-glossary' ) ) . '</h1>' .
			'	<span>v' . esc_html( Glossary::VERSION ) . '</span>' .
			'	<a class="page-title-action aria-button-if-js" role="button" aria-expanded="false" ' .
					'href="' . esc_url( Helpers::generate_url( array( 'action' => 'add' ) ) ) .'"> ' .
			'		' . esc_html( __( 'Add entry', 'arteeo-glossary' ) ) .
			'	</a>' .
			'	<hr class="wp-header-end">';
	}

	private function render_message() {
		if ( null !== $this->message ) {
			if ( Message::SUCCESS === $this->message->type ) {
				echo '' .
					'	<div id="message" class="updated notice is-dismissible">' .
					'		<p>' . esc_html( $this->message->content ) . '</p>' .
					'		<button class="notice-dismiss" type="button">' .
					'			<span class="screen-reader-text">' .
					'				' . esc_html( __( 'Hide this message', 'arteeo-glossary' ) ) .
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

	private function render_letters() {
		echo '	<ul class="subsubsub">';
		if ( 0 < $this->letters->count() ) {
			echo '' .
				'		<li class="all">' .
				'			<a class="' . ( ( 'all' === $this->filter->letter ) ? 'current ' : '' ) . '" ' .
								'href="' . esc_url( Helpers::generate_url( array( 'glossary_show' => 'all' ) ) ) . '">' .
								esc_html( __( 'All', 'arteeo-glossary' ) ) .
				'				<span class="count">(' . esc_html( $this->letters->count() ) . ')</span>' .
				'			</a>' .
				'			|' .
				'		</li>';
		}
		$hashtag = 0;
		foreach ( $this->letters as $letter ) {
			if ( '#' === $letter->letter ) {
				$hashtag = $letter->count;
			} else {
				echo '' .
				'		<li class="' . esc_html( $letter->letter ) . '">' .
				'			<a class="' . ( ( $letter->letter === $this->filter->letter ) ? 'current ' : '' ) . '" ' .
				'					href="' . esc_url( Helpers::generate_url( array( 'glossary_show' => $letter->letter ) ) ) .
									'">' . esc_html( $letter->letter ) .
				'				<span class="count">(' . esc_html( $letter->count ) . ')</span>' .
				'			</a>' .
				'			|' .
				'		</li>';
			}
		}
		if ( 0 < $hashtag ) {
			echo '' .
				'		<li class="#">' .
				'			<a class="' . ( ( '#' === $this->filter->letter ) ? 'current ' : '' ) . '" ' .
				'				href="' . esc_url( Helpers::generate_url( array( 'glossary_show' => 'hashtag' ) ) ) . '">' .
				'				#<span class="count">(' . esc_html( $hashtag ) . ')</span>' .
				'			</a>' .
				'		</li>';
		}
		echo '	</ul>';
	}

	private function render_language_filter() {
		echo '' .
			'	<div class="tablenav top">' .
			'		<div class="alignleft actions">' .
			'			<form method="get">' .
			'				<label class="screen-reader-text" for="language_filter">' .
			'					' . esc_html( __( 'Filter by language', 'arteeo-glossary' ) ) .
			'				</label>';
		foreach ( $_GET as $name => $value ) {
			if ( 'language_filter' === $name ) {
				continue;
			}
			echo '' .
				'<input type="hidden" name="' . esc_html( $name ) . '" value="' . esc_html( $value ) . '">';
		}

		$languages = Helpers::get_locales();
		Helpers::render_dropdown_languages( 'language_filter', $languages, $this->filter->locale, true );
		echo '' .
			'				<input id="post-query-submit" class="button" type="submit" ' .
								'value="' . esc_html( __( 'Filter by language', 'arteeo-glossary' ) ) . '">' .
			'			</form>' .
			'		</div>' .
			'	</div>';
	}

	private function render_table() {
		echo '' .
			'	<h2 class="screen-reader-text">' . esc_html( __( 'Glossary entries', 'arteeo-glossary' ) ) . '</h2>' .
			'	<table class="wp-list-table widefat fixed striped">' .
			'		<thead>' .
			'			<th id="term" class="manage-column column-term column-primary sorted ' .
								esc_html( strtolower( $this->filter->sorting ) ) . '" scope="col">' .
			'				<a href="' .
									esc_url(
										Helpers::generate_url(
											array(
												'glossary_sort' => 'term',
												'order'         => ( ( 'DESC' === $this->filter->sorting ) ? 'asc' : 'desc' ),
											)
										)
									) .
			'					">' .
			'					<span>' . esc_html( __( 'Term', 'arteeo-glossary' ) ) . '</span>' .
			'					<span class="sorting-indicator"></span>' .
			'				</a>' .
			'			</th>' .
			'			<th id="description" class="manage-column column-description" scope="col">' .
			'				' . esc_html( __( 'Description', 'arteeo-glossary' ) ) .
			'			</th>' .
			'			<th id="locale" class="manage-column column-locale" scope="col">' .
			'				' . esc_html( __( 'Language', 'arteeo-glossary' ) ) .
			'			</th>' .
			'		</thead>' .
			'		<tbody id="the-list">';
		$this->render_entries();
		echo '' .
			'		</tbody>' .
			'	</table>';
	}

	private function render_entries() {
		if ( 0 === $this->entries->count() ) {
			echo '' .
				'			<tr id="entry-?">' .
				'				<td class="term column-term has-row-actions column-primary" ' .
									'data-colname="' . esc_html( __( 'Term', 'arteeo-glossary' ) ) . '">' .
				'				<strong class="row-Term">' .
				'					?' .
				'				</stong>' .
				'			</td>' .
				'			<td class="description column-description" ' .
									'data-colname="' . esc_html( __( 'Description', 'arteeo-glossary' ) ) . '">' .
				'				' . esc_html( __( 'No entries found.', 'arteeo-glossary' ) ) .
				'			</td>' .
				'			<td class="locale column-locale" ' .
									'data-colname="' . esc_html( __( 'Language', 'arteeo-glossary' ) ) . '">' .
				'				-' .
				'			</td>' .
				'		</tr>';
		}

		foreach ( $this->entries as $entry ) {
			echo '' .
				'			<tr id="entry-' . esc_html( $entry->id ) . '">' .
				'				<td class="term column-term has-row-actions column-primary" ' .
										'data-colname="' . esc_html( __( 'Term', 'arteeo-glossary' ) ) . '">' .
				'					<strong class="row-Term">' .
				'						' . esc_html( $entry->term ) .
				'					</stong>' .
				'					<div class="row-actions">' .
				'						<span class="edit">' .
				'							<a href="' .
												esc_url(
													Helpers::generate_url(
														array(
															'action' => 'edit',
															'id'     => $entry->id,
														)
													)
												) .
												'" aria-label="\'' . esc_html( $entry->term ) . '\' ' .
													'(' . esc_html( __( 'Edit', 'arteeo-glossary' ) ) . ')">' .
				'								' . esc_html( __( 'Edit', 'arteeo-glossary' ) ) .
				'							</a>' .
				'							|' .
				'						</span>' .
				'						<span class="delete">' .
				'							<a class="delete" href="' .
													esc_url(
														Helpers::generate_url(
															array(
																'action' => 'delete',
																'id'     => $entry->id,
															)
														)
													) .
													'" aria-label="\'' . esc_html( $entry->term ) . '\' ' .
													'(' . esc_html( __( 'Delete', 'arteeo-glossary' ) ) . ')">' .
				'								' . esc_html( __( 'Delete', 'arteeo-glossary' ) ) .
				'							</a>' .
				'						</span>' .
				'					</div>' .
				'				</td>' .
				'				<td class="description column-description" ' .
										'data-colname="' . esc_html( __( 'Description', 'arteeo-glossary' ) ) . '">' .
				'					' . nl2br( esc_html( $entry->description ) ) .	
				'				</td>' .
				'				<td class="locale column-locale" ' .
										'data-colname="' . esc_html( __( 'Language', 'arteeo-glossary' ) ) . '">' .
				'					' . esc_html( \Locale::getDisplayName( $entry->locale, get_user_locale() ) ) .
				'				</td>' .
				'			</tr>';
		}
	}
}
