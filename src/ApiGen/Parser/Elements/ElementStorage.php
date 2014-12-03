<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use Nette;


/**
 * @method getNamespaces()
 * @method getPackages()
 * @method getClasses()
 * @method getInterfaces()
 * @method getTraits()
 * @method getExceptions()
 * @method getConstants()
 * @method getFunctions()
 * @method setNamespaces()
 * @method setPackages()
 * @method setClasses()
 * @method setInterfaces()
 * @method setTraits()
 * @method setExceptions()
 * @method setConstants()
 * @method setFunctions()
 */
class ElementStorage extends Nette\Object
{

	/**
	 * @var array
	 */
	private $namespaces = [];

	/**
	 * @var array
	 */
	private $packages = [];

	/**
	 * @var array
	 */
	private $classes = [];

	/**
	 * @var array
	 */
	private $interfaces = [];

	/**
	 * @var array
	 */
	private $traits = [];

	/**
	 * @var array
	 */
	private $exceptions = [];

	/**
	 * @var array
	 */
	private $constants = [];

	/**
	 * @var array
	 */
	private $functions = [];

}
