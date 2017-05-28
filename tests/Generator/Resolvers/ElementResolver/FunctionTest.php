<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;

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
}
