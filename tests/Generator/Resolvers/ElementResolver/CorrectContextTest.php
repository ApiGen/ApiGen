<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\ParameterReflectionInterface;
use ApiGen\Tests\MethodInvoker;
use PHPUnit_Framework_MockObject_MockObject;

final class CorrectContextTest extends AbstractElementResolverTest
{
    public function test(): void
    {
        $reflectionParameterMock = $this->createReflectionParameterMock();

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'correctContextForParameterOrClassMember',
            [$reflectionParameterMock]
        );

        $this->assertSame($reflectionParameterMock, $resolvedElement);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ParameterReflectionInterface
     */
    private function createReflectionParameterMock()
    {
        $parameterReflectionMock = $this->createMock(ParameterReflectionInterface::class);
        $parameterReflectionMock->method('getName')
            ->willReturn('NiceName');

        $parameterReflectionMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');

        return $parameterReflectionMock;
    }
}
