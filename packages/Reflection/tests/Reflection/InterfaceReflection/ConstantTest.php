<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\InterfaceReflection;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ConstantTest extends AbstractParserAwareTestCase
{
    /**
     * @var InterfaceReflectionInterface
     */
    private $interfaceReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $interfaceReflections = $this->reflectionStorage->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[0];
    }

    public function test()
    {
        $this->assertCount(1, $this->interfaceReflection->getOwnConstants());
//        $this->assertCount(1, $this->interfaceReflection->getInheritedConstants());
    }
}
