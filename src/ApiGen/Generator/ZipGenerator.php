<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\FileSystem\Zip;
use Nette;


class ZipGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var Zip
	 */
	private $zip;


	public function __construct(Configuration $configuration, Zip $zip)
	{
		$this->configuration = $configuration;
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
		return $this->configuration->getOption('zip');
	}

}
