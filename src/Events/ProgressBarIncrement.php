<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Events;

use ApiGen\Console\ProgressBar;
use ApiGen\Generator\TemplateGenerators\ClassElementGenerator;
use ApiGen\Generator\TemplateGenerators\ConstantElementGenerator;
use ApiGen\Generator\TemplateGenerators\FunctionElementGenerator;
use ApiGen\Generator\TemplateGenerators\NamespaceGenerator;
use ApiGen\Generator\TemplateGenerators\PackageGenerator;
use ApiGen\Generator\TemplateGenerators\SourceCodeGenerator;
use Kdyby\Events\Subscriber;


class ProgressBarIncrement implements Subscriber
{

	/**
	 * @var ProgressBar
	 */
	private $progressBar;


	public function __construct(ProgressBar $progressBar)
	{
		$this->progressBar = $progressBar;
	}


	/**
	 * @return string[]
	 */
	public function getSubscribedEvents()
	{
		return [
			NamespaceGenerator::class . '::onGenerateProgress',
			PackageGenerator::class . '::onGenerateProgress',
			ClassElementGenerator::class . '::onGenerateProgress',
			ConstantElementGenerator::class . '::onGenerateProgress',
			FunctionElementGenerator::class . '::onGenerateProgress',
			SourceCodeGenerator::class . '::onGenerateProgress'
		];
	}


	/**
	 * @param int $size
	 */
	public function onGenerateProgress($size = 1)
	{
		$this->progressBar->increment($size);
	}

}
