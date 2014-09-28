<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

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
	private $excludeMasks = array();


	public function __construct(RecursiveDirectoryIterator $iterator, $excludeMasks = array())
	{
		parent::__construct($iterator);
		$this->excludeMasks = $excludeMasks;
	}


	/**
	 * Returns if the current file/directory should be processed.
	 *
	 * @return boolean
	 */
	public function accept()
	{
		/** @var \SplFileInfo */
		$current = $this->current();

		foreach ($this->excludeMasks as $mask) {
			if (fnmatch($mask, $current->getPathName(), FNM_NOESCAPE)) {
				return FALSE;
			}
		}

		if ( ! is_readable($current->getPathname())) {
			throw new \InvalidArgumentException(sprintf('File/directory "%s" is not readable.', $current->getPathname()));
		}

		return TRUE;
	}


	/**
	 * Returns the iterator of the current element's children.
	 *
	 * @return SourceFilesFilterIterator
	 */
	public function getChildren()
	{
		return new static($this->getInnerIterator()->getChildren(), $this->excludeMasks);
	}

}
