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
	private $zipArchiveGenerator;


	public function __construct(
		Configuration $configuration,
		TemplateFactory $templateFactory,
		ZipArchiveGenerator $zipArchiveGenerator
	) {
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
		$this->zipArchiveGenerator = $zipArchiveGenerator;
	}


	public function generate()
	{
		$destination = $this->configuration->getOption(CO::DESTINATION);
		$zipFile = $destination . '/' . $this->configuration->getZipFileName();
		$this->zipArchiveGenerator->zipDirToFile($destination, $zipFile);
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return (bool) $this->configuration->getOption(CO::DOWNLOAD);
	}

}
