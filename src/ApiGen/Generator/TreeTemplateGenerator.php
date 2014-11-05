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
use ApiGen\Parser\ParserStorage;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Tree;
use Nette;


class TreeTemplateGenerator extends Nette\Object implements TemplateGenerator
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
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var ParserStorage
	 */
	private $parserStorage;


	public function __construct(
		ElementSorter $elementSorter,
		ElementFilter $elementFilter,
		ElementStorage $elementStorage,
		Elements $elements,
		TemplateFactory $templateFactory,
		Configuration $configuration,
		ParserStorage $parserStorage
	) {
		$this->elementSorter = $elementSorter;
		$this->elementFilter = $elementFilter;
		$this->elements = $elements;
		$this->templateFactory = $templateFactory;
		$this->configuration = $configuration;
		$this->elementStorage = $elementStorage;
		$this->parserStorage = $parserStorage;
	}


	public function generate()
	{
		$template = $this->templateFactory->create('tree');

		$classTree = array();
		$interfaceTree = array();
		$traitTree = array();
		$exceptionTree = array();

		$processed = array();

		$classes = $this->parserStorage->getElementsByType(Elements::CLASSES);
		foreach ($classes as $className => $reflection) {
			if ( ! $reflection->isMain() || ! $reflection->isDocumented() || isset($processed[$className])) {
				continue;
			}

			/** @var ReflectionClass $reflection */
			if ($reflection->getParentClassName() === NULL) {
				// No parent classes
				if ($reflection->isInterface()) {
					$t = &$interfaceTree;

				} elseif ($reflection->isTrait()) {
					$t = &$traitTree;

				} elseif ($reflection->isException()) {
					$t = &$exceptionTree;

				} else {
					$t = &$classTree;
				}

			} else {
				foreach (array_values(array_reverse($reflection->getParentClasses())) as $level => $parent) {
					if ($level === 0) {
						// The topmost parent decides about the reflection type
						/** @var ReflectionClass $parent */
						if ($parent->isInterface()) {
							$t = &$interfaceTree;

						} elseif ($parent->isTrait()) {
							$t = &$traitTree;

						} elseif ($parent->isException()) {
							$t = &$exceptionTree;

						} else {
							$t = &$classTree;
						}
					}
					$parentName = $parent->getName();

					if ( ! isset($t[$parentName])) {
						$t[$parentName] = array();
						$processed[$parentName] = TRUE;
						ksort($t, SORT_STRING);
					}

					$t = &$t[$parentName];
				}
			}

			$t[$className] = array();
			ksort($t, SORT_STRING);
			$processed[$className] = TRUE;
			unset($t);
		}

		$template->classTree = new Tree($classTree, $classes);
		$template->interfaceTree = new Tree($interfaceTree, $classes);
		$template->traitTree = new Tree($traitTree, $classes);
		$template->exceptionTree = new Tree($exceptionTree, $classes);

		$template->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption('tree');
	}

}
