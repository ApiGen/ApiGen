<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use RecursiveDirectoryIterator;
use RecursiveFilterIterator;

/**
 * Filters excluded files and directories.
 */
class SourceFilesFilterIterator extends RecursiveFilterIterator
{
	/**
	 * File/directory exclude masks.
	 *
	 * @var array
	 */
	private $excludeMasks;

	/**
	 * Creates the iterator.
	 *
	 * @param \RecursiveDirectoryIterator $iterator Directory iterator
	 * @param array $excludeMasks File/directory exlude masks
	 */
	public function __construct(RecursiveDirectoryIterator $iterator, array $excludeMasks)
	{
		parent::__construct($iterator);

		$this->excludeMasks = $excludeMasks;
	}

	/**
	 * Returns if the current file/directory should be processed.
	 *
	 * @return boolean
	 */
	public function accept() {
		/** @var \SplFileInfo */
		$current = $this->current();

		foreach ($this->excludeMasks as $mask) {
			if (fnmatch($mask, $current->getPathName(), FNM_NOESCAPE)) {
				return false;
			}
		}

		if (!is_readable($current->getPathname())) {
			throw new \InvalidArgumentException(sprintf('File/directory "%s" is not readable.', $current->getPathname()));
		}

		return true;
	}

	/**
	 * Returns the iterator of the current element's children.
	 *
	 * @return \ApiGen\SourceFilesFilterIterator
	 */
	public function getChildren()
	{
		return new static($this->getInnerIterator()->getChildren(), $this->excludeMasks);
	}
}
