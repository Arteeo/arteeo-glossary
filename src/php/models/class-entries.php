<?php
/**
 * Entries model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

require_once __DIR__ . '/class-entry.php';

/**
 * Collection of entries
 *
 * Contains a collection of entries.
 *
 * @since 1.0.0
 */
class Entries implements \IteratorAggregate {
	/**
	 * The array to store the entries.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private array $entries;

	/**
	 * The Constructor of the collection
	 *
	 * May be used to initially add some entries to the collection.
	 *
	 * @since 1.0.0
	 * @param Entry ...$entries Multiple entry objects can be given to the constructor and will then be added to the
	 *                          internal array.
	 */
	public function __construct( Entry ...$entries ) {
		$this->entries = $entries;
	}

	/**
	 * Add entry
	 *
	 * Adds an entry to the internal array.
	 *
	 * @since 1.0.0
	 * @param Entry $entry the entry to add to the array.
	 */
	public function add( Entry $entry ) {
		array_push( $this->entries, $entry );
	}

	/**
	 * Entry count
	 *
	 * Returns the count of how many entries are inside the collection.
	 *
	 * @since 1.0.0
	 * @return int Returns the entry-count.
	 */
	public function count() : int {
		return count( $this->entries );
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
	public function getIterator() : \ArrayIterator {
		return new \ArrayIterator( $this->entries );
	}

	/**
	 * Get array
	 *
	 * Get a clone of the internal array
	 *
	 * @since 1.0.0
	 * @return array Returns a clone of the internal array.
	 */
	public function get_array() : array {
		$copy = $this->entries;
		return $copy;
	}
}

