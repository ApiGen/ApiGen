<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Reflection\ReflectionElement;
use Nette;


class ElementFilter extends Nette\Object
{

	/**
	 * @param array $elements
	 * @return array
	 */
	public function filterForMain($elements)
	{
		return array_filter($elements, function ($element) {
			/** @var ReflectionElement $element */
			return $element->isMain();
		});
	}


	/**
	 * @param array $elements
	 * @param $annotation
	 * @return mixed
	 */
	public function filterByAnnotation($elements, $annotation)
	{
		return array_filter($elements, function ($element) use ($annotation) {
			/** @var ReflectionElement $element */
			return $element->hasAnnotation($annotation);
		});
	}

}
