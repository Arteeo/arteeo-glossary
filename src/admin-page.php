<?php
/**
 * Admin Page
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'glossary_settings_page' );

function glossary_settings_page() {
	//add_options_page( 'Settings API Page', 'Settings API Page', 'manage_options', 'settings-api-page', 'settings_api_page' );
	add_menu_page( 'Glossary', 'Glossary', 'manage_options', 'glossary_entries_page', 'create_glossary_admin_page', 'dashicons-book-alt', null );
	//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
}

function create_glossary_admin_page() {
	?>
	<div>
		<p>
			Hello World
		</p>
	</div>
	<?php
}