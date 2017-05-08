<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\InterfaceReflection;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\InterfaceReflection\Source\PoorInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ImplementersTest extends AbstractParserAwareTestCase
{
    /**
     * @var InterfaceReflectionInterface
     */
    private $interfaceReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $interfaceReflections = $this->reflectionStorage->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[2];
    }

    public function testExists()
    {
        $this->assertSame(PoorInterface::class, $this->interfaceReflection->getName());
    }

    public function testGetInterfaces(): void
    {
        $this->assertCount(0, $this->interfaceReflection->getInterfaces());
    }

    public function testGetDirectImplementers(): void
    {
        $this->assertCount(1, $this->interfaceReflection->getDirectImplementers());
    }

    public function testGetIndirectImplementers(): void
    {
        $indirectImplementers = $this->interfaceReflection->getIndirectImplementers();
        $this->assertSame([], $indirectImplementers);
    }
}
