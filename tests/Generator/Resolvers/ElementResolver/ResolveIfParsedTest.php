<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;

final class ResolveIfParsedTest extends AbstractElementResolverTest
{
    public function test(): void
    {
        $methodReflectionMock = $this->createMethodReflectionMock();
        $this->parserStorage->setFunctions([
            'SomeFunction' => $methodReflectionMock
        ]);

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'resolveIfParsed',
            ['SomeFunction()', $methodReflectionMock]
        );

        $this->assertInstanceOf(ReflectionInterface::class, $resolvedElement);
    }
}
