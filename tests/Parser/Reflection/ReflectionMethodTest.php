<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Project\ReflectionMethod;

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
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionMethodSource']);

        $this->reflectionClass = $parserStorage->getClasses()[ReflectionMethod::class];
        $this->reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionMethod->getDeclaringClass());
    }

    public function testGetDeclaringClassName(): void
    {
        $this->assertSame(ReflectionMethod::class, $this->reflectionMethod->getDeclaringClassName());
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
