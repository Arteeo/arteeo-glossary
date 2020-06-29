<?php

namespace arteeo\glossary;

class Message{
	const SUCCESS = 'success';
	const ERROR   = 'error';

	public string $type    = '';
	public string $content = '';

	public function __construct( string $type, string $content ) {
		$this->type    = $type;
		$this->content = $content;
	}
}