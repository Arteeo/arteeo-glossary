<?php
/**
 * Glossary entry model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

class Glossary_Entry{
	public ?int $id            = null;
	public string $letter      = '';
	public string $term        = '';
	public string $description = '';
	public string $locale      = '';
}