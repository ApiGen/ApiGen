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


class CommonTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;


	public function __construct(Configuration $configuration, TemplateFactory $templateFactory)
	{
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
	}


	public function generate()
	{
		$commonTemplates = array('overview', 'combined', 'elementlist', '404');
		foreach ($commonTemplates as $type) {
			$this->templateFactory->createNamed($type)->save();
		}
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return TRUE;
	}

}
