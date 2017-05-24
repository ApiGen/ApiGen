<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use PHPUnit_Framework_MockObject_MockObject;

final class FunctionTest extends AbstractElementResolverTest
{
    public function testGetFunction(): void
    {
        $this->parserStorage->setFunctions([
            'SomeFunction' => $this->createFunctionReflection()
        ]);

        $element = $this->elementResolver->getFunction('SomeFunction');
        $this->assertInstanceOf(ReflectionInterface::class, $element);
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
        return $this->createMock(FunctionReflectionInterface::class);
    }
}
