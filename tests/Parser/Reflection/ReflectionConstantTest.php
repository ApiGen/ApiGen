<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\ConstantInClass;

final class ReflectionConstantTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ConstantReflectionInterface
     */
    private $constantReflection;

    /**
     * @var ConstantReflectionInterface
     */
    private $constantReflectionInClass;

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionConstantSource']);

        $this->constantReflection = $parserStorage->getConstants()['SOME_CONSTANT'];
        $this->reflectionClass = $parserStorage->getClasses()[ConstantInClass::class];

        $this->constantReflectionInClass = $this->reflectionClass->getConstant('CONSTANT_INSIDE');
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ConstantReflectionInterface::class, $this->constantReflection);
        $this->assertInstanceOf(ConstantReflectionInterface::class, $this->constantReflectionInClass);
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertNull($this->constantReflection->getDeclaringClass());
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->constantReflectionInClass->getDeclaringClass());
    }

    public function testGetDeclaringClassName(): void
    {
        $this->assertSame('', $this->constantReflection->getDeclaringClassName());
        $this->assertSame(ConstantInClass::class, $this->constantReflectionInClass->getDeclaringClassName());
    }

    public function testGetName(): void
    {
        $this->assertSame('SOME_CONSTANT', $this->constantReflection->getName());
        $this->assertSame('CONSTANT_INSIDE', $this->constantReflectionInClass->getName());
    }

    public function testGetShortName(): void
    {
        $this->assertSame('SOME_CONSTANT', $this->constantReflection->getShortName());
        $this->assertSame('CONSTANT_INSIDE', $this->constantReflectionInClass->getShortName());
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('string', $this->constantReflection->getTypeHint());
        $this->assertSame('int', $this->constantReflectionInClass->getTypeHint());
    }

    public function testGetValue(): void
    {
        $this->assertSame('some value', $this->constantReflection->getValue());
        $this->assertSame(55, $this->constantReflectionInClass->getValue());
    }

    public function testGetDefinition(): void
    {
        $this->assertSame("'some value'", $this->constantReflection->getValueDefinition());
        $this->assertSame('55', $this->constantReflectionInClass->getValueDefinition());
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->constantReflection->isDocumented());
        $this->assertTrue($this->constantReflectionInClass->isDocumented());
    }
}
