<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Generator\ConditionalTemplateGenerator;
use ApiGen\Parser\Elements\ElementFilter;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Parser\Elements\ElementSorter;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;


/**
 * @todo
 */
class TodoGenerator implements ConditionalTemplateGenerator
{

	const TODO = 'todo';

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var Elements
	 */
	private $elements;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var ElementFilter
	 */
	private $elementFilter;

	/**
	 * @var ElementSorter
	 */
	private $elementSorter;


	public function __construct(
		Configuration $configuration,
		TemplateFactory $templateFactory,
		Elements $elements,
		ElementFilter $elementFilter,
		ElementSorter $elementSorter,
		ElementStorage $elementStorage
	) {
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
		$this->elements = $elements;
		$this->elementStorage = $elementStorage;
		$this->elementFilter = $elementFilter;
		$this->elementSorter = $elementSorter;
	}


	public function generate()
	{
		$template = $this->templateFactory->createForType(TCO::TODO);
		$template = $this->setTodoElementToTemplate($template);
		$template->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption(CO::TODO);
	}


	/**
	 * @return Template
	 */
	private function setTodoElementToTemplate(Template $template)
	{
		$elements = $this->elements->getEmptyList();
		$elements[Elements::METHODS] = array();
		$elements[Elements::PROPERTIES] = array();
		foreach ($this->elementStorage->getElements() as $type => $elementList) {
			// this repeats!
			$elementsForMain = $this->elementFilter->filterForMain($elementList);
			$elements[$type] = $this->elementFilter->filterByAnnotation($elementsForMain, self::TODO);
			if ($type === Elements::CONSTANTS || $type === Elements::FUNCTIONS) {
				continue;
			}
			foreach ($elementList as $class) {
				/** @var ReflectionClass $class */
				if ( ! $class->isMain()) {
					continue;
				}

				// this repeats, only annotation has changed!
				$classTodoMethods = $this->elementFilter->filterByAnnotation($class->getOwnMethods(), self::TODO);
				$elements[Elements::METHODS] = array_merge($elements[Elements::METHODS], array_values($classTodoMethods));
				$classTodoConstants = $this->elementFilter->filterByAnnotation($class->getOwnConstants(), self::TODO);
				$elements[Elements::CONSTANTS] = array_merge($elements[Elements::CONSTANTS], array_values($classTodoConstants));
				$classTodoProperties = $this->elementFilter->filterByAnnotation($class->getOwnProperties(), self::TODO);
				$elements[Elements::PROPERTIES] = array_merge($elements[Elements::PROPERTIES], array_values($classTodoProperties));
			}
		}

		$template->setParameters([
			'todoClasses' => $this->elementSorter->sortElementsByFqn($elements[Elements::CLASSES]),
			'todoInterfaces' => $this->elementSorter->sortElementsByFqn($elements[Elements::INTERFACES]),
			'todoTraits' => $this->elementSorter->sortElementsByFqn($elements[Elements::TRAITS]),
			'todoExceptions' => $this->elementSorter->sortElementsByFqn($elements[Elements::EXCEPTIONS]),
			'todoConstants' => $this->elementSorter->sortElementsByFqn($elements[Elements::CONSTANTS]),
			'todoMethods' => $this->elementSorter->sortElementsByFqn($elements[Elements::METHODS]),
			'todoFunctions' => $this->elementSorter->sortElementsByFqn($elements[Elements::FUNCTIONS]),
			'todoProperties' => $this->elementSorter->sortElementsByFqn($elements[Elements::PROPERTIES])
		]);

		return $template;
	}

}
