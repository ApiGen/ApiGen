<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Templating\TemplateFactory;
use Nette;


class OpensearchTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Configuration $configuration, TemplateFactory $templateFactory)
	{
		$this->templateFactory = $templateFactory;
		$this->configuration = $configuration;
	}


	public function generate()
	{
		$this->templateFactory->create('opensearch')->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		$options = $this->configuration->getOptions();
		return (bool) $options['googleCseId'] && $options['baseUrl'];
	}

}
