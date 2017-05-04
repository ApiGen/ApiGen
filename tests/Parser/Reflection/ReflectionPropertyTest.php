<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Parser\Reflection\ReflectionMethodSource\ReflectionMethod;

final class ReflectionPropertyTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    /**
     * @var PropertyReflectionInterface
     */
    private $reflectionProperty;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionMethodSource']);

        $this->reflectionClass = $parserStorage->getClasses()[ReflectionMethod::class];
        $this->reflectionProperty = $this->reflectionClass->getProperty('memberCount');
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(PropertyReflectionInterface::class, $this->reflectionProperty);
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('int', $this->reflectionProperty->getTypeHint());
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionProperty->getDeclaringClass());
    }

    public function testGetDeclaringClassName(): void
    {
        $this->assertSame(
            'ApiGen\Tests\Parser\Reflection\ReflectionMethodSource\ReflectionMethod',
            $this->reflectionProperty->getDeclaringClassName()
        );
    }

    public function testGetDefaultValue(): void
    {
        $this->assertSame(52, $this->reflectionProperty->getDefaultValue());
    }

    public function testIsDefault(): void
    {
        $this->assertTrue($this->reflectionProperty->isDefault());
    }

    public function testIsStatic(): void
    {
        $this->assertFalse($this->reflectionProperty->isStatic());
    }

    public function testGetDeclaringTrait(): void
    {
        $this->assertNull($this->reflectionProperty->getDeclaringTrait());
    }

    public function testGetDeclaringTraitName(): void
    {
        $this->assertSame('', $this->reflectionProperty->getDeclaringTraitName());
    }
}
