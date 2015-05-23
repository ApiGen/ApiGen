<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Utils\Finder;

use Nette\Utils\Finder;
use SplFileInfo;


class NetteFinder implements FinderInterface
{

	/**
	 * @param array|string $source
	 * @param array $exclude
	 * @param array $extensions
	 * @return SplFileInfo[]
	 */
	public function find($source, array $exclude = [], array $extensions = ['php'])
	{
		$sources = $this->turnToIterator($source);
		$fileMasks = $this->turnExtensionsToMask($extensions);
		$finder = Finder::findFiles($fileMasks)->exclude($exclude)
			->from($sources)->exclude($exclude);
		$files = $this->convertFinderToArray($finder);

		return $files;
	}


	/**
	 * @param array|string $source
	 * @return array
	 */
	private function turnToIterator($source)
	{
		if ( ! is_array($source)) {
			return [$source];
		}
		return $source;
	}


	/**
	 * @return array
	 */
	private function turnExtensionsToMask(array $extensions)
	{
		array_walk($extensions, function (&$value) {
			$value = '*.' . $value;
		});
		return $extensions;
	}


	/**
	 * @return SplFileInfo[]
	 */
	private function convertFinderToArray(Finder $finder)
	{
		return iterator_to_array($finder->getIterator());
	}

}
