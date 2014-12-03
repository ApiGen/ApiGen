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
use Latte;
use Nette;
use Nette\Utils\ArrayHash;
use stdClass;


/**
 * @method TemplateFactory setConfig(array $config)
 */
class TemplateFactory extends Nette\Object
{

	/**
	 * @var array
	 */
	private $config;

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
	 * @return Template|stdClass
	 */
	public function create()
	{
		return $this->buildTemplate();
	}


	/**
	 * @param string $type
	 * @return Template|stdClass
	 */
	public function createForType($type)
	{
		$template = $this->buildTemplate();
		$template->setFile($this->templateNavigator->getTemplatePath($type));
		$template->setSavePath($this->templateNavigator->getTemplateFileName($type));
		return $template;
	}


	/**
	 * @return Template|stdClass
	 */
	private function buildTemplate()
	{
		/** @var Template|stdClass $template */
		$template = new Template($this->latteEngine);
		$template->config = ArrayHash::from($this->config);
		$template->basePath = $this->config[CO::TEMPLATE][TCO::TEMPLATES_PATH];
		$template = $this->templateElementsLoader->addElementsToTemplate($template);
		return $template;
	}

}
