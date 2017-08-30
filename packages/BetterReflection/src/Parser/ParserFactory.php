<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\Parser;

use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory as NikicParserFactory;
use Roave\BetterReflection\SourceLocator\Ast\Parser\MemoizingParser;

final class ParserFactory
{
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var NikicParserFactory
     */
    private $nikicParserFactory;

    public function __construct(Lexer $lexer, NikicParserFactory $nikicParserFactory)
    {
        $this->lexer = $lexer;
        $this->nikicParserFactory = $nikicParserFactory;
    }

    public function create(): Parser
    {
        $nativeParser = $this->nikicParserFactory->create(NikicParserFactory::PREFER_PHP7, $this->lexer);

        return new MemoizingParser($nativeParser);
    }
}
