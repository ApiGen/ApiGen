<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Tests\MethodInvoker;

final class IsContextUsableTest extends AbstractElementResolverTest
{
    public function testFalse(): void
    {
        $this->assertFalse(
            MethodInvoker::callMethodOnObject($this->elementResolver, 'isContextUsable', [null])
        );

        $reflectionConstantMock = $this->createMock(ClassConstantReflectionInterface::class);
        $this->assertFalse(
            MethodInvoker::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionConstantMock])
        );

        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $this->assertFalse(
            MethodInvoker::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionFunctionMock])
        );
    }

    public function testTrue(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $this->assertTrue(
            MethodInvoker::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionClassMock])
        );
    }
}
