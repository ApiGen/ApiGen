<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser;

use ApiGen\Parser\Elements\Elements;
use ApiGen\Reflection\ReflectionElement;
use ArrayObject;
use Nette;


/**
 * @method setClasses(object)
 * @method setConstants(object)
 * @method setFunctions(object)
 * @method setInternalClasses(object)
 * @method setTokenizedClasses(object)
 * @method getClasses(object)
 * @method getConstants(object)
 * @method getFunctions(object)
 * @method getTypes()
 */
class ParserResult extends Nette\Object
{

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
	 * @var ArrayObject
	 */
	private $tokenizedClasses;

	/**
	 * @var array
	 */
	private $types = [Elements::CLASSES, Elements::CONSTANTS, Elements::FUNCTIONS];


	public function __construct()
	{
		$this->classes = new ArrayObject;
		$this->constants = new ArrayObject;
		$this->functions = new ArrayObject;
		$this->internalClasses = new ArrayObject;
		$this->tokenizedClasses = new ArrayObject;
	}


	/**
	 * @param string $type
	 * @return ArrayObject
	 */
	public function getElementsByType($type)
	{
		if ($type === Elements::CLASSES) {
			return $this->classes;

		} elseif ($type === Elements::CONSTANTS) {
			return $this->constants;

		} elseif ($type === Elements::FUNCTIONS) {
			return $this->functions;
		}

		return FALSE;
	}


	/**
	 * @return array
	 */
	public function getDocumentedStats()
	{
		return [
			'classes' => $this->getDocumentedElementsCount($this->tokenizedClasses),
			'constants' => $this->getDocumentedElementsCount($this->constants),
			'functions' => $this->getDocumentedElementsCount($this->functions),
			'internalClasses' => $this->getDocumentedElementsCount($this->internalClasses)
		];
	}


	/**
	 * @param ReflectionElement[]|ArrayObject $result
	 * @return int
	 */
	private function getDocumentedElementsCount(ArrayObject $result)
	{
		$count = 0;
		foreach ($result as $element) {
			$count += (int) $element->isDocumented();
		}
		return $count;
	}

}
