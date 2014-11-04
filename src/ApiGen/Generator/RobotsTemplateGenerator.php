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


class RobotsTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(TemplateFactory $templateFactory, Configuration $configuration)
	{
		$this->templateFactory = $templateFactory;
		$this->configuration = $configuration;
	}


	public function generate()
	{
		$template = $this->templateFactory->create('robots', 'optional');
		$template->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return (bool) $this->configuration->getOption('baseUrl');
	}

}
