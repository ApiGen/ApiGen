<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection\TokenReflection;

use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionConstant;
use TokenReflection\IReflectionFunction;


class ReflectionCrateBridge
{

	/**
	 * @var ReflectionFactory
	 */
	private $reflectionFactory;


	public function __construct(ReflectionFactory $reflectionFactory)
	{
		$this->reflectionFactory = $reflectionFactory;
	}


	/**
	 * @param IReflectionClass[] $tokenReflectionClasses
	 * @return ReflectionClass[]
	 */
	public function crossClassReflections($tokenReflectionClasses)
	{
		array_walk_recursive($tokenReflectionClasses, function (&$reflection) {
			if ($reflection instanceof IReflectionClass) {
				$reflection = $this->reflectionFactory->createFromReflection($reflection);
			}
		});

		return $tokenReflectionClasses;
	}


	/**
	 * @param IReflectionConstant[] $tokenReflectionConstants
	 * @return ReflectionConstant[]
	 */
	public function crossConstantReflections($tokenReflectionConstants)
	{
		$constants = array_map(function (IReflectionConstant $reflection) {
			return $this->reflectionFactory->createFromReflection($reflection);
		}, $tokenReflectionConstants);

		return $constants;
	}


	/**
	 * @param IReflectionFunction[] $tokenReflectionFunctions
	 * @return ReflectionFunction[]
	 */
	public function crossFunctionReflections($tokenReflectionFunctions)
	{
		$functions = array_map(function (IReflectionFunction $reflection) {
			return $this->reflectionFactory->createFromReflection($reflection);
		}, $tokenReflectionFunctions);

		return $functions;
	}

}
