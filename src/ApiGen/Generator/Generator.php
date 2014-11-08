<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Elements\ElementStorage;
use ApiGen\FileSystem;
use ApiGen\Reflection;
use ApiGen\Templating\TemplateNavigator;
use Nette;


/**
 * @method onGenerateStart($steps)
 * @method onGenerateProgress($size)
 */
class Generator extends Nette\Object
{

	/**
	 * @var array
	 */
	public $onGenerateStart = array();

	/**
	 * @var array
	 */
	public $onGenerateProgress = array();

	/**
	 * @var TemplateGenerator[]
	 */
	public $processQueue = array();

	/**
	 * @var TemplateNavigator
	 */
	private $templateNavigator;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;


	public function __construct(TemplateNavigator $templateNavigator, ElementStorage $elementStorage)
	{
		$this->templateNavigator = $templateNavigator;
		$this->elementStorage = $elementStorage;
	}


	public function generate()
	{
		$this->templateNavigator->copyResourcesToDestination();
		$this->templateNavigator->prepareTempDir();

		$this->onGenerateStart($this->elementStorage->getElementCount());

		foreach ($this->processQueue as $generator) {
			if ($generator->isAllowed()) {
				$generator->generate();
			}
		}

		$this->templateNavigator->removeTempDir();
	}

}
