<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Tests\MethodInvoker;
use PHPUnit_Framework_MockObject_MockObject;

final class CorrectContextForFunctionParameterTest extends AbstractElementResolverTest
{
    public function test(): void
    {
        $functionReflectionMock = $this->createFunctionReflectionMock();

        $this->parserStorage->setFunctions([
            'SomeFunction' => $functionReflectionMock
        ]);

        $reflectionParameterMock = $this->createReflectionParameterInFunctionMock();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'correctContextForParameterOrClassMember',
            [$reflectionParameterMock]
        );


        $this->assertInstanceOf(FunctionReflectionInterface::class, $resolvedElement);
        $this->assertSame($functionReflectionMock, $resolvedElement);
    }


    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ParameterReflectionInterface
     */
    private function createReflectionParameterInFunctionMock()
    {
        $reflectionParameterMock = $this->createMock(ParameterReflectionInterface::class);
        $reflectionParameterMock->method('getDeclaringClassName')
            ->willReturn('');

        $reflectionParameterMock->method('getDeclaringFunctionName')
            ->willReturn('SomeFunction');

        return $reflectionParameterMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|FunctionReflectionInterface
     */
    private function createFunctionReflectionMock()
    {
        $functionReflectionMock = $this->createMock(FunctionReflectionInterface::class);
        $functionReflectionMock->method('getName')
            ->willReturn('SomeFunction');

        $functionReflectionMock->method('isDocumented')
            ->willReturn(true);

        return $functionReflectionMock;
    }
}
