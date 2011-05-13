<?php
/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;
use RecursiveTreeIterator;

/**
 * Class tree.
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
	 * @var integer
	 */
	const LAST = '0';

	/**
	 * Reflections in the tree.
	 *
	 * @var \ArrayObject
	 */
	private $reflections;

	/**
	 * Constructor.
	 *
	 * @param array $treePart Part of the tree
	 * @param \ArrayObject $reflection Array of reflections in the tree part
	 */
	public function __construct(array $treePart, \ArrayObject $reflections)
	{
		parent::__construct(
			new \RecursiveArrayIterator($treePart),
			RecursiveTreeIterator::BYPASS_KEY,
			null,
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
		return !empty($prefix) && self::HAS_NEXT === substr($prefix, -1);
	}

	/**
	 * Returns the current reflection.
	 *
	 * @return \Apigen\Reflection
	 */
	public function current()
	{
		$className = $this->key();
		if (!isset($this->reflections[$className])) {
			throw new \UnexpectedValueException(sprintf('Class "%s" is not in the reflection array.', $className));
		}

		return $this->reflections[$className];
	}
}
