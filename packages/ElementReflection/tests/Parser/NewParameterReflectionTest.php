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

    public function testTypeHints()
    {
        $this->assertSame('', $this->parameterReflection->getTypeHint());
        $this->assertTrue($this->parameterReflection->isVariadic());
    }

    public function testDescription()
    {
        $this->assertSame('', $this->parameterReflection->getDescription());
    }
}
