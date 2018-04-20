<?php
namespace App\Page;

use App\Data\RequiredDataValidationException;
use App\Data\ValidationException;
use App\Data\Validator;
use Gt\Dom\NodeList;
use Gt\Dom\Element;
use Gt\Input\InputData\InputData;

class IndexPage extends \Gt\WebEngine\Logic\Page {
	const INVALID_MESSAGE = [
		"required" => "Please fill out this field",
	];

	public function go() {
		$form = $this->document->getElementById("example-form");
		$sectionList = $form->querySelectorAll("section");

		$this->hideSections(
			$sectionList,
			$this->session->get("section") ?? 0
		);
		$this->setSectionStepCounts($sectionList);

		$this->input->do("submit")
			->call([$this, "handleSubmit"], $form);

		$this->populateForm($form);
		$this->markRequiredFields($form);
	}

	public function handleSubmit(InputData $data, Element $form):void {
		$validator = new Validator($form);
		$sectionIndex = $this->session->get("section") ?? 0;
		$this->persistData($data);

		try {
			$validator->validate($data);
		}
		catch(ValidationException $exception) {
			foreach($exception->getInvalidFields() as $field) {
				$this->markInvalidField(
					$form,
					$field,
					$exception->getValidationType()
				);
			}

			return;
		}

		$this->session->set("section", $sectionIndex + 1);
		$this->reload();
	}

	protected function reload():void {
		header("Location: " . (string)$this->serverInfo->getRequestUri());
		exit;
	}

	protected function hideSections(NodeList $sectionList, int $activeIndex) {
		foreach($sectionList as $i => $section) {
			if($activeIndex !== $i) {
				$section->remove();
			}
		}
	}

	protected function setSectionStepCounts(NodeList $sectionList) {
		foreach($sectionList as $i => $section) {
			$section->setAttribute(
				"data-section-step",
				$i + 1
			);
			$section->setAttribute(
				"data-section-step-count",
				count($sectionList)
			);
		}
	}

	protected function markInvalidField(Element $form, string $field, string $type) {
		$element = $form->querySelector("[name='$field']");
		$element->classList->add("invalid");

		$invalidMessage = $form->ownerDocument->createElement("span");
		$invalidMessage->classList->add("invalid-message");
		$invalidMessage->innerText = self::INVALID_MESSAGE[$type];
		$element->after($invalidMessage);
	}

	protected function populateForm(Element $form) {
		$data = $this->session->get("section-data") ?? [];

		foreach($data as $key => $value) {
			$element = $form->querySelector("[name='$key']");
			if(!$element) {
				continue;
			}

			$element->value = $value;
		}
	}

	protected function persistData(InputData $data):void {
		$sectionData = $this->session->get("section-data") ?? [];

		foreach($data as $key => $value) {
			$sectionData[$key] = $value;
		}

		$this->session->set("section-data", $sectionData);
	}

	protected function markRequiredFields(Element $form):void {
		foreach($form->querySelectorAll("[required]") as $requiredField) {
			$requiredField->closest("label")->classList->add("required");
		}
	}
}