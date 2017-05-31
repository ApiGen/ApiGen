<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\SourceCodeHighlighter;

use ApiGen\Contracts\Generator\SourceCodeHighlighter\SourceCodeHighlighterInterface;
use ApiGen\Generator\SourceCodeHighlighter\FshlSourceCodeHighlighter;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class FshlSourceCodeHighlighterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var FshlSourceCodeHighlighter
     */
    private $sourceCodeHighlighter;

    protected function setUp(): void
    {
        $this->sourceCodeHighlighter = $this->container->getByType(SourceCodeHighlighterInterface::class);
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
