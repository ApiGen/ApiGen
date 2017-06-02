<?php declare(strict_types=1);

namespace ApiGen\Tests\Utils;

use ApiGen\Utils\LinkBuilder;
use Nette\Utils\Html;
use PHPUnit\Framework\TestCase;

final class LinkBuilderTest extends TestCase
{
    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    protected function setUp(): void
    {
        $this->linkBuilder = new LinkBuilder;
    }

    /**
     * @dataProvider getBuildData()
     *
     * @param string[] $classes
     */
    public function testBuild(string $url, string $text, bool $escape, array $classes, string $expectedLink): void
    {
        $this->assertSame($expectedLink, $this->linkBuilder->build($url, $text, $escape, $classes));
    }

    /**
     * @return mixed[]
     */
    public function getBuildData(): array
    {
        return [
            ['url', 'text', true, [], '<a href="url">text</a>'],
            ['url', 'text', false, ['class'], '<a href="url" class="class">text</a>'],
            ['url', Html::el('b')->setText('text'), true, [], '<a href="url">&lt;b&gt;text&lt;/b&gt;</a>'],
            ['url', Html::el('b')->setText('text'), false, [], '<a href="url"><b>text</b></a>'],
            ['url', '<b>text</b>', true, [], '<a href="url">&lt;b&gt;text&lt;/b&gt;</a>'],
            ['url', '<b>text</b>', false, [], '<a href="url"><b>text</b></a>'],
        ];
    }
}
