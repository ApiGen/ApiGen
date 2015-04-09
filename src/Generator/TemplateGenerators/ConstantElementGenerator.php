<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Generator\StepCounter;
use ApiGen\Generator\TemplateGenerator;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceAndPackageLoader;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;


class ConstantElementGenerator implements TemplateGenerator, StepCounter
{

	/**
	 * @var array
	 */
	public $onGenerateProgress = [];

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var NamespaceAndPackageLoader
	 */
	private $namespaceAndPackageLoader;

	/**
	 * @var EventDispatcherInterface
	 */
	private $eventDispatcher;


	public function __construct(
		TemplateFactory $templateFactory,
		ElementStorage $elementStorage,
		NamespaceAndPackageLoader $namespaceAndPackageLoader,
		EventDispatcherInterface $eventDispatcher
	) {
		$this->templateFactory = $templateFactory;
		$this->elementStorage = $elementStorage;
		$this->namespaceAndPackageLoader = $namespaceAndPackageLoader;
		$this->eventDispatcher = $eventDispatcher;
	}


	public function generate()
	{
		foreach ($this->elementStorage->getConstants() as $name => $reflectionConstant) {
			$template = $this->templateFactory->createForReflection($reflectionConstant);
			$template = $this->loadTemplateWithParameters($template, $reflectionConstant);
			$template->save();

			$this->eventDispatcher->dispatch(new GenerateProgressEvent(GeneratorEvents::ON_GENERATE_PROGRESS));
		}
	}


	/**
	 * @return int
	 */
	public function getStepCount()
	{
		return count($this->elementStorage->getConstants());
	}


	/**
	 * @return Template
	 */
	private function loadTemplateWithParameters(Template $template, ConstantReflectionInterface $constant)
	{
		$template = $this->namespaceAndPackageLoader->loadTemplateWithElementNamespaceOrPackage($template, $constant);
		$template->setParameters([
			'constant' => $constant
		]);
		return $template;
	}

}
