<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\ParserStorage;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Parser\ParserStorage\ImplementersSource\ChildInterface;
use ApiGen\Tests\Parser\ParserStorage\ImplementersSource\ParentInterface;
use ApiGen\Tests\Parser\ParserStorage\ImplementersSource\SomeClass;

// @remove covered by InterfaceReflectionInterface
final class ImplementersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    /**
     * @var ClassReflectionInterface
     */
    private $parentInterfaceReflection;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/ImplementersSource']);

        $this->parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $classes = $this->parserStorage->getClasses();

        $this->parentInterfaceReflection = $classes[ParentInterface::class];
    }

    public function testGetDirectImplementersOfInterface(): void
    {
        $implementers = $this->parserStorage->getDirectImplementersOfInterface($this->parentInterfaceReflection);
        $this->assertCount(1, $implementers);

        $implementer = $implementers[0];
        $this->assertInstanceOf(ClassReflectionInterface::class, $implementer);
        $this->assertSame(ChildInterface::class, $implementer->getName());
    }

    public function testGetIndirectImplementersOfInterface(): void
    {
        $implementers = $this->parserStorage->getIndirectImplementersOfInterface($this->parentInterfaceReflection);
        $this->assertCount(1, $implementers);

        $implementer = $implementers[0];
        $this->assertInstanceOf(ClassReflectionInterface::class, $implementer);
        $this->assertSame(SomeClass::class, $implementer->getName());
    }
}
