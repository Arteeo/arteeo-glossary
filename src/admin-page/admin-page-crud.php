<?php
/**
 * Admin Page CRUD
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show entry form
 * 
 * Used for creating or editing entries
 * @param integer $id id of the entry to change. If
 *                set to null a new entry will be
 *                created.
 */
function glossary_entry_form($id) {
	?>
	<div class="wrap">
		<?php
			if (isset($_POST['action']) && $_POST['action'] == 'create_glossary_entry') {
				echo '
					<div id="message" class="updated notice is-dismissible">
						<p>Awesome message</p>
						<button class="notice-dismiss" type="button">
							<span class="screen-reader-text"> Diese Meldung ausblenden</span>
						</button>
					</div>
				';
			}
		?>
		<h1 class="add-entry"><?php echo __('Glossar Eintrag hinzufügen'); ?></h1>
		<div id="ajax-response"></div>
		<p>Legen Sie einen neuen Eintrag für den Glossar an.</p>
		<form id="entry_form" class="validate" method="post" name="entry_form" novalidate="novalidate">
			<input name="action" type="hidden" value="create_glossary_entry">
			<?php wp_nonce_field( 'create_glossary_entry' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr class="form-field form-required">
					<th scope="row">
						<label for="glossary_term">
						Begriff
						<span class="description">(erforderlich)</span>
						</label>
					</th>
					<td>
						<input id="glossary_term" name="term" type="text" value="" aria-required="true" autocapitalize="none" autocorrect="on" maxlenght="60">
					</td>
					</tr>
					<tr class="form-field form-required">
					<th scope="row">
						<label for="glossary_description">
						Beschreibung
						<span class="description">(erforderlich)</span>
						</label>
					</th>
					<td>
						<textarea id="glossary_description" name="description" aria-required="true" autocapitalize="none" autocorrect="on">
						</textarea>
					</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
			<input id="entry_submit" class="button button-primary" type="submit" name="entry_form" value="Neuen Eintrag anlegen">
			</p>
		</form>
	</div>
	<?php
}