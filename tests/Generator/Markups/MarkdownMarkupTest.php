<?php

namespace ApiGen\Tests\Generator\Markups;

use ApiGen\Generator\Markups\MarkdownMarkup;
use ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter;
use Michelf\MarkdownExtra;
use Mockery;
use PHPUnit\Framework\TestCase;

class MarkdownMarkupTest extends TestCase
{

    /**
     * @var MarkdownMarkup
     */
    private $markdownMarkup;


    protected function setUp()
    {
        $highlighterMock = Mockery::mock(SourceCodeHighlighter::class);
        $highlighterMock->shouldReceive('highlight')
            ->andReturnUsing(function ($args) {
                return $args;
            });

        $highlighterMock->shouldReceive('highlightAndAddLineNumbers')
            ->andReturnUsing(function ($args) {
                return $args;
            });

        $this->markdownMarkup = new MarkdownMarkup(new MarkdownExtra, $highlighterMock);
    }


    public function testLine()
    {
        $this->assertSame('...', $this->markdownMarkup->line('<p>...</p>'));

        $this->assertSame(
            'This is very <strong>bold</strong>',
            $this->markdownMarkup->line('This is very **bold**')
        );
    }


    public function testBlock()
    {
        $this->assertSame('<p>...</p>', $this->markdownMarkup->block('...'));
        $this->assertSame('<p>...</p>', $this->markdownMarkup->block('<p>...</p>'));
    }


    public function testBlockCode()
    {
        $input = <<<INPUT
<code>
THREE
</code>
INPUT;
        $this->assertSame('<pre>THREE</pre>', $this->markdownMarkup->block($input));
    }


    public function testBlockPre()
    {
        $input = <<<INPUT
<pre>
FOUR
</pre>
INPUT;
        $this->assertSame('<pre>FOUR</pre>', $this->markdownMarkup->block($input));
    }


    public function testBlockMarkdownHighlight()
    {
        $input = <<<INPUT
```php
FIVE
```
INPUT;
        $this->assertSame('<pre>FIVE</pre>', $this->markdownMarkup->block($input));
    }
}
