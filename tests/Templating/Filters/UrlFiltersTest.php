<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Parser\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\Helpers\ElementLinkFactory;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UrlFiltersTest extends TestCase
{

    const APIGEN_LINK = '<code><a href="class-link-ApiGen\ApiGen" class="deprecated invalid">ApiGen\ApiGen</a></code>';

    /**
     * @var UrlFilters
     */
    private $urlFilters;


    protected function setUp()
    {
        $sourceCodeHighlighterMock = Mockery::mock(SourceCodeHighlighter::class);
        $sourceCodeHighlighterMock->shouldReceive('highlight')->andReturnUsing(function ($arg) {
            return 'Highlighted: ' . $arg;
        });
        $elementResolverMock = $this->getElementResolverMock();

        $elementLinkFactoryMock = Mockery::mock(ElementLinkFactory::class);
        $elementLinkFactoryMock->shouldReceive('createForElement')->andReturnUsing(
            function (ElementReflectionInterface $reflectionElement, $classes = '') {
                $name = $reflectionElement->getName();
                if ($classes) {
                    $classes = ' class="' . implode($classes, ' ') . '"';
                } else {
                    $classes = '';
                }

                if ($reflectionElement instanceof ClassReflectionInterface) {
                    return '<a href="class-link-' . $name . '"' . $classes . '>' . $name . '</a>';
                } elseif ($reflectionElement instanceof FunctionReflectionInterface) {
                    return '<a href="function-link-' . $name . '"' . $classes . '>' . $name . '()</a>';
                } elseif ($reflectionElement instanceof MethodReflectionInterface) {
                    return '<a href="method-link-' . $name . '"' . $classes . '>' .
                        $reflectionElement->getDeclaringClassName() . '::' . $name . '()</a>';
                }

                throw new \InvalidArgumentException();
            }
        );

        $this->urlFilters = new UrlFilters(
            $this->getConfigurationMock(),
            $sourceCodeHighlighterMock,
            $elementResolverMock,
            new LinkBuilder,
            $elementLinkFactoryMock,
            new EventDispatcher()
        );
    }


    public function testDoc()
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $this->assertSame('...', $this->urlFilters->doc('...', $reflectionClassMock));
    }


    /**
     * @dataProvider getInternalData()
     */
    public function testResolveInternal($docBlock, $expectedLink)
    {
        $this->assertSame(
            $expectedLink,
            MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveInternalAnnotation', [$docBlock])
        );
    }


    public function getInternalData()
    {
        return [
            ['{@internal Inside {@link some comment}, foo}', 'Inside {@link some comment}, foo'],
            ['{@internal}', ''],
            ['{@inherited bar}', '{@inherited bar}'],
        ];
    }


    /**
     * @dataProvider getLinkAndSeeData()
     */
    public function testResolveLinkAndSeeAnnotation($docBlock, $expectedLink)
    {
        $reflectionElementMock = Mockery::mock(ReflectionElement::class);
        $this->assertSame(
            $expectedLink,
            MethodInvoker::callMethodOnObject($this->urlFilters, 'resolveLinkAndSeeAnnotation', [
                $docBlock, $reflectionElementMock
            ])
        );
    }


    /**
     * @return array[]
     */
    public function getLinkAndSeeData()
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
            [
                '{@see ApiGen\ApiGen}',
                self::APIGEN_LINK
            ],

            // issue #753
            [
                '{@see ApiGen\ApiGen::testMethod()}',
                '<code><a href="method-link-testMethod">ApiGen\ApiGen::testMethod()</a></code>'
            ],
        ];
    }


    public function testDescription()
    {
        $docBlock = <<<DOC
/**
 * Some annotation
 * with more rows
 */
DOC;

        $reflectionElementMock = Mockery::mock(ElementReflectionInterface::class);
        $expected = <<<EXP
* Some annotation
 * with more rows
 */
EXP;
        $this->assertSame($expected, $this->urlFilters->description($docBlock, $reflectionElementMock));
    }


    public function testShortDescription()
    {
        $reflectionElementMock = Mockery::mock(ElementReflectionInterface::class);
        $reflectionElementMock->shouldReceive('getShortDescription')->andReturn('Some short description');

        $this->assertSame(
            'Some short description',
            $this->urlFilters->shortDescription($reflectionElementMock)
        );

        $this->assertSame(
            'Some short description',
            $this->urlFilters->shortDescription($reflectionElementMock, true)
        );
    }


    public function testLongDescription()
    {
        $longDescription = <<<DOC
Some long description with example:
<code>echo "hi";</code>
DOC;
        $reflectionElementMock = Mockery::mock(ElementReflectionInterface::class);
        $reflectionElementMock->shouldReceive('getLongDescription')->andReturn($longDescription);

        $expected = <<<EXPECTED
Some long description with example:
<code>echo "hi";</code>
EXPECTED;
        $this->assertSame($expected, $this->urlFilters->longDescription($reflectionElementMock));
    }


    public function testHighlightPhp()
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $this->assertSame(
            'Highlighted: ...',
            $this->urlFilters->highlightPhp('...', $reflectionClassMock)
        );
    }


    public function testHighlightValue()
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $this->assertSame(
            'Highlighted: ...',
            $this->urlFilters->highlightValue('...', $reflectionClassMock)
        );
    }


    /**
     * @dataProvider getTypeLinksData()
     */
    public function testTypeLinks($annotation, $expected)
    {
        $reflectionClass = Mockery::mock(ClassReflectionInterface::class);
        $this->assertSame($expected, $this->urlFilters->typeLinks($annotation, $reflectionClass));
    }


    /**
     * @return array[]
     */
    public function getTypeLinksData()
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
                '<code><a href="class-link-ApiGen\ApiGen" class="deprecated invalid">ApiGen\ApiGen</a></code>|string'
            ],
            ['int|int[]', 'integer|integer[]']
        ];
    }


    /**
     * @dataProvider getResolveLinksData()
     */
    public function testResolveLink($definition, $expected)
    {
        $reflectionClass = Mockery::mock(ClassReflectionInterface::class);
        $this->assertSame($expected, $this->urlFilters->resolveLink($definition, $reflectionClass));
    }


    /**
     * @return array[]
     */
    public function getResolveLinksData()
    {
        return [
            ['int', null],
            [
                'ApiGen\ApiGen[]',
                '<code><a href="class-link-ApiGen\ApiGen" class="deprecated invalid">ApiGen\ApiGen</a>[]</code>'
            ]
        ];
    }


    /**
     * @dataProvider getAnnotationData()
     */
    public function testAnnotation($annotation, $name, $expected)
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $this->assertSame($expected, $this->urlFilters->annotation($annotation, $name, $reflectionClassMock));
    }


    /**
     * @return array[]
     */
    public function getAnnotationData()
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


    /**
     * @return Mockery\MockInterface
     */
    private function getElementResolverMock()
    {
        $elementResolverMock = Mockery::mock(ElementResolverInterface::class);
        $elementResolverMock->shouldReceive('resolveElement')->andReturnUsing(function ($arg) {
            if ($arg === 'ApiGen\ApiGen') {
                $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
                $reflectionClassMock->shouldReceive('getName')->andReturn('ApiGen\ApiGen');
                $reflectionClassMock->shouldReceive('isDeprecated')->andReturn(true);
                $reflectionClassMock->shouldReceive('isValid')->andReturn(false);
                return $reflectionClassMock;
            } elseif ($arg === 'int') {
                $reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
                $reflectionFunctionMock->shouldReceive('getName')->andReturn('int');
                $reflectionFunctionMock->shouldReceive('isDeprecated')->andReturn(false);
                $reflectionFunctionMock->shouldReceive('isValid')->andReturn(true);
                return $reflectionFunctionMock;
            } elseif ($arg === 'ApiGen\ApiGen::testMethod()') {
                $reflectionMethodMock = Mockery::mock(MethodReflectionInterface::class);
                $reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('ApiGen\ApiGen');
                $reflectionMethodMock->shouldReceive('getName')->andReturn('testMethod');
                $reflectionMethodMock->shouldReceive('isDeprecated')->andReturn(false);
                $reflectionMethodMock->shouldReceive('isValid')->andReturn(true);
                return $reflectionMethodMock;
            } else {
                return null;
            }
        });
        return $elementResolverMock;
    }


    /**
     * @return Mockery\MockInterface
     */
    private function getConfigurationMock()
    {
        $configurationMock = Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getOption')->with(CO::INTERNAL)->andReturn(true);
        return $configurationMock;
    }
}
