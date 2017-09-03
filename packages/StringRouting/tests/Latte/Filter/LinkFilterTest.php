<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Tests\Latte\Filter;

use ApiGen\StringRouting\Tests\Latte\Filter\Source\TestClass;
use ApiGen\Tests\AbstractParserAwareTestCase;
use Latte\Engine;
use Nette\InvalidArgumentException;

final class LinkFilterTest extends AbstractParserAwareTestCase
{
    /**
     * @var Engine
     */
    private $latte;

    protected function setUp(): void
    {
        $this->latte = $this->container->get(Engine::class);
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument for filter "linkSource" has to be type of ' .
            '"ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface". "string" given.'
        );

        $this->latte->renderToString(__DIR__ . '/Source/template.latte', [
            'classReflection' => 'SomeClass',
        ]);
    }

    public function testBuildLinkIfReflectionFoundFilter(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source/TestClass.php']);
        $this->parser->parse();

        $html = $this->latte->renderToString(__DIR__ . '/Source/buildLinkIfReflectionFound-template.latte', [
            'className' => TestClass::class,
        ]);
        $this->assertSame(
            '<a href="class-ApiGen.StringRouting.Tests.Latte.Filter.Source.TestClass.html">'
            . TestClass::class . '</a>',
            trim($html)
        );

        $html = $this->latte->renderToString(__DIR__ . '/Source/buildLinkIfReflectionFound-template.latte', [
            'className' => 'string',
        ]);
        $this->assertSame('string', trim($html));
    }
}
