<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use Nette;


class ElementSorter extends Nette\Object
{

	/**
	 * @param ReflectionConstant[]|ReflectionFunction[]|ReflectionMethod[]|ReflectionProperty[] $elements
	 * @return ReflectionConstant[]|ReflectionFunction[]|ReflectionMethod[]|ReflectionProperty[]
	 */
	public function sortElementsByFqn($elements)
	{
		if (count($elements)) {
			$firstElement = $elements[0];
			if ($firstElement instanceof ReflectionConstant) {
				return $this->sortConstantsByFqn($elements);

			} elseif ($firstElement instanceof ReflectionFunction) {
				return $this->sortFunctionsByFqn($elements);

			} elseif ($firstElement instanceof ReflectionMethod) {
				return $this->sortMethodsByFqn($elements);

			} elseif ($firstElement instanceof ReflectionProperty) {
				return $this->sortPropertiesByFqn($elements);
			}
		}

		return $elements;
	}


	/**
	 * @param ReflectionConstant[] $constants
	 * @return ReflectionConstant[]
	 */
	private function sortConstantsByFqn($constants)
	{
		usort($constants, [$this, 'compareConstantsByFqn']);
		return $constants;
	}


	/**
	 * @param ReflectionFunction[] $functions
	 * @return ReflectionFunction[]
	 */
	private function sortFunctionsByFqn($functions)
	{
		usort($functions, [$this, 'compareFunctionsByFqn']);
		return $functions;
	}


	/**
	 * @param ReflectionMethod[] $methods
	 * @return ReflectionMethod[]
	 */
	private function sortMethodsByFqn($methods)
	{
		usort($methods, [$this, 'compareMethodsOrPropertiesByFqn']);
		return $methods;
	}


	/**
	 * @param ReflectionProperty[] $properties
	 * @return ReflectionProperty[]
	 */
	private function sortPropertiesByFqn($properties)
	{
		usort($properties, [$this, 'compareMethodsOrPropertiesByFqn']);
		return $properties;
	}


	/**
	 * @return integer
	 */
	private function compareConstantsByFqn(ReflectionConstant $reflection1, ReflectionConstant $reflection2)
	{
		return strcasecmp(
			$this->getConstantFqnName($reflection1),
			$this->getConstantFqnName($reflection2)
		);
	}


	/**
	 * @return integer
	 */
	private function compareFunctionsByFqn(ReflectionFunction $reflection1, ReflectionFunction $reflection2)
	{
		return strcasecmp(
			$this->getFunctionFqnName($reflection1),
			$this->getFunctionFqnName($reflection2)
		);
	}


	/**
	 * @param ReflectionMethod|ReflectionProperty $reflection1
	 * @param ReflectionMethod|ReflectionProperty $reflection2
	 * @return integer
	 */
	private function compareMethodsOrPropertiesByFqn($reflection1, $reflection2)
	{
		return strcasecmp(
			$this->getPropertyOrMethodFqnName($reflection1),
			$this->getPropertyOrMethodFqnName($reflection2)
		);
	}


	/**
	 * @return string
	 */
	private function getConstantFqnName(ReflectionConstant $reflection)
	{
		$class = $reflection->getDeclaringClassName() ?: $reflection->getNamespaceName();
		return $class . '\\' . $reflection->getName();
	}


	/**
	 * @return string
	 */
	private function getFunctionFqnName(ReflectionFunction $reflection)
	{
		return $reflection->getNamespaceName() . '\\' . $reflection->getName();
	}


	/**
	 * @param ReflectionMethod|ReflectionProperty $reflection
	 * @return string
	 */
	private function getPropertyOrMethodFqnName($reflection)
	{
		return $reflection->getDeclaringClassName() . '::' . $reflection->getName();
	}

}
