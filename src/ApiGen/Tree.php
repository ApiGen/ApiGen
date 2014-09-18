<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use RecursiveTreeIterator;
use RuntimeException;


/**
 * Customized recursive tree iterator.
 */
class Tree extends RecursiveTreeIterator
{

	/**
	 * Has a sibling on the same level.
	 *
	 * @var string
	 */
	const HAS_NEXT = '1';

	/**
	 * Last item on the current level.
	 *
	 * @var string
	 */
	const LAST = '0';

	/**
	 * Reflections in the tree.
	 *
	 * @var \ArrayObject
	 */
	private $reflections;


	/**
	 * @param array $treePart Part of the tree
	 * @param \ArrayObject $reflections Array of reflections in the tree part
	 */
	public function __construct(array $treePart, \ArrayObject $reflections)
	{
		parent::__construct(
			new \RecursiveArrayIterator($treePart),
			RecursiveTreeIterator::BYPASS_KEY,
			NULL,
			\RecursiveIteratorIterator::SELF_FIRST
		);
		$this->setPrefixPart(RecursiveTreeIterator::PREFIX_END_HAS_NEXT, self::HAS_NEXT);
		$this->setPrefixPart(RecursiveTreeIterator::PREFIX_END_LAST, self::LAST);
		$this->rewind();

		$this->reflections = $reflections;
	}


	/**
	 * Returns if the current item has a sibling on the same level.
	 *
	 * @return boolean
	 */
	public function hasSibling()
	{
		$prefix = $this->getPrefix();
		return ! empty($prefix) && self::HAS_NEXT === substr($prefix, -1);
	}


	/**
	 * Returns the current reflection.
	 *
	 * @return \ApiGen\Reflection\ReflectionElement
	 * @throws \UnexpectedValueException If current is not reflection array.
	 */
	public function current()
	{
		$className = $this->key();
		if ( ! isset($this->reflections[$className])) {
			throw new RuntimeException(sprintf('Class "%s" is not in the reflection array', $className));
		}

		return $this->reflections[$className];
	}

}
