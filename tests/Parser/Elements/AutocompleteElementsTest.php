<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Elements\AutocompleteElements;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class AutocompleteElementsTest extends TestCase
{
    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;

    protected function setUp(): void
    {
        $elementsStorageMock = $this->createElementStorageMock();
        $this->autocompleteElements = new AutocompleteElements($elementsStorageMock);
    }

    public function testGetElementsClasses(): void
    {
        $elements = $this->autocompleteElements->getElements();
        $this->assertSame([
            ['c', 'ClassPrettyName'],
            ['p', 'ClassPrettyName::$propertyName'],
            ['m', 'ClassPrettyName::methodName'],
            ['f', 'FunctionPrettyName'],
        ], $elements);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ElementStorageInterface
     */
    private function createElementStorageMock(): ElementStorageInterface
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getPrettyName')
            ->willReturn('ClassPrettyName');
        $classReflectionMock->method('getOwnConstants')
            ->willReturn([]);

        $methodReflection = $this->createMock(MethodReflectionInterface::class);
        $methodReflection->method('getPrettyName')
            ->willReturn('ClassPrettyName::methodName');
        $classReflectionMock->method('getOwnMethods')
            ->willReturn([$methodReflection]);

        $propertyReflection = $this->createMock(PropertyReflectionInterface::class);
        $propertyReflection->method('getPrettyName')
            ->willReturn('ClassPrettyName::$propertyName');
        $classReflectionMock->method('getOwnProperties')
            ->willReturn([$propertyReflection]);

        $functionReflectionMock = $this->createMock(FunctionReflectionInterface::class);
        $functionReflectionMock->method('getPrettyName')
            ->willReturn('FunctionPrettyName');

        $elementsStorageMock = $this->createMock(ElementStorageInterface::class);
        $elementsStorageMock->method('getElements')
            ->willReturn([
                'classes' => [$classReflectionMock],
                'functions' => [$functionReflectionMock]
            ]);
        return $elementsStorageMock;
    }
}
