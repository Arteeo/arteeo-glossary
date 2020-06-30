<?php
/**
 * Entry model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

require_once __DIR__ . '/../db/class-glossary-db.php';
require_once __DIR__ . '/../helper/class-helpers.php';

/**
 * Entry
 *
 * Represents one entry inside the glossary.
 *
 * @since 1.0.0
 */
class Entry {
	/**
	 * The db to be used for the entry.
	 *
	 * @since 1.0.0
	 * @var Glossary_DB
	 */
	private Glossary_DB $db;

	/**
	 * The id of the entry, default null.
	 *
	 * @since 1.0.0
	 * @var ?int
	 */
	public ?int $id = null;

	/**
	 * The letter of the entry, default ''.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $letter = '';

	/**
	 * The term of the entry, default ''.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $term = '';

	/**
	 * The description of the entry, default ''.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $description = '';

	/**
	 * The locale of the entry, default ''.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $locale = '';

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

	/**
	 * Save the entry
	 *
	 * Insert or updates the entry within the database depending on if the id is set.
	 *
	 * @since 1.0.0
	 * @return int Returns the number of rows updated or -1 if an error occured.
	 */
	public function save() : int {
		$result = -1;

		if ( null === $this->id ) {
			$result = $this->db->insert_entry( $this );
		} else {
			$result = $this->db->update_entry( $this );
		}

		return $result;
	}

	/**
	 * Delete the entry
	 *
	 * Removes the entry from the database.
	 *
	 * @since 1.0.0
	 * @return int Returns the number of rows updated or -1 if an error occured.
	 */
	public function delete() : int {
		if ( null === $this->id ) {
			return -1;
		} else {
			return $this->db->delete_entry( $this );
		}
	}

	/**
	 * Convert object
	 *
	 * Converts an object into an entry.
	 *
	 * @since 1.0.0
	 * @param object      $object The object to be converted.
	 * @param Glossary_DB $db The database to associate the entry with.
	 * @return Entry Returns the entry generated from the object.
	 *
	 * @throws \InvalidArgumentException If the object given is no entry.
	 */
	public static function from_object( $object, Glossary_DB $db ) : Entry {
		if ( isset( $object->id, $object->letter, $object->term, $object->description, $object->locale ) ) {
			try {
				$entry              = new Entry( $db );
				$entry->id          = $object->id;
				$entry->letter      = $object->letter;
				$entry->term        = $object->term;
				$entry->description = $object->description;
				$entry->locale      = $object->locale;

				return $entry;
			} catch ( \Exception $e ) {
				// Is handled below.
			}
		}

		throw new \InvalidArgumentException( 'The given object is no entry.' );
	}
}
