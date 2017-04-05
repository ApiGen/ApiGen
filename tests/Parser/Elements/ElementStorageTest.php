<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class ElementStorageTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    protected function setUp(): void
    {
        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses($this->getReflectionClassMocks());
        $parserStorage->setFunctions([$this->getFunctionReflectionMock()]);

        $this->elementStorage = $this->container->getByType(ElementStorageInterface::class);
    }

    public function testEnsureCategorization(): void
    {
        $this->assertCount(1, $this->elementStorage->getClasses());
        $this->assertCount(1, $this->elementStorage->getTraits());
        $this->assertCount(1, $this->elementStorage->getInterfaces());
        $this->assertCount(1, $this->elementStorage->getExceptions());
        $this->assertCount(4, $this->elementStorage->getClassElements());

        $this->assertCount(1, $this->elementStorage->getFunctions());

        $this->assertCount(1, $this->elementStorage->getNamespaces());
    }

    /**
     * @return ReflectionClass[]
     */
    private function getReflectionClassMocks(): array
    {
        $classes = [];
        $reflectionClassMock = $this->getReflectionClassMock();
        $reflectionClassMock->method('isDocumented')
            ->willReturn(true);
        $reflectionClassMock->method('isInterface')
            ->willReturn(false);
        $reflectionClassMock->method('isTrait')
            ->willReturn(false);
        $reflectionClassMock->method('isException')
            ->willReturn(false);
        $classes[] = $reflectionClassMock;

        $reflectionClassMock2 = $this->getReflectionClassMock();
        $reflectionClassMock2->method('isDocumented')
            ->willReturn(false);
        $classes[] = $reflectionClassMock2;

        $reflectionClassMock3 = $this->getReflectionClassMock();
        $reflectionClassMock3->method('isDocumented')
            ->willReturn(true);
        $reflectionClassMock3->method('isInterface')
            ->willReturn(true);
        $reflectionClassMock3->method('isTrait')
            ->willReturn(false);
        $reflectionClassMock3->method('isException')
            ->willReturn(false);
        $classes[] = $reflectionClassMock3;

        $reflectionClassMock4 = $this->getReflectionClassMock();
        $reflectionClassMock4->method('isDocumented')
            ->willReturn(true);
        $reflectionClassMock4->method('isInterface')
            ->willReturn(false);
        $reflectionClassMock4->method('isTrait')
            ->willReturn(true);
        $reflectionClassMock4->method('isException')
            ->willReturn(false);
        $classes[] = $reflectionClassMock4;

        $reflectionClassMock5 = $this->getReflectionClassMock();
        $reflectionClassMock5->method('isDocumented')
            ->willReturn(true);
        $reflectionClassMock5->method('isInterface')
            ->willReturn(false);
        $reflectionClassMock5->method('isTrait')
            ->willReturn(false);
        $reflectionClassMock5->method('isException')
            ->willReturn(true);
        $classes[] = $reflectionClassMock5;
        return $classes;
    }

    /**
     * @return ClassReflectionInterface|PHPUnit_Framework_MockObject_MockObject

     */
    private function getReflectionClassMock()
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getPseudoNamespaceName')
            ->willReturn('SomeNamespace');
        $reflectionClassMock->method('getShortName')
            ->willReturn('SomeShortClass');
        $reflectionClassMock->method('getOwnMethods')
            ->willReturn([]);
        $reflectionClassMock->method('getOwnConstants')
            ->willReturn([]);
        $reflectionClassMock->method('getOwnProperties')
            ->willReturn([]);

        return $reflectionClassMock;
    }

    /**
     * @return FunctionReflectionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private function getFunctionReflectionMock()
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('isDocumented')
            ->willReturn(true);
        $reflectionFunctionMock->method('getPseudoNamespaceName')
            ->willReturn('SomeNamespace');
        $reflectionFunctionMock->method('getShortName')
            ->willReturn('SomeShortClass');

        return $reflectionFunctionMock;
    }
}
