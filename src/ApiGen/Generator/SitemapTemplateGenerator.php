<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Templating\TemplateFactory;
use Nette;


class SitemapTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;


	public function __construct(TemplateFactory $templateFactory)
	{
		$this->templateFactory = $templateFactory;
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return ! empty($this->config['baseUrl']);
	}


	public function generate()
	{
		$template = $this->templateFactory->create('sitemap', 'optional');
//		$template = $this->addBaseVariablesToTemplate($template);
		$template->save();
	}

}
