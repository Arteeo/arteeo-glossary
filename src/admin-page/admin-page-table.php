<?php
/**
 * Admin Page table
 *
 * @package glossary
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show overview screen
 *
 * Show current entries of the glossary with several filter options.
 *
 * @global string $glossary_page_id    The slug of the glossary admin page.
 * @global string $glossary_version    The current version of the glossary plugin.
 * @global object $wpdb                The WordPress database instance.
 * @global string $glossary_table_name The name of the glossary database table.
 */
function create_glossary_admin_table() {
	global $glossary_page_id;
	global $glossary_version;
	global $wpdb;
	global $glossary_table_name;

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

	$entries = get_filtered_entries( $filters, $sorting );
	$letters = get_filtered_letters( $filters );
	$letter_count = 0;
	foreach ( $letters as $letter ) {
		$letter_count += $letter->count;
	}

	if ( 0 === count( $entries ) && 'all' !== $glossary_show ) {
		redirect_to( generate_url( array( 'glossary_show' => 'all' ) ) );
	}

	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Glossary', 'glossary'); ?></h1><span>v<?php echo $glossary_version; ?></span><a class="page-title-action aria-button-if-js" role="button" aria-expanded="false" href="<?php echo generate_url(array('action'=>'add')); ?>"><?php _e('Add entry', 'glossary'); ?></a>
		<hr class="wp-header-end">
		<?php
			if (isset($_GET['message']) && isset($_GET['message_type'])) {
				if($_GET['message_type'] == 'success') {
					echo '
						<div id="message" class="updated notice is-dismissible">
							<p>'.sanitize_text_field($_GET['message']).'</p>
							<button class="notice-dismiss" type="button">
								<span class="screen-reader-text">'.__('Hide this message', 'glossary').'</span>
							</button>
						</div>
					';
				} else if($_GET['message_type'] == 'error') {
					echo '
						<div id="message" class="error">
							<p>'.sanitize_text_field($_GET['message']).'</p>
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
						'href="' . generate_url( array( 'glossary_show' => 'all' ) ) . '">' . __( 'All', 'glossary' ) .
				'		<span class="count">(' . $letter_count . ')</span>' .
				'	</a>' .
				'	|' .
				'</li>';
			}
			$hashtag = 0;
			foreach ($letters as $letter){
				if ($letter->letter == '#') {
					$hashtag = $letter->count;
				} else {
					echo ' 
						<li class="'.$letter->letter.'">
							<a class="'.($glossary_show == $letter->letter ? 'current ' : '').'" href="'.generate_url(array('glossary_show'=>$letter->letter)).'">'.$letter->letter.'
								<span class="count">('.$letter->count.')</span>
							</a>
							|
						</li>
					';
				}
			}
			if ($hashtag > 0) {
				echo '
					<li class="#">
						<a class="'.($glossary_show == '#' ? 'current ' : '').'" href="'.generate_url(array('glossary_show'=>'hashtag')).'">#
							<span class="count">('.$hashtag.')</span>
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
						<?php esc_html_e( 'Filter by language', 'glossary' ); ?>
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
							value="<?php esc_html_e( 'Filter by language', 'glossary' ); ?>">
				</form>
			</div>
		</div>
		<h2 class="screen-reader-text"><?php _e('Glossary entries', 'glossary'); ?></h2>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<th id="term" class="manage-column column-term column-primary sorted <?php echo strtolower($sorting);?>" scope="col">
					<a href="<?php echo generate_url(array('glossary_sort'=>'term', 'order'=>($sorting == 'DESC' ? 'asc' : 'desc'))) ?>">
						<span><?php _e('Term', 'glossary'); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th id="description" class="manage-column column-description" scope="col">
					<?php _e('Description', 'glossary'); ?>
				</th>
				<th id="locale" class="manage-column column-locale" scope="col">
					<?php _e('Language', 'glossary'); ?>
				</th>
			</thead>
			<tbody id="the-list">
				<?php
				if ( 0 === count( $entries ) ) {
					echo '
						<tr id="entry-?">
							<td class="term column-term has-row-actions column-primary" data-colname="'.__('Term', 'glossary').'">
								<strong class="row-Term">
									?
								</stong>
							</td>
							<td class="description column-description" data-colname="'.__('Description', 'glossary').'">
								'. __( 'No entries found.' , 'glossary') . '	
							</td>
							<td class="locale column-locale" data-colname="'.__('Language', 'glossary').'">
								-
							</td>
						</tr>	
					';
				}

				foreach ( $entries as $entry ) {
					echo '
						<tr id="entry-'.$entry->id.'">
							<td class="term column-term has-row-actions column-primary" data-colname="'.__('Term', 'glossary').'">
								<strong class="row-Term">
									'.$entry->term.'
								</stong>
								<div class="row-actions">
									<span class="edit">
										<a href="'.generate_url(array('action'=>'edit', 'id'=>$entry->id)).'" aria-label="\''.$entry->term.'\' ('.__('Edit', 'glossary').')">
											'.__('Edit', 'glossary').'
										</a>
										|
									</span>
									<span class="delete">
										<a class="delete" href="'.generate_url(array('action'=>'delete', 'id'=>$entry->id)).'" aria-label="\''.$entry->term.'\' ('.__('Delete', 'glossary').')">
											'.__('Delete', 'glossary').'
										</a>
									</span>
								</div>
							</td>
							<td class="description column-description" data-colname="'.__('Description', 'glossary').'">
								'.nl2br($entry->description).'	
							</td>
							<td class="locale column-locale" data-colname="'.__('Language', 'glossary').'">
								' . esc_html( Locale::getDisplayName( $entry->locale, get_user_locale() ) ) . '
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

function glossary_dropdown_languages( $name, $languages, $selected, $include_all = false ) {
	echo '<select id="' . esc_html( $name ) . '" name="' . esc_html( $name ) . '">';

	if ( $include_all ) {
		echo '' .
		'	<option value=""' . ( ( '' === $selected ) ? 'selected >' : '>' ) .
				esc_html( __( 'All', 'glossary' ) ) .
		'	</option>';
	}

	foreach ( $languages as $language ) {
		echo '' .
			'	<option value="' . esc_html( $language ) . '" ' .
					( ( $language === $selected ) ? 'selected >' : '>' ) .
				esc_html( Locale::getDisplayName( $language, get_user_locale() ) ) .
			'	</option>';
	}
	echo '</select>';
}
