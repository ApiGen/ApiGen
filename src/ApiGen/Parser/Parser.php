<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Reflection\ReflectionElement;
use ArrayObject;
use Nette;
use SplFileInfo;
use TokenReflection\Broker;


/**
 * @method ArrayObject  getClasses()
 * @method ArrayObject  getConstants()
 * @method ArrayObject  getFunctions()
 * @method array        getErrors()
 * @method Parser       onParseStart()
 * @method Parser       onParseProgress()
 * @method Parser       onParseFinish()
 */
class Parser extends Nette\Object
{

	/**
	 * @var array
	 */
	public $onParseStart = array();

	/**
	 * @var array
	 */
	public $onParseProgress = array();

	/**
	 * @var array
	 */
	public $onParseFinish = array();

	/**
	 * @var Broker
	 */
	private $broker;

	/**
	 * @var CharsetConvertor
	 */
	private $charsetConvertor;

	/**
	 * @var ArrayObject
	 */
	private $classes;

	/**
	 * @var ArrayObject
	 */
	private $constants;

	/**
	 * @var ArrayObject
	 */
	private $functions;

	/**
	 * @var ArrayObject
	 */
	private $internalClasses;

	/**
	 * @var array
	 */
	private $errors;


	public function __construct(Broker $broker, CharsetConvertor $charsetConvertor)
	{
		$this->broker = $broker;
		$this->charsetConvertor = $charsetConvertor;

		// todo: parser storage...
		$this->classes = new ArrayObject;
		$this->constants = new ArrayObject;
		$this->functions = new ArrayObject;
		$this->internalClasses = new ArrayObject;
	}


	/**
	 * @param SplFileInfo[] $files
	 */
	public function parse($files)
	{
		$this->onParseStart(count($files));

		foreach ($files as $file) {
			$content = $this->charsetConvertor->convertFile($file->getPathname());
			try {
				$this->broker->processString($content, $file->getPathname());

			} catch (\Exception $e) {
				$this->errors[] = $e;
			}

			$this->onParseProgress(1);
		}

		$allFoundClasses = $this->broker->getClasses(Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES
			| Backend::NONEXISTENT_CLASSES);


		$this->classes->exchangeArray($allFoundClasses);
		$this->constants->exchangeArray($this->broker->getConstants());
		$this->functions->exchangeArray($this->broker->getFunctions());
		$internalClasses = $this->broker->getClasses(Backend::INTERNAL_CLASSES);
		$this->internalClasses->exchangeArray($internalClasses);

		$this->classes->uksort('strcasecmp');
		$this->constants->uksort('strcasecmp');
		$this->functions->uksort('strcasecmp');

		$this->onParseFinish($this->classes, $this->constants, $this->functions);
	}


	/**
	 * @return array
	 */
	public function getDocumentedStats()
	{
		return array(
			'classes' => $this->getDocumentedElementsCount($this->broker->getClasses(Backend::TOKENIZED_CLASSES)),
			'constants' => $this->getDocumentedElementsCount($this->constants->getArrayCopy()),
			'functions' => $this->getDocumentedElementsCount($this->functions->getArrayCopy()),
			'internalClasses' => $this->getDocumentedElementsCount($this->internalClasses->getArrayCopy())
		);
	}


	/**
	 * @param ReflectionElement[] $result
	 * @return int
	 */
	private function getDocumentedElementsCount($result)
	{
		$count = 0;
		foreach ($result as $element) {
			if ($element->isDocumented()) {
				$count++;
			}
		}
		return $count;
	}

}
