<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser;

use ApiGen\Elements\Elements;
use ArrayObject;
use Nette;


/**
 * @method string[] getTypes()
 *
 * @method setClasses(object)
 * @method setConstants(object)
 * @method setFunctions(object)
 */
class ParserStorage extends Nette\Object
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
	 * @var array
	 */
	private $types = array(Elements::CLASSES, Elements::CONSTANTS, Elements::FUNCTIONS);


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

}
