<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\FileSystem\ZipArchiveGenerator;
use ApiGen\Generator\ConditionalTemplateGenerator;
use ApiGen\Templating\TemplateFactory;


class ZipGenerator implements ConditionalTemplateGenerator
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var ZipArchiveGenerator
	 */
	private $zip;


	public function __construct(Configuration $configuration, TemplateFactory $templateFactory, ZipArchiveGenerator $zip)
	{
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
		$this->zip = $zip;
	}


	public function generate()
	{
		$this->zip->generate();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption(CO::DOWNLOAD);
	}

}
