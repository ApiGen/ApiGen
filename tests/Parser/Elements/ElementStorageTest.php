<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Parser\Elements\GroupSorter;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionElement;
use ApiGen\Parser\Tests\MethodInvoker;
use ApiGen\Tests\ContainerAwareTestCase;
use PHPUnit_Framework_MockObject_MockObject;
use TokenReflection\Php\IReflection;

final class ElementStorageTest extends ContainerAwareTestCase
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    protected function setUp()
    {
        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses($this->getReflectionClassMocks());
        $parserStorage->setFunctions([$this->getFunctionReflectionMock()]);
        $parserStorage->setConstants([$this->getConstantReflectionMock()]);

        $this->elementStorage = $this->container->getByType(ElementStorageInterface::class);
    }


//    public function testEnsureCategorization(): void
//    {
//        $this->assertCount(1, $this->elementStorage->getClasses());
//        $this->assertCount(1, $this->elementStorage->getTraits());
//        $this->assertCount(1, $this->elementStorage->getInterfaces());
//        $this->assertCount(1, $this->elementStorage->getExceptions());
//        $this->assertCount(4, $this->elementStorage->getClassElements());
//
//        $this->assertCount(1, $this->elementStorage->getFunctions());
//        $this->assertCount(1, $this->elementStorage->getConstants());
//
//        $this->assertCount(1, $this->elementStorage->getNamespaces());
//    }


    private function prepareElementStorage(): ElementStorage
    {
        $iReflectionClassMock = $this->createMock(IReflection::class);
        $iReflectionClassMock->method('getAnnotations')
            ->willReturn([]);

//        $this->reflectionClass = new ReflectionClass($iReflectionClassMock);
//        $elementResolverMock = $this->createMock(ElementResolverInterface::class);
//        $elementResolverMock->method('resolveElement')
//            ->willReturn($this->reflectionClass);

//        return new ElementStorage(
//            $parserStorageMock,
//            $groupSorterMock,
//            $elementResolverMock
//        );
    }


    /**
     * @return ReflectionClass[]
     */
    private function getReflectionClassMocks(): array
    {
        $classes = [];
        $reflectionClassMock = $this->getReflectionClassMock();
        $reflectionClassMock->method('isDocumented')->willReturn(true);
        $reflectionClassMock->method('isInterface')->willReturn(false);
        $reflectionClassMock->method('isTrait')->willReturn(false);
        $reflectionClassMock->method('isException')->willReturn(false);
        $classes[] = $reflectionClassMock;

        $reflectionClassMock2 = $this->getReflectionClassMock();
        $reflectionClassMock2->method('isDocumented')->willReturn(false);
        $classes[] = $reflectionClassMock2;

        $reflectionClassMock3 = $this->getReflectionClassMock();
        $reflectionClassMock3->method('isDocumented')->willReturn(true);
        $reflectionClassMock3->method('isInterface')->willReturn(true);
        $reflectionClassMock3->method('isTrait')->willReturn(false);
        $reflectionClassMock3->method('isException')->willReturn(false);
        $classes[] = $reflectionClassMock3;

        $reflectionClassMock4 = $this->getReflectionClassMock();
        $reflectionClassMock4->method('isDocumented')->willReturn(true);
        $reflectionClassMock4->method('isInterface')->willReturn(false);
        $reflectionClassMock4->method('isTrait')->willReturn(true);
        $reflectionClassMock4->method('isException')->willReturn(false);
        $classes[] = $reflectionClassMock4;

        $reflectionClassMock5 = $this->getReflectionClassMock();
        $reflectionClassMock5->method('isDocumented')->willReturn(true);
        $reflectionClassMock5->method('isInterface')->willReturn(false);
        $reflectionClassMock5->method('isTrait')->willReturn(false);
        $reflectionClassMock5->method('isException')->willReturn(true);
        $classes[] = $reflectionClassMock5;
        return $classes;
    }


    /**
     * @return ClassReflectionInterface|

     */
    private function getReflectionClassMock()
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getPseudoNamespaceName')->willReturn('SomeNamespace');
        $reflectionClassMock->method('getShortName')->willReturn('SomeShortClass');
        $reflectionClassMock->method('getOwnMethods')->willReturn([]);
        $reflectionClassMock->method('getOwnConstants')->willReturn([]);
        $reflectionClassMock->method('getOwnProperties')->willReturn([]);
        $reflectionClassMock->method('getAnnotation')->willReturn([]);

        return $reflectionClassMock;
    }


    /**
     * @return FunctionReflectionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private function getFunctionReflectionMock()
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('isDocumented')->willReturn(true);
        $reflectionFunctionMock->method('getPseudoNamespaceName')->willReturn('SomeNamespace');
        $reflectionFunctionMock->method('getShortName')->willReturn('SomeShortClass');
        $reflectionFunctionMock->method('getAnnotation')->willReturn([]);

        return $reflectionFunctionMock;
    }


    /**
     * @return ConstantReflectionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private function getConstantReflectionMock()
    {
        $constantReflectionMock = $this->createMock(ConstantReflectionInterface::class);
        $constantReflectionMock->method('isDocumented')
            ->willReturn(true);
        $constantReflectionMock->method('getPseudoNamespaceName')
            ->willReturn('SomeNamespace');
        $constantReflectionMock->method('getShortName')
            ->willReturn('SomeShortClass');
        $constantReflectionMock->method('getAnnotation')
            ->willReturn([]);

        return $constantReflectionMock;
    }
}
