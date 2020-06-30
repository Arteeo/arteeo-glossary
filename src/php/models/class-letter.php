<?php
/**
 * Letter model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

require_once __DIR__ . '/../db/class-glossary-db.php';

/**
 * Letter
 *
 * Represents one letter inside the glossary.
 *
 * @since 1.0.0
 */
class Letter {
	/**
	 * The letter itself.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $letter;

	/**
	 * How many entries to this letter are int the database.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public int $count;

	/**
	 * The Constructor of the letter
	 *
	 * Checks if the letter is valid.
	 *
	 * @since 1.0.0
	 * @param string $letter The letter which should be used.
	 * @param int    $count  How many terms with this letter there are.
	 * @throws \InvalidArgumentException If the letter is not valid.
	 */
	public function __construct( string $letter, int $count ) {
		if ( 1 !== strlen( $letter ) || ( ! ctype_alpha( $letter ) && '#' !== $letter ) ) {
			throw new \InvalidArgumentException( 'The given letter ' . $letter . ' is not valid.' );
		} else {
			$this->letter = $letter;
			$this->count  = $count;
		}
	}

	/**
	 * Convert object
	 *
	 * Converts an object into a letter.
	 *
	 * @since 1.0.0
	 * @param object $object The object to be converted.
	 * @return Letter Returns the entry generated from the object.
	 *
	 * @throws \InvalidArgumentException If the object given is no entry.
	 */
	public static function from_object( $object ) : Letter {
		if ( isset( $object->letter, $object->count ) ) {
			try {
				$letter = new Letter( $object->letter, $object->count );
				return $letter;
			} catch ( \Exception $e ) {
				// Is handled below.
			}
		}

		throw new \InvalidArgumentException( 'The given object is no letter.' );
	}
}
