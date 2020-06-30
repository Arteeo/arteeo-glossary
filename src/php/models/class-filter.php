<?php
/**
 * Filter model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

/**
 * Filter
 *
 * Holds the filter options of the admin table.
 *
 * @since 1.0.0
 */
class Filter {
	/**
	 * The locale which should be filtered, default null.
	 *
	 * @since 1.0.0
	 * @var ?string
	 */
	public ?string $locale = null;

	/**
	 * The letter which should be filtered, default null.
	 *
	 * @since 1.0.0
	 * @var ?string
	 */
	public ?string $letter = null;

	/**
	 * The sorting which should be used, default ''.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $sorting = '';
}
