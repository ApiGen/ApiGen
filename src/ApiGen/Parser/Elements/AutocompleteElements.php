<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Reflection\ReflectionBase;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;


class AutocompleteElements
{

	const CLASS_CONSTANTS = 'classconstants';

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var array
	 */
	private $elements = [];


	public function __construct(Configuration $configuration, ElementStorage $elementStorage)
	{
		$this->configuration = $configuration;
		$this->elementStorage = $elementStorage;
	}


	/**
	 * @return array
	 */
	public function getElements()
	{
		foreach ($this->elementStorage->getElements() as $type => $elementList) {
			foreach ($elementList as $element) {
				$this->processElement($element);
			}
		}

		$this->sortElements();

		return $this->elements;
	}


	private function sortElements()
	{
		usort($this->elements, function ($one, $two) {
			return strcasecmp($one[1], $two[1]);
		});
	}


	private function processElement(ReflectionBase $element)
	{
		$autocomplete = $this->configuration->getOption(CO::AUTOCOMPLETE);

		if ($element instanceof ReflectionClass) {
			$this->addClassElements($element);

		} elseif ($element instanceof ReflectionConstant && isset($autocomplete[Elements::CONSTANTS])) {
			$this->elements[] = ['co', $element->getPrettyName()];

		} elseif ($element instanceof ReflectionFunction && isset($autocomplete[Elements::FUNCTIONS])) {
			$this->elements[] = ['f', $element->getPrettyName()];
		}
	}


	private function addClassElements(ReflectionClass $element)
	{
		$autocompleteOption = $this->getAutocompleteOption();
		if (isset($autocompleteOption[Elements::CLASSES])) {
			$this->elements[] = ['c', $element->getPrettyName()];
		}

		if (isset($autocompleteOption[Elements::METHODS])) {
			foreach ($element->getOwnMethods() as $method) {
				$this->elements[] = ['m', $method->getPrettyName()];
			}
			foreach ($element->getOwnMagicMethods() as $method) {
				$this->elements[] = ['mm', $method->getPrettyName()];
			}
		}

		if (isset($autocompleteOption[Elements::PROPERTIES])) {
			foreach ($element->getOwnProperties() as $property) {
				$this->elements[] = ['p', $property->getPrettyName()];
			}
			foreach ($element->getOwnMagicProperties() as $property) {
				$this->elements[] = ['mp', $property->getPrettyName()];
			}
		}

		if (isset($autocompleteOption[self::CLASS_CONSTANTS])) {
			foreach ($element->getOwnConstants() as $constant) {
				$this->elements[] = ['cc', $constant->getPrettyName()];
			}
		}
	}


	/**
	 * @return string
	 */
	private function getAutocompleteOption()
	{
		return $this->configuration->getOption(CO::AUTOCOMPLETE);
	}

}
