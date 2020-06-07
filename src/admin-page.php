<?php
/**
 * Admin Page
 */
global $glossary_page_id;
$glossary_page_id = 'glossary_entries_page';

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'glossary_settings_page' );

function glossary_settings_page() {
	global $glossary_page_id;
	//add_options_page( 'Settings API Page', 'Settings API Page', 'manage_options', 'settings-api-page', 'settings_api_page' );
	add_menu_page( 'Glossary', 'Glossary', 'manage_options', $glossary_page_id, 'create_glossary_admin_page', 'dashicons-book-alt', null );
	//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
}

function create_glossary_admin_page() {
	global $glossary_page_id;
	global $glossary_db_version;
	echo $_GET['action'];
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Glossary</h1><span>v<?php echo $glossary_db_version; ?></span><a class="page-title-action aria-button-if-js" role="button" aria-expanded="false" href="?page=<?php echo $glossary_page_id; ?>&action=new"><?php echo __('Eintrag hinzufügen'); ?></a>
		<hr class="wp-header-end">
		<ul class="subsubsub">
			<li class="all">
				<a class="current" href="?page=<?php echo $glossary_page_id; ?>&glossary_show=all"><?php echo __('Alle'); ?>
					<span class="count">(2)</span>
				</a>
				|
			</li>
			<?php
				foreach (range('A', 'Z') as $letter){
					echo ' 
						<li class="'.$letter.'">
							<a href="?page='.$glossary_page_id.'&glossary_show='.$letter.'">'.$letter.'
								<span class="count">(2)</span>
							</a>
							|
						</li>
					';
				}
			?>
			<li class="#">
				<a href="?page=<?php echo $glossary_page_id; ?>&glossary_show=#">#
					<span class="count">(2)</span>
				</a>
			</li>
		</ul>
		<h2 class="screen-reader-text">Glossar Einträge</h2>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<th id="name" class="manage-column column-name column-primary sortable desc" scope="col">
					<a href="?page=<?php echo $glossary_page_id; ?>&glossary_sort=name&order=desc">
						<span>Name</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th id="description" class="manage-column column-description" scope="col">
					Beschreibung
				</th>
			</thead>
		</table>
	</div>
	<?php
}