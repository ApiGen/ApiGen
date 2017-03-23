<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\SourceCodeHighlighter;

use ApiGen\Generator\SourceCodeHighlighter\FshlSourceCodeHighlighter;
use FSHL\Highlighter;
use FSHL\Lexer\Php;
use FSHL\Output\Html;
use PHPUnit\Framework\TestCase;

final class FshlSourceCodeHighlighterTest extends TestCase
{
    /**
     * @var FshlSourceCodeHighlighter
     */
    private $sourceCodeHighlighter;

    protected function setUp(): void
    {
        $highlighter = new Highlighter(new Html);
        $highlighter->setLexer(new Php);
        $this->sourceCodeHighlighter = new FshlSourceCodeHighlighter($highlighter);
    }

    public function testHighlight(): void
    {
        $this->assertSame(
            '<span class="php-var">$a</span> = <span class="php-num">1</span>',
            $this->sourceCodeHighlighter->highlight('$a = 1')
        );
    }

    public function testHighlightAndAddLineNumbers(): void
    {
        $this->assertSame(
            '<span class="line">1: </span><span class="php-var">$a</span> = <span class="php-num">1</span>',
            $this->sourceCodeHighlighter->highlightAndAddLineNumbers('$a = 1')
        );
    }
}
