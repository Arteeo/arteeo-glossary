<?php
/**
 * Message model
 *
 * @package arteeo\glossary
 */

namespace arteeo\glossary;

/**
 * Message
 *
 * Represents a message to be shown to the user.
 *
 * @since 1.0.0
 */
class Message {
	const SUCCESS = 'success';
	const ERROR   = 'error';

	/**
	 * The type of the message, default 'error'.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public string $type = self::ERROR;

	/**
	 * The content of the message, default ''.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public string $content = '';

	/**
	 * The Constructor of the message
	 *
	 * Used to initially set the values of the class variables.
	 *
	 * @since 1.0.0
	 * @param string $type    @see $type class variable.
	 * @param string $content @see $content class variable.
	 */
	public function __construct( string $type, string $content ) {
		$this->type    = $type;
		$this->content = $content;
	}
}
