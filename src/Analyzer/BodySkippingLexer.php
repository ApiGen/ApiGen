<?php declare(strict_types = 1);

namespace ApiGen\Analyzer;

use PhpParser\ErrorHandler;
use PhpParser\Lexer;
use PhpParser\Token;

use function count;

use const T_CURLY_OPEN;
use const T_DOLLAR_OPEN_CURLY_BRACES;
use const T_FUNCTION;
use const T_USE;
use const T_WHITESPACE;


class BodySkippingLexer extends Lexer
{
	private const CURLY_BRACE_OPEN = 0x7B;
	private const CURLY_BRACE_CLOSE = 0x7D;
	private const SEMICOLON = 0x3B;


	/**
	 * @param  list<Token> $tokens
	 */
	protected function postprocessTokens(array &$tokens, ErrorHandler $errorHandler): void
	{
		parent::postprocessTokens($tokens, $errorHandler);

		$tokenCount = count($tokens);
		for ($i = 0; $i < $tokenCount; $i++) { // looking for function start
			if ($tokens[$i]->id === T_FUNCTION && $tokens[$i - 2]->id !== T_USE) {
				for ($i++; $i < $tokenCount; $i++) { // looking for opening curly brace of function body or semicolon
					switch ($tokens[$i]->id) {
						case self::SEMICOLON:
							continue 3; // look for next function

						case self::CURLY_BRACE_OPEN:
							break 2;
					}
				}

				for ($i++, $level = 0; $i < $tokenCount; $i++) { // looking for closing curly brace of function body
					switch ($tokens[$i]->id) {
						case T_WHITESPACE:
							continue 2;

						case self::CURLY_BRACE_OPEN:
						case T_CURLY_OPEN:
						case T_DOLLAR_OPEN_CURLY_BRACES:
							$level++;
							break;

						case self::CURLY_BRACE_CLOSE:
							if ($level === 0) {
								continue 3; // look for next function
							}

							$level--;
							break;
					}

					$tokens[$i] = new Token(T_WHITESPACE, ' '); // @phpstan-ignore parameterByRef.type
				}
			}
		}
	}
}
