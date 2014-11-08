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
use ApiGen\Elements\ElementStorage;
use ApiGen\Elements\ElementSorter;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Templating\TemplateFactory;
use Nette;


class TodoTemplateGenerator extends Nette\Object implements TemplateGenerator
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
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var Elements
	 */
	private $elements;

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
		$this->templateFactory = $templateFactory;
		$this->elements = $elements;
		$this->configuration = $configuration;
	}


	public function generate()
	{
		$template = $this->templateFactory->create('todo');
		$elements = $this->elements->getEmptyList();
		$elements[Elements::METHODS] = array();
		$elements[Elements::PROPERTIES] = array();
		foreach ($this->elementStorage->getElements() as $type => $elementList) {
			// this repeats!
			$elementsForMain = $this->elementFilter->filterForMain($elementList);
			$elements[$type] = $this->elementFilter->filterByAnnotation($elementsForMain, 'todo');

			if ($type === Elements::CONSTANTS || $type === Elements::FUNCTIONS) {
				continue;
			}

			foreach ($elementList as $class) {
				/** @var ReflectionClass $class */
				if ( ! $class->isMain()) {
					continue;
				}

				// this repeats, only annotation has changed!
				$classTodoMethods = $this->elementFilter->filterByAnnotation($class->getOwnMethods(), 'todo');
				$elements[Elements::METHODS] = array_merge($elements[Elements::METHODS], array_values($classTodoMethods));

				$classTodoConstants = $this->elementFilter->filterByAnnotation($class->getOwnConstants(), 'todo');
				$elements[Elements::CONSTANTS] = array_merge($elements[Elements::CONSTANTS], array_values($classTodoConstants));

				$classTodoProperties = $this->elementFilter->filterByAnnotation($class->getOwnProperties(), 'todo');
				$elements[Elements::PROPERTIES] = array_merge($elements[Elements::PROPERTIES], array_values($classTodoProperties));
			}
		}

		$template->todoClasses = $this->elementSorter->sortElementsByFqn($elements[Elements::CLASSES]);
		$template->todoInterfaces = $this->elementSorter->sortElementsByFqn($elements[Elements::INTERFACES]);
		$template->todoTraits = $this->elementSorter->sortElementsByFqn($elements[Elements::TRAITS]);
		$template->todoExceptions = $this->elementSorter->sortElementsByFqn($elements[Elements::EXCEPTIONS]);

		$template->todoConstants = $this->elementSorter->sortElementsByFqn($elements[Elements::CONSTANTS]);
		$template->todoMethods = $this->elementSorter->sortElementsByFqn($elements[Elements::METHODS]);
		$template->todoFunctions = $this->elementSorter->sortElementsByFqn($elements[Elements::FUNCTIONS]);
		$template->todoProperties = $this->elementSorter->sortElementsByFqn($elements[Elements::PROPERTIES]);

		$template->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption('todo');
	}

}
