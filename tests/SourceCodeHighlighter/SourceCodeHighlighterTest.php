<?php declare(strict_types=1);

namespace ApiGen\Tests\SourceCodeHighlighter;

use ApiGen\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class SourceCodeHighlighterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var SourceCodeHighlighter
     */
    private $sourceCodeHighlighter;

    protected function setUp(): void
    {
        $this->sourceCodeHighlighter = $this->container->get(SourceCodeHighlighter::class);
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
