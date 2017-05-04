<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NewParameterReflectionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ParameterReflectionInterface
     */
    private $parameterReflection;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->getByType(Parser::class);
        $parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $functionReflections = $parser->getFunctionReflections();
        $functionReflection = array_pop($functionReflections);

        $parameterReflections = $functionReflection->getParameters();

        $this->parameterReflection = array_pop($parameterReflections);
    }

    public function testName(): void
    {
        $this->assertSame('arguments', $this->parameterReflection->getName());
        $this->assertSame(
            'SomeNamespace\someAloneFunction($arguments)',
            $this->parameterReflection->getPrettyName()
        );
    }

    public function testDeclaringFunction(): void
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

    public function testType(): void
    {
        $this->assertSame('string', $this->parameterReflection->getTypeHint());
        $this->assertTrue($this->parameterReflection->isVariadic());
        $this->assertFalse($this->parameterReflection->isArray());
        $this->assertFalse($this->parameterReflection->isCallable());
        $this->assertNull($this->parameterReflection->getClass());
        $this->assertNull($this->parameterReflection->getClassName());
    }

    public function testDescription(): void
    {
        $this->assertSame('and their description', $this->parameterReflection->getDescription());
    }

    public function testDefaults(): void
    {
        $this->assertNull($this->parameterReflection->getDefaultValueDefinition());
    }

    public function testDeclaringClass(): void
    {
        $this->assertNull($this->parameterReflection->getDeclaringClass());
        $this->assertSame('', $this->parameterReflection->getDeclaringClassName());
    }
}
