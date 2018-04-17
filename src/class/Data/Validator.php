<?php
namespace App\Data;

use Gt\Dom\Element;
use Gt\Input\InputData\InputData;

class Validator {
	protected $requiredFields = [];

	public function __construct(Element $form) {
		$this->findRequiredFields($form);
	}

	public function validate(InputData $data) {
		$this->validateRequired($data);
	}

	protected function findRequiredFields(Element $form) {
		foreach($form->querySelectorAll("[required]") as $required) {
			$name = $required->getAttribute("name");
			$this->requiredFields []= $name;
		}
	}

	protected function validateRequired(InputData $data):void {
		$requiredFieldsMissing = [];
		foreach($this->requiredFields as $requirement) {
			if(!$data->hasValue($requirement)) {
				$requiredFieldsMissing [] = $requirement;
			}
		}
		if(!empty($requiredFieldsMissing)) {
			throw new RequiredDataValidationException(
				"Not all required fields are present",
				...$requiredFieldsMissing
			);
		}
	}
}