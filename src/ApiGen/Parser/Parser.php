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
use SplFileInfo;
use TokenReflection\Broker;


class Parser
{

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
	private $errors = [];

	/**
	 * @var ParserResult
	 */
	private $parserResult;


	public function __construct(Broker $broker, CharsetConvertor $charsetConvertor, ParserResult $parserResult)
	{
		$this->broker = $broker;
		$this->charsetConvertor = $charsetConvertor;
		$this->parserResult = $parserResult;

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
		foreach ($files as $file) {
			$content = $this->charsetConvertor->convertFileToUtf($file->getPathname());
			try {
				$this->broker->processString($content, $file->getPathname());

			} catch (\Exception $e) {
				$this->errors[] = $e;
			}
		}

		$allFoundClasses = $this->broker->getClasses(
			Backend::TOKENIZED_CLASSES
			| Backend::INTERNAL_CLASSES
			| Backend::NONEXISTENT_CLASSES
		);
		$this->classes->exchangeArray($allFoundClasses);
		$this->constants->exchangeArray($this->broker->getConstants());
		$this->functions->exchangeArray($this->broker->getFunctions());
		$this->internalClasses->exchangeArray($this->broker->getClasses(Backend::INTERNAL_CLASSES));

		$this->classes->uksort('strcasecmp');
		$this->constants->uksort('strcasecmp');
		$this->functions->uksort('strcasecmp');

		$this->loadToParserResult();
	}


	/**
	 * @return array
	 */
	public function getDocumentedStats()
	{
		return [
			'classes' => $this->getDocumentedElementsCount($this->broker->getClasses(Backend::TOKENIZED_CLASSES)),
			'constants' => $this->getDocumentedElementsCount($this->constants->getArrayCopy()),
			'functions' => $this->getDocumentedElementsCount($this->functions->getArrayCopy()),
			'internalClasses' => $this->getDocumentedElementsCount($this->internalClasses->getArrayCopy())
		];
	}


	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
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


	private function loadToParserResult()
	{
		$this->parserResult->setClasses($this->classes);
		$this->parserResult->setConstants($this->constants);
		$this->parserResult->setFunctions($this->functions);

		// temporary workaround for reflections
		ParserResult::$classesStatic = $this->classes;
		ParserResult::$constantsStatic = $this->constants;
		ParserResult::$functionsStatic = $this->functions;
	}

}
