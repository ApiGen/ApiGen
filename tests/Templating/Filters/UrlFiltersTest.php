<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Console\Command\GenerateCommand;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use PHPUnit_Framework_MockObject_MockObject;

final class UrlFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private const SOME_CLASS_LINK = '<code><a href="class-SomeClass.html" class="deprecated">SomeClass</a></code>';

    /**
     * @var string
     */
    private const SOME_CLASS_LINK_MULTI = '<code><a href="class-SomeClass.html" class="deprecated">SomeClass</a>'
        . '[]</code>';

    /**
     * @var string
     */
    private const APIGEN_LINK = '<code><a href="class-ApiGen.Console.Command.GenerateCommand.html" class="deprecated">'
        . 'ApiGen\Console\Command\GenerateCommand</a></code>';

    /**
     * @var UrlFilters
     */
    private $urlFilters;

    protected function setUp(): void
    {
        $this->urlFilters = $this->container->getByType(UrlFilters::class);

        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')
            ->willReturn('SomeClass');
        $classReflectionMock->method('isDeprecated')
            ->willReturn(true);
        $classReflectionMock->method('isDocumented')
            ->willReturn(true);

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            'SomeClass' => $classReflectionMock
        ]);
    }

    public function testDoc(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame('...', $this->urlFilters->doc('...', $reflectionClassMock));
    }

    /**
     * @dataProvider getInternalData()
     */
    public function testResolveInternal(string $docBlock, string $expectedLink): void
    {
        $this->assertSame(
            $expectedLink,
            MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveInternalAnnotation', [$docBlock])
        );
    }

    /**
     * @return string[][]
     */
    public function getInternalData(): array
    {
        return [
            ['{@internal Inside {@link some comment}, foo}', ''],
            ['{@internal} Inside {@link some comment}', ' Inside {@link some comment}'],
            ['{@internal}', ''],
            ['{@inherited bar}', '{@inherited bar}'],
        ];
    }

    /**
     * @dataProvider getLinkAndSeeData()
     */
    public function testResolveLinkAndSeeAnnotation(string $docBlock, string $expectedLink): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')->willReturn(GenerateCommand::class);
        $reflectionClassMock->method('isDeprecated')->willReturn(true);

        $this->assertSame(
            $expectedLink,
            MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveLinkAndSeeAnnotation', [
                $docBlock, $reflectionClassMock
            ])
        );
    }

    /**
     * @return string[][]
     */
    public function getLinkAndSeeData(): array
    {
        return [
            [
                '{@link bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK Donations}',
                '<a href="bitcoin:1335STSwu9hST4vcMRppEPgENMHD2r1REK">Donations</a>'
            ],
            ['{@link http://apigen.org Description}', '<a href="http://apigen.org">Description</a>'],
            ['{@link http://apigen.org}', '<a href="http://apigen.org">http://apigen.org</a>'],
            ['{@see http://php.net/manual/en PHP Manual}', '<a href="http://php.net/manual/en">PHP Manual</a>'],
            ['{@see NotActiveClass}', 'NotActiveClass'],
            [sprintf('{@see %s}', GenerateCommand::class), self::APIGEN_LINK],
        ];
    }

    /**
     * Issue #753
     *
     * @todo needs to be resolved.
     */
    public function testResolveLinkAndSeeAnnotationForMethod(): void
    {
        $reflectionMethodMock = $this->createMock(ClassMethodReflectionInterface::class);
        $reflectionMethodMock->method('getDeclaringClassName')->willReturn(GenerateCommand::class);
        $reflectionMethodMock->method('getName')->willReturn('testMethod');
        $reflectionMethodMock->method('isDeprecated')->willReturn(false);

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            'SomeClass' => $this->createClassReflection()
        ]);

        $this->assertSame(
            '<a href="SomeClass::someMethod()">SomeClass::someMethod()</a>',
            MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveLinkAndSeeAnnotation', [
                '{@see SomeClass::someMethod()}', $reflectionMethodMock
            ])
        );
    }

    public function testAnnotationDescription(): void
    {
        $docBlock = <<<DOC
/**
 * Some annotation
 * with more rows
 */
DOC;

        $reflectionElementMock = $this->createMock(ReflectionInterface::class);
        $expected = <<<EXP
* Some annotation
 * with more rows
 */
EXP;
        $this->assertSame($expected, $this->urlFilters->annotationDescription($docBlock, $reflectionElementMock));
    }

    public function testDescription(): void
    {
        $longDescription = <<<DOC
Some long description with example:
<code>echo "hi";</code>
DOC;
        $reflectionElementMock = $this->createMock(ReflectionInterface::class);
        $reflectionElementMock->method('getDescription')
            ->willReturn($longDescription);

        $this->assertSame($longDescription, $this->urlFilters->description($reflectionElementMock));
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
     * @dataProvider getResolveLinksData()
     */
    public function testResolveLink(string $definition, ?string $expected): void
    {
        $reflectionClass = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame($expected, $this->urlFilters->resolveLink($definition, $reflectionClass));
    }

    /**
     * @return mixed[]
     */
    public function getResolveLinksData(): array
    {
        return [
            ['int', null],
            ['SomeClass[]', self::SOME_CLASS_LINK_MULTI]
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
            ['http://licence.com MIT', 'license', '<a href="http://licence.com">MIT</a>'],
            ['http://licence.com MIT', 'link', '<a href="http://licence.com">MIT</a>'],
            ['SomeClass', 'link', ''],
            ['SomeClass', 'see', self::SOME_CLASS_LINK],
            ['SomeClass', 'uses', self::SOME_CLASS_LINK]
        ];
    }

    /**
     * @return ClassReflectionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private function createClassReflection()
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')
            ->willReturn(GenerateCommand::class);
        $reflectionClassMock->method('isDeprecated')
            ->willReturn(true);

        return $reflectionClassMock;
    }
}
