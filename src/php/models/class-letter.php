<?php

namespace arteeo\glossary;

require_once __DIR__ . '/../db/class-glossary-db.php';

class Letter{
	public string $letter;
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

	public static function from_object( $object ) : Letter {
		if ( isset( $object->letter, $object->count ) ) {
			$letter = new Letter( $object->letter, $object->count );
			return $letter;
		}

		throw new \InvalidArgumentException( 'The given object is no letter.' );
	}
}
