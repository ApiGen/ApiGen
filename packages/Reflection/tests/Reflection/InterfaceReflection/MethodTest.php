<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\InterfaceReflection;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\InterfaceReflection\Source\PoorInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class MethodTest extends AbstractParserAwareTestCase
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

    public function testNaming()
    {
        $this->assertSame(PoorInterface::class, $this->interfaceReflection->getName());
    }

    public function test()
    {
        $this->assertCount(1, $this->interfaceReflection->getMethods());
        $this->assertCount(1, $this->interfaceReflection->getOwnMethods());
    }
}
