<?php
namespace App\Data;

use RuntimeException;

abstract class ValidationException extends RuntimeException {
	const VALIDATION_TYPE = "invalid";

	/** @var string[] */
	protected $fields;

	public function __construct(string $message, string...$fields) {
		$this->fields = $fields;
		parent::__construct($message);
	}

	public function getInvalidFields():array {
		return $this->fields;
	}

	public function getValidationType():string {
		return static::VALIDATION_TYPE;
	}
}