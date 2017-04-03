<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

/**
 * Mirror to Parameter test @see \ApiGen\Parser\Tests\ParserTest
 */
final class NewParameterReflectionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ParameterReflectionInterface
     */
    private $parameterReflection;

    protected function setUp()
    {
        /** @var Parser $parser */
        $parser = $this->container->getByType(Parser::class);
        $parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $functionReflections = $parser->getFunctionReflections();
        $functionReflection = array_pop($functionReflections);

        $parameterReflections = $functionReflection->getParameters();

        $this->parameterReflection = array_pop($parameterReflections);
    }

    public function testName()
    {
        $this->assertSame('arguments', $this->parameterReflection->getName());
        $this->assertSame(
            'SomeNamespace\someAloneFunction($arguments)',
            $this->parameterReflection->getPrettyName()
        );
    }

    public function testDeclaringFunction()
    {
        $this->assertInstanceOf(
            FunctionReflectionInterface::class,
            $this->parameterReflection->getDeclaringFunction()
        );

        $this->assertSame(
            'SomeNamespace\someAloneFunction',
            $this->parameterReflection->getDeclaringFunctionName()
        );
    }

    public function testType()
    {
        $this->assertSame('string', $this->parameterReflection->getTypeHint());
        $this->assertTrue($this->parameterReflection->isVariadic());
        $this->assertFalse($this->parameterReflection->isArray());
        $this->assertNull($this->parameterReflection->getClass());
        $this->assertNull($this->parameterReflection->getClassName());
    }

    public function testDescription()
    {
        $this->assertSame('and their description', $this->parameterReflection->getDescription());
    }

    public function testDefaults()
    {
        $this->assertNull($this->parameterReflection->getDefaultValueDefinition());
    }

    public function testDeclaringClass()
    {
        $this->assertNull($this->parameterReflection->getDeclaringClass());
        $this->assertSame('', $this->parameterReflection->getDeclaringClassName());
    }
}
