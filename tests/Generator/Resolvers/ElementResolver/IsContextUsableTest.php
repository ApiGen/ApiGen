<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Tests\MethodInvoker;

final class IsContextUsableTest extends AbstractElementResolverTest
{
    public function testFalse(): void
    {
        $this->assertFalse(
            MethodInvoker::callMethodOnObject($this->elementResolver, 'isContextUsable', [null])
        );

        $reflectionConstantMock = $this->createMock(ConstantReflectionInterface::class);
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
