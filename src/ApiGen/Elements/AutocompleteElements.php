<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Elements;

use ApiGen\Configuration\Configuration;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
use Nette;


class AutocompleteElements extends Nette\Object
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;


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
		$elements = array();
		$autocomplete = $this->configuration->getOption('autocomplete');
		foreach ($this->elementStorage->getElements() as $type => $elementList) {
			foreach ($elementList as $element) {
				if ($element instanceof ReflectionClass) {
					/** @var ReflectionClass $element */
					if (isset($autocomplete['classes'])) {
						$elements[] = array('c', $element->getPrettyName());
					}
					if (isset($autocomplete['methods'])) {
						foreach ($element->getOwnMethods() as $method) {
							$elements[] = array('m', $method->getPrettyName());
						}
						foreach ($element->getOwnMagicMethods() as $method) {
							$elements[] = array('mm', $method->getPrettyName());
						}
					}
					if (isset($autocomplete['properties'])) {
						foreach ($element->getOwnProperties() as $property) {
							$elements[] = array('p', $property->getPrettyName());
						}
						foreach ($element->getOwnMagicProperties() as $property) {
							$elements[] = array('mp', $property->getPrettyName());
						}
					}
					if (isset($autocomplete['classconstants'])) {
						foreach ($element->getOwnConstants() as $constant) {
							$elements[] = array('cc', $constant->getPrettyName());
						}
					}

				} elseif ($element instanceof ReflectionConstant && isset($autocomplete['constants'])) {
					$elements[] = array('co', $element->getPrettyName());

				} elseif ($element instanceof ReflectionFunction && isset($autocomplete['functions'])) {
					$elements[] = array('f', $element->getPrettyName());
				}
			}
		}

		$elements = $this->sortElements($elements);

		return $elements;
	}


	/**
	 * @param array $elements
	 * @return array
	 */
	private function sortElements($elements)
	{
		usort($elements, function ($one, $two) {
			return strcasecmp($one[1], $two[1]);
		});
		return $elements;
	}

}
