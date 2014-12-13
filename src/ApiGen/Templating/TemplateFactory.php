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
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Reflection\ReflectionElement;
use Latte;
use Nette;
use Nette\Utils\ArrayHash;


class TemplateFactory
{

	const ELEMENT_CLASS = 'class';
	const ELEMENT_SOURCE = 'source';
	const ELEMENT_PACKAGE = 'package';
	const ELEMENT_NAMESPACE = 'namespace';
	const ELEMENT_CONSTANT = 'constant';
	const ELEMENT_FUNCTION = 'function';

	/**
	 * @var Latte\Engine
	 */
	private $latteEngine;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var TemplateNavigator
	 */
	private $templateNavigator;

	/**
	 * @var TemplateElementsLoader
	 */
	private $templateElementsLoader;

	/**
	 * @var Template
	 */
	private $builtTemplate;


	public function __construct(
		Latte\Engine $latteEngine,
		Configuration $configuration,
		TemplateNavigator $templateNavigator,
		TemplateElementsLoader $templateElementsLoader
	) {
		$this->latteEngine = $latteEngine;
		$this->configuration = $configuration;
		$this->templateNavigator = $templateNavigator;
		$this->templateElementsLoader = $templateElementsLoader;
	}


	/**
	 * @return Template
	 */
	public function create()
	{
		return $this->buildTemplate();
	}


	/**
	 * @param string $type
	 * @return Template
	 */
	public function createForType($type)
	{
		$template = $this->buildTemplate();
		$template->setFile($this->templateNavigator->getTemplatePath($type));
		$template->setSavePath($this->templateNavigator->getTemplateFileName($type));
		return $template;
	}


	/**
	 * @param string $name
	 * @param ReflectionElement|string $element
	 * @throws \Exception
	 * @return Template
	 */
	public function createNamedForElement($name, $element)
	{
		$template = $this->buildTemplate();
		$template->setFile($this->templateNavigator->getTemplatePath($name));

		if ($name === self::ELEMENT_SOURCE) {
			$template->setSavePath($this->templateNavigator->getTemplatePathForSourceElement($element));

		} elseif ($name === self::ELEMENT_CLASS) {
			$template->setSavePath($this->templateNavigator->getTemplatePathForClass($element));

		} elseif ($name === self::ELEMENT_CONSTANT) {
			$template->setSavePath($this->templateNavigator->getTemplatePathForConstant($element));

		} elseif ($name === self::ELEMENT_FUNCTION) {
			$template->setSavePath($this->templateNavigator->getTemplatePathForFunction($element));

		} elseif ($name === self::ELEMENT_NAMESPACE) {
			$template->setSavePath($this->templateNavigator->getTemplatePathForNamespace($element));

		} elseif ($name === self::ELEMENT_PACKAGE) {
			$template->setSavePath($this->templateNavigator->getTemplatePathForPackage($element));

		} else {
			throw new \Exception($name . ' is not supported template type.');
		}
		return $template;
	}


	/**
	 * @return Template
	 */
	private function buildTemplate()
	{
		if ($this->builtTemplate === NULL) {
			$options = $this->configuration->getOptions();
			$template = new Template($this->latteEngine);
			$template->setParameters([
				'config' => ArrayHash::from($options),
				'basePath' => $options[CO::TEMPLATE][TCO::TEMPLATES_PATH]
			]);
			$this->builtTemplate = $this->templateElementsLoader->addElementsToTemplate($template);
		}
		return $this->builtTemplate;
	}

}
