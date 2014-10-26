<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Highlighter;


interface SourceCodeHighlighter
{

	/**
	 * @param string $sourceCode
	 * @return string
	 */
	function highlight($sourceCode);


	/**
	 * @param string $sourceCode
	 * @return string
	 */
	function highlightAndAddLineNumbers($sourceCode);

}
