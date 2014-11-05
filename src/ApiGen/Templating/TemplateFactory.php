<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\TemplateConfiguration;
use ApiGen\Elements\AutocompleteElements;
use ApiGen\Elements\Elements;
use ApiGen\Elements\ElementStorage;
use ApiGen\FileSystem\Zip;
use ApiGen\Reflection\ReflectionElement;
use Latte;
use Nette;
use Nette\Utils\ArrayHash;
use stdClass;


class TemplateFactory extends Nette\Object
{

	/**
	 * @var Latte\Engine
	 */
	private $latteEngine;

	/**
	 * @var TemplateNavigator
	 */
	private $templateNavigator;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var Zip
	 */
	private $zip;

	/**
	 * @var AutocompleteElements
	 */
	private $autocompleteElements;


	public function __construct(
		Latte\Engine $latteEngine,
		TemplateNavigator $templateNavigator,
		Configuration $configuration,
		ElementStorage $elementStorage,
		Zip $zip,
		AutocompleteElements $autocompleteElements
	) {
		$this->latteEngine = $latteEngine;
		$this->templateNavigator = $templateNavigator;
		$this->configuration = $configuration;
		$this->elementStorage = $elementStorage;
		$this->zip = $zip;
		$this->autocompleteElements = $autocompleteElements;
	}


//	/**
//	 * @return Template|stdClass
//	 */
//	public function create()
//	{
//		return $this->buildTemplate();
//	}


	/**
	 * @param string $name
	 * @return Template|stdClass
	 */
	public function create($name = NULL)
	{
		$template = $this->buildTemplate();
		if ($name) {
			$template->setFile($this->templateNavigator->getTemplatePath($name));
			$template->setSavePath($this->templateNavigator->getTemplateFileName($name));
		}
		return $template;
	}


	/**
	 * @param string $name
	 * @param ReflectionElement|string $element
	 * @return Template|stdClass
	 */
	public function createNamedForElement($name, $element)
	{
		$template = $this->buildTemplate();
		$template->setFile($this->templateNavigator->getTemplatePath($name));

		if ($name === 'source') {
			$template->setSavePath($this->templateNavigator->getTemplatePathForSourceElement($element));

		} elseif ($name === 'class') {
			$template->setSavePath($this->templateNavigator->getTemplatePathForClass($element));

		} elseif ($name === 'constant') {
			$template->setSavePath($this->templateNavigator->getTemplatePathForConstant($element));

		} elseif ($name === 'function') {
			$template->setSavePath($this->templateNavigator->getTemplatePathForFunction($element));
		}

		return $template;
	}


	/**
	 * @return Template|stdClass
	 */
	private function buildTemplate()
	{
		/** @var Template|stdClass $template */
		$template = new Template($this->latteEngine);
		$template->config = ArrayHash::from($this->configuration->getOptions());
		$options = $this->configuration->getOptions();
		$template->basePath = $options['template']['templatesPath'];

		$template->getLatte()->addFilter('url', 'rawurlencode');
		$template = $this->setBaseVariables($template);

		return $template;
	}


	/**
	 * @param Template|stdClass $template
	 * @return Template
	 */
	private function setBaseVariables(Template $template)
	{
		$template->namespace = NULL;
		$template->package = NULL;
		$template->class = NULL;
		$template->constant = NULL;
		$template->function = NULL;

		$template->namespaces = array_keys($this->elementStorage->getNamespaces());
		$template->packages = array_keys($this->elementStorage->getPackages());
		$template->archive = basename($this->zip->getArchivePath());

		$elements = $this->elementStorage->getElements();
		$template->classes = array_filter($elements[Elements::CLASSES], $this->getMainFilter());
		$template->constants = array_filter($elements[Elements::CONSTANTS], $this->getMainFilter());
		$template->exceptions = array_filter($elements[Elements::EXCEPTIONS], $this->getMainFilter());
		$template->functions = array_filter($elements[Elements::FUNCTIONS], $this->getMainFilter());
		$template->interfaces = array_filter($elements[Elements::INTERFACES], $this->getMainFilter());
		$template->traits = array_filter($elements[Elements::TRAITS], $this->getMainFilter());

		$template->elements = $this->autocompleteElements->getElements();

		return $template;
	}


	/**
	 * @return \Closure
	 */
	private function getMainFilter()
	{
		return function ($element) {
			/** @var ReflectionElement $element */
			return $element->isMain();
		};
	}

}
