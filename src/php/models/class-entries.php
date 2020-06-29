<?php
namespace arteeo\glossary;

require_once __DIR__ . '/class-entry.php';

class Entries implements \IteratorAggregate {
	private array $entries;

	/**
	 * The Constructor of the entry
	 *
	 * Links the database with the entry.
	 *
	 * @since 1.0.0
	 * @param Entry ...$entries @see $entries class variable.
	 */
	public function __construct( Entry ...$entries ) {
		$this->entries = $entries;
	}

	public function add( Entry $entry ) {
		array_push( $this->entries, $entry );
	}

	public function count() {
		return count( $this->entries );
	}

	public function getIterator() {
		return new \ArrayIterator( $this->entries );
	}
}

