<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\SourceCodeHighlighter;

use ApiGen\Generator\SourceCodeHighlighter\FshlSourceCodeHighlighter;
use FSHL\Highlighter;
use FSHL\Lexer\Php;
use FSHL\Output\Html;
use PHPUnit\Framework\TestCase;

class FshlSourceCodeHighlighterTest extends TestCase
{

    /**
     * @var FshlSourceCodeHighlighter
     */
    private $fshlSourceCodeHighlighter;


    protected function setUp(): void
    {
        $fshlHighlighter = new Highlighter(new Html);
        $fshlHighlighter->setLexer(new Php);
        $this->fshlSourceCodeHighlighter = new FshlSourceCodeHighlighter($fshlHighlighter);
    }


    public function testHighlight(): void
    {
        $this->assertSame(
            '<span class="php-var">$a</span> = <span class="php-num">1</span>',
            $this->fshlSourceCodeHighlighter->highlight('$a = 1')
        );
    }


    public function testHighlightAndAddLineNumbers(): void
    {
        $this->assertSame(
            '<span class="line">1: </span><span class="php-var">$a</span> = <span class="php-num">1</span>',
            $this->fshlSourceCodeHighlighter->highlightAndAddLineNumbers('$a = 1')
        );
    }
}
