<?php declare(strict_types = 1);

namespace ApiGen\Analyzer;

use PhpParser\ErrorHandler;
use PhpParser\Lexer\Emulative as EmulativeLexer;

use function count;
use function is_array;

use const T_CURLY_OPEN;
use const T_DOLLAR_OPEN_CURLY_BRACES;
use const T_FUNCTION;
use const T_WHITESPACE;


class BodySkippingLexer extends EmulativeLexer
{
	protected function postprocessTokens(ErrorHandler $errorHandler): void
	{
		parent::postprocessTokens($errorHandler);

		$tokenCount = count($this->tokens);
		$level = null;

		for ($i = 0; $i < $tokenCount; $i++) {
			$token = is_array($this->tokens[$i]) ? $this->tokens[$i][0] : $this->tokens[$i];

			if ($level === null) {
				if ($token === T_FUNCTION) {
					$level = 0;
				}

			} else {
				if ($token === '{' || $token === T_CURLY_OPEN || $token === T_DOLLAR_OPEN_CURLY_BRACES) {
					$level++;

				} elseif ($token === '}') {
					$level--;

					if ($level <= 0) {
						$level = null;
						continue;
					}

				} elseif ($token === ';') {
					if ($level <= 0) {
						$level = null;
						continue;
					}
				}

				if ($level > ($token === '{' ? 1 : 0)) {
					if (is_array($this->tokens[$i])) {
						$this->tokens[$i][0] = T_WHITESPACE;

					} else {
						$this->tokens[$i] = [T_WHITESPACE, ' '];
					}
				}
			}
		}
	}
}
