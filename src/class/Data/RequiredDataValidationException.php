<?php
namespace App\Data;

class RequiredDataValidationException extends ValidationException {
	const VALIDATION_TYPE = "required";
}