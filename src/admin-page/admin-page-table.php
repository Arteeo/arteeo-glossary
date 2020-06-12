<?php
/**
 * Admin Page table
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show main screen
 * Show current entries in table.
 */
function create_glossary_admin_table(){
	global $glossary_page_id;
	global $glossary_version;
	global $wpdb;
	global $glossary_table_name;

	$sorting = "ASC";
	if (isset($_GET['glossary_sort']) && isset($_GET['order'])) {
		if ($_GET['glossary_sort'] == 'term' && $_GET['order'] == 'desc') {
			$sorting = "DESC";
		}
	}

	$entries;
	$glossary_show;
	if(isset($_GET['glossary_show'])) {
		$glossary_show = sanitize_text_field($_GET['glossary_show']);

		if (strlen($glossary_show) == 1) {
			$entries = $wpdb->get_results( "SELECT id, letter, term, description FROM $glossary_table_name WHERE letter = '$glossary_show' ORDER BY term $sorting");
			if ($wpdb->num_rows < 1) {
				redirectTo(generate_url(array('glossary_show' => 'all')));
			}
		} else if ($glossary_show == "hashtag") {
			$glossary_show = '#';
			$entries = $wpdb->get_results( "SELECT id, letter, term, description FROM $glossary_table_name WHERE letter = '$glossary_show' ORDER BY term $sorting");
			if ($wpdb->num_rows < 1) {
				redirectTo(generate_url(array('glossary_show' => 'all')));
			}
		} else if ($glossary_show == 'all') {
			$entries = $wpdb->get_results( "SELECT id, letter, term, description FROM $glossary_table_name ORDER BY term $sorting");
		} else {
			redirectTo(generate_url(array('glossary_show' => 'all')));
		}
	} else {
		redirectTo(generate_url(array('glossary_show' => 'all')));
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
			<li class="all">
				<a class="<?php echo $glossary_show == 'all' ? 'current ' : '';?>" href="<?php echo generate_url(array('glossary_show'=>'all')); ?>"><?php _e('All', 'glossary'); ?>
					<span class="count">(<?php echo $wpdb->get_results( "SELECT count(letter) AS count FROM $glossary_table_name")[0]->count;?>)</span>
				</a>
				|
			</li>
			<?php
				$letters = $wpdb->get_results( "SELECT letter, count(letter) AS count FROM $glossary_table_name GROUP BY letter ORDER BY letter ASC");
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
			</thead>
			<tbody id="the-list">
				<?php
					foreach($entries as $entry) {
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
							</tr>	
						';
					}
				?>
			</tbody>
		</table>
	</div>
	<?php
}