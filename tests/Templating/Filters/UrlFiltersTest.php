<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class UrlFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const SOME_CLASS_LINK = '<code><a href="class-SomeClass.html" class="deprecated">SomeClass</a></code>';

    /**
     * @var string
     */
    private const APIGEN_LINK = '<code><a href="class-ApiGen.Console.Command.GenerateCommand.html" class="deprecated">'
        . 'ApiGen\Console\Command\GenerateCommand</a></code>';

    /**
     * @var UrlFilters
     */
    private $urlFilters;

    /**
     * @todo move to see and link annotation subscribers!
     * @return string[][]
     */
    public function getLinkAndSeeData(): array
    {
        return [
            [
                '{@link bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK Donations}',
                '<a href="bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK">Donations</a>'
            ],
            ['@link http://apigen.org Description', '<a href="http://apigen.org">Description</a>'],
            ['{@link http://apigen.org Description}', '<a href="http://apigen.org">Description</a>'],
            ['{@link http://apigen.org}', '<a href="http://apigen.org">http://apigen.org</a>'],
            ['{@see NotActiveClass}', 'NotActiveClass'],
            [sprintf('{@see %s}', GenerateCommand::class), self::APIGEN_LINK],
        ];
    }

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
            ['...', 'return', '...'],
            ['http://licence.com MIT', 'link', '<a href="http://licence.com">MIT</a>'],
            ['SomeClass', 'see', self::SOME_CLASS_LINK],
            ['SomeClass', 'uses', self::SOME_CLASS_LINK]
        ];
    }
}
