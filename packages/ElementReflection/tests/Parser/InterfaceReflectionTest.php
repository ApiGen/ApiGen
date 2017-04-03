<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\ElementReflection\Reflection\InterfaceReflection;
use ApiGen\Parser\Tests\Parser\ParserSource\SomeInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

/**
 * Mirror to Function test @see \ApiGen\Parser\Tests\ParserTest
 */
final class InterfaceReflectionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var InterfaceReflection
     */
    private $interfaceReflection;

    protected function setUp()
    {
        /** @var Parser $parser */
        $parser = $this->container->getByType(Parser::class);
        $parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $interfaceReflections = $parser->getInterfaceReflections();
        $this->interfaceReflection = array_shift($interfaceReflections);
    }

    public function testLines()
    {
        $this->assertSame(5, $this->interfaceReflection->getStartLine());
        $this->assertSame(11, $this->interfaceReflection->getEndLine());
    }

        public function testNames(): void
    {
        $this->assertSame(SomeInterface::class, $this->interfaceReflection->getName());
        $this->assertSame('SomeInterface', $this->interfaceReflection->getShortName());
        $this->assertSame(SomeInterface::class. '()', $this->interfaceReflection->getPrettyName());
    }
//
//    public function testNamespaces()
//    {
//        $this->assertSame('SomeNamespace', $this->functionReflection->getNamespaceName());
//        $this->assertSame('SomeNamespace', $this->functionReflection->getPseudoNamespaceName());
//        $this->testNames();
//    }
//
//    public function testAnnotations(): void
//    {
//        $this->assertCount(4, $this->functionReflection->getAnnotations());
//        $this->assertTrue($this->functionReflection->hasAnnotation('return'));
//        $this->assertTrue($this->functionReflection->hasAnnotation('param'));
//
//        $returnAnnotation = $this->functionReflection->getAnnotation('return')[0];
//        $this->assertInstanceOf(Return_::class, $returnAnnotation);
//        $this->assertCount(3, $this->functionReflection->getAnnotation('param'));
//
//        $this->assertFalse($this->functionReflection->isDeprecated());
//
//        $this->assertSame(
//            'Some description.' . PHP_EOL . PHP_EOL . 'And more lines!',
//            $this->functionReflection->getDescription()
//        );
//    }
//
//    public function testParameters()
//    {
//        $parameters = $this->functionReflection->getParameters();
//        $this->assertCount(3, $parameters);
//    }
//
//    public function testMisc()
//    {
//        $this->assertTrue($this->functionReflection->isDocumented());
//    }
}
