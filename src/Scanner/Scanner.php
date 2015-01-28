<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Scanner;

use Nette\Utils\Finder;
use RuntimeException;
use SplFileInfo;


class Scanner
{

	/**
	 * @param array|string $source
	 * @param array $exclude
	 * @param array $extensions
	 * @throws RuntimeException
	 * @return SplFileInfo[]
	 */
	public function scan($source, array $exclude = [], array $extensions = ['php'])
	{
		$sources = $this->turnToIterator($source);
		$fileMasks = $this->turnExtensionsToMask($extensions);
		$finder = Finder::findFiles($fileMasks)->exclude($exclude)
			->from($sources)->exclude($exclude);
		$files = $this->convertFinderToArray($finder);

		if (count($files) === 0) {
			throw new RuntimeException('No PHP files found');
		}

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
