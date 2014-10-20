<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser;

use ArrayObject;
use Nette;


class ParserResult extends Nette\Object
{

	/**
	 * @var ArrayObject
	 */
	public static $classes;

	/**
	 * @var ArrayObject
	 */
	public static $constants;

	/**
	 * @var ArrayObject
	 */
	public static $functions;


//	/**
//	 * @return ArrayObject
//	 */
//	public static function getClasses()
//	{
//		return self::$classes;
//	}
//
//
//	/**
//	 * @return ArrayObject
//	 */
//	public static function getConstants()
//	{
//		return self::$constants;
//	}
//
//
//	/**
//	 * @return ArrayObject
//	 */
//	public static function getFunctions()
//	{
//		return self::$functions;
//	}
//
//
//	public static function setClasses(ArrayObject $classes)
//	{
//		var_dump('divneee');die;
//		var_dump(count($classes));
//		self::$classes = $classes;
//	}
//
//
//	public static function setConstants(ArrayObject $constants)
//	{
//		self::$classes = $constants;
//	}
//
//
//	public static function setFunctions(ArrayObject $functions)
//	{
//		self::$classes = $functions;
//	}

}
