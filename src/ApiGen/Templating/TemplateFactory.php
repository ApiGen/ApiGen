<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\ApiGen;
use ApiGen\Configuration\Configuration;
use Latte;
use Nette;
use Nette\Utils\ArrayHash;


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


	public function __construct(Latte\Engine $latteEngine, Configuration $configuration)
	{
		$this->latteEngine = $latteEngine;
		$this->configuration = $configuration;
	}


	/**
	 * @return Template|\stdClass
	 */
	public function create()
	{
		/** @var Template|\stdClass $template */
		$template = new Template($this->latteEngine);
		$template->generator = ApiGen::NAME;
		$template->version = ApiGen::VERSION;
		$template->config = ArrayHash::from($this->config);
		$template->basePath = dirname($this->config['templateConfig']);
		return $template;
	}

}
