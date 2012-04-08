<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

class CharsetConvertor
{
	/**
	 * List of possible character sets.
	 *
	 * @var array
	 */
	private $charsets = array();

	public function __construct(array $charsets)
	{
		if (1 === count($charsets) && 'AUTO' !== $charsets[0]) {
			// One character set
			$this->charsets = $charsets;
		} else {
			if (1 === count($charsets) && 'AUTO' === $charsets[0]) {
				// Autodetection
				$this->charsets = array(
					'Windows-1251', 'Windows-1252', 'ISO-8859-2', 'ISO-8859-1', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6',
					'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15'
				);
			} else {
				// More character sets
				$this->charsets = $charsets;
				if (false !== ($key = array_search('WINDOWS-1250', $this->charsets))) {
					// WINDOWS-1250 is not supported
					$this->charsets[$key] = 'ISO-8859-2';
				}
			}

			// Only supported character sets
			$this->charsets = array_intersect($this->charsets, mb_list_encodings());

			// UTF-8 has to be first
			array_unshift($this->charsets, 'UTF-8');
		}
	}

	/**
	 * Converts content of the given file to UTF-8.
	 *
	 * @param string $filePath File path
	 * @return string
	 */
	public function convertFile($filePath)
	{
		$content = file_get_contents($filePath);

		static $cache = array();
		if (!isset($cache[$filePath])) {
			if (1 === count($this->charsets)) {
				// One character set
				$charset = $this->charsets[0];
			} else {
				// Detection
				$charset = mb_detect_encoding($content, $this->charsets);

				// The previous function can not handle WINDOWS-1250 and returns ISO-8859-2 instead
				if ('ISO-8859-2' === $charset && preg_match('~[\x7F-\x9F\xBC]~', $content)) {
					$charset = 'WINDOWS-1250';
				}
			}

			$cache[$filePath] = $charset;
		} else {
			$charset = $cache[$filePath];
		}

		if ('UTF-8' === $charset) {
			return $content;
		}

		return @iconv($charset, 'UTF-8//TRANSLIT//IGNORE', $content);
	}
}
