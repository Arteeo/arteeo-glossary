<?php
/**
 * Migrations model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

require_once __DIR__ . '/interface-migration.php';

/**
 * Collection of migrations
 *
 * Contains a sorted collection of migrations.
 *
 * @since 1.0.0
 */
class Migrations implements \IteratorAggregate {
	/**
	 * The array to store the migrations.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private array $migrations;

	/**
	 * The timestamp of the newest migration for use within update checks.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private int $latest_timestamp = 0;

	/**
	 * The Constructor of the collection
	 *
	 * May be used to initially add some entries to the collection.
	 *
	 * @since 1.0.0
	 * @param Migration ...$migrations Multiple migration objects can be given to the constructor and will then be
	 *                                 added to the internal array.
	 */
	public function __construct( Migration ...$migrations ) {
		$this->migrations = $migrations;

		foreach ( $this->migrations as $migration ) {
			if ( $this->latest_timestamp < $migration->get_timestamp() ) {
				$this->latest_timestamp = $migration->get_timestamp();
			}
		}
	}

	/**
	 * Add migration
	 *
	 * Adds a migration to the internal array and sorts the array.
	 *
	 * @since 1.0.0
	 * @param Migration $migration the migration to add to the array.
	 */
	public function add( Migration $migration ) {
		array_push( $this->migrations, $migration );
		usort( $this->migrations, 'self::sort' );
		if ( $this->latest_timestamp < $migration->get_timestamp() ) {
			$this->latest_timestamp = $migration->get_timestamp();
		}
	}

	/**
	 * Migration count
	 *
	 * Returns the count of how many migrations are inside the collection.
	 *
	 * @since 1.0.0
	 * @return int Returns the migration-count.
	 */
	public function count() : int {
		return count( $this->migrations );
	}

	/**
	 * Return latest
	 *
	 * Returns the timestamp of the newest migration inside the collection. This can be used to check for updates.
	 *
	 * @since 1.0.0
	 * @return int Timestamp of the newest migration,
	 */
	public function get_latest_timestamp() : int {
		return $this->latest_timestamp;
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
		return new \ArrayIterator( $this->migrations );
	}

	/**
	 * Get reverse iterator
	 *
	 * Returns iterator of the internal array in reversed order.
	 *
	 * @since 1.0.0
	 * @return \ArrayIterator Returns the iterator of the reversed internal array.
	 */
	public function get_reverse_iterator() : \ArrayIterator {
		return new \ArrayIterator( array_reverse( $this->migrations ) );
	}

	/**
	 * Sort internal array
	 *
	 * Sort function to be used with usort(). Sorts the migrations by timestamp.
	 *
	 * @since 1.0.0
	 * @param Migration $a First migration to be compared.
	 * @param Migration $b Second migration to be compared.
	 * @return int The result of the current comparison.
	 */
	private static function sort( Migration $a, Migration $b ) : int {
		if ( $a->get_timestamp() === $b->get_timestamp() ) {
			return 0;
		}
		return ( $a->get_timestamp() < $b->get_timestamp() ) ? -1 : 1;
	}
}

