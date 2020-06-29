<?php
namespace arteeo\glossary;

require_once __DIR__ . '/class-letter.php';

class Letters implements \IteratorAggregate {
	private array $letters;

	public function __construct( Letter ...$letters ) {
		$this->letters = $letters;
	}

	public function add( Letter $letter ) {
		array_push( $this->letters, $letter );
	}

	public function count() : int {
		$count = 0;
		foreach ( $this->letters as $letter ) {
			$count += $letter->count;
		}
		return $count;
	}

	public function getIterator() {
		return new \ArrayIterator( $this->letters );
	}
}
