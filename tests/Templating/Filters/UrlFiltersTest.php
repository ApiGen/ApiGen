<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class UrlFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const SOME_CLASS_LINK = '<code><a href="class-SomeClass.html" class="deprecated">SomeClass</a></code>';

    public function testHighlightPhp(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame(
            '<span class="php-keyword1">echo</span> <span class="php-quote">&quot;hi&quot;</span>;',
            $this->urlFilters->highlightPhp('echo "hi";', $reflectionClassMock)
        );
    }

    public function testHighlightValue(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame(
            '<span class="php-var">$var</span> = <span class="php-num">5</span>;',
            $this->urlFilters->highlightValue('$var = 5;', $reflectionClassMock)
        );
    }

    /**
     * @dataProvider getTypeLinksData()
     */
    public function testTypeLinks(string $annotation, string $expected): void
    {
        $reflectionClass = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame($expected, $this->urlFilters->typeLinks($annotation, $reflectionClass));
    }

    /**
     * @return string[][]
     */
    public function getTypeLinksData(): array
    {
        return [
            ['int|string[]', 'integer|string[]'],
            ['string|$this', 'string|<code><a href="class-.html"></a></code>'],
            ['SomeClass|string', self::SOME_CLASS_LINK . '|string']
        ];
    }

    /**
     * @dataProvider getAnnotationData()
     */
    public function testAnnotation(string $annotation, string $name, string $expected): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame($expected, $this->urlFilters->annotation($annotation, $name, $classReflectionMock));
    }

    /**
     * @return mixed[]
     */
    public function getAnnotationData(): array
    {
        return [
            ['SomeClass', 'return', self::SOME_CLASS_LINK],
            ['SomeClass special class', 'return', self::SOME_CLASS_LINK . '<br>special class'],
            ['SomeClass', 'throws', self::SOME_CLASS_LINK],
            ['http://licence.com MIT', 'link', '<a href="http://licence.com">MIT</a>'],
            ['SomeClass', 'uses', self::SOME_CLASS_LINK]
        ];
    }
}
