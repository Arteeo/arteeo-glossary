<?php
/**
 * Letters model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

require_once __DIR__ . '/class-letter.php';

/**
 * Collection of letters
 *
 * Contains a collection of letters.
 *
 * @since 1.0.0
 */
class Letters implements \IteratorAggregate {
	/**
	 * The array to store the letters.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private array $letters;

	/**
	 * The Constructor of the collection
	 *
	 * May be used to initially add some letters to the collection.
	 *
	 * @since 1.0.0
	 * @param Letter ...$letters Multiple letter objects can be given to the constructor and will then be added to the
	 *                           internal array.
	 */
	public function __construct( Letter ...$letters ) {
		$this->letters = $letters;
	}

	/**
	 * Add letter
	 *
	 * Adds a letter to the internal array.
	 *
	 * @since 1.0.0
	 * @param Letter $letter the letter to add to the array.
	 */
	public function add( Letter $letter ) {
		array_push( $this->letters, $letter );
	}

	/**
	 * Entry count
	 *
	 * Returns the count of how many entries are covered by the letters in the collection by adding the counts of all
	 * letters.
	 *
	 * @since 1.0.0
	 * @return int Returns the entries count.
	 */
	public function count() : int {
		$count = 0;
		foreach ( $this->letters as $letter ) {
			$count += $letter->count;
		}
		return $count;
	}

	/**
	 * Get iterator
	 *
	 * Returns iterator of the internal array. This is a function of the \IteratorAggregate interface and is used to
	 * enable foreach operations on this collection.
	 *
	 * @since 1.0.0
	 * @return \ArrayIterator Returns the iterator of the internal array.
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->letters );
	}

	/**
	 * Unset letter
	 *
	 * Can be used to unset a letter inside the internal array.
	 *
	 * @since 1.0.0
	 * @param int $key The key of the letter to be unset.
	 */
	public function unset( int $key ) {
		unset( $this->letters[ $key ] );
	}

	/**
	 * Check last
	 *
	 * Checks if the given key belongs to the last letter inside of the internal array.
	 *
	 * @since 1.0.0
	 * @param int $key The key of the letter to be checked.
	 * @return bool Returns true if letter is the last in the array, false otherwise.
	 */
	public function is_last( int $key ) : bool {
		return ( array_key_last( $this->letters ) === $key );
	}
}
