<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use PHPUnit_Framework_MockObject_MockObject;

final class CorrectContextForClassMemberTest extends AbstractElementResolverTest
{
    public function test(): void
    {
        $reflectionMethodMock = $this->createMethodReflectionMock();

        $classReflectionMock = $this->createClassReflectionMock();
        $this->parserStorage->setClasses([
            'SomeClass' => $classReflectionMock
        ]);

        $resolvedElement = MethodInvoker::callMethodOnObject(
            $this->elementResolver,
            'correctContextForParameterOrClassMember',
            [$reflectionMethodMock]
        );

        $this->assertSame($classReflectionMock, $resolvedElement);
    }

//    /**
//     * @return PHPUnit_Framework_MockObject_MockObject|ClassMethodReflectionInterface
//     */
//    private function createMethodReflectionMock()
//    {
//        $reflectionMethodMock = $this->createMock(ClassMethodReflectionInterface::class);
//        $reflectionMethodMock->method('getDeclaringClassName')
//            ->willReturn('SomeClass');
//
//        return $reflectionMethodMock;
//    }

//    /**
//     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
//     */
//    private function createClassReflectionMock()
//    {
//        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
//        $classReflectionMock->method('getName')
//            ->willReturn('SomeClass');
//
//        return $classReflectionMock;
//    }
}
