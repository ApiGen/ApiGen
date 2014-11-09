<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Charset;

use ApiGen\Charset\Configuration\CharsetOptionsResolver;
use Nette;


class CharsetConvertor extends Nette\Object
{

	/**
	 * @var array
	 */
	private $charsets = array();

	/**
	 * @var CharsetOptionsResolver
	 */
	private $charsetOptionsResolver;

	/**
	 * @var array { filePath => fileEncoding }
	 */
	private $detectedFileEncodings = array();


	public function __construct(CharsetOptionsResolver $charsetOptionsResolver)
	{
		$this->charsetOptionsResolver = $charsetOptionsResolver;
	}


	public function setCharsets(array $charsets)
	{
		$options = array('charsets' => $charsets);
		$this->charsets = $this->charsetOptionsResolver->resolve($options);
	}


	/**
	 * @param string $filePath
	 * @return string
	 */
	public function convertFileToUtf($filePath)
	{
		if ($this->charsets === array()) {
			$this->charsets = $this->charsetOptionsResolver->resolve();
		}

		$fileEncoding = $this->getFileEncoding($filePath);
		$content = file_get_contents($filePath);

		if ($fileEncoding === Encoding::UTF_8) {
			return $content;

		} else {
			return $this->convertContentToUtf($content, $fileEncoding);
		}
	}


	/**
	 * @param string $filePath
	 * @return string
	 */
	private function getFileEncoding($filePath)
	{
		if (isset($this->detectedFileEncodings[$filePath])) {
			return $this->detectedFileEncodings[$filePath];
		}

		return $this->detectedFileEncodings[$filePath] = $this->detectFileEncoding($filePath);
	}


	/**
	 * @param string $filePath
	 * @return string
	 */
	private function detectFileEncoding($filePath)
	{
		if (count($this->charsets) === 1) {
			return $this->charsets[0];

		} else {
			$content = file_get_contents($filePath);
			$fileEncoding = mb_detect_encoding($content, $this->charsets);

			// The previous function can not handle WINDOWS-1250 and returns ISO-8859-1 instead
			if ($fileEncoding === Encoding::ISO_8859_1 && preg_match('~[\x7F-\x9F\xBC]~', $content)) {
				return Encoding::WIN_1250;
			}

			return $fileEncoding;
		}

	}


	/**
	 * @param string $content
	 * @param string $fileEncoding
	 * @return string
	 */
	private function convertContentToUtf($content, $fileEncoding)
	{
		return @iconv($fileEncoding, 'UTF-8//TRANSLIT//IGNORE', $content);
	}

}
