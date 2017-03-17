<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Generator\Resolvers\ElementResolver;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionInterface;
use ApiGen\Tests\ContainerFactory;
use ApiGen\Tests\Generator\Resolvers\ElementResolver\AbstractElementResolverTest;
use ApiGen\Tests\MethodInvoker as MI;
use Mockery;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class ElementResolverTest extends AbstractElementResolverTest
{
//    public function testResolveElement(): void
//    {
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $reflectionClassMock->shouldReceive('getNamespaceAliases')->andReturn([]);
//        $reflectionClassMock->shouldReceive('getNamespaceName')->andReturn('SomeNamespace');
//        $reflectionClassMock->shouldReceive('hasProperty')->andReturn(false);
//        $reflectionClassMock->shouldReceive('hasMethod')->with('someMethod')->andReturn(true);
//        $reflectionClassMock->shouldReceive('hasMethod')->andReturn(false);
//        $reflectionClassMock->shouldReceive('getMethod')->andReturn('someMethod');
//        $reflectionClassMock->shouldReceive('hasConstant')->andReturn(false);
//        $this->assertNull($this->elementResolver->resolveElement('nonExistingMethod', $reflectionClassMock));
//
//        $this->assertSame('someMethod', $this->elementResolver->resolveElement('someMethod', $reflectionClassMock));
//        $this->assertNull($this->elementResolver->resolveElement('string', $reflectionClassMock));
//        $this->assertSame($reflectionClassMock, $this->elementResolver->resolveElement('$this', $reflectionClassMock));
//        $this->assertSame($reflectionClassMock, $this->elementResolver->resolveElement('self', $reflectionClassMock));
//    }

//    public function testRemoveStartDollar(): void
//    {
//        $this->assertSame(
//            'property',
//            MI::callMethodOnObject($this->elementResolver, 'removeStartDollar', ['$property'])
//        );
//    }
//
//
//    public function testCorrectContextForParameterOrClassMemberWithClass(): void
//    {
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $this->assertSame(
//            $reflectionClassMock,
//            MI::callMethodOnObject(
//                $this->elementResolver,
//                'correctContextForParameterOrClassMember',
//                [$reflectionClassMock]
//            )
//        );
//    }
//
//
//    public function testCorrectContextForParameterOrClassMemberWithParameter(): void
//    {
//        $reflectionParameterMock = Mockery::mock(ParameterReflectionInterface::class);
//        $reflectionParameterMock->shouldReceive('getName')->andReturn('NiceName');
//        $reflectionParameterMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
//        $resolvedElement = MI::callMethodOnObject(
//            $this->elementResolver,
//            'correctContextForParameterOrClassMember',
//            [$reflectionParameterMock]
//        );
//
//        $this->assertInstanceOf(ReflectionInterface::class, $resolvedElement);
//        $this->assertSame('NiceName', $resolvedElement->getName());
//    }
//
//
//    public function testCorrectContextForParameterOrClassMemberWithParameterAndNoClass(): void
//    {
//        $reflectionParameterMock = Mockery::mock(ParameterReflectionInterface::class);
//        $reflectionParameterMock->shouldReceive('getDeclaringClassName')->andReturnNull();
//        $reflectionParameterMock->shouldReceive('getDeclaringFunctionName')->andReturn('SomeFunction');
//
//        $resolvedElement = MI::callMethodOnObject(
//            $this->elementResolver,
//            'correctContextForParameterOrClassMember',
//            [$reflectionParameterMock]
//        );
//        $this->assertInstanceOf(ElementReflectionInterface::class, $resolvedElement);
//        $this->assertSame('NiceName', $resolvedElement->getName());
//    }
//
//
//    public function testCorrectContextForParameterOrClassMemberWithMethod(): void
//    {
//        $reflectionMethodMock = Mockery::mock(MethodReflectionInterface::class);
//        $reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
//        $resolvedElement = MI::callMethodOnObject(
//            $this->elementResolver,
//            'correctContextForParameterOrClassMember',
//            [$reflectionMethodMock]
//        );
//
//        $this->assertInstanceOf(ElementReflectionInterface::class, $resolvedElement);
//        $this->assertSame('NiceName', $resolvedElement->getName());
//    }
//
//
//    public function testResolveContextForClassPropertyInParent(): void
//    {
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn('SomeClass');
//
//        $resolvedElement = MI::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
//            'parent::$start', $reflectionClassMock, 5
//        ]);
//
//        $this->assertInstanceOf(ElementReflectionInterface::class, $resolvedElement);
//    }
//
//
//    public function testResolveContextForClassPropertyInSelf(): void
//    {
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn('SomeClass');
//
//        $resolvedElement = MI::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
//            'self::$start', $reflectionClassMock, 25
//        ]);
//
//        $this->assertInstanceOf(ClassReflectionInterface::class, $resolvedElement);
//    }
//
//
//    public function testResolveContextForClassPropertyForNonExisting(): void
//    {
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn('SomeClass');
//        $reflectionClassMock->shouldReceive('getNamespaceName')->andReturn('SomeNamespace');
//        $reflectionClassMock->shouldReceive('getNamespaceAliases')->andReturn([]);
//
//        $resolvedElement = MI::callMethodOnObject($this->elementResolver, 'resolveContextForClassProperty', [
//            '$start', $reflectionClassMock, 25
//        ]);
//        $this->assertNull($resolvedElement);
//    }
//
//
//    public function testResolveContextForSelfProperty(): void
//    {
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $reflectionClassMock->shouldReceive('getParentClassName')->andReturn('SomeClass');
//        $reflectionClassMock->shouldReceive('getNamespaceName')->andReturn('SomeNamespace');
//        $reflectionClassMock->shouldReceive('getNamespaceAliases')->andReturn([]);
//
//        $class = MI::callMethodOnObject(
//            $this->elementResolver,
//            'resolveContextForSelfProperty',
//            ['SomeClass::$property', 9, $reflectionClassMock]
//        );
//        $this->assertInstanceOf(ElementReflectionInterface::class, $class);
//
//        $class = MI::callMethodOnObject(
//            $this->elementResolver,
//            'resolveContextForSelfProperty',
//            ['SomeOtherClass::$property', 14, $reflectionClassMock]
//        );
//        $this->assertInstanceOf(ElementReflectionInterface::class, $class);
//
//        $this->assertNull(
//            MI::callMethodOnObject(
//                $this->elementResolver,
//                'resolveContextForSelfProperty',
//                ['NonExistingClass::$property', 14, $reflectionClassMock]
//            )
//        );
//    }
//
//
//    public function testIsContextUsable(): void
//    {
//        $this->assertFalse(
//            MI::callMethodOnObject($this->elementResolver, 'isContextUsable', [null])
//        );
//
//        $reflectionConstantMock = Mockery::mock(ConstantReflectionInterface::class);
//        $this->assertFalse(
//            MI::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionConstantMock])
//        );
//
//        $reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
//        $this->assertFalse(
//            MI::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionFunctionMock])
//        );
//
//        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
//        $this->assertTrue(
//            MI::callMethodOnObject($this->elementResolver, 'isContextUsable', [$reflectionClassMock])
//        );
//    }
}
