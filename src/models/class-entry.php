<?php
/**
 * Glossary entry model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

require_once __DIR__ . '/../db/class-glossary-db.php';

class Entry{
	private Glossary_DB $db;

	public ?int $id            = null;
	public string $letter      = '';
	public string $term        = '';
	public string $description = '';
	public string $locale      = '';

	/**
	 * The Constructor of the entry
	 *
	 * Links the database with the entry.
	 *
	 * @since 1.0.0
	 * @param Glossary_DB $db @see $db class variable.
	 */
	public function __construct( Glossary_DB $db ) {
		$this->db = $db;
	}

	public function save() {
		$result = false;

		if ( null === $this->id ) {
			$result = $this->db->insert_entry( $this );
		} else {
			$result = $this->db->update_entry( $this );
		}

		return $result;
	}

	public function delete() {
		if ( null === $this->id ) {
			return false;
		} else {
			return $this->db->delete_entry( $this );
		}
	}

	public static function from_object( $object, Glossary_DB $db ) : Entry {
		if ( isset( $object->id, $object->letter, $object->term, $object->description, $object->locale ) ) {
			$entry              = new Entry( $db );
			$entry->id          = $object->id;
			$entry->letter      = $object->letter;
			$entry->term        = $object->term;
			$entry->description = $object->description;
			$entry->locale      = $object->locale;

			return $entry;
		}

		throw new \InvalidArgumentException( 'The given object is no entry.' );
	}
}
