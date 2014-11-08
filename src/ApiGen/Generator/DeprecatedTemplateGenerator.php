<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Elements\ElementFilter;
use ApiGen\Elements\Elements;
use ApiGen\Elements\ElementSorter;
use ApiGen\Elements\ElementStorage;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Templating\TemplateFactory;
use Nette;


class DeprecatedTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var ElementSorter
	 */
	private $elementSorter;

	/**
	 * @var ElementFilter
	 */
	private $elementFilter;

	/**
	 * @var Elements
	 */
	private $elements;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(
		ElementSorter $elementSorter,
		ElementFilter $elementFilter,
		ElementStorage $elementStorage,
		Elements $elements,
		TemplateFactory $templateFactory,
		Configuration $configuration
	) {
		$this->elementSorter = $elementSorter;
		$this->elementFilter = $elementFilter;
		$this->elementStorage = $elementStorage;
		$this->elements = $elements;
		$this->templateFactory = $templateFactory;
		$this->configuration = $configuration;
	}


	public function generate()
	{
		$template = $this->templateFactory->create('deprecated');
		$elements = $this->elements->getEmptyList();
		$elements[Elements::METHODS] = array();
		$elements[Elements::PROPERTIES] = array();

//		foreach (array_reverse($elements) as $type => $content) { // todo: what far array_reverse?
		foreach ($this->elements->getEmptyList() as $type => $content) {
			$foundElements = $this->elementStorage->getElementsByType($type);
			$elementsForMain = $this->elementFilter->filterForMain($foundElements);
			$elements[$type] = $this->elementFilter->filterByDeprecatedAnnotation($elementsForMain);

			if ($type === Elements::CONSTANTS || $type === Elements::FUNCTIONS) {
				continue;
			}

			foreach ($foundElements as $class) {
				/** @var ReflectionClass $class */
				if ( ! $class->isMain() || $class->isDeprecated()) {
					continue;
				}

				$classDeprecatedConstants = $this->elementFilter->filterByDeprecatedAnnotation($class->getOwnConstants());
				$elements[Elements::CONSTANTS] = array_merge($elements[Elements::CONSTANTS], $classDeprecatedConstants);

				$classDeprecatedProperties = $this->elementFilter->filterByDeprecatedAnnotation($class->getOwnProperties());
				$elements[Elements::PROPERTIES] = array_merge($elements[Elements::PROPERTIES], $classDeprecatedProperties);

				$classDeprecatedMethods = $this->elementFilter->filterByDeprecatedAnnotation($class->getOwnMethods());
				$elements[Elements::METHODS] = array_merge($elements[Elements::METHODS], $classDeprecatedMethods);
			}
		}

		$template->deprecatedClasses = $this->elementSorter->sortElementsByFqn($elements[Elements::CLASSES]);
		$template->deprecatedInterfaces = $this->elementSorter->sortElementsByFqn($elements[Elements::INTERFACES]);
		$template->deprecatedTraits = $this->elementSorter->sortElementsByFqn($elements[Elements::TRAITS]);
		$template->deprecatedExceptions = $this->elementSorter->sortElementsByFqn($elements[Elements::EXCEPTIONS]);

		$template->deprecatedConstants = $this->elementSorter->sortElementsByFqn($elements[Elements::CONSTANTS]);
		$template->deprecatedFunctions = $this->elementSorter->sortElementsByFqn($elements[Elements::FUNCTIONS]);
		$template->deprecatedMethods = $this->elementSorter->sortElementsByFqn($elements[Elements::METHODS]);
		$template->deprecatedProperties = $this->elementSorter->sortElementsByFqn($elements[Elements::PROPERTIES]);

		$template->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption('deprecated');
	}

}
