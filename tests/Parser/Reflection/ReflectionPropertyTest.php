<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Project\ReflectionMethod;
use TokenReflection\Broker;

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
        /** @var Backend $backend */
        $backend = $this->container->getByType(Backend::class);

        /** @var Broker $broker */
        $broker = $this->container->getByType(Broker::class);

        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()[ReflectionMethod::class];

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            ReflectionMethod::class => $this->reflectionClass
        ]);

        $this->reflectionProperty = $this->reflectionClass->getProperty('memberCount');
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(PropertyReflectionInterface::class, $this->reflectionProperty);
    }

    public function testIsReadOnly(): void
    {
        $this->assertFalse($this->reflectionProperty->isReadOnly());
    }

    public function testIsWriteOnly(): void
    {
        $this->assertFalse($this->reflectionProperty->isWriteOnly());
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
        $this->assertSame('Project\ReflectionMethod', $this->reflectionProperty->getDeclaringClassName());
    }

    public function testGetDefaultValue(): void
    {
        $this->assertSame(52, $this->reflectionProperty->getDefaultValue());
    }

    public function testIsDefault(): void
    {
        $this->assertTrue($this->reflectionProperty->isDefault());
    }

    public function testIsPrivate(): void
    {
        $this->assertFalse($this->reflectionProperty->isPrivate());
    }

    public function testIsProtected(): void
    {
        $this->assertFalse($this->reflectionProperty->isProtected());
    }

    public function testIsPublic(): void
    {
        $this->assertTrue($this->reflectionProperty->isPublic());
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
