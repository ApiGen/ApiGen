<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Herrera\Box\Compactor;


/**
 * Keeps Nette system annotations "method" and "return", minimizes PHP source and preserves line numbers.
 */
class PhpNette
{

	/**
	 * Compacts the file contents.
	 *
	 * @param string $contents
	 * @return string
	 */
	public function compact($contents)
	{
		$output = '';
		foreach (token_get_all($contents) as $token) {
			if (is_string($token)) {
				$output .= $token;

			} elseif ($token[0] === T_COMMENT) {
				$output .= $this->preserveLineNumbers($token);

			} elseif ($this->isCommentWithoutAnnotations($token, ['@return', '@method'])) {
				$output .= $this->preserveLineNumbers($token);

			} elseif ($token[0] === T_WHITESPACE) {
				if (strpos($token[1], "\n") === FALSE) {
					$output .= ' ';

				} else {
					$output .= $this->preserveLineNumbers($token);
				}

			} else {
				$output .= $token[1];
			}
		}

		return $output;
	}


	/**
	 * Checks if the file is supported.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function supports($file)
	{
		return (pathinfo($file, PATHINFO_EXTENSION) === 'php');
	}


	/**
	 * @return string
	 */
	private function preserveLineNumbers(array $token)
	{
		return str_repeat("\n", substr_count($token[1], "\n"));
	}


	/**
	 * @return bool
	 */
	private function isCommentWithoutAnnotations(array $token, array $annotationList)
	{
		if ($token[0] !== T_DOC_COMMENT) {
			return FALSE;
		}
		foreach ($annotationList as $annotation) {
			if (strpos($token[1], $annotation) !== FALSE) {
				return FALSE;
			}
		}
		return TRUE;
	}

}
