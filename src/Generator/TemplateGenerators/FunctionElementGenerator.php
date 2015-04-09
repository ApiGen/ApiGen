<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Generator\StepCounter;
use ApiGen\Generator\TemplateGenerator;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceAndPackageLoader;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;


class FunctionElementGenerator implements TemplateGenerator, StepCounter
{

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
		foreach ($this->elementStorage->getFunctions() as $name => $reflectionFunction) {
			$template = $this->templateFactory->createForReflection($reflectionFunction);
			$template = $this->loadTemplateWithParameters($template, $reflectionFunction);
			$template->save();

			$this->eventDispatcher->dispatch(new GenerateProgressEvent(GeneratorEvents::ON_GENERATE_PROGRESS));
		}
	}


	/**
	 * @return int
	 */
	public function getStepCount()
	{
		return count($this->elementStorage->getFunctions());
	}


	/**
	 * @return Template
	 */
	private function loadTemplateWithParameters(Template $template, ReflectionFunction $function)
	{
		$template = $this->namespaceAndPackageLoader->loadTemplateWithElementNamespaceOrPackage($template, $function);
		$template->setParameters([
			'function' => $function
		]);
		return $template;
	}

}
