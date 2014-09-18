<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Charset;

use Nette;


class CharsetConvertor extends Nette\Object
{

	/**
	 * List of possible character sets.
	 *
	 * @var array
	 */
	private $charsets = array();


	public function setCharset(array $charsets)
	{
		$firstValue = array_pop($charsets);

		if (count($charsets) === 1 && $firstValue !== 'AUTO') {
			// One character set
			$this->charsets = $charsets;

		} else {
			if (count($charsets) === 1 && $firstValue === 'AUTO') {
				// Autodetection
				$this->charsets = array(
					'Windows-1251', 'Windows-1252', 'ISO-8859-2', 'ISO-8859-1', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
					'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14',
					'ISO-8859-15'
				);

			} else {
				// More character sets
				$this->charsets = $charsets;
				if (($key = array_search('WINDOWS-1250', $this->charsets)) !== FALSE) {
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
	 * @param string $filePath
	 * @return string
	 */
	public function convertFile($filePath)
	{
		$content = file_get_contents($filePath);

		static $cache = array();
		if ( ! isset($cache[$filePath])) {
			if (count($this->charsets) === 1) {
				// One character set
				$charset = $this->charsets[0];

			} else {
				// Detection
				$charset = mb_detect_encoding($content, $this->charsets);

				// The previous function can not handle WINDOWS-1250 and returns ISO-8859-2 instead
				if ($charset === 'ISO-8859-2' && preg_match('~[\x7F-\x9F\xBC]~', $content)) {
					$charset = 'WINDOWS-1250';
				}
			}

			$cache[$filePath] = $charset;

		} else {
			$charset = $cache[$filePath];
		}

		if ($charset === 'UTF-8') {
			return $content;
		}

		return @iconv($charset, 'UTF-8//TRANSLIT//IGNORE', $content);
	}

}
