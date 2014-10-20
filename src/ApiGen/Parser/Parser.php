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
use TokenReflection\Broker;


/**
 * @method ArrayObject  getClasses()
 * @method ArrayObject  getConstants()
 * @method ArrayObject  getFunctions()
 * @method array        getErrors()
 * @method Parser       onParseStart($steps)
 * @method Parser       onParseProgress($size)
 * @method Parser       onParseFinish(Parser $parser)
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

		$this->classes = new ArrayObject;
		$this->constants = new ArrayObject;
		$this->functions = new ArrayObject;
		$this->internalClasses = new ArrayObject;
	}


	public function parse(array $files)
	{
		$this->onParseStart(array_sum($files));

		foreach ($files as $filePath => $size) {
			$content = $this->charsetConvertor->convertFile($filePath);
			try {
				$this->broker->processString($content, $filePath);

			} catch (\Exception $e) {
				$this->errors[] = $e;
			}

			$this->onParseProgress($size);
		}

		$allFoundClasses = $this->broker->getClasses(Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES
			| Backend::NONEXISTENT_CLASSES);
		$this->classes->exchangeArray($allFoundClasses);
		$this->constants->exchangeArray($this->broker->getConstants());
		$this->functions->exchangeArray($this->broker->getFunctions());
		$this->internalClasses->exchangeArray($this->broker->getClasses(Backend::INTERNAL_CLASSES));

		$this->classes->uksort('strcasecmp');
		$this->constants->uksort('strcasecmp');
		$this->functions->uksort('strcasecmp');

		$this->onParseFinish($this);
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
			$count += (int) $element->isDocumented();
		}
		return $count;
	}

}
