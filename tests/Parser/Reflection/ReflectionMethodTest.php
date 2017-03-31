<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Project\ReflectionMethod;
use TokenReflection\Broker;

final class ReflectionMethodTest extends AbstractContainerAwareTestCase
{
    /**
     * @var MethodReflectionInterface
     */
    private $reflectionMethod;

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

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

        $this->reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionMethod->getDeclaringClass());
    }

    public function testGetDeclaringClassName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionMethod->getDeclaringClassName());
    }

    public function testIsAbstract(): void
    {
        $this->assertFalse($this->reflectionMethod->isAbstract());
    }

    public function testIsFinal(): void
    {
        $this->assertFalse($this->reflectionMethod->isFinal());
    }

    public function testIsPrivate(): void
    {
        $this->assertFalse($this->reflectionMethod->isPrivate());
    }

    public function testIsProtected(): void
    {
        $this->assertFalse($this->reflectionMethod->isProtected());
    }

    public function testIsPublic(): void
    {
        $this->assertTrue($this->reflectionMethod->isPublic());
    }

    public function testIsStatic(): void
    {
        $this->assertFalse($this->reflectionMethod->isStatic());
    }

    public function testGetDeclaringTrait(): void
    {
        $this->assertNull($this->reflectionMethod->getDeclaringTrait());
    }

    public function testGetDeclaringTraitName(): void
    {
        $this->assertSame('', $this->reflectionMethod->getDeclaringTraitName());
    }

    public function testGetOriginalName(): void
    {
        $this->assertSame('', $this->reflectionMethod->getOriginalName());
    }

    public function testGetParameters(): void
    {
        $this->assertCount(3, $this->reflectionMethod->getParameters());
    }
}
