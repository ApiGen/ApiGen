<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Console\Application;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Tests\ContainerFactory;
use ApiGen\Tests\MethodInvoker;
use ArrayObject;
use Mockery;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

final class UrlFiltersTest extends TestCase
{

    /**
     * @var string
     */
    private const APIGEN_LINK = '<code><a href="class-ApiGen.Console.Application.html" class="deprecated">ApiGen\Console\Application</a></code>';

    /**
     * @var UrlFilters
     */
    private $urlFilters;

    /**
     * @var Container
     */
    private $container;


    protected function setUp(): void
    {
        $this->container = (new ContainerFactory())->create();
        $this->urlFilters = $this->container->getByType(UrlFilters::class);

        /** @var Configuration configuration */
        $configuration = $this->container->getByType(Configuration::class);
        $configuration->resolveOptions([
            ConfigurationOptions::SOURCE => __DIR__,
            ConfigurationOptions::DESTINATION => __DIR__ . '/Destination'
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
     * @return string[]
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
        $reflectionClassMock->method('getName')->willReturn(Application::class);
        $reflectionClassMock->method('isDeprecated')->willReturn(true);

        $this->assertSame(
            $expectedLink,
            MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveLinkAndSeeAnnotation', [
                $docBlock, $reflectionClassMock
            ])
        );
    }


    /**
     * @return array[]
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
            [sprintf('{@see %s}', Application::class), self::APIGEN_LINK],
        ];
    }


    /**
     * Issue #753
     */
    public function testResolveLinkAndSeeAnnotationForMethod():void
    {
        $reflectionMethodMock = $this->createMock(MethodReflectionInterface::class);
        $reflectionMethodMock->method('getDeclaringClassName')->willReturn(Application::class);
        $reflectionMethodMock->method('getName')->willReturn('testMethod');
        $reflectionMethodMock->method('isDeprecated')->willReturn(false);

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses(new ArrayObject([
            Application::class => $this->createClassReflection()
        ]));

        $this->assertSame(
            '<code><a href="method-link-testMethod">ApiGen\Console\Application::testMethod()</a></code>',
            MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveLinkAndSeeAnnotation', [
                '{@see ApiGen\Console\Application::testMethod()}', $reflectionMethodMock
            ])
        );
    }


    public function testDescription(): void
    {
        $docBlock = <<<DOC
/**
 * Some annotation
 * with more rows
 */
DOC;

        $reflectionElementMock = $this->createMock(ElementReflectionInterface::class);
        $expected = <<<EXP
* Some annotation
 * with more rows
 */
EXP;
        $this->assertSame($expected, $this->urlFilters->description($docBlock, $reflectionElementMock));
    }


    public function testShortDescription(): void
    {
        $reflectionElementMock = $this->createMock(ElementReflectionInterface::class);
        $reflectionElementMock->method('getShortDescription')->willReturn('Some short description');

        $this->assertSame(
            'Some short description',
            $this->urlFilters->shortDescription($reflectionElementMock)
        );

        $this->assertSame(
            'Some short description',
            $this->urlFilters->shortDescription($reflectionElementMock, true)
        );
    }


    public function testLongDescription(): void
    {
        $longDescription = <<<DOC
Some long description with example:
<code>echo "hi";</code>
DOC;
        $reflectionElementMock = $this->createMock(ElementReflectionInterface::class);
        $reflectionElementMock->method('getLongDescription')->willReturn($longDescription);

        $expected = <<<EXPECTED
Some long description with example:
<code>echo "hi";</code>
EXPECTED;
        $this->assertSame($expected, $this->urlFilters->longDescription($reflectionElementMock));
    }


    public function testHighlightPhp(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame(
            'Highlighted: ...',
            $this->urlFilters->highlightPhp('...', $reflectionClassMock)
        );
    }


    public function testHighlightValue(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertSame(
            'Highlighted: ...',
            $this->urlFilters->highlightValue('...', $reflectionClassMock)
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
     * @return array[]
     */
    public function getTypeLinksData(): array
    {
        return [
            ['int|string', 'integer|string'],
            ['string|$this', 'string|$this'],
            ['$this', '$this'], // expected $this because context is unresolved and $this is valid type
            [
                'ApiGen\ApiGen',
                self::APIGEN_LINK
            ],
            [
                'ApiGen\ApiGen|string',
                '<code><a href="class-link-ApiGen\ApiGen" class="deprecated">ApiGen\ApiGen</a></code>|string'
            ],
            ['int|int[]', 'integer|integer[]']
        ];
    }


    /**
     * @dataProvider getResolveLinksData()
     */
    public function testResolveLink(string $definition, string $expected): void
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
            [
                'ApiGen\ApiGen[]',
                '<code><a href="class-link-ApiGen\ApiGen" class="deprecated">ApiGen\ApiGen</a>[]</code>'
            ]
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
            ['ApiGen\ApiGen', 'return', self::APIGEN_LINK],
            ['ApiGen\ApiGen special class', 'return', self::APIGEN_LINK . '<br>special class'],
            ['ApiGen\ApiGen', 'throws', self::APIGEN_LINK],
            ['...', 'return', '...'],
            ['http://licence.com MIT', 'license', '<a href="http://licence.com">MIT</a>'],
            ['http://licence.com MIT', 'link', '<a href="http://licence.com">MIT</a>'],
            ['ApiGen\ApiGen', 'link', 'ApiGen\ApiGen'],
            ['ApiGen\ApiGen', 'see', self::APIGEN_LINK],
            ['ApiGen\ApiGen', 'uses', self::APIGEN_LINK],
            ['ApiGen\ApiGen', 'usedby', self::APIGEN_LINK]
        ];
    }


    private function createClassReflection(): ClassReflectionInterface
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')->willReturn(Application::class);
        $reflectionClassMock->method('isDeprecated')->willReturn(true);

        return $reflectionClassMock;
    }
}
