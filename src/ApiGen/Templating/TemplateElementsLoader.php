<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Elements\AutocompleteElements;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Reflection\ReflectionElement;
use Closure;


class TemplateElementsLoader
{

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var AutocompleteElements
	 */
	private $autocompleteElements;


	public function __construct(
		ElementStorage $elementStorage,
		Configuration $configuration,
		AutocompleteElements $autocompleteElements
	) {
		$this->elementStorage = $elementStorage;
		$this->configuration = $configuration;
		$this->autocompleteElements = $autocompleteElements;
	}


	/**
	 * @return Template
	 */
	public function addElementsToTemplate(Template $template)
	{
		$template->setParameters([
			'namespace' => NULL,
			'package' => NULL,
			'class' => NULL,
			'constant' => NULL,
			'function' => NULL,
			'namespaces' => array_keys($this->elementStorage->getNamespaces()),
			'packages' => array_keys($this->elementStorage->getPackages()),
			'classes' => array_filter($this->elementStorage->getClasses(), $this->getMainFilter()),
			'interfaces' => array_filter($this->elementStorage->getClasses(), $this->getMainFilter()),
			'traits' => array_filter($this->elementStorage->getTraits(), $this->getMainFilter()),
			'exceptions' => array_filter($this->elementStorage->getExceptions(), $this->getMainFilter()),
			'constants' => array_filter($this->elementStorage->getConstants(), $this->getMainFilter()),
			'functions' => array_filter($this->elementStorage->getFunctions(), $this->getMainFilter()),
			'elements' => $this->autocompleteElements->getElements()
		]);

		if ($this->configuration->getOption(CO::DOWNLOAD)) {
			$template->setParameters([
				'archive' => basename($this->configuration->getZipFileName())
			]);
		}

		return $template;
	}


	/**
	 * @return Closure
	 */
	private function getMainFilter()
	{
		return function (ReflectionElement $element) {
			return $element->isMain();
		};
	}

}
