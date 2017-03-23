<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use PHPUnit_Framework_MockObject_MockObject;

final class FunctionTest extends AbstractElementResolverTest
{
    public function testGetFunction(): void
    {
        $this->parserStorage->setFunctions([
            'SomeFunction' => $this->createFunctionReflection()
        ]);

        $element = $this->elementResolver->getFunction('SomeFunction');
        $this->assertInstanceOf(ElementReflectionInterface::class, $element);
        $this->assertTrue($element->isDocumented());
    }

    public function testNonExistingFunction(): void
    {
        $this->assertNull($this->elementResolver->getFunction('NotExistingFunction'));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|FunctionReflectionInterface
     */
    private function createFunctionReflection()
    {
        $functionReflection = $this->createMock(FunctionReflectionInterface::class);
        $functionReflection->method('isDocumented')
            ->willReturn(true);

        return $functionReflection;
    }
}
