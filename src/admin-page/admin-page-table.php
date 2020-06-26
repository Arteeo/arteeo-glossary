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

require_once __DIR__ . '/../class-glossary.php';

class Admin_Page_Table {

	public function __construct() {}

	/**
	 * Show overview screen
	 *
	 * Show current entries of the glossary with several filter options.
	 */
	public function render() {
		$sorting = 'ASC';
		if ( isset( $_GET['glossary_sort'] ) && isset( $_GET['order'] ) ) {
			if ( 'term' === $_GET['glossary_sort'] && 'desc' === $_GET['order'] ) {
				$sorting = 'DESC';
			}
		}

		$filters = array();

		$languages = glossary_get_locales();
		if ( isset( $_GET['language_filter'] ) && false !== array_search( $_GET['language_filter'], $languages, true ) ) {
			$filters['locale'] = sanitize_text_field( $_GET['language_filter'] );
		}

		$entries;
		$glossary_show = '';
		if ( isset( $_GET['glossary_show'] ) ) {
			$glossary_show = sanitize_text_field( $_GET['glossary_show'] );
		}

		switch ( $glossary_show ) {
			case 'hashtag':
				$glossary_show     = '#';
				$filters['letter'] = $glossary_show;
				break;
			case 'all':
				break;
			case ( 1 === strlen( $glossary_show ) ):
				$filters['letter'] = $glossary_show;
				break;
			default:
				redirect_to( generate_url( array( 'glossary_show' => 'all' ) ) );
				break;
		}

		$entries      = get_filtered_entries( $filters, $sorting );
		$letters      = get_filtered_letters( $filters );
		$letter_count = 0;
		foreach ( $letters as $letter ) {
			$letter_count += $letter->count;
		}

		if ( 0 === count( $entries ) && 'all' !== $glossary_show ) {
			redirect_to( generate_url( array( 'glossary_show' => 'all' ) ) );
		}

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Glossary', 'arteeo-glossary' ); ?></h1>
			<span>v<?php echo esc_html( Glossary::VERSION ); ?></span>
			<a class="page-title-action aria-button-if-js" role="button" aria-expanded="false"
					href="<?php echo esc_url( generate_url( array( 'action' => 'add' ) ) ); ?>">
				<?php esc_html_e( 'Add entry', 'arteeo-glossary' ); ?>
			</a>
			<hr class="wp-header-end">
			<?php
			if ( isset( $_GET['message'] ) && isset( $_GET['message_type'] ) ) {
				if ( 'success' === $_GET['message_type'] ) {
					echo '
						<div id="message" class="updated notice is-dismissible">
							<p>' . esc_html( $_GET['message'] ) . '</p>
							<button class="notice-dismiss" type="button">
								<span class="screen-reader-text">' .
									esc_html( __( 'Hide this message', 'arteeo-glossary' ) ) .
								'</span>
							</button>
						</div>
					';
				} elseif ( 'error' === $_GET['message_type'] ) {
					echo '
						<div id="message" class="error">
							<p>' . esc_html( $_GET['message'] ) . '</p>
						</div>
					';
				}
			}
			?>
			<ul class="subsubsub">
				<?php
				if ( 0 < $letter_count ) {
					echo '' .
					'<li class="all">' .
					'	<a class="' . ( ( 'all' === $glossary_show ) ? 'current ' : '' ) . '" ' .
							'href="' . esc_url( generate_url( array( 'glossary_show' => 'all' ) ) ) . '">' .
							esc_html( __( 'All', 'arteeo-glossary' ) ) .
					'		<span class="count">(' . esc_html( $letter_count ) . ')</span>' .
					'	</a>' .
					'	|' .
					'</li>';
				}
				$hashtag = 0;
				foreach ( $letters as $letter ) {
					if ( '#' === $letter->letter ) {
						$hashtag = $letter->count;
					} else {
						echo ' 
							<li class="' . esc_html( $letter->letter ) . '">
								<a class="' . ( ( $letter->letter === $glossary_show ) ? 'current ' : '' ) . '"
										href="' . esc_url( generate_url( array( 'glossary_show' => $letter->letter ) ) ) .
										'">' . esc_html( $letter->letter ) . '
									<span class="count">(' . esc_html( $letter->count ) . ')</span>
								</a>
								|
							</li>
						';
					}
				}
				if ( 0 < $hashtag ) {
					echo '
						<li class="#">
							<a class="' . ( ( '#' === $glossary_show ) ? 'current ' : '' ) . '" 
									href="' . esc_url( generate_url( array( 'glossary_show' => 'hashtag' ) ) ) . '">#
								<span class="count">(' . esc_html( $hashtag ) . ')</span>
							</a>
						</li>
					';
				}
				?>
			</ul>
			<div class="tablenav top">
				<div class="alignleft actions">
					<form method="get">
						<label class="screen-reader-text" for="language_filter">
							<?php esc_html_e( 'Filter by language', 'arteeo-glossary' ); ?>
						</label>
						<?php
						foreach ( $_GET as $name => $value ) {
							if ( 'language_filter' === $name ) {
								continue;
							}
							echo '' .
								'<input type="hidden" name="' . esc_html( $name ) . '" value="' . esc_html( $value ) . '">';
						}
						?>
						<?php
						$selected = '';
						if ( isset( $filters['locale'] ) ) {
							$selected = $filters['locale'];
						}
						glossary_dropdown_languages( 'language_filter', $languages, $selected, true );
						?>
						<input id="post-query-submit" class="button" type="submit"
								value="<?php esc_html_e( 'Filter by language', 'arteeo-glossary' ); ?>">
					</form>
				</div>
			</div>
			<h2 class="screen-reader-text"><?php esc_html_e( 'Glossary entries', 'arteeo-glossary' ); ?></h2>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<th id="term" class="manage-column column-term column-primary sorted 
							<?php echo esc_html( strtolower( $sorting ) ); ?>" scope="col">
						<a href="
								<?php
								echo esc_url(
									generate_url(
										array(
											'glossary_sort' => 'term',
											'order'         => ( ( 'DESC' === $sorting ) ? 'asc' : 'desc' ),
										)
									)
								);
								?>
							">
							<span><?php esc_html_e( 'Term', 'arteeo-glossary' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th id="description" class="manage-column column-description" scope="col">
						<?php esc_html_e( 'Description', 'arteeo-glossary' ); ?>
					</th>
					<th id="locale" class="manage-column column-locale" scope="col">
						<?php esc_html_e( 'Language', 'arteeo-glossary' ); ?>
					</th>
				</thead>
				<tbody id="the-list">
					<?php
					if ( 0 === count( $entries ) ) {
						echo '
							<tr id="entry-?">
								<td class="term column-term has-row-actions column-primary" 
										data-colname="' . esc_html( __( 'Term', 'arteeo-glossary' ) ) . '">
									<strong class="row-Term">
										?
									</stong>
								</td>
								<td class="description column-description" 
										data-colname="' . esc_html( __( 'Description', 'arteeo-glossary' ) ) . '">
									' . esc_html( __( 'No entries found.', 'arteeo-glossary' ) ) . '	
								</td>
								<td class="locale column-locale" 
										data-colname="' . esc_html( __( 'Language', 'arteeo-glossary' ) ) . '">
									-
								</td>
							</tr>	
						';
					}

					foreach ( $entries as $entry ) {
						echo '
							<tr id="entry-' . esc_html( $entry->id ) . '">
								<td class="term column-term has-row-actions column-primary" 
										data-colname="' . esc_html( __( 'Term', 'arteeo-glossary' ) ) . '">
									<strong class="row-Term">
										' . esc_html( $entry->term ) . '
									</stong>
									<div class="row-actions">
										<span class="edit">
											<a href="' .
													esc_url(
														generate_url(
															array(
																'action' => 'edit',
																'id'     => $entry->id,
															)
														)
													)
													. '" aria-label="\'' . esc_html( $entry->term ) . '\' 
													(' . esc_html( __( 'Edit', 'arteeo-glossary' ) ) . ')">
												' . esc_html( __( 'Edit', 'arteeo-glossary' ) ) . '
											</a>
											|
										</span>
										<span class="delete">
											<a class="delete" href="' .
													esc_url(
														generate_url(
															array(
																'action' => 'delete',
																'id'     => $entry->id,
															)
														)
													) .
													'" aria-label="\'' . esc_html( $entry->term ) . '\' 
													(' . esc_html( __( 'Delete', 'arteeo-glossary' ) ) . ')">
												' . esc_html( __( 'Delete', 'arteeo-glossary' ) ) . '
											</a>
										</span>
									</div>
								</td>
								<td class="description column-description" 
										data-colname="' . esc_html( __( 'Description', 'arteeo-glossary' ) ) . '">
									' . nl2br( esc_html( $entry->description ) ) . '	
								</td>
								<td class="locale column-locale" 
										data-colname="' . esc_html( __( 'Language', 'arteeo-glossary' ) ) . '">
									' . esc_html( \Locale::getDisplayName( $entry->locale, get_user_locale() ) ) . '
								</td>
							</tr>	
						';
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
