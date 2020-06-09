<?php
/**
 * Admin Page CRUD
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle entry crud
 * 
 * Used for CRUD operation on glossary entries
 * @param string  $action the action which has to
 * 								be done.
 * @param integer $id id of the entry to change.
 * 								Set to null for new entry.
 */
function glossary_entry_crud($action, $id) {
	global $wpdb;
	global $glossary_table_name;
	if (isset($_POST['action']) && isset($_POST['term']) && isset($_POST['description'])) {
			$error = glossary_entry_form_check_errors();
			if ($error == null) {
				$term = sanitize_text_field( $_POST['term'] );
				$description = sanitize_text_field( $_POST['description'] );
				$letter = substr($term, 0, 1);
				if (!ctype_alpha($letter)) {
					$letter = '#';
				} else {
					$letter = strtoupper($letter);
				}
				
				$result = false;
				if ($_POST['action'] == 'create_glossary_entry') {
					$result = $wpdb->insert( 
						$glossary_table_name, 
						array( 
							'letter' => $letter,
							'term' => $term, 
							'description' => $description 
						), 
						array( 
							'%s',
							'%s',
							'%s'
						) 
					);
				} else if ($_POST['action'] == 'edit_glossary_entry') {
					$result = $wpdb->update( 
						$glossary_table_name, 
						array( 
							'letter' => $letter,
							'term' => $term,
							'description' => $description
						), 
						array( 'id' => $_POST['id'] ), 
						array( 
							'%s',
							'%s',
							'%s'
						), 
						array( '%d' ) 
					);
				}

				if ($result === false) {
					echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'message_type' => 'error', 'message' => 'Datenbank fehler')).'">';
				} else {
					if ($_POST['action'] == 'create_glossary_entry') {
						echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'message_type' => 'success', 'message' => 'Eintrag wurde angelegt')).'">';
					} else if ($_POST['action'] == 'edit_glossary_entry') {
						if ($result == 0) {
							echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'id' => 'null', 'message_type' => 'success', 'message' => 'Keine Änderungen wurden vorgenommen.')).'">';
						} else {
							echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'id' => 'null',  'message_type' => 'success', 'message' => 'Eintrag wurde angepasst')).'">';
						}
					}
				}
			} else {
				if ($_POST['action'] == 'create_glossary_entry') {
					glossary_entry_form(null, $error);
				} else if ($_POST['action'] == 'edit_glossary_entry') {
					$entry = new stdClass();
					$entry->id = $_POST['id'];
					glossary_entry_form($entry, $error);
				}
			}
	} else {
		if ($action == 'add') {
			glossary_entry_form(null, null);
		} else if ($action == 'edit') {
				if (is_numeric($id)) {
					$entries = $wpdb->get_results("SELECT * FROM $glossary_table_name WHERE id = $id");
					if( $wpdb->num_rows == 1) {
						glossary_entry_form($entries[0], null);
					} else {
						glossary_entry_form(null, "Eintrag nicht gefunden.");
					}
				} else {
					glossary_entry_form(null, "Id nicht gültig.");
				}
		} else if ($action == 'delete') {
			if(is_numeric($id)) {
				$entries = $wpdb->get_results("SELECT * FROM $glossary_table_name WHERE id = $id");
				if($wpdb->num_rows == 1) {
					glossary_delete_form($id, $entries[0]->term);
					return;
				}
			}
			echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'id' => 'null', 'message_type' => 'error', 'message' => 'Id nicht gültig.')).'">';
			exit;
		} else if ($action == 'force-delete') {
			if(is_numeric($id)) {
				$entries = $wpdb->get_results("SELECT * FROM $glossary_table_name WHERE id = $id");
				if($wpdb->num_rows == 1) {
					glossary_delete_entry($id);
					return;
				}
			}
			echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'id' => 'null', 'message_type' => 'error', 'message' => 'Id nicht gültig.')).'">';
			exit;
		}
	}
}

/**
 * Show entry form
 * 
 * Used for creating or editing entries
 * @param object  $entry the entry to change.
 * 									$entry->id;
 * 									$entry->term;
 * 									$entry->description;
 * @param string 	$errormessage the error to be
 * 								shown to the user.
 */
