<?php declare(strict_types=1);


namespace ApiGen\BetterReflection\Parser;

use PhpParser\ErrorHandler;
use PhpParser\Node;
use PhpParser\Parser;

final class CachedParser implements Parser
{
    public function __construct()
    {
    }

    /**
     * Parses PHP code into a node tree.
     *
     * @param string $code The source code to parse
     * @param ErrorHandler|null $errorHandler Error handler to use for lexer/parser errors, defaults
     *                                        to ErrorHandler\Throwing.
     *
     * @return Node[]|null Array of statements (or null if the 'throwOnError' option is disabled and the parser was
     *                     unable to recover from an error).
     */
    public function parse($code, ErrorHandler $errorHandler = null)
    {
        // TODO: Implement parse() method.
    }
}