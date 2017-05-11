<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassTypeTest extends AbstractParserAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection\Source';

    /**
     * @var FunctionParameterReflectionInterface
     */
    private $functionParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $functionReflections = $this->reflectionStorage->getFunctionReflections();
        $functionReflection = $functionReflections[$this->namespacePrefix . '\functionWithClass'];

        $this->functionParameterReflection = $functionReflection->getParameters()['splFileInfo'];
    }

    public function testType(): void
    {
        $this->assertSame('SplFileInfo', $this->functionParameterReflection->getTypeHint());

        $typeHintClassReflection = $this->functionParameterReflection->getClass();
        $this->assertSame('SplFileInfo', $this->functionParameterReflection->getClassName());

        $this->assertInstanceOf(ClassReflectionInterface::class, $typeHintClassReflection);
    }
}