function glossary_entry_form($entry, $errormessage) {
		$term = '';
		$description = '';

		if (isset($_POST['term']) || isset($_POST['description'])) {
			if(isset($_POST['term'])) {
				$term = sanitize_text_field( $_POST['term'] );
			} 
			if(isset($_POST['description'])) {
				$description = sanitize_text_field( $_POST['description'] );
			}	
		} else if ($entry != null) {
			$term = $entry->term;
			$description = $entry->description;
		}
	?>
	<div class="wrap">
		<?php
			if ($errormessage != null) {
				echo '
					<div id="message" class="error">
						<p>'.$errormessage.'</p>
					</div>
				';
			}
		?>
		
		<?php
			if ($entry == null) {
				echo '
					<h1 class="add-entry">'.__('Glossar Eintrag hinzufügen').'</h1>
					<p>Legen Sie einen neuen Eintrag für den Glossar an.</p>
				';
			} else {
				echo '
					<h1 class="edit-entry">'.__('Glossar Eintrag bearbeiten').'</h1>
					<p>Bearbeiten Sie den Eintrag des Glossars.</p>
				';
			}
		?>
		<form id="entry_form" class="validate" method="post" name="entry_form" novalidate="novalidate">
			<?php
				if ($entry == null) {
					echo '
						<input name="action" type="hidden" value="create_glossary_entry">
					';
					wp_nonce_field( 'create_glossary_entry' );
				} else {
					echo '
						<input name="id" type="hidden" value="'.$entry->id.'">
						<input name="action" type="hidden" value="edit_glossary_entry">
					';
					wp_nonce_field( 'edit_glossary_entry' );
				}
			?>
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
							<input id="glossary_term" name="term" type="text" value="<?php echo $term; ?>" aria-required="true" autocapitalize="none" autocorrect="on" maxlenght="60">
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
							<textarea id="glossary_description" name="description" aria-required="true" autocapitalize="none" autocorrect="on"><?php echo $description; ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
			<?php
				if ($entry == null) {
					echo '
						<input id="entry_submit" class="button button-primary" type="submit" name="entry_form" value="Neuen Eintrag anlegen">
					';
				} else {
					echo '
						<input id="entry_submit" class="button button-primary" type="submit" name="entry_form" value="Eintrag speichern">
					';
				}
			?>
			</p>
		</form>
	</div>
	<?php
}

/**
 * Validate entry form
 * 
 * Used for validating the entry form
 * @return string $error the error message
 * 								if an error was found else
 * 								returns null
 */
function glossary_entry_form_check_errors() {
	global $wpdb;
	global $glossary_table_name;

	if (!isset($_POST['term']) || $_POST['term'] == '') {
		return 'Feld "Begriff" muss ausgefüllt werden.';
	}
	if (!isset($_POST['description']) || $_POST['description'] == '') {
		return 'Feld "Beschreibung" muss ausgefüllt werden.';
	}

	if (isset($_POST['id'])) {
		$id = $_POST['id'];
		if(is_numeric($id)) {
			$entry = $wpdb->get_results("SELECT * FROM $glossary_table_name WHERE id = $id");
			if($wpdb->num_rows == 1) {
				return;
			}
		}
		echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'id' => 'null', 'message_type' => 'error', 'message' => 'Id nicht gültig.')).'">';
		exit;
	}
}

/**
 * Show delete form
 * 
 * Used for deleting entries
 * @param string 	$id the id of the entry to be deleted
 * @param string 	$term the name of the entry
 */
function glossary_delete_form($id, $term) {
	?>
	<div class="wrap">
		<h1 class="delete-entry"><?php echo __('Glossar Eintrag löschen'); ?></h1>
		<p>Wollen Sie den Eintrag "<?php echo $term; ?>" wirklich löschen?</p>
		<a id="entry_delete" class="button button-primary" href="<?php echo generate_url(array('action' => 'force-delete', 'id' => $id)); ?>">Löschen</a>
	</div>
	<?php
}

/**
 * Delete the entry
 * 
 * Used for deleting entries
 * @param string 	$id the id of the entry to be deleted
 * @param string 	$term the name of the entry
 */
function glossary_delete_entry($id) {
	global $wpdb;
	global $glossary_table_name;
	// Default usage.
	$result = $wpdb->delete( $glossary_table_name, array( 'id' => $id ));
	if ($result === false) {
		echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'message_type' => 'error', 'message' => 'Datenbank fehler')).'">';
	} else {
		if ($result == 0) {
			echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'id' => 'null', 'message_type' => 'error', 'message' => 'Eintrag nicht gefunden.')).'">';
		} else {
			echo '<meta http-equiv="refresh" content="0; URL='.generate_url(array('action' => 'null', 'id' => 'null',  'message_type' => 'success', 'message' => 'Eintrag wurde gelöscht.')).'">';
		}
	}
}